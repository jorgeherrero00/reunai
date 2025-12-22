<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GoogleSheetsService
{
    private $accessToken;
    private $baseUrl = 'https://sheets.googleapis.com/v4/spreadsheets';

    public function __construct($accessToken)
    {
        $this->accessToken = $accessToken;
    }

    /**
     * Añade una fila con el resumen de la reunión
     */
    public function agregarResumenReunion($spreadsheetId, $sheetName, $titulo, $resumen, $tareas = [])
    {
        try {
            Log::info('📊 Enviando resumen a Google Sheets', [
                'spreadsheet_id' => $spreadsheetId,
                'sheet_name' => $sheetName,
                'titulo' => $titulo
            ]);

            // Formatear tareas como texto
            $tareasTexto = '';
            if (!empty($tareas)) {
                $tareasTexto = implode(' | ', $tareas);
            }

            // Preparar los datos para la fila
            $valores = [
                [
                    now()->format('Y-m-d H:i:s'), // Fecha
                    $titulo,                       // Título
                    $resumen,                     // Resumen
                    $tareasTexto,                 // Tareas
                    count($tareas)                // Número de tareas
                ]
            ];

            $payload = [
                'values' => $valores
            ];

            // Determinar el rango donde insertar
            $range = $sheetName . '!A:E';

            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->accessToken,
                'Content-Type' => 'application/json'
            ])->post(
                $this->baseUrl . "/{$spreadsheetId}/values/{$range}:append?valueInputOption=USER_ENTERED&insertDataOption=INSERT_ROWS",
                $payload
            );


            if ($response->successful()) {
                Log::info('✅ Resumen añadido a Google Sheets exitosamente', [
                    'updated_range' => $response->json('updates.updatedRange')
                ]);
                return [
                    'success' => true,
                    'updated_range' => $response->json('updates.updatedRange')
                ];
            } else {
                Log::error('❌ Error al enviar a Google Sheets', [
                    'status' => $response->status(),
                    'body' => $response->body()
                ]);
                return [
                    'success' => false,
                    'error' => $response->body()
                ];
            }

        } catch (\Exception $e) {
            Log::error('❌ Excepción al enviar a Google Sheets', [
                'mensaje' => $e->getMessage(),
                'linea' => $e->getLine()
            ]);
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Crea las cabeceras si la hoja está vacía
     */
    public function configurarCabeceras($spreadsheetId, $sheetName)
    {
        try {
            // Verificar si ya tiene cabeceras
            $range = $sheetName . '!A1:E1';
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->accessToken
            ])->get($this->baseUrl . "/{$spreadsheetId}/values/{$range}");

            if ($response->successful() && empty($response->json('values'))) {
                // No hay cabeceras, las creamos
                $cabeceras = [
                    [
                        'Fecha',
                        'Título',
                        'Resumen',
                        'Tareas',
                        'Número de Tareas'
                    ]
                ];

                $payload = [
                    'values' => $cabeceras
                ];

                $response = Http::withHeaders([
                    'Authorization' => 'Bearer ' . $this->accessToken,
                    'Content-Type' => 'application/json'
                ])->put(
                    $this->baseUrl . "/{$spreadsheetId}/values/{$range}",
                    array_merge($payload, [
                        'valueInputOption' => 'USER_ENTERED'
                    ])
                );

                if ($response->successful()) {
                    Log::info('✅ Cabeceras configuradas en Google Sheets');
                    return ['success' => true];
                }
            }

            return ['success' => true]; // Ya tenía cabeceras o se configuraron

        } catch (\Exception $e) {
            Log::error('❌ Error al configurar cabeceras', [
                'mensaje' => $e->getMessage()
            ]);
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Lista las hojas disponibles en un spreadsheet
     */
    public function listarHojas($spreadsheetId)
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->accessToken
            ])->get($this->baseUrl . "/{$spreadsheetId}", [
                'fields' => 'sheets.properties'
            ]);

            if ($response->successful()) {
                $sheets = collect($response->json('sheets'))
                    ->map(function ($sheet) {
                        return [
                            'id' => $sheet['properties']['sheetId'],
                            'title' => $sheet['properties']['title']
                        ];
                    });

                return [
                    'success' => true,
                    'sheets' => $sheets
                ];
            } else {
                return [
                    'success' => false,
                    'error' => $response->body()
                ];
            }

        } catch (\Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Verifica si el token de acceso es válido
     */
    public function verificarToken()
    {
        try {
            Log::info('🔐 Verificando token de Google');

            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->accessToken
            ])->get('https://www.googleapis.com/oauth2/v1/tokeninfo');

            Log::info('🔍 Respuesta de verificación de token', [
                'status' => $response->status(),
                'body' => $response->body()
            ]);

            if ($response->successful()) {
                $tokenInfo = $response->json();
                return [
                    'success' => true,
                    'scope' => $tokenInfo['scope'] ?? 'No disponible',
                    'expires_in' => $tokenInfo['expires_in'] ?? 'No disponible',
                    'audience' => $tokenInfo['audience'] ?? 'No disponible'
                ];
            } else {
                return [
                    'success' => false,
                    'error' => 'Token inválido o expirado',
                    'details' => $response->json()
                ];
            }

        } catch (\Exception $e) {
            Log::error('❌ Error verificando token', [
                'error' => $e->getMessage()
            ]);
            
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Verifica si el token tiene acceso al spreadsheet
     */
    public function verificarAcceso($spreadsheetId)
    {
        try {
            Log::info('🔍 Verificando acceso a Google Sheets', [
                'spreadsheet_id' => $spreadsheetId,
                'token_length' => strlen($this->accessToken)
            ]);

            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->accessToken
            ])->get($this->baseUrl . "/{$spreadsheetId}", [
                'fields' => 'properties.title'
            ]);

            Log::info('📊 Respuesta de Google Sheets API', [
                'status' => $response->status(),
                'body' => $response->body()
            ]);

            if ($response->successful()) {
                return [
                    'success' => true,
                    'title' => $response->json('properties.title')
                ];
            } else {
                $errorBody = $response->json();
                $errorMessage = $errorBody['error']['message'] ?? 'Error desconocido';
                $errorCode = $errorBody['error']['code'] ?? $response->status();
                
                return [
                    'success' => false,
                    'error' => "Error {$errorCode}: {$errorMessage}",
                    'details' => $errorBody
                ];
            }

        } catch (\Exception $e) {
            Log::error('❌ Excepción al verificar acceso a Google Sheets', [
                'error' => $e->getMessage(),
                'line' => $e->getLine()
            ]);
            
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }
}