<?php

namespace App\Http\Controllers;

use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TaskController extends Controller
{
    /**
     * Enviar una tarea individual a una plataforma específica
     */
    public function sendTask(Task $task, Request $request)
    {
        $platform = $request->input('platform');
        $user = auth()->user();
        
        try {
            switch ($platform) {
                case 'slack':
                    return $this->sendToSlack($task, $user);
                case 'notion':
                    return $this->sendToNotion($task, $user);
                case 'sheets':
                    return $this->sendToGoogleSheets($task, $user);
                default:
                    return response()->json(['success' => false, 'message' => 'Plataforma no soportada'], 400);
            }
        } catch (\Exception $e) {
            Log::error('Error enviando tarea', [
                'task_id' => $task->id,
                'platform' => $platform,
                'error' => $e->getMessage()
            ]);
            
            return response()->json(['success' => false, 'message' => 'Error interno del servidor'], 500);
        }
    }

    /**
     * Enviar tarea a Slack
     */
    private function sendToSlack(Task $task, $user)
    {
        $integration = $user->integrations()->where('tipo', 'slack')->first();
        
        if (!$integration) {
            return response()->json(['success' => false, 'message' => 'Integración de Slack no configurada'], 400);
        }

        $meetingTitle = $task->meeting->titulo ?? 'Reunión sin título';
        $config = json_decode($integration->config ?? '{}', true);
        $channel = $config['channel'] ?? '#general';

        $payload = [
            'channel' => $channel,
            'text' => "📋 Nueva tarea desde Meetlyze",
            'attachments' => [
                [
                    'color' => '#f97316', // Orange color
                    'fields' => [
                        [
                            'title' => 'Reunión',
                            'value' => $meetingTitle,
                            'short' => true
                        ],
                        [
                            'title' => 'Tarea',
                            'value' => $task->descripcion,
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
            return response()->json(['success' => true, 'message' => 'Tarea enviada a Slack']);
        }

        return response()->json(['success' => false, 'message' => 'Error al enviar a Slack'], 400);
    }

private function sendToNotion(Task $task, $user)
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

    $meetingTitle = $task->meeting->titulo ?? 'Reunión sin título';

    // Primero, obtener la estructura de la database para saber qué propiedades existen
    $databaseResponse = Http::withToken($integration->token)
        ->withHeaders([
            'Notion-Version' => '2022-06-28',
        ])
        ->get("https://api.notion.com/v1/databases/{$databaseId}");

    if (!$databaseResponse->successful()) {
        return response()->json(['success' => false, 'message' => 'No se puede acceder a la base de datos'], 400);
    }

    $databaseSchema = $databaseResponse->json();
    $properties = $databaseSchema['properties'] ?? [];

    // Construir payload dinámicamente basado en las propiedades existentes
    $payload = [
        'parent' => ['database_id' => $databaseId],
        'properties' => []
    ];

    // Buscar la propiedad de título (puede tener cualquier nombre)
    $titleProperty = null;
    foreach ($properties as $name => $property) {
        if ($property['type'] === 'title') {
            $titleProperty = $name;
            break;
        }
    }

    // Si encontramos propiedad de título, usarla
    if ($titleProperty) {
        $payload['properties'][$titleProperty] = [
            'title' => [
                [
                    'text' => [
                        'content' => $task->descripcion
                    ]
                ]
            ]
        ];
    }

    // Agregar otras propiedades si existen
    foreach ($properties as $name => $property) {
        $lowerName = strtolower($name);
        
        // Propiedad para reunión (rich_text)
        if (($lowerName === 'reunión' || $lowerName === 'reunion' || $lowerName === 'meeting') && $property['type'] === 'rich_text') {
            $payload['properties'][$name] = [
                'rich_text' => [
                    [
                        'text' => [
                            'content' => $meetingTitle
                        ]
                    ]
                ]
            ];
        }
        
        // Propiedad para estado (select)
        elseif (($lowerName === 'estado' || $lowerName === 'status' || $lowerName === 'state') && $property['type'] === 'select') {
            // Obtener las opciones disponibles
            $options = $property['select']['options'] ?? [];
            $pendingOption = null;
            
            // Buscar opción "Pendiente" o similar
            foreach ($options as $option) {
                $optionName = strtolower($option['name']);
                if (in_array($optionName, ['pendiente', 'pending', 'todo', 'por hacer', 'to do'])) {
                    $pendingOption = $option['name'];
                    break;
                }
            }
            
            // Si no encontramos opción pendiente, usar la primera disponible
            if (!$pendingOption && count($options) > 0) {
                $pendingOption = $options[0]['name'];
            }
            
            if ($pendingOption) {
                $payload['properties'][$name] = [
                    'select' => [
                        'name' => $pendingOption
                    ]
                ];
            }
        }
        
        // Propiedad para fecha (date)
        elseif (($lowerName === 'fecha' || $lowerName === 'date' || $lowerName === 'created') && $property['type'] === 'date') {
            $payload['properties'][$name] = [
                'date' => [
                    'start' => now()->toDateString()
                ]
            ];
        }
    }

    // Si no se pudo mapear ninguna propiedad de título, usar la primera disponible
    if (empty($payload['properties']) && count($properties) > 0) {
        $firstProperty = array_keys($properties)[0];
        $firstPropertyType = $properties[$firstProperty]['type'];
        
        if ($firstPropertyType === 'title') {
            $payload['properties'][$firstProperty] = [
                'title' => [
                    [
                        'text' => [
                            'content' => $task->descripcion
                        ]
                    ]
                ]
            ];
        } elseif ($firstPropertyType === 'rich_text') {
            $payload['properties'][$firstProperty] = [
                'rich_text' => [
                    [
                        'text' => [
                            'content' => $task->descripcion
                        ]
                    ]
                ]
            ];
        }
    }

    \Log::info('Notion payload', $payload); // Para debug

    $response = Http::withToken($integration->token)
        ->withHeaders([
            'Notion-Version' => '2022-06-28',
            'Content-Type' => 'application/json'
        ])
        ->post('https://api.notion.com/v1/pages', $payload);

    if ($response->successful()) {
        return response()->json(['success' => true, 'message' => 'Tarea enviada a Notion']);
    }

    // Log del error para debug
    \Log::error('Error enviando a Notion', [
        'status' => $response->status(),
        'body' => $response->body(),
        'payload' => $payload
    ]);

    return response()->json(['success' => false, 'message' => 'Error al enviar a Notion: ' . $response->body()], 400);
}

    /**
     * Enviar tarea a Google Sheets
     */
    private function sendToGoogleSheets(Task $task, $user)
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

        $meetingTitle = $task->meeting->titulo ?? 'Reunión sin título';
        $range = $sheetName . '!A:D';

        $values = [
            [
                now()->format('d/m/Y H:i'),
                $meetingTitle,
                $task->descripcion,
                'Pendiente'
            ]
        ];

        $payload = [
            'values' => $values,
            'majorDimension' => 'ROWS'
        ];

        $response = Http::withToken($integration->token)
            ->post("https://sheets.googleapis.com/v4/spreadsheets/{$spreadsheetId}/values/{$range}:append?valueInputOption=USER_ENTERED", $payload);

        if ($response->successful()) {
            return response()->json(['success' => true, 'message' => 'Tarea enviada a Google Sheets']);
        }

        return response()->json(['success' => false, 'message' => 'Error al enviar a Google Sheets'], 400);
    }
}