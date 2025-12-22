<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class NotionService
{
    private $token;
    private $baseUrl = 'https://api.notion.com/v1';

    public function __construct($token)
    {
        $this->token = $token;
    }

    public function listarBaseDatos()
    {
        try {
            Log::info('🔍 Listando bases de datos de Notion', [
                'token_length' => strlen($this->token),
                'base_url' => $this->baseUrl
            ]);

            $response = Http::timeout(30)->withHeaders([
                'Authorization' => 'Bearer ' . $this->token,
                'Notion-Version' => '2022-06-28',
                'Content-Type' => 'application/json',
            ])->post($this->baseUrl . '/search', [
                'filter' => [
                    'value' => 'database',
                    'property' => 'object'
                ]
            ]);

            Log::info('📊 Respuesta de Notion Search', [
                'status' => $response->status(),
                'successful' => $response->successful(),
                'body_preview' => substr($response->body(), 0, 500)
            ]);

            if (!$response->successful()) {
                return [
                    'success' => false,
                    'error' => 'Error HTTP: ' . $response->status() . ' - ' . $response->body()
                ];
            }

            $data = $response->json();
            
            return [
                'success' => true,
                'databases' => collect($data['results'] ?? [])->map(function ($db) {
                    return [
                        'id' => $db['id'],
                        'title' => $db['title'][0]['plain_text'] ?? 'Sin título',
                        'url' => $db['url'] ?? null,
                        'properties' => array_keys($db['properties'] ?? [])
                    ];
                })->toArray()
            ];

        } catch (\Exception $e) {
            Log::error('❌ Error listando databases de Notion', [
                'error' => $e->getMessage(),
                'line' => $e->getLine(),
                'file' => $e->getFile()
            ]);
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    public function obtenerEsquemaDatabase($databaseId)
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->token,
                'Notion-Version' => '2022-06-28',
            ])->get($this->baseUrl . '/databases/' . $databaseId);

            if (!$response->successful()) {
                return [
                    'success' => false,
                    'error' => 'Error obteniendo esquema: ' . $response->body()
                ];
            }

            $data = $response->json();
            $properties = $data['properties'] ?? [];

            // Analizar propiedades disponibles
            $schema = [];
            foreach ($properties as $name => $property) {
                $schema[$name] = $property['type'];
            }

            return [
                'success' => true,
                'schema' => $schema,
                'title' => $data['title'][0]['plain_text'] ?? 'Sin título'
            ];

        } catch (\Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    public function enviarResumenReunion($databaseId, $titulo, $resumen, $tareas = [])
    {
        try {
            // Primero obtenemos el esquema de la base de datos
            $schemaResult = $this->obtenerEsquemaDatabase($databaseId);
            
            if (!$schemaResult['success']) {
                return $schemaResult;
            }

            $schema = $schemaResult['schema'];
            
            // Asegurarse de que las tareas estén en el formato correcto
            $tareasFormateadas = $this->formatearTareas($tareas);
            
            // Construir las propiedades dinámicamente basado en el esquema disponible
            $properties = $this->construirPropiedades($schema, $titulo, $resumen, $tareasFormateadas);

            if (empty($properties)) {
                return [
                    'success' => false,
                    'error' => 'No se pudieron mapear las propiedades. Esquema disponible: ' . implode(', ', array_keys($schema))
                ];
            }

            $payload = [
                'parent' => [
                    'database_id' => $databaseId
                ],
                'properties' => $properties
            ];

            Log::info('Enviando a Notion', [
                'database_id' => $databaseId,
                'schema_disponible' => $schema,
                'tareas_originales' => $tareas,
                'tareas_formateadas' => $tareasFormateadas,
                'payload' => $payload
            ]);

            $response = Http::timeout(30)->withHeaders([
                'Authorization' => 'Bearer ' . $this->token,
                'Notion-Version' => '2022-06-28',
                'Content-Type' => 'application/json',
            ])->post($this->baseUrl . '/pages', $payload);

            if (!$response->successful()) {
                Log::error('❌ Error enviando a Notion', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                    'payload_sent' => $payload
                ]);
                
                return [
                    'success' => false,
                    'error' => $response->body(),
                    'schema_disponible' => $schema,
                    'payload_enviado' => $payload
                ];
            }

            return [
                'success' => true,
                'page_id' => $response->json()['id'],
                'propiedades_usadas' => array_keys($properties)
            ];

        } catch (\Exception $e) {
            Log::error('❌ Error enviando a Notion', [
                'error' => $e->getMessage(),
                'line' => $e->getLine(),
                'file' => $e->getFile()
            ]);
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Formatear las tareas para envío a Notion
     */
    private function formatearTareas($tareas)
    {
        if (empty($tareas)) {
            return '';
        }

        // Si es un array, convertir a texto con formato
        if (is_array($tareas)) {
            // Filtrar tareas vacías
            $tareasLimpias = array_filter($tareas, function($tarea) {
                return !empty(trim($tarea));
            });

            if (empty($tareasLimpias)) {
                return '';
            }

            // Formatear como lista con viñetas
            return "• " . implode("\n• ", $tareasLimpias);
        }

        // Si ya es string, devolverlo tal como está
        return (string) $tareas;
    }

    private function construirPropiedades($schema, $titulo, $resumen, $tareas)
    {
        $properties = [];

        // Mapeo flexible de nombres de propiedades
        $mappings = [
            'title' => ['title', 'título', 'name', 'nombre', 'reunión', 'meeting'],
            'resumen' => ['resumen', 'summary', 'descripción', 'description', 'contenido', 'content'],
            'tareas' => ['tareas', 'tasks', 'acciones', 'actions', 'pendientes', 'todo'],
            'estado' => ['estado', 'status', 'situación', 'situation'],
            'fecha' => ['fecha', 'date', 'created', 'creado']
        ];

        // Buscar propiedades que coincidan
        foreach ($mappings as $categoria => $aliases) {
            $foundProperty = null;
            $expectedType = null;
            
            // Determinar el tipo esperado según la categoría
            switch ($categoria) {
                case 'title':
                    $expectedType = 'title';
                    break;
                case 'resumen':
                case 'tareas':
                    $expectedType = 'rich_text';
                    break;
                case 'estado':
                    $expectedType = 'select';
                    break;
                case 'fecha':
                    $expectedType = 'date';
                    break;
            }
            
            foreach ($aliases as $alias) {
                foreach (array_keys($schema) as $propertyName) {
                    if (strtolower($propertyName) === strtolower($alias)) {
                        $foundProperty = $propertyName;
                        break 2;
                    }
                }
            }

            if ($foundProperty && isset($schema[$foundProperty]) && $schema[$foundProperty] === $expectedType) {
                switch ($expectedType) {
                    case 'title':
                        $properties[$foundProperty] = [
                            'title' => [
                                [
                                    'text' => [
                                        'content' => substr($titulo, 0, 2000) // Notion tiene límite de caracteres
                                    ]
                                ]
                            ]
                        ];
                        break;

                    case 'rich_text':
                        if ($categoria === 'tareas') {
                            // Es para tareas - ya vienen formateadas del método formatearTareas
                            $tareasTexto = $tareas;
                            if (!empty($tareasTexto)) {
                                $properties[$foundProperty] = [
                                    'rich_text' => [
                                        [
                                            'text' => [
                                                'content' => substr($tareasTexto, 0, 2000)
                                            ]
                                        ]
                                    ]
                                ];
                            }
                        } else {
                            // Es para resumen
                            $properties[$foundProperty] = [
                                'rich_text' => [
                                    [
                                        'text' => [
                                            'content' => substr($resumen, 0, 2000)
                                        ]
                                    ]
                                ]
                            ];
                        }
                        break;

                    case 'select':
                        // Verificar que el valor sea válido para el select
                        $properties[$foundProperty] = [
                            'select' => [
                                'name' => 'Procesado'
                            ]
                        ];
                        break;

                    case 'date':
                        $properties[$foundProperty] = [
                            'date' => [
                                'start' => now()->format('Y-m-d')
                            ]
                        ];
                        break;
                }
            }
        }

        // Si no encontramos título, usar la primera propiedad de tipo title
        if (empty($properties)) {
            foreach ($schema as $propertyName => $type) {
                if ($type === 'title') {
                    $properties[$propertyName] = [
                        'title' => [
                            [
                                'text' => [
                                    'content' => substr($titulo, 0, 2000)
                                ]
                            ]
                        ]
                    ];
                    break;
                }
            }
        }

        return $properties;
    }
}