@extends('layouts.app')
@section('content')
    <div class="relative z-10 max-w-4xl mx-auto">
        <!-- Header section with gradient text -->
        <div class="mb-8 text-center">
            <div class="inline-flex items-center bg-gradient-to-r from-orange-500/30 to-red-500/30 px-4 py-2 rounded-full backdrop-blur-sm mb-4 border border-orange-500/20 shadow-lg">
                <span class="animate-pulse mr-2 h-2 w-2 bg-orange-500 rounded-full"></span>
                <span class="text-orange-300 font-medium tracking-wider text-sm">INTEGRACIONES</span>
            </div>
            <h2 class="text-3xl font-bold mb-2 bg-gradient-to-r from-white via-gray-100 to-gray-300 text-transparent bg-clip-text">Conecta tus <span class="bg-gradient-to-r from-orange-400 to-red-500 text-transparent bg-clip-text">herramientas favoritas</span></h2>
            <p class="text-gray-400 text-sm max-w-lg mx-auto">Introduce tus claves de API para habilitar integraciones automáticas y potenciar tu experiencia</p>
        </div>

        <!-- Integraciones existentes -->
        @if($integrations->count() > 0)
        <div class="mb-8">
            <h3 class="text-xl font-semibold mb-4 text-white">Integraciones activas</h3>
            <div class="grid gap-4">
                @foreach($integrations as $integration)
                <div class="bg-gray-800/50 backdrop-blur-sm border border-gray-700/50 rounded-xl p-4 flex justify-between items-center">
                    <div class="flex items-center gap-3">
                        @if($integration->tipo === 'notion')
                            <div class="bg-gradient-to-br from-orange-500/20 to-red-600/20 p-2 rounded-lg">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-orange-400" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M4.459 4.208c.746.606 1.026.56 2.428.466l13.215-.793c.28 0 .047-.28-.046-.326L17.86 1.968c-.42-.326-.981-.7-2.055-.607L3.01 2.295c-.466.046-.56.28-.374.466l1.823 1.447zm1.775 2.055c.187.466.187.886.187 1.727v13.775c0 .746.373 1.027 1.213 1.027.933 0 1.353-.28 1.727-1.12l7.318-11.85c.14-.186.047-.373-.094-.373l-3.896.7c-.28.047-.327.234-.187.42l4.734 7.318c.047.047.047.094.047.187v-1.727l-4.734-7.318c-.094-.14-.14-.326-.047-.514l.886-1.96c.187-.466.047-.98-.42-1.12L4.696 3.628c-.42-.14-.933 0-1.12.42l-.374.793c-.14.326-.28.7-.28 1.213L3.01 6.24c0 .466.28.653.653.653h2.147c.326-.047.42-.28.42-.606zm6.638 2.568c.187-.14.467-.047.653.093l1.12.84c.187.187.187.33.187.514v.046l-1.214 1.68-1.493-1.40c-.094-.187-.094-.33-.047-.514l.794-1.26zm-3.57 11.664c-.14.187-.047.373.094.467l1.214.84c.187.14.373.094.513-.047l2.708-3.734V8.954l-1.214 1.826-3.316 4.56z"/>
                                </svg>
                            </div>
                            <span class="text-white font-medium">Notion</span>
                        @elseif($integration->tipo === 'google_sheets')
                            <div class="bg-gradient-to-br from-orange-500/20 to-red-600/20 p-2 rounded-lg">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-orange-400" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M19.993 8.448c-.006-.12-.012-.24-.018-.358H8v5.01h7.34c-.305 1.59-1.533 2.962-3.296 3.638v3.033h5.35c3.12-2.867 4.917-7.08 4.917-12.03 0-1.131-.099-2.242-.29-3.323z" fill="#4285F4"/>
                                    <path d="M8 21c4.468 0 8.223-1.456 10.957-3.933l-5.35-3.027c-1.48.995-3.38 1.585-5.607 1.585-4.295 0-7.934-2.898-9.235-6.8H-6.58v3.127C-3.825 17.735 1.816 21 8 21z" fill="#34A853"/>
                                    <path d="M-1.235 8.825c-.336-1.007-.536-2.086-.536-3.202s.2-2.195.536-3.202V-.706h-5.345a13.25 13.25 0 000 12.658l5.345-3.127z" fill="#FBBC05"/>
                                    <path d="M8 3.375c2.433 0 4.616.84 6.335 2.472l4.743-4.743C17.093.415 12.957-1 8-1 1.816-1-3.825 2.265-6.58 8.025l5.345 3.127c1.3-3.902 4.94-6.8 9.235-6.8z" fill="#EA4335"/>
                                </svg>
                            </div>
                            
                            <span class="text-white font-medium">Google Sheets</span>
                        @elseif($integration->tipo === 'slack')
                            <div class="bg-gradient-to-br from-orange-500/20 to-red-600/20 p-2 rounded-lg">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-orange-400" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M5.042 15.165a2.528 2.528 0 0 1-2.52 2.523A2.528 2.528 0 0 1 0 15.165a2.527 2.527 0 0 1 2.522-2.52h2.52v2.52zM6.313 15.165a2.527 2.527 0 0 1 2.521-2.52 2.527 2.527 0 0 1 2.521 2.52v6.313A2.528 2.528 0 0 1 8.834 24a2.528 2.528 0 0 1-2.521-2.522v-6.313zM8.834 5.042a2.528 2.528 0 0 1-2.521-2.52A2.528 2.528 0 0 1 8.834 0a2.528 2.528 0 0 1 2.521 2.522v2.52H8.834zM8.834 6.313a2.528 2.528 0 0 1 2.521 2.521 2.528 2.528 0 0 1-2.521 2.521H2.522A2.528 2.528 0 0 1 0 8.834a2.528 2.528 0 0 1 2.522-2.521h6.312zM18.956 8.834a2.528 2.528 0 0 1 2.522-2.521A2.528 2.528 0 0 1 24 8.834a2.528 2.528 0 0 1-2.522 2.521h-2.522V8.834zM17.688 8.834a2.528 2.528 0 0 1-2.523 2.521 2.527 2.527 0 0 1-2.52-2.521V2.522A2.527 2.527 0 0 1 15.165 0a2.528 2.528 0 0 1 2.523 2.522v6.312zM15.165 18.956a2.528 2.528 0 0 1 2.523 2.522A2.528 2.528 0 0 1 15.165 24a2.527 2.527 0 0 1-2.52-2.522v-2.522h2.52zM15.165 17.688a2.527 2.527 0 0 1-2.52-2.523 2.526 2.526 0 0 1 2.52-2.52h6.313A2.527 2.527 0 0 1 24 15.165a2.528 2.528 0 0 1-2.522 2.523h-6.313z"/>
                                </svg>
                            </div>
                            <span class="text-white font-medium">Slack</span>
                        @endif
                        <span class="text-green-400 text-sm">● Activa</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <button onclick="testIntegration('{{ $integration->tipo }}', {{ $integration->id }})" 
                                class="text-blue-400 hover:text-blue-300 text-sm flex items-center gap-1">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            Probar
                        </button>
                        <form method="POST" action="{{ route('integrations.destroy', $integration->id) }}" class="inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-400 hover:text-red-300 text-sm">Eliminar</button>
                        </form>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @endif

        <!-- Form with styled inputs -->
        <form method="POST" action="{{ route('integrations.store') }}" class="space-y-6">
            @csrf

            <div class="group bg-gray-800/50 backdrop-blur-sm border border-gray-700/50 rounded-xl p-6 transition-all duration-300 hover:border-orange-500/30 hover:shadow-lg hover:shadow-orange-500/10">
    <label for="notion_api" class="flex items-center gap-3 mb-3 text-white">
        <div class="bg-gradient-to-br from-orange-500/20 to-red-600/20 p-2 rounded-lg">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-orange-400" fill="currentColor" viewBox="0 0 24 24">
                <path d="M4.459 4.208c.746.606 1.026.56 2.428.466l13.215-.793c.28 0 .047-.28-.046-.326L17.86 1.968c-.42-.326-.981-.7-2.055-.607L3.01 2.295c-.466.046-.56.28-.374.466l1.823 1.447zm1.775 2.055c.187.466.187.886.187 1.727v13.775c0 .746.373 1.027 1.213 1.027.933 0 1.353-.28 1.727-1.12l7.318-11.85c.14-.186.047-.373-.094-.373l-3.896.7c-.28.047-.327.234-.187.42l4.734 7.318c.047.047.047.094.047.187v-1.727l-4.734-7.318c-.094-.14-.14-.326-.047-.514l.886-1.96c.187-.466.047-.98-.42-1.12L4.696 3.628c-.42-.14-.933 0-1.12.42l-.374.793c-.14.326-.28.7-.28 1.213L3.01 6.24c0 .466.28.653.653.653h2.147c.326-.047.42-.28.42-.606zm6.638 2.568c.187-.14.467-.047.653.093l1.12.84c.187.187.187.33.187.514v.046l-1.214 1.68-1.493-1.4c-.094-.187-.094-.33-.047-.514l.794-1.26zm-3.57 11.664c-.14.187-.047.373.094.467l1.214.84c.187.14.373.094.513-.047l2.708-3.734V8.954l-1.214 1.826-3.316 4.56z"/>
            </svg>
        </div>
        <div class="flex-1">
            <span class="font-medium">Notion API Key</span>
            @if(auth()->user()->integrations->where('tipo', 'notion')->count() > 0)
                <span class="ml-2 px-2 py-1 bg-green-500/20 text-green-300 text-xs rounded-full">Configurada</span>
            @endif
        </div>
    </label>
    
    <input id="notion_api" name="notion_api" type="text" placeholder="secret_xxx..."
           value="{{ auth()->user()->integrations->where('tipo', 'notion')->first()?->token }}"
           class="mt-1 block w-full bg-gray-900/50 text-white border border-gray-700 rounded-lg p-3 shadow-sm focus:ring-orange-500 focus:border-orange-500 transition-all duration-300" />
    
    <div class="flex items-center justify-between mt-3">
        <p class="text-sm">
            <a href="https://developers.notion.com/docs/getting-started" target="_blank" class="inline-flex items-center text-orange-400 hover:text-orange-300 transition-colors duration-300 group">
                <span class="underline-offset-4 group-hover:underline">¿Cómo obtenerla?</span>
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 ml-1 transform transition-transform group-hover:translate-x-0.5" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M10.293 5.293a1 1 0 011.414 0l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414-1.414L12.586 11H5a1 1 0 110-2h7.586l-2.293-2.293a1 1 0 010-1.414z" clip-rule="evenodd" />
                </svg>
            </a>
        </p>
        
        @if(auth()->user()->integrations->where('tipo', 'notion')->count() > 0)
            @php $notionConfig = json_decode(auth()->user()->integrations->where('tipo', 'notion')->first()->config ?? '{}', true); @endphp
            <div class="flex items-center gap-2">
                @if(isset($notionConfig['database_id']))
                    <span class="text-xs text-green-300">✓ Database: {{ $notionConfig['database_title'] ?? 'Configurada' }}</span>
                @endif
                <a href="{{ route('integrations.notion.databases') }}" class="text-xs text-blue-400 hover:text-blue-300 underline">
                    {{ isset($notionConfig['database_id']) ? 'Cambiar' : 'Seleccionar' }} database
                </a>
            </div>
        @endif
    </div>
</div>

            <!-- Google Sheets Token -->
            <div class="group bg-gray-800/50 backdrop-blur-sm border border-gray-700/50 rounded-xl p-6 transition-all duration-300 hover:border-orange-500/30 hover:shadow-lg hover:shadow-orange-500/10">
                <label for="google_token" class="flex items-center gap-3 mb-3 text-white">
                    <div class="bg-gradient-to-br from-orange-500/20 to-red-600/20 p-2 rounded-lg">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-orange-400" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M19.993 8.448c-.006-.12-.012-.24-.018-.358H8v5.01h7.34c-.305 1.59-1.533 2.962-3.296 3.638v3.033h5.35c3.12-2.867 4.917-7.08 4.917-12.03 0-1.131-.099-2.242-.29-3.323z" fill="#4285F4"/>
                            <path d="M8 21c4.468 0 8.223-1.456 10.957-3.933l-5.35-3.027c-1.48.995-3.38 1.585-5.607 1.585-4.295 0-7.934-2.898-9.235-6.8H-6.58v3.127C-3.825 17.735 1.816 21 8 21z" fill="#34A853"/>
                            <path d="M-1.235 8.825c-.336-1.007-.536-2.086-.536-3.202s.2-2.195.536-3.202V-.706h-5.345a13.25 13.25 0 000 12.658l5.345-3.127z" fill="#FBBC05"/>
                            <path d="M8 3.375c2.433 0 4.616.84 6.335 2.472l4.743-4.743C17.093.415 12.957-1 8-1 1.816-1-3.825 2.265-6.58 8.025l5.345 3.127c1.3-3.902 4.94-6.8 9.235-6.8z" fill="#EA4335"/>
                        </svg>
                    </div>
                    <span class="font-medium">Token de Google Sheets</span>
                </label>
                <input id="google_token" name="google_token" type="text" placeholder="ya29.a0AfH6SM..."
                       class="mt-1 block w-full bg-gray-900/50 text-white border border-gray-700 rounded-lg p-3 shadow-sm focus:ring-orange-500 focus:border-orange-500 transition-all duration-300" />
                <p class="text-sm mt-3">
                    <a href="{{ route('oauth.google.redirect') }}" class="inline-flex items-center text-orange-400 hover:text-orange-300 transition-colors duration-300 group mr-4">
                        <span class="underline-offset-4 group-hover:underline">Conectar con Google OAuth</span>
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 ml-1 transform transition-transform group-hover:translate-x-0.5" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10.293 5.293a1 1 0 011.414 0l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414-1.414L12.586 11H5a1 1 0 110-2h7.586l-2.293-2.293a1 1 0 010-1.414z" clip-rule="evenodd" />
                        </svg>
                    </a>
                    @if(auth()->user()->integrations()->where('tipo', 'google_sheets')->exists())
                        <a href="{{ route('oauth.google.sheets') }}" class="inline-flex items-center text-blue-400 hover:text-blue-300 transition-colors duration-300 group">
                            <span class="underline-offset-4 group-hover:underline">Configurar Spreadsheet</span>
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 ml-1" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M11.49 3.17c-.38-1.56-2.6-1.56-2.98 0a1.532 1.532 0 01-2.286.948c-1.372-.836-2.942.734-2.106 2.106.54.886.061 2.042-.947 2.287-1.561.379-1.561 2.6 0 2.978a1.532 1.532 0 01.947 2.287c-.836 1.372.734 2.942 2.106 2.106a1.532 1.532 0 012.287.947c.379 1.561 2.6 1.561 2.978 0a1.533 1.533 0 012.287-.947c1.372.836 2.942-.734 2.106-2.106a1.533 1.533 0 01.947-2.287c1.561-.379 1.561-2.6 0-2.978a1.532 1.532 0 01-.947-2.287c.836-1.372-.734-2.942-2.106-2.106a1.532 1.532 0 01-2.287-.947zM10 13a3 3 0 100-6 3 3 0 000 6z" clip-rule="evenodd" />
                            </svg>
                        </a>
                    @endif
                </p>
            </div>

            <!-- Slack Bot Token -->
            <div class="group bg-gray-800/50 backdrop-blur-sm border border-gray-700/50 rounded-xl p-6 transition-all duration-300 hover:border-orange-500/30 hover:shadow-lg hover:shadow-orange-500/10">
                <label for="slack_token" class="flex items-center gap-3 mb-3 text-white">
                    <div class="bg-gradient-to-br from-orange-500/20 to-red-600/20 p-2 rounded-lg">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-orange-400" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M5.042 15.165a2.528 2.528 0 0 1-2.52 2.523A2.528 2.528 0 0 1 0 15.165a2.527 2.527 0 0 1 2.522-2.52h2.52v2.52zM6.313 15.165a2.527 2.527 0 0 1 2.521-2.52 2.527 2.527 0 0 1 2.521 2.52v6.313A2.528 2.528 0 0 1 8.834 24a2.528 2.528 0 0 1-2.521-2.522v-6.313zM8.834 5.042a2.528 2.528 0 0 1-2.521-2.52A2.528 2.528 0 0 1 8.834 0a2.528 2.528 0 0 1 2.521 2.522v2.52H8.834zM8.834 6.313a2.528 2.528 0 0 1 2.521 2.521 2.528 2.528 0 0 1-2.521 2.521H2.522A2.528 2.528 0 0 1 0 8.834a2.528 2.528 0 0 1 2.522-2.521h6.312zM18.956 8.834a2.528 2.528 0 0 1 2.522-2.521A2.528 2.528 0 0 1 24 8.834a2.528 2.528 0 0 1-2.522 2.521h-2.522V8.834zM17.688 8.834a2.528 2.528 0 0 1-2.523 2.521 2.527 2.527 0 0 1-2.52-2.521V2.522A2.527 2.527 0 0 1 15.165 0a2.528 2.528 0 0 1 2.523 2.522v6.312zM15.165 18.956a2.528 2.528 0 0 1 2.523 2.522A2.528 2.528 0 0 1 15.165 24a2.527 2.527 0 0 1-2.52-2.522v-2.522h2.52zM15.165 17.688a2.527 2.527 0 0 1-2.52-2.523 2.526 2.526 0 0 1 2.52-2.52h6.313A2.527 2.527 0 0 1 24 15.165a2.528 2.528 0 0 1-2.522 2.523h-6.313z"/>
                        </svg>
                    </div>
                    <span class="font-medium">Slack Bot Token</span>
                </label>
                <input id="slack_token" name="slack_token" type="text" placeholder="xoxb-1234..."
                       class="mt-1 block w-full bg-gray-900/50 text-white border border-gray-700 rounded-lg p-3 shadow-sm focus:ring-orange-500 focus:border-orange-500 transition-all duration-300" />
                <p class="text-sm mt-3">
                    <a href="https://api.slack.com/authentication/token-types#bot" target="_blank" class="inline-flex items-center text-orange-400 hover:text-orange-300 transition-colors duration-300 group">
                        <span class="underline-offset-4 group-hover:underline">Ver documentación de Slack</span>
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 ml-1 transform transition-transform group-hover:translate-x-0.5" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10.293 5.293a1 1 0 011.414 0l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414-1.414L12.586 11H5a1 1 0 110-2h7.586l-2.293-2.293a1 1 0 010-1.414z" clip-rule="evenodd" />
                        </svg>
                    </a>
                </p>
            </div>

            <!-- Submit button with animation -->
            <div class="pt-6">
                <button type="submit" 
                        class="group relative w-full overflow-hidden py-4 px-6 text-center rounded-xl bg-gradient-to-r from-orange-500 to-red-500 text-white font-bold text-lg shadow-xl shadow-orange-500/20 transform transition-all duration-300 hover:-translate-y-1 hover:shadow-orange-500/30">
                    <span class="relative z-10">Guardar claves</span>
                    <div class="absolute inset-0 bg-gradient-to-r from-orange-600 to-red-600 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                </button>
            </div>
        </form>

        <!-- Help section -->
        <div class="mt-12 text-center">
            <p class="text-gray-400 text-sm">¿Necesitas ayuda con tus integraciones?</p>
            <a href="#faq" class="inline-flex items-center px-5 py-2 mt-2 bg-white/10 hover:bg-white/20 backdrop-blur-sm border border-white/20 rounded-full text-orange-400 transition-colors">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-8-3a1 1 0 00-.867.5 1 1 0 11-1.731-1A3 3 0 0113 8a3.001 3.001 0 01-2 2.83V11a1 1 0 11-2 0v-1a1 1 0 011-1 1 1 0 100-2zm0 8a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd" />
                </svg>
                Consulta las preguntas frecuentes
            </a>
        </div>
    </div>

    <!-- Background decorative elements -->
    <div class="fixed inset-0 -z-10 overflow-hidden">
        <div class="absolute top-1/4 left-1/5 w-96 h-96 bg-orange-500/10 rounded-full filter blur-[80px] animate-pulse"></div>
        <div class="absolute bottom-1/3 right-1/4 w-80 h-80 bg-purple-600/10 rounded-full filter blur-[100px] animate-pulse" style="animation-delay: 1s;"></div>
    </div>

    <script>
        function testIntegration(tipo, integrationId) {
            const button = event.target.closest('button');
            const originalText = button.innerHTML;
            
            // Mostrar loading
            button.innerHTML = '<svg class="animate-spin h-4 w-4" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg> Probando...';
            button.disabled = true;

            const endpoints = {
                'notion': '/test/notion',
                'slack': '/test/slack',
                'google_sheets': '/test/google-sheets'
            };

            fetch(endpoints[tipo], {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    Swal.fire({
                        icon: 'success',
                        title: '✅ Integración funcionando',
                        text: data.message,
                        confirmButtonColor: '#f97316'
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: '❌ Error en la integración',
                        text: data.message,
                        confirmButtonColor: '#f43f5e'
                    });
                }
            })
            .catch(error => {
                console.error('Error:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Error de conexión',
                    text: 'No se pudo probar la integración. Revisa tu conexión.',
                    confirmButtonColor: '#f43f5e'
                });
            })
            .finally(() => {
                // Restaurar botón
                button.innerHTML = originalText;
                button.disabled = false;
            });
        }

        // Botón para probar todas las integraciones
        function testAllIntegrations() {
            Swal.fire({
                icon: 'info',
                title: 'Probando todas las integraciones...',
                text: 'Esto puede tomar unos segundos',
                allowOutsideClick: false,
                showConfirmButton: false,
                willOpen: () => {
                    Swal.showLoading();
                }
            });

            fetch('/test/all-integrations', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => response.json())
            .then(data => {
                let message = '';
                let hasErrors = false;

                Object.entries(data.results).forEach(([tipo, result]) => {
                    const icon = result.success ? '✅' : '❌';
                    const name = tipo.charAt(0).toUpperCase() + tipo.slice(1).replace('_', ' ');
                    message += `${icon} ${name}: ${result.message}\n`;
                    if (!result.success) hasErrors = true;
                });

                Swal.fire({
                    icon: hasErrors ? 'warning' : 'success',
                    title: 'Resultado de las pruebas',
                    text: message,
                    confirmButtonColor: '#f97316'
                });
            })
            .catch(error => {
                console.error('Error:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'No se pudieron probar las integraciones',
                    confirmButtonColor: '#f43f5e'
                });
            });
        }
    </script>
@endsection