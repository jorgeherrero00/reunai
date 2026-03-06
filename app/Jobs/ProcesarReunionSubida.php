<?php

namespace App\Jobs;

use App\Models\Meeting;
use App\Services\NotionService;
use App\Services\SlackService;
use App\Services\GoogleSheetsService;
use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use FFMpeg;

class ProcesarReunionSubida implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $meeting;

    public function __construct(Meeting $meeting)
    {
        $this->meeting = $meeting;
    }

    public function handle()
    {
        Log::info('🎯 Iniciando procesamiento de reunión', ['meeting_id' => $this->meeting->id]);

        try {
            // 1. Procesar archivo (video → audio si es necesario)
            $audioPath = $this->procesarArchivo();
            
            // 2. Transcribir con Whisper
            $transcripcion = $this->transcribirAudio($audioPath);
            Log::info('📝 Transcripción completada', ['length' => strlen($transcripcion)]);

            // 🚨 Si no hay nada transcrito, no seguir con GPT
            Log::info('Transcripcion válida?', ['valida' => $this->transcripcionValida($transcripcion)]);
        if (!$this->transcripcionValida($transcripcion)) {
            $this->meeting->update([
                'transcripcion' => $transcripcion,
                'resumen'       => '⚠️ No se detectó contenido hablado en el audio.',
            ]);

            Log::warning('⚠️ Reunión sin contenido hablado', [
                'meeting_id' => $this->meeting->id,
                'transcripcion' => $transcripcion
            ]);

            return;
        }
            
            // 3. Generar resumen y tareas con GPT
            $resultado = $this->generarResumenYTareas($transcripcion);
            
            // 4. Guardar en base de datos
            $this->guardarResultados($transcripcion, $resultado);
            
            // 5. Enviar a integraciones
            $this->enviarAIntegraciones($resultado);
            
            // 6. Notificar vía webhook/email
            $this->enviarNotificaciones($resultado);

            Log::info('✅ Reunión procesada exitosamente', ['meeting_id' => $this->meeting->id]);

        } catch (\Exception $e) {
            Log::error('❌ Error procesando reunión', [
                'meeting_id' => $this->meeting->id,
                'error' => $e->getMessage(),
                'line' => $e->getLine()
            ]);
            throw $e;
        }
    }

    private function procesarArchivo()
    {
        $pathOriginal = storage_path('app/public/' . $this->meeting->archivo);
        
        if ($this->meeting->formato_origen === 'video') {
            Log::info('🎬 Convirtiendo video a audio');
            
            $nombreBase = pathinfo($this->meeting->archivo, PATHINFO_FILENAME);
            $nuevoNombre = $nombreBase . '.mp3';
            $nuevoPath = storage_path('app/public/reuniones/' . $nuevoNombre);

            $ffmpeg = \FFMpeg\FFMpeg::create([
                'ffmpeg.binaries'  => '/usr/bin/ffmpeg',
                'ffprobe.binaries' => '/usr/bin/ffprobe',
            ]);

            $video = $ffmpeg->open($pathOriginal);
            $video->save(new \FFMpeg\Format\Audio\Mp3(), $nuevoPath);

            // Actualizar registro
            Storage::disk('public')->delete($this->meeting->archivo);
            $this->meeting->update([
                'archivo' => 'reuniones/' . $nuevoNombre,
                'formato_origen' => 'audio_extraido',
            ]);
            
            return $nuevoPath;
        }
        
        return $pathOriginal;
    }

    private function transcribirAudio($audioPath)
    {
        Log::info('🧠 Transcribiendo audio con Whisper');

        $fileSizeMB = filesize($audioPath) / 1024 / 1024;
        Log::info('📦 Tamaño del archivo de audio', ['mb' => round($fileSizeMB, 2)]);

        // Whisper API tiene un límite de 25MB por archivo
        // Para reuniones largas, dividimos en chunks de ~10 minutos
        if ($fileSizeMB > 24) {
            Log::info('⚠️ Archivo grande detectado, dividiendo en chunks para Whisper');
            return $this->transcribirAudioEnChunks($audioPath);
        }

        return $this->transcribirChunk($audioPath, basename($audioPath));
    }

    private function transcribirAudioEnChunks($audioPath)
    {
        // Obtener duración total del audio con ffprobe
        $durationProcess = new \Symfony\Component\Process\Process([
            'ffprobe',
            '-v', 'error',
            '-show_entries', 'format=duration',
            '-of', 'default=noprint_wrappers=1:nokey=1',
            $audioPath,
        ]);
        $durationProcess->run();

        if (!$durationProcess->isSuccessful()) {
            throw new \Exception('No se pudo obtener la duración del audio: ' . $durationProcess->getErrorOutput());
        }

        $totalSeconds = (int) floatval(trim($durationProcess->getOutput()));
        $chunkSeconds = 600; // 10 minutos por chunk
        $transcripciones = [];
        $chunkDir = sys_get_temp_dir() . '/reunai_chunks_' . $this->meeting->id;

        if (!is_dir($chunkDir)) {
            mkdir($chunkDir, 0755, true);
        }

        try {
            $chunkIndex = 0;
            for ($start = 0; $start < $totalSeconds; $start += $chunkSeconds) {
                $chunkPath = $chunkDir . '/chunk_' . $chunkIndex . '.mp3';
                $duration = min($chunkSeconds, $totalSeconds - $start);

                Log::info("🔪 Generando chunk {$chunkIndex}", [
                    'start' => $start,
                    'duration' => $duration,
                    'path' => $chunkPath,
                ]);

                $splitProcess = new \Symfony\Component\Process\Process([
                    'ffmpeg', '-y',
                    '-i', $audioPath,
                    '-ss', (string) $start,
                    '-t', (string) $duration,
                    '-vn',
                    '-ar', '16000',  // 16kHz suficiente para Whisper y reduce tamaño
                    '-ac', '1',      // mono
                    '-b:a', '32k',   // bitrate bajo para minimizar tamaño
                    $chunkPath,
                ]);
                $splitProcess->setTimeout(120);
                $splitProcess->run();

                if (!$splitProcess->isSuccessful()) {
                    throw new \Symfony\Component\Process\Exception\ProcessFailedException($splitProcess);
                }

                $chunkSizeMB = filesize($chunkPath) / 1024 / 1024;
                Log::info("📦 Chunk {$chunkIndex} generado", ['mb' => round($chunkSizeMB, 2)]);

                $transcripciones[] = $this->transcribirChunk($chunkPath, "chunk_{$chunkIndex}.mp3");
                $chunkIndex++;

                // Pequeña pausa para no saturar la API
                if ($start + $chunkSeconds < $totalSeconds) {
                    usleep(500000); // 0.5s
                }
            }
        } finally {
            // Limpiar chunks temporales
            $files = glob($chunkDir . '/*.mp3');
            foreach ($files as $file) {
                @unlink($file);
            }
            @rmdir($chunkDir);
        }

        $transcripcionCompleta = implode(' ', array_filter($transcripciones));
        Log::info('📝 Transcripción completa (chunks)', [
            'chunks' => count($transcripciones),
            'length' => strlen($transcripcionCompleta),
        ]);

        return $transcripcionCompleta;
    }

    private function transcribirChunk($audioPath, $filename)
    {
        $response = Http::timeout(300)
            ->attach('file', fopen($audioPath, 'r'), $filename)
            ->withToken(config('services.openai.key'))
            ->post('https://api.openai.com/v1/audio/transcriptions', [
                'model' => 'whisper-1',
            ]);

        if (!$response->successful()) {
            throw new \Exception('Error en transcripción de chunk: ' . $response->body());
        }

        return $response->json()['text'] ?? '';
    }

    private function generarResumenYTareas($transcripcion)
    {
         Log::info('🤖 Generando resumen, tareas e insights con GPT-4');

        // Generar resumen
        $resumen = $this->generarResumen($transcripcion);
        
        // Extraer tareas
        $tareas = $this->extraerTareas($transcripcion);

        // Generar insight conductual
        $insight = $this->generarInsight($transcripcion);

        // 🆕 Generar análisis de sentimiento
        $sentimentAnalysis = $this->generarAnalisisSentimiento($transcripcion);

        return [
            'resumen' => $resumen,
            'tareas' => $tareas,
            'insight' => $insight,
            'sentiment_analysis' => $sentimentAnalysis, // 👈 Nuevo
        ];
    }

    private function generarResumen($transcripcion)
{
    $prompt = <<<TXT
    INSTRUCCIÓN CRÍTICA: Responde ÚNICAMENTE con HTML puro. NO uses bloques de código Markdown.

    Tu respuesta debe empezar directamente con <h3> y terminar con </ul> o </p>.
    NO añadas ```html al inicio ni ``` al final.

    Genera un resumen ejecutivo usando esta estructura HTML:

    <h3>Objetivo de la reunión</h3>
    <p>Descripción del objetivo principal</p>

    <h3>Puntos clave tratados</h3>
    <ul>
        <li>Punto importante 1</li>
        <li>Punto importante 2</li>
    </ul>

    <h3>Decisiones tomadas</h3>
    <p>Decisiones concretas o "No se tomaron decisiones explícitas"</p>

    <h3>Próximos pasos</h3>
    <ul>
        <li>Acción 1</li>
        <li>Acción 2</li>
    </ul>

    Texto de la reunión:
    ---
    $transcripcion
    TXT;

    $response = Http::withToken(config('services.openai.key'))
        ->post('https://api.openai.com/v1/chat/completions', [
            'model' => 'gpt-4o-mini',
            'messages' => [
                [
                    'role' => 'system', 
                    'content' => 'Eres un asistente que responde SOLO en HTML válido. Tu respuesta siempre empieza con una etiqueta HTML como <h3>, nunca con texto plano ni bloques de código.'
                ],
                ['role' => 'user', 'content' => $prompt],
            ],
            'temperature' => 0.3,
        ]);

    if (!$response->successful()) {
        throw new \Exception('Error generando resumen: ' . $response->body());
    }

    $html = $response->json()['choices'][0]['message']['content'] ?? '';
    
    // 🧹 CRÍTICO: Eliminar bloques de código Markdown
    $html = preg_replace('/^```html\s*/i', '', $html);
    $html = preg_replace('/^```\s*/i', '', $html);
    $html = preg_replace('/\s*```$/i', '', $html);
    $html = trim($html);
    
    Log::info('📄 HTML generado (limpio)', ['preview' => substr($html, 0, 200)]);
    
    return $html;
}

    private function extraerTareas($transcripcion)
    {
        $prompt = <<<TXT
        Extrae una lista clara y numerada de tareas pendientes encontradas en esta transcripción.

        Por cada tarea, indica:
        - Responsable (si aparece)
        - Acción concreta
        - Fecha límite (si se menciona)

        Formato:
        1. [Responsable]: [Acción] (Fecha si la hay)

        Si no hay tareas, responde sólo con: "NINGUNA"

        Texto de la reunión:
        ---
        $transcripcion
        TXT;

        $response = Http::withToken(config('services.openai.key'))
            ->post('https://api.openai.com/v1/chat/completions', [
                'model' => 'gpt-4o-mini',
                'messages' => [
                    ['role' => 'system', 'content' => 'Eres un asistente que extrae tareas de reuniones.'],
                    ['role' => 'user', 'content' => $prompt],
                ],
            ]);

        if (!$response->successful()) {
            throw new \Exception('Error extrayendo tareas: ' . $response->body());
        }

        $tareasTexto = $response->json()['choices'][0]['message']['content'] ?? '';
        
        if (strtoupper(trim($tareasTexto)) === 'NINGUNA') {
            return [];
        }

        // Procesar líneas de tareas
        $tareas = [];
        $lineas = explode("\n", $tareasTexto);
        
        foreach ($lineas as $linea) {
            $descripcion = preg_replace('/^\d+\.\s*/', '', trim($linea));
            if (!empty($descripcion)) {
                $tareas[] = $descripcion;
            }
        }

        return $tareas;
    }

    private function generarInsight($transcripcion)
{
    $prompt = <<<TXT
    INSTRUCCIÓN CRÍTICA: Responde ÚNICAMENTE con HTML puro. NO uses bloques de código Markdown.

    Tu respuesta debe empezar directamente con <h4> y terminar con </p>.
    NO añadas ```html al inicio ni ``` al final.

    Analiza esta reunión y genera un insight conductual con esta estructura HTML:

    <h4>Estructura y liderazgo</h4>
    <p>Análisis del liderazgo y dinámica de control</p>

    <h4>Distribución de carga y accountability</h4>
    <p>Análisis de asignación de tareas</p>

    <h4>Tono y orientación</h4>
    <p>Análisis del tono general</p>

    <h4>Recomendaciones</h4>
    <p>Sugerencias para mejorar</p>

    Texto de la reunión:
    ---
    $transcripcion
    TXT;

    $response = Http::withToken(config('services.openai.key'))
        ->post('https://api.openai.com/v1/chat/completions', [
            'model' => 'gpt-4o-mini',
            'messages' => [
                [
                    'role' => 'system', 
                    'content' => 'Eres un analista experto. Respondes SOLO en HTML válido. Tu respuesta siempre empieza con <h4>, nunca con texto plano ni bloques de código.'
                ],
                ['role' => 'user', 'content' => $prompt],
            ],
            'temperature' => 0.7,
        ]);

    if (!$response->successful()) {
        Log::warning('⚠️ No se pudo generar insight', ['error' => $response->body()]);
        return null;
    }

    $html = $response->json()['choices'][0]['message']['content'] ?? null;
    
    if ($html) {
        // 🧹 CRÍTICO: Eliminar bloques de código Markdown
        $html = preg_replace('/^```html\s*/i', '', $html);
        $html = preg_replace('/^```\s*/i', '', $html);
        $html = preg_replace('/\s*```$/i', '', $html);
        $html = trim($html);
        
        Log::info('💡 HTML generado (limpio)', ['preview' => substr($html, 0, 200)]);
    }
    
    return $html;
}


private function generarAnalisisSentimiento($transcripcion)
{
    $prompt = <<<TXT
    Analiza el tono emocional de esta reunión y proporciona ÚNICAMENTE un JSON válido con esta estructura exacta:

    {
        "positivo": 68,
        "neutral": 24,
        "critico": 8,
        "resumen_sentimiento": "La reunión muestra un ambiente mayormente positivo con alta participación del equipo."
    }

    Los porcentajes deben sumar 100. Analiza:
    - Palabras positivas (logros, acuerdos, felicitaciones)
    - Palabras neutrales (información, datos, reportes)
    - Palabras críticas (problemas, preocupaciones, desacuerdos)

    NO agregues explicaciones, SOLO el JSON.

    Texto de la reunión:
    ---
    $transcripcion
    TXT;

    $response = Http::withToken(config('services.openai.key'))
        ->post('https://api.openai.com/v1/chat/completions', [
            'model' => 'gpt-4o-mini',
            'messages' => [
                [
                    'role' => 'system', 
                    'content' => 'Eres un analista de sentimientos. Respondes ÚNICAMENTE con JSON válido, sin texto adicional.'
                ],
                ['role' => 'user', 'content' => $prompt],
            ],
            'temperature' => 0.3,
        ]);

    if (!$response->successful()) {
        Log::warning('⚠️ No se pudo generar análisis de sentimiento');
        return null;
    }

    $jsonText = $response->json()['choices'][0]['message']['content'] ?? null;
    
    if ($jsonText) {
        // Limpiar posibles bloques de código markdown
        $jsonText = preg_replace('/^```json\s*/i', '', $jsonText);
        $jsonText = preg_replace('/^```\s*/i', '', $jsonText);
        $jsonText = preg_replace('/\s*```$/i', '', $jsonText);
        $jsonText = trim($jsonText);
        
        try {
            $sentimentData = json_decode($jsonText, true);
            
            if (json_last_error() === JSON_ERROR_NONE) {
                Log::info('😊 Análisis de sentimiento generado', $sentimentData);
                return $sentimentData;
            }
        } catch (\Exception $e) {
            Log::error('❌ Error parseando JSON de sentimiento', ['error' => $e->getMessage()]);
        }
    }
    
    return null;
}

    private function guardarResultados($transcripcion, $resultado)
    {
        Log::info('💾 Guardando resultados en base de datos');

        // Actualizar meeting
        $this->meeting->update([
            'transcripcion' => $transcripcion,
            'resumen' => $resultado['resumen'],
            'insight' => $resultado['insight'],
            'sentiment_analysis' => $resultado['sentiment_analysis'], // 👈 Nuevo
        ]);

        // Guardar tareas
        foreach ($resultado['tareas'] as $tareaDescripcion) {
            $this->meeting->tasks()->create([
                'descripcion' => $tareaDescripcion,
            ]);
    }
    }

    private function enviarAIntegraciones($resultado)
    {
        Log::info('🔗 Enviando a integraciones activas');

        $user = $this->meeting->user;
        $integrations = $user->integrations;

        foreach ($integrations as $integration) {
            try {
                switch ($integration->tipo) {
                    case 'notion':
                        $this->enviarANotion($integration, $resultado);
                        break;
                    
                    case 'slack':
                        $this->enviarASlack($integration, $resultado);
                        break;
                    
                    case 'google_sheets':
                        $this->enviarAGoogleSheets($integration, $resultado);
                        break;
                }
            } catch (\Exception $e) {
                Log::error("❌ Error enviando a {$integration->tipo}", [
                    'integration_id' => $integration->id,
                    'error' => $e->getMessage()
                ]);
                // Continúa con otras integraciones aunque una falle
            }
        }
    }

    private function enviarANotion($integration, $resultado)
    {
        $config = json_decode($integration->config ?? '{}', true);
        $databaseId = $config['database_id'] ?? null;

        if (!$databaseId) {
            Log::warning('⚠️ Notion: No se ha configurado database_id');
            return;
        }

        $notion = new NotionService($integration->token);
        $response = $notion->enviarResumenReunion(
            $databaseId,
            $this->meeting->titulo ?? 'Reunión sin título',
            $resultado['resumen'],
            $resultado['tareas']
        );

        if ($response['success']) {
            Log::info('✅ Enviado a Notion exitosamente');
        } else {
            Log::error('❌ Error enviando a Notion', ['error' => $response['error']]);
        }
    }

    private function enviarASlack($integration, $resultado)
    {
        $config = json_decode($integration->config ?? '{}', true);
        $canal = $config['channel'] ?? '#general';

        $slack = new SlackService($integration->token);
        $response = $slack->enviarResumenReunion(
            $canal,
            $this->meeting->titulo ?? 'Reunión sin título',
            $resultado['resumen'],
            $resultado['tareas']
        );

        if ($response['success']) {
            Log::info('✅ Enviado a Slack exitosamente');
        } else {
            Log::error('❌ Error enviando a Slack', ['error' => $response['error']]);
        }
    }

    private function enviarAGoogleSheets($integration, $resultado)
    {
        $config = json_decode($integration->config ?? '{}', true);
        $spreadsheetId = $config['spreadsheet_id'] ?? null;
        $sheetName = $config['sheet_name'] ?? 'Hoja 1';

        if (!$spreadsheetId) {
            Log::warning('⚠️ Google Sheets: No se ha configurado spreadsheet_id');
            return;
        }

        $sheets = new GoogleSheetsService($integration->token);
        
        // Configurar cabeceras si es necesario
        $sheets->configurarCabeceras($spreadsheetId, $sheetName);
        
        // Añadir fila con datos
        $response = $sheets->agregarResumenReunion(
            $spreadsheetId,
            $sheetName,
            $this->meeting->titulo ?? 'Reunión sin título',
            $resultado['resumen'],
            $resultado['tareas']
        );

        if ($response['success']) {
            Log::info('✅ Enviado a Google Sheets exitosamente');
        } else {
            Log::error('❌ Error enviando a Google Sheets', ['error' => $response['error']]);
        }
    }

    private function enviarNotificaciones($resultado)
    {
        Log::info('📧 Enviando notificaciones');

        // Webhook a N8N (como ya tenías)
        $this->enviarWebhookN8n($resultado);
        
        // Aquí podrías añadir otras notificaciones como email directo
    }

    private function transcripcionValida($texto)
{
    $limpio = trim(mb_strtolower($texto));

    // Quitar signos, espacios, puntos suspensivos, corchetes
    $limpio = preg_replace('/[^a-záéíóúñ0-9]+/u', '', $limpio);

    // Si tras limpiar no queda nada o es demasiado corto, no es válido
    return strlen($limpio) > 20;
}


    private function enviarWebhookN8n($resultado)
    {
        $payload = [
            'reunion_id' => $this->meeting->id,
            'titulo' => $this->meeting->titulo,
            'resumen' => $resultado['resumen'],
            'tareas' => $resultado['tareas'],
            'email_usuario' => $this->meeting->user->email,
        ];

        // Añadir info de Google Sheets si está configurado
        if ($this->meeting->guardar_en_google_sheets) {
            $integration = $this->meeting->user->integrations()
                ->where('tipo', 'google_sheets')
                ->first();
                
            if ($integration) {
                $config = json_decode($integration->config ?? '{}', true);
                $payload['google_sheets'] = [
                    'access_token' => $integration->token,
                    'spreadsheet_id' => $config['spreadsheet_id'] ?? null,
                    'sheet_name' => $config['sheet_name'] ?? 'Hoja 1',
                ];
            }
        }

        try {
            $response = Http::post(env('N8N_WEBHOOK_URL'), $payload);
            
            Log::info('✅ Webhook N8N enviado', [
                'status' => $response->status(),
                'body_preview' => Str::limit($response->body(), 200)
            ]);
        } catch (\Exception $e) {
            Log::error('❌ Error enviando webhook N8N', [
                'error' => $e->getMessage()
            ]);
        }
    }
}