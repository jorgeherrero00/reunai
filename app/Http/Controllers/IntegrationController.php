<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Models\UserIntegration;

class IntegrationController extends Controller
{
    public function index()
    {
        $integrations = auth()->user()->integrations;
        return view('integrations.api-keys', compact('integrations'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'notion_api' => 'nullable|string|max:255',
            'google_token' => 'nullable|string|max:500',
            'slack_token' => 'nullable|string|max:255',
        ]);

        $user = auth()->user();

        // Notion API
        if ($request->filled('notion_api')) {
            UserIntegration::updateOrCreate([
                'user_id' => $user->id,
                'tipo' => 'notion',
            ], [
                'token' => $request->notion_api,
                'config' => json_encode([
                    'database_id' => null, // Se configurará después
                ])
            ]);
        }

        // Google Sheets
        if ($request->filled('google_token')) {
            UserIntegration::updateOrCreate([
                'user_id' => $user->id,
                'tipo' => 'google_sheets',
            ], [
                'token' => $request->google_token,
                'config' => json_encode([
                    'spreadsheet_id' => null, // Se configurará después
                ])
            ]);
        }

        // Slack
        if ($request->filled('slack_token')) {
            UserIntegration::updateOrCreate([
                'user_id' => $user->id,
                'tipo' => 'slack',
            ], [
                'token' => $request->slack_token,
                'config' => json_encode([
                    'channel' => '#general', // Canal por defecto
                ])
            ]);
        }

        return redirect()->route('integrations.index')->with('success', 'Integraciones guardadas correctamente.');
    }

    public function destroy($integration)
    {
        $integration = UserIntegration::where('user_id', auth()->id())
                                    ->where('id', $integration)
                                    ->firstOrFail();
        
        $integration->delete();
        
        return redirect()->route('integrations.index')->with('success', 'Integración eliminada correctamente.');
    }

    /**
     * Listar databases de Notion usando la API key
     */
    public function listNotionDatabases()
    {
        $integration = auth()->user()->integrations()->where('tipo', 'notion')->first();

        if (!$integration) {
            return redirect()->route('integrations.index')->with('error', 'No se encontró integración con Notion.');
        }

        $response = Http::withToken($integration->token)
            ->withHeaders([
                'Notion-Version' => '2022-06-28',
            ])
            ->post('https://api.notion.com/v1/search', [
                'filter' => [
                    'property' => 'object',
                    'value' => 'database'
                ],
                'sort' => [
                    'direction' => 'descending',
                    'timestamp' => 'last_edited_time'
                ]
            ]);

        if (!$response->successful()) {
            return redirect()->route('integrations.index')->with('error', 'Error obteniendo bases de datos de Notion. Verifica tu API key.');
        }

        $databases = collect($response->json()['results'] ?? [])
            ->filter(function ($db) {
                // Solo mostrar databases que el bot puede editar
                return isset($db['parent']) && $db['object'] === 'database';
            })
            ->map(function ($db) {
                return [
                    'id' => $db['id'],
                    'title' => $this->getNotionDatabaseTitle($db),
                    'url' => $db['url'] ?? null,
                    'last_edited' => $db['last_edited_time'] ?? null,
                ];
            });

        return view('integrations.notion_databases', compact('databases'));
    }

    /**
     * Guardar database seleccionada
     */
    public function saveNotionDatabase(Request $request)
    {
        $integration = auth()->user()->integrations()->where('tipo', 'notion')->first();

        if (!$integration) {
            return redirect()->route('integrations.index')->with('error', 'No se encontró integración con Notion.');
        }

        // Verificar que la database existe y es accesible
        $response = Http::withToken($integration->token)
            ->withHeaders([
                'Notion-Version' => '2022-06-28',
            ])
            ->get("https://api.notion.com/v1/databases/{$request->database_id}");

        if (!$response->successful()) {
            return redirect()->back()->with('error', 'No se puede acceder a esa base de datos.');
        }

        $databaseInfo = $response->json();
        
        $config = json_decode($integration->config, true) ?? [];
        $config['database_id'] = $request->database_id;
        $config['database_title'] = $this->getNotionDatabaseTitle($databaseInfo);

        $integration->update([
            'config' => json_encode($config),
        ]);

        return redirect()->route('integrations.index')->with('success', 'Base de datos de Notion configurada correctamente.');
    }

    /**
     * Extraer título de la database de Notion
     */
    private function getNotionDatabaseTitle($database)
    {
        if (isset($database['title']) && is_array($database['title']) && count($database['title']) > 0) {
            return $database['title'][0]['plain_text'] ?? 'Sin título';
        }
        
        return 'Database sin título';
    }
}