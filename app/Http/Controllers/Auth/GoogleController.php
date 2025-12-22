<?php

namespace App\Http\Controllers\Auth;


use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Auth;
use App\Models\UserIntegration;

class GoogleController extends Controller
{
    public function redirect()
    {
        $query = http_build_query([
            'client_id' => config('services.google.client_id'),
            'redirect_uri' => config('services.google.redirect'),
            'response_type' => 'code',
            'scope' => 'https://www.googleapis.com/auth/spreadsheets https://www.googleapis.com/auth/drive.readonly',
            'access_type' => 'offline',
            'prompt' => 'consent',
        ]);

        return redirect('https://accounts.google.com/o/oauth2/v2/auth?' . $query);
    }

    public function callback(Request $request)
{
    // Verificar que tenemos el código de autorización
    if (!$request->has('code')) {
        \Log::error('❌ OAuth callback sin código', [
            'request' => $request->all(),
            'url' => $request->fullUrl()
        ]);
        
        return redirect()->route('integrations.index')
            ->with('error', 'Error en la autorización de Google. Inténtalo de nuevo.');
    }

    // Si hay error en la respuesta OAuth
    if ($request->has('error')) {
        \Log::error('❌ Error OAuth de Google', [
            'error' => $request->get('error'),
            'error_description' => $request->get('error_description')
        ]);
        
        return redirect()->route('integrations.index')
            ->with('error', 'Autorización cancelada o denegada por Google.');
    }

    try {
        \Log::info('🔄 Intercambiando código OAuth por tokens', [
            'code_length' => strlen($request->code),
            'redirect_uri' => config('services.google.redirect')
        ]);

        $response = Http::timeout(30)->asForm()->post('https://oauth2.googleapis.com/token', [
            'code' => $request->code,
            'client_id' => config('services.google.client_id'),
            'client_secret' => config('services.google.client_secret'),
            'redirect_uri' => config('services.google.redirect'),
            'grant_type' => 'authorization_code',
        ]);

        \Log::info('📡 Respuesta de Google OAuth', [
            'status' => $response->status(),
            'successful' => $response->successful(),
            'body_preview' => substr($response->body(), 0, 200)
        ]);

        if (!$response->successful()) {
            \Log::error('❌ Error HTTP en intercambio de tokens', [
                'status' => $response->status(),
                'body' => $response->body()
            ]);
            
            return redirect()->route('integrations.index')
                ->with('error', 'Error comunicándose con Google. Código HTTP: ' . $response->status());
        }

        $data = $response->json();

        // Verificar que tenemos los datos necesarios
        if (!isset($data['access_token'])) {
            \Log::error('❌ Respuesta de Google sin access_token', [
                'response_data' => $data
            ]);
            
            $errorMsg = 'Google no devolvió un token de acceso válido.';
            if (isset($data['error'])) {
                $errorMsg .= ' Error: ' . $data['error'];
                if (isset($data['error_description'])) {
                    $errorMsg .= ' - ' . $data['error_description'];
                }
            }
            
            return redirect()->route('integrations.index')->with('error', $errorMsg);
        }

        \Log::info('✅ Tokens recibidos correctamente', [
            'has_access_token' => !empty($data['access_token']),
            'has_refresh_token' => !empty($data['refresh_token']),
            'expires_in' => $data['expires_in'] ?? 'No especificado',
            'token_type' => $data['token_type'] ?? 'No especificado',
            'scope' => $data['scope'] ?? 'No especificado'
        ]);

        // Mostrar vista de confirmación
        return view('oauth.google.confirm', [
            'access_token' => $data['access_token'],
            'refresh_token' => $data['refresh_token'] ?? null,
            'expires_in' => $data['expires_in'] ?? 3600,
            'scope' => $data['scope'] ?? 'No especificado'
        ]);

    } catch (\Exception $e) {
        \Log::error('❌ Excepción en callback OAuth', [
            'error' => $e->getMessage(),
            'line' => $e->getLine(),
            'file' => $e->getFile()
        ]);
        
        return redirect()->route('integrations.index')
            ->with('error', 'Error interno procesando la autorización de Google.');
    }
}

    public function store(Request $request)
{
    UserIntegration::updateOrCreate([
        'user_id' => Auth::id(),
        'tipo' => 'google_sheets',
    ], [
        'token' => $request->access_token,
        'config' => json_encode([
            'refresh_token' => $request->refresh_token,
        ]),
    ]);

    return redirect()->route('dashboard')->with('success', 'Integración guardada correctamente.');
}

public function listSheets()
{
    $integration = Auth::user()->integrations()->where('tipo', 'google_sheets')->first();

    if (! $integration) {
        return redirect()->route('dashboard')->with('error', 'No se encontró integración con Google.');
    }

    $accessToken = $integration->token;

    $response = Http::withToken($accessToken)->get('https://www.googleapis.com/drive/v3/files', [
        'q' => "mimeType='application/vnd.google-apps.spreadsheet'",
        'fields' => 'files(id, name)',
    ]);


    $files = $response->json()['files'] ?? [];

    return view('oauth.google.select_sheet', compact('files'));
}

public function saveSheet(Request $request)
{
    $integration = Auth::user()->integrations()->where('tipo', 'google_sheets')->first();

    if (! $integration) {
        return redirect()->route('dashboard')->with('error', 'No se encontró integración con Google.');
    }

    $config = json_decode($integration->config, true) ?? [];
    $config['spreadsheet_id'] = $request->spreadsheet_id;

    $integration->update([
        'config' => json_encode($config),
    ]);

    return redirect()->route('dashboard')->with('success', 'Google Sheet guardada correctamente.');
}



}

