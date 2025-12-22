<?php

namespace App\Http\Controllers;

use App\Services\GoogleSheetsService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GoogleDebugController extends Controller
{
    public function debug(Request $request)
    {
        $integration = auth()->user()->integrations()
            ->where('tipo', 'google_sheets')
            ->first();

        $debug = [
            'integration_exists' => !!$integration,
            'token_exists' => $integration ? !empty($integration->token) : false,
            'token_length' => $integration ? strlen($integration->token ?? '') : 0,
            'config' => $integration ? json_decode($integration->config ?? '{}', true) : null,
            'token_info' => null,
            'sheets_access' => null,
            'drive_files' => null,
        ];

        if ($integration && $integration->token) {
            // Verificar token
            $sheets = new GoogleSheetsService($integration->token);
            $debug['token_info'] = $sheets->verificarToken();

            // Probar acceso a Drive API para listar archivos
            try {
                $response = Http::withHeaders([
                    'Authorization' => 'Bearer ' . $integration->token
                ])->get('https://www.googleapis.com/drive/v3/files', [
                    'q' => "mimeType='application/vnd.google-apps.spreadsheet'",
                    'fields' => 'files(id, name)',
                    'pageSize' => 5
                ]);

                if ($response->successful()) {
                    $debug['drive_files'] = $response->json();
                } else {
                    $debug['drive_files'] = [
                        'error' => $response->body(),
                        'status' => $response->status()
                    ];
                }
            } catch (\Exception $e) {
                $debug['drive_files'] = [
                    'error' => $e->getMessage()
                ];
            }

            // Si hay spreadsheet_id configurado, probar acceso
            $config = json_decode($integration->config ?? '{}', true);
            if ($config['spreadsheet_id'] ?? null) {
                $debug['sheets_access'] = $sheets->verificarAcceso($config['spreadsheet_id']);
            }
        }

        return response()->json([
            'debug' => $debug,
            'suggestions' => $this->getSuggestions($debug)
        ]);
    }

    private function getSuggestions($debug)
    {
        $suggestions = [];

        if (!$debug['integration_exists']) {
            $suggestions[] = '1. No tienes integración con Google. Ve a /oauth/google para conectar tu cuenta.';
            return $suggestions;
        }

        if (!$debug['token_exists'] || $debug['token_length'] < 50) {
            $suggestions[] = '2. Tu token está vacío o es muy corto. Vuelve a conectar tu cuenta de Google.';
        }

        if ($debug['token_info'] && !$debug['token_info']['success']) {
            $suggestions[] = '3. Tu token ha expirado o es inválido. Reconecta tu cuenta de Google.';
        }

        if ($debug['token_info'] && $debug['token_info']['success']) {
            $scope = $debug['token_info']['scope'] ?? '';
            if (!str_contains($scope, 'spreadsheets')) {
                $suggestions[] = '4. Tu token no tiene permisos para Google Sheets. Reconecta con los permisos correctos.';
            }
        }

        if ($debug['drive_files'] && isset($debug['drive_files']['error'])) {
            $suggestions[] = '5. No se pueden listar tus archivos de Google Drive. Verifica permisos.';
        }

        if ($debug['sheets_access'] && !$debug['sheets_access']['success']) {
            $suggestions[] = '6. No se puede acceder al spreadsheet configurado. Verifica el ID o selecciona otro.';
        }

        if (empty($suggestions)) {
            $suggestions[] = '✅ Todo parece estar configurado correctamente.';
        }

        return $suggestions;
    }

    public function listSpreadsheets(Request $request)
    {
        $integration = auth()->user()->integrations()
            ->where('tipo', 'google_sheets')
            ->first();

        if (!$integration || !$integration->token) {
            return response()->json([
                'success' => false,
                'message' => 'No hay token de Google configurado'
            ]);
        }

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $integration->token
            ])->get('https://www.googleapis.com/drive/v3/files', [
                'q' => "mimeType='application/vnd.google-apps.spreadsheet'",
                'fields' => 'files(id, name, webViewLink)',
                'pageSize' => 20
            ]);

            if ($response->successful()) {
                $files = $response->json('files', []);
                return response()->json([
                    'success' => true,
                    'spreadsheets' => $files
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Error listando spreadsheets: ' . $response->body()
                ]);
            }

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error inesperado: ' . $e->getMessage()
            ]);
        }
    }
}