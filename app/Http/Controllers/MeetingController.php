<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use getID3;
use App\Models\Meeting;
use App\Jobs\ProcesarReunionSubida;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\TaskController;
use App\Services\NotionService;
use App\Services\SlackService;
use App\Services\GoogleSheetsService;
use App\Services\UsageService;
use App\Models\Task;
use Illuminate\Support\Facades\Http;
use App\Mail\MeetingSummaryMail;
use Illuminate\Support\Facades\Mail;

class MeetingController extends Controller
{


    protected $usageService;

    public function __construct(UsageService $usageService)
    {
        $this->usageService = $usageService;
    }
        public function index()
    {
        $reuniones = auth()->user()->meetings()->latest()->get();
        $stats = $this->usageService->getUserStats(auth()->user());
        
        return view('reuniones.index', compact('reuniones', 'stats'));
    }
    public function store(Request $request)
    {
        $request->validate([
            'titulo' => 'nullable|string|max:255',
            'archivo' => 'required|file|mimes:mp3,wav,m4a,mp4,mov,avi,webm|max:102400',
        ]);

        $user = auth()->user();

        // ✅ VALIDACIÓN 1: Verificar límite mensual
        $canUpload = $this->usageService->canUploadMeeting($user);
        
        if (!$canUpload['allowed']) {
            $errorData = [
                'message' => $canUpload['message'],
                'reason' => $canUpload['reason'],
                'current' => $canUpload['current'],
                'limit' => $canUpload['limit'],
                'plan' => $canUpload['plan'],
                'next_reset' => $canUpload['next_reset'],
            ];

            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'error' => $canUpload['message'],
                    'data' => $errorData,
                    'upgrade_needed' => true,
                ], 403);
            }

            return back()->withErrors([
                'archivo' => $canUpload['message']
            ])->with('upgrade_needed', true);
        }

        $guardarEnSheets = $request->input('enviar_google_sheets') == 1;

        $archivo = $request->file('archivo');
        $pathTemporal = $archivo->getPathname();

        // Analizar el archivo
        $getID3 = new \getID3;
        $info = $getID3->analyze($pathTemporal);

        // ✅ VALIDACIÓN 2: Verificar duración
        $duracionSegundos = $info['playtime_seconds'] ?? 0;
        $duracionMinutos = ceil($duracionSegundos / 60);
        
        $canUploadDuration = $this->usageService->canUploadDuration($user, $duracionMinutos);
        
        if (!$canUploadDuration['allowed']) {
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'error' => $canUploadDuration['message'],
                    'upgrade_needed' => true,
                ], 403);
            }

            return back()->withErrors([
                'archivo' => $canUploadDuration['message']
            ])->with('upgrade_needed', true);
        }

        // Detectar tipo: audio o video
        $formato = $info['fileformat'] ?? 'desconocido';
        $tipo = isset($info['video']) ? 'video' : 'audio';

        // Guardar archivo
        $ruta = $archivo->store('reuniones', 'public');

        // Guardar en la base de datos
        $meeting = $user->meetings()->create([
            'titulo' => $request->input('titulo'),
            'archivo' => $ruta,
            'formato_origen' => $tipo,
            'guardar_en_google_sheets' => $guardarEnSheets,
        ]);

        ProcesarReunionSubida::dispatch($meeting);

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Reunión subida correctamente',
                'meeting_id' => $meeting->id,
                'redirect' => route('reuniones.index'),
                'remaining' => $canUpload['remaining'] - 1, // Mostrar reuniones restantes
            ]);
        }

        return redirect()->route('reuniones.index')
            ->with('success', 'Reunión subida correctamente. Te quedan ' . ($canUpload['remaining'] - 1) . ' reuniones este mes.');
    }


public function show(Meeting $meeting, UsageService $usageService)
{
    $meeting->load('tasks');

    $stats = $usageService->getUserStats(auth()->user());

    return view('reuniones.show', compact('meeting', 'stats'));
}

/**
     * Enviar todas las tareas de una reunión a una plataforma específica
     */
    public function sendAllTasks(Meeting $meeting, Request $request)
    {
        $platform = $request->input('platform');
        $user = auth()->user();
        
        // Verificar que el usuario es dueño de la reunión
        if ($meeting->user_id !== $user->id) {
            return response()->json(['success' => false, 'message' => 'No autorizado'], 403);
        }

        if ($meeting->tasks->count() === 0) {
            return response()->json(['success' => false, 'message' => 'No hay tareas para enviar'], 400);
        }

        try {
            switch ($platform) {
                case 'slack':
                    return $this->sendAllTasksToSlack($meeting, $user);
                case 'notion':
                    return $this->sendAllTasksToNotion($meeting, $user);
                case 'sheets':
                    return $this->sendAllTasksToGoogleSheets($meeting, $user);
                default:
                    return response()->json(['success' => false, 'message' => 'Plataforma no soportada'], 400);
            }
        } catch (\Exception $e) {
            Log::error('Error enviando todas las tareas', [
                'meeting_id' => $meeting->id,
                'platform' => $platform,
                'error' => $e->getMessage()
            ]);
            
            return response()->json(['success' => false, 'message' => 'Error interno del servidor'], 500);
        }
    }

    /**
     * Enviar todas las tareas a Slack
     */
    private function sendAllTasksToSlack(Meeting $meeting, $user)
    {
        $integration = $user->integrations()->where('tipo', 'slack')->first();
        
        if (!$integration) {
            return response()->json(['success' => false, 'message' => 'Integración de Slack no configurada'], 400);
        }

        $meetingTitle = $meeting->titulo ?? 'Reunión sin título';
        $config = json_decode($integration->config ?? '{}', true);
        $channel = $config['channel'] ?? '#general';

        // Crear lista de tareas
        $tasksList = $meeting->tasks->map(function ($task, $index) {
            return ($index + 1) . ". " . $task->descripcion;
        })->join("\n");

        $payload = [
            'channel' => $channel,
            'text' => "📋 Tareas de reunión desde Meetlyze",
            'attachments' => [
                [
                    'color' => '#f97316',
                    'fields' => [
                        [
                            'title' => 'Reunión',
                            'value' => $meetingTitle,
                            'short' => false
                        ],
                        [
                            'title' => "Tareas ({$meeting->tasks->count()})",
                            'value' => $tasksList,
                            'short' => false
                        ]
                    ],
                    'footer' => 'Meetlyze',
                    'ts' => now()->timestamp
                ]
            ]
        ];

        $response = Http::withToken($integration->token)
            ->post('https://slack.com/api/chat.postMessage', $payload);

        if ($response->successful() && $response->json('ok')) {
            return response()->json(['success' => true, 'message' => 'Todas las tareas enviadas a Slack']);
        }

        return response()->json(['success' => false, 'message' => 'Error al enviar a Slack'], 400);
    }

    /**
     * Enviar todas las tareas a Notion (usando NotionService)
     */
    private function sendAllTasksToNotion(Meeting $meeting, $user)
    {
        $integration = $user->integrations()->where('tipo', 'notion')->first();
        
        if (!$integration) {
            return response()->json(['success' => false, 'message' => 'Integración de Notion no configurada'], 400);
        }

        $config = json_decode($integration->config ?? '{}', true);
        $databaseId = $config['database_id'] ?? null;

        if (!$databaseId) {
            return response()->json(['success' => false, 'message' => 'Base de datos de Notion no configurada'], 400);
        }

        try {
            $notionService = new NotionService($integration->token);
            $meetingTitle = $meeting->titulo ?? 'Reunión sin título';
            $successCount = 0;
            $errors = [];

            Log::info('🚀 Enviando tareas individuales a Notion', [
                'meeting_id' => $meeting->id,
                'total_tasks' => $meeting->tasks->count(),
                'database_id' => $databaseId
            ]);

            foreach ($meeting->tasks as $index => $task) {
                // Para cada tarea, crear una entrada individual
                $taskTitle = "Tarea " . ($index + 1) . ": " . substr($task->descripcion, 0, 100);
                $taskContent = $task->descripcion;
                
                // Resumen mínimo para la tarea individual
                $taskSummary = "📋 Tarea de la reunión: {$meetingTitle}";

                $resultado = $notionService->enviarResumenReunion(
                    $databaseId,
                    $taskTitle,
                    $taskSummary,
                    [$taskContent] // Enviar como array de una tarea
                );

                if ($resultado['success']) {
                    $successCount++;
                    Log::info("✅ Tarea {$index} enviada exitosamente", [
                        'task_id' => $task->id,
                        'page_id' => $resultado['page_id'] ?? null
                    ]);
                } else {
                    $errorMsg = "Error en tarea {$index}: " . ($resultado['error'] ?? 'Error desconocido');
                    $errors[] = $errorMsg;
                    Log::error("❌ Error enviando tarea {$index}", [
                        'task_id' => $task->id,
                        'error' => $resultado['error'] ?? 'Error desconocido'
                    ]);
                }

                // Pausa para no saturar la API de Notion
                if ($index < $meeting->tasks->count() - 1) {
                    usleep(300000); // 0.3 segundos entre requests
                }
            }

            // Devolver resultado basado en el éxito
            if ($successCount === $meeting->tasks->count()) {
                return response()->json([
                    'success' => true, 
                    'message' => "✅ Todas las {$successCount} tareas enviadas a Notion exitosamente"
                ]);
            } elseif ($successCount > 0) {
                return response()->json([
                    'success' => true, 
                    'message' => "⚠️ Se enviaron {$successCount} de {$meeting->tasks->count()} tareas a Notion",
                    'errors' => $errors
                ]);
            } else {
                return response()->json([
                    'success' => false, 
                    'message' => '❌ No se pudo enviar ninguna tarea a Notion',
                    'errors' => $errors
                ], 400);
            }

        } catch (\Exception $e) {
            Log::error('❌ Excepción enviando tareas a Notion', [
                'meeting_id' => $meeting->id,
                'error' => $e->getMessage(),
                'line' => $e->getLine(),
                'file' => $e->getFile()
            ]);

            return response()->json([
                'success' => false, 
                'message' => 'Error interno: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Alternativa: Enviar todas las tareas como UN SOLO registro en Notion
     */
    private function sendAllTasksToNotionAsSingle(Meeting $meeting, $user)
    {
        $integration = $user->integrations()->where('tipo', 'notion')->first();
        
        if (!$integration) {
            return response()->json(['success' => false, 'message' => 'Integración de Notion no configurada'], 400);
        }

        $config = json_decode($integration->config ?? '{}', true);
        $databaseId = $config['database_id'] ?? null;

        if (!$databaseId) {
            return response()->json(['success' => false, 'message' => 'Base de datos de Notion no configurada'], 400);
        }

        try {
            $notionService = new NotionService($integration->token);
            $meetingTitle = $meeting->titulo ?? 'Reunión sin título';
            
            // Crear array con todas las tareas
            $allTasks = $meeting->tasks->pluck('descripcion')->toArray();
            
            // Resumen de la reunión
            $resumen = $meeting->resumen ?? "Reunión procesada el " . now()->format('d/m/Y H:i');

            Log::info('🚀 Enviando todas las tareas como un registro a Notion', [
                'meeting_id' => $meeting->id,
                'total_tasks' => count($allTasks),
                'database_id' => $databaseId
            ]);

            $resultado = $notionService->enviarResumenReunion(
                $databaseId,
                $meetingTitle,
                $resumen,
                $allTasks
            );

            if ($resultado['success']) {
                return response()->json([
                    'success' => true, 
                    'message' => "✅ Reunión con {$meeting->tasks->count()} tareas enviada a Notion",
                    'page_id' => $resultado['page_id'] ?? null
                ]);
            } else {
                return response()->json([
                    'success' => false, 
                    'message' => '❌ Error enviando a Notion: ' . ($resultado['error'] ?? 'Error desconocido')
                ], 400);
            }

        } catch (\Exception $e) {
            Log::error('❌ Excepción enviando reunión completa a Notion', [
                'meeting_id' => $meeting->id,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false, 
                'message' => 'Error interno: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Enviar todas las tareas a Google Sheets
     */
    private function sendAllTasksToGoogleSheets(Meeting $meeting, $user)
    {
        $integration = $user->integrations()->where('tipo', 'google_sheets')->first();
        
        if (!$integration) {
            return response()->json(['success' => false, 'message' => 'Integración de Google Sheets no configurada'], 400);
        }

        $config = json_decode($integration->config ?? '{}', true);
        $spreadsheetId = $config['spreadsheet_id'] ?? null;
        $sheetName = $config['sheet_name'] ?? 'Hoja 1';

        if (!$spreadsheetId) {
            return response()->json(['success' => false, 'message' => 'Hoja de cálculo no configurada'], 400);
        }

        $meetingTitle = $meeting->titulo ?? 'Reunión sin título';
        $range = $sheetName . '!A:D';

        // Preparar todas las filas de una vez
        $values = $meeting->tasks->map(function ($task) use ($meetingTitle) {
            return [
                now()->format('d/m/Y H:i'),
                $meetingTitle,
                $task->descripcion,
                'Pendiente'
            ];
        })->toArray();

        $payload = [
            'values' => $values,
            'majorDimension' => 'ROWS'
        ];

        $response = Http::withToken($integration->token)
            ->post("https://sheets.googleapis.com/v4/spreadsheets/{$spreadsheetId}/values/{$range}:append?valueInputOption=USER_ENTERED", $payload);

        if ($response->successful()) {
            return response()->json(['success' => true, 'message' => 'Todas las tareas enviadas a Google Sheets']);
        }

        return response()->json(['success' => false, 'message' => 'Error al enviar a Google Sheets'], 400);
    }

    /**
     * Enviar un correo con el resumen e insight de la reunión
     */
        public function sendEmail(Meeting $meeting)
        {
            
            try {
                Mail::to(auth()->user()->email)->queue(new MeetingSummaryMail($meeting));
                // Verificar si el correo se envió correctamente y mostrarlo en el log
                Log::info('✅ Correo de resumen de reunión enviado', ['meeting_id' => $meeting->id]);
                return response()->json(['success' => true]);
            } catch (\Throwable $e) {
                return response()->json(['success' => false, 'message' => $e->getMessage()]);
            }
        }
}
