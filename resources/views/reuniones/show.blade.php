@extends('layouts.app')

@section('content')
<div class="py-12 px-6 max-w-6xl mx-auto">
    <!-- Header con navegación -->
    <div class="mb-8 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div class="flex items-center gap-4">
            <a href="{{ route('reuniones.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-700/50 hover:bg-gray-700 text-gray-300 hover:text-white rounded-lg transition-colors">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z" clip-rule="evenodd" />
                </svg>
                Volver a reuniones
            </a>
            <div class="h-6 w-px bg-gray-600"></div>
            <h1 class="text-2xl font-bold bg-gradient-to-r from-orange-400 to-red-500 bg-clip-text text-transparent">
                {{ $meeting->titulo ?? 'Reunión sin título' }}
            </h1>
        </div>
        
        <!-- Estado y acciones -->
        <div class="flex items-center gap-3">
            @if($meeting->resumen)
                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm bg-green-500/20 text-green-300 border border-green-500/30">
                    <svg class="w-4 h-4 mr-1.5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                    </svg>
                    Procesada
                </span>
            @else
                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm bg-yellow-500/20 text-yellow-300 border border-yellow-500/30">
                    <svg class="animate-spin w-4 h-4 mr-1.5" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    Procesando...
                </span>
            @endif
            
            <button onclick="sendMeetingByEmail()" class="inline-flex items-center px-4 py-2 bg-blue-600/20 hover:bg-blue-600/30 text-blue-300 rounded-lg transition-colors border border-blue-600/30">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12H8m8 0l-4 4m4-4l-4-4m8 8V4a2 2 0 00-2-2H6a2 2 0 00-2 2v16a2 2 0 002 2h8" />
                </svg>
                Enviar por correo
            </button>
        </div>
    </div>

    <!-- Metadatos de la reunión -->
    <div class="mb-8 grid grid-cols-1 md:grid-cols-4 gap-4">
        <div class="bg-gray-800/40 backdrop-blur-sm border border-gray-700/50 rounded-xl p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-400">Fecha</p>
                    <p class="text-lg font-semibold text-white">{{ $meeting->created_at->format('d/m/Y') }}</p>
                    <p class="text-xs text-gray-500">{{ $meeting->created_at->format('H:i') }}</p>
                </div>
                <div class="text-orange-500">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-gray-800/40 backdrop-blur-sm border border-gray-700/50 rounded-xl p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-400">Formato</p>
                    <p class="text-lg font-semibold text-white">{{ ucfirst($meeting->formato_origen) }}</p>
                    <p class="text-xs text-gray-500">{{ pathinfo($meeting->archivo, PATHINFO_EXTENSION) }}</p>
                </div>
                <div class="text-blue-500">
                    @if($meeting->formato_origen === 'video')
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                        </svg>
                    @else
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11a7 7 0 01-7 7m0 0a7 7 0 01-7-7m7 7v4m0 0H8m4 0h4m-4-8a3 3 0 01-3-3V5a3 3 0 116 0v6a3 3 0 01-3 3z"></path>
                        </svg>
                    @endif
                </div>
            </div>
        </div>

        <div class="bg-gray-800/40 backdrop-blur-sm border border-gray-700/50 rounded-xl p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-400">Tareas</p>
                    <p class="text-lg font-semibold text-white">{{ $meeting->tasks->count() }}</p>
                    <p class="text-xs text-gray-500">
                        {{ $meeting->tasks->where('completada', true)->count() }} completadas
                    </p>
                </div>
                <div class="text-green-500">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-gray-800/40 backdrop-blur-sm border border-gray-700/50 rounded-xl p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-400">Estado</p>
                    @if($meeting->guardar_en_google_sheets)
                        <p class="text-lg font-semibold text-white">Sincronizada</p>
                        <p class="text-xs text-gray-500">Google Sheets</p>
                    @else
                        <p class="text-lg font-semibold text-white">Local</p>
                        <p class="text-xs text-gray-500">Solo en Reunai</p>
                    @endif
                </div>
                <div class="{{ $meeting->guardar_en_google_sheets ? 'text-green-500' : 'text-gray-500' }}">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Columna principal: Resumen -->
        <div class="lg:col-span-2 space-y-8">
            <!-- Resumen generado por IA -->
            @if($meeting->resumen)
            <div class="bg-gray-800/50 backdrop-blur-sm border border-gray-700/50 rounded-xl p-6">
                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-xl font-semibold text-white flex items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 mr-2 text-orange-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        Análisis de la reunión
                    </h2>
                    <div class="flex gap-2">
                        <button onclick="copyToClipboard('resumen')" class="p-2 text-gray-400 hover:text-white hover:bg-gray-700 rounded-lg transition-colors" title="Copiar resumen">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                            </svg>
                        </button>
                        <button onclick="toggleFullscreen('resumen-content')" class="p-2 text-gray-400 hover:text-white hover:bg-gray-700 rounded-lg transition-colors" title="Pantalla completa">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4"></path>
                            </svg>
                        </button>
                    </div>
                </div>
                <div id="resumen-content" class="prose prose-invert max-w-none text-white">
                        {!! $meeting->resumen !!}
                </div>
            </div>
            @else
            <div class="bg-gray-800/50 backdrop-blur-sm border border-gray-700/50 rounded-xl p-6 text-center">
                <div class="flex flex-col items-center py-8">
                    <svg class="animate-spin w-12 h-12 text-orange-500 mb-4" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    <h3 class="text-lg font-semibold text-white mb-2">Procesando reunión...</h3>
                    <p class="text-gray-400 text-sm">La IA está analizando tu reunión. Esto puede tomar varios minutos.</p>
                    <button onclick="window.location.reload()" class="mt-4 px-4 py-2 bg-orange-500 hover:bg-orange-600 text-white rounded-lg transition-colors">
                        Actualizar página
                    </button>
                </div>
            </div>
            @endif


            @if($meeting->insight && $stats['plan'] !== 'free')
                <div class="bg-gradient-to-br from-purple-900/30 to-blue-900/30 backdrop-blur-sm border border-purple-500/30 rounded-xl p-6 shadow-lg shadow-purple-500/10">
                    <div class="flex items-center justify-between mb-6">
                        <h2 class="text-xl font-semibold text-white flex items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 mr-2 text-purple-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z" />
                            </svg>
                            Insight conductual
                           
                        </h2>
                        <div class="flex gap-2">
                            <button onclick="copyToClipboard('insight')" class="p-2 text-gray-400 hover:text-white hover:bg-purple-700/50 rounded-lg transition-colors" title="Copiar insight">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                                </svg>
                            </button>
                        </div>
                    </div>
                    
                    <div class="bg-gray-900/50 rounded-lg p-5 border-purple-500/20">
                        <div id="insight-content" class="prose prose-invert max-w-none text-gray-200 leading-relaxed">
                            {!! Str::markdown($meeting->insight) !!}
                        </div>
                    </div>
                    
                    <!-- Badge decorativo -->
                    <div class="mt-4 flex items-center gap-2 text-xs text-purple-300/70">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                        </svg>
                        <span>Análisis automático basado en patrones de comunicación</span>
                    </div>
                </div>
                @elseif($meeting->insight && $stats['plan'] === 'free')
                    <div class="bg-gray-800/50 border border-gray-700/50 rounded-xl p-6 text-center">
                        <svg class="w-10 h-10 mx-auto text-purple-400 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <p class="text-white font-semibold mb-1">Insight avanzado</p>
                        <p class="text-sm text-gray-400 mb-4">
                            Disponible solo en planes Starter y Pro
                        </p>
                        <a href="{{ route('subscription.manage') }}"
                        class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-orange-500 to-red-500 text-white rounded-lg">
                            Actualizar plan
                        </a>
                    </div>
                @endif
            @if($meeting->sentiment_analysis)
<div class="bg-gray-800/40 backdrop-blur-sm border border-gray-700/50 rounded-xl p-6 mb-6">
    <h3 class="text-xl font-semibold mb-4 flex items-center text-white">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 mr-2 text-purple-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.828 14.828a4 4 0 01-5.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
        </svg>
        Análisis de Sentimiento
    </h3>
    
    <div class="space-y-4">
        <!-- Positivo -->
        <div class="bg-gray-800/60 backdrop-blur-sm rounded-xl p-4 border border-gray-700/50">
            <div class="flex items-center justify-between mb-3">
                <div class="flex items-center">
                    <div class="bg-green-500/20 p-2 rounded-lg mr-3">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-green-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.828 14.828a4 4 0 01-5.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <span class="text-gray-300 font-medium">Positivo</span>
                </div>
                <span class="text-green-400 font-bold text-lg">{{ $meeting->sentiment_analysis['positivo'] ?? 0 }}%</span>
            </div>
            <!-- Contenedor con ancho 100% -->
            <div class="relative w-full h-2.5 bg-gray-700/50 rounded-full overflow-hidden">
                <!-- Barra con width inline -->
                <div class="absolute top-0 left-0 h-full rounded-full shadow-lg"
                     style="width: {{ $meeting->sentiment_analysis['positivo'] ?? 0 }}%; background: linear-gradient(to right, #4ade80, #10b981);"></div>
            </div>
        </div>
        
        <!-- Neutral -->
        <div class="bg-gray-800/60 backdrop-blur-sm rounded-xl p-4 border border-gray-700/50">
            <div class="flex items-center justify-between mb-3">
                <div class="flex items-center">
                    <div class="bg-blue-500/20 p-2 rounded-lg mr-3">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-blue-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                    </div>
                    <span class="text-gray-300 font-medium">Neutral</span>
                </div>
                <span class="text-blue-400 font-bold text-lg">{{ $meeting->sentiment_analysis['neutral'] ?? 0 }}%</span>
            </div>
            <div class="relative w-full h-2.5 bg-gray-700/50 rounded-full overflow-hidden">
                <div class="absolute top-0 left-0 h-full rounded-full shadow-lg"
                     style="width: {{ $meeting->sentiment_analysis['neutral'] ?? 0 }}%; background: linear-gradient(to right, #60a5fa, #06b6d4);"></div>
            </div>
        </div>
        
        <!-- Crítico -->
        <div class="bg-gray-800/60 backdrop-blur-sm rounded-xl p-4 border border-gray-700/50">
            <div class="flex items-center justify-between mb-3">
                <div class="flex items-center">
                    <div class="bg-yellow-500/20 p-2 rounded-lg mr-3">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-yellow-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                    </div>
                    <span class="text-gray-300 font-medium">Crítico</span>
                </div>
                <span class="text-yellow-400 font-bold text-lg">{{ $meeting->sentiment_analysis['critico'] ?? 0 }}%</span>
            </div>
            <div class="relative w-full h-2.5 bg-gray-700/50 rounded-full overflow-hidden">
                <div class="absolute top-0 left-0 h-full rounded-full shadow-lg"
                     style="width: {{ $meeting->sentiment_analysis['critico'] ?? 0 }}%; background: linear-gradient(to right, #facc15, #f97316);"></div>
            </div>
        </div>
    </div>
    
    <!-- Resumen del sentimiento -->
    @if(isset($meeting->sentiment_analysis['resumen_sentimiento']))
    <div class="mt-6 bg-gradient-to-r from-purple-500/10 to-pink-500/10 backdrop-blur-sm rounded-xl p-4 border border-purple-500/20">
        <div class="flex items-start">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-purple-400 mr-2 flex-shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <p class="text-sm text-gray-300">
                <span class="font-semibold text-white">Clima general:</span> 
                {{ $meeting->sentiment_analysis['resumen_sentimiento'] }}
            </p>
        </div>
    </div>
    @endif
    
    <!-- Badge del sentimiento dominante -->
    @php
        $sentimientos = array_filter($meeting->sentiment_analysis, function($key) {
            return in_array($key, ['positivo', 'neutral', 'critico']);
        }, ARRAY_FILTER_USE_KEY);
        
        arsort($sentimientos);
        $dominante = array_key_first($sentimientos);
        
        $badges = [
            'positivo' => [
                'class' => 'bg-green-500/20 text-green-300 border-green-500/30',
                'texto' => 'Reunión predominantemente positiva',
                'icon' => 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z'
            ],
            'neutral' => [
                'class' => 'bg-blue-500/20 text-blue-300 border-blue-500/30',
                'texto' => 'Reunión de carácter informativo',
                'icon' => 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z'
            ],
            'critico' => [
                'class' => 'bg-yellow-500/20 text-yellow-300 border-yellow-500/30',
                'texto' => 'Reunión con puntos críticos a revisar',
                'icon' => 'M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z'
            ]
        ];
        
        $badge = $badges[$dominante] ?? $badges['neutral'];
    @endphp
    
    <div class="mt-4 flex justify-center">
        <span class="inline-flex items-center px-4 py-2 rounded-full text-sm font-medium border {{ $badge['class'] }}">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $badge['icon'] }}" />
            </svg>
            {{ $badge['texto'] }}
        </span>
    </div>
</div>
@endif

            <!-- Transcripción completa -->
            @if($meeting->transcripcion)
            <div class="bg-gray-800/50 backdrop-blur-sm border border-gray-700/50 rounded-xl p-6">
                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-xl font-semibold text-white flex items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 mr-2 text-blue-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-1l-4 4z" />
                        </svg>
                        Transcripción completa
                    </h2>
                    <div class="flex gap-2">
                        <button onclick="copyToClipboard('transcripcion')" class="p-2 text-gray-400 hover:text-white hover:bg-gray-700 rounded-lg transition-colors" title="Copiar transcripción">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                            </svg>
                        </button>
                        <button onclick="toggleTranscript()" class="p-2 text-gray-400 hover:text-white hover:bg-gray-700 rounded-lg transition-colors" title="Mostrar/Ocultar">
                            <svg id="transcript-toggle-icon" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </button>
                    </div>
                </div>
                <div id="transcripcion-content" class="max-h-96 overflow-y-auto bg-gray-900/50 rounded-lg p-4 hidden">
                    <p class="text-gray-300 leading-relaxed whitespace-pre-wrap">{{ $meeting->transcripcion }}</p>
                </div>
            </div>
            @endif
        </div>

        <!-- Sidebar: Tareas -->
       <!-- Sidebar: Tareas y Acciones -->
        @if($stats['plan'] !== 'free')

        <div class="space-y-6">
            <!-- Lista de tareas -->
            <div class="bg-gray-800/50 backdrop-blur-sm border border-gray-700/50 rounded-xl p-6">
                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-xl font-semibold text-white flex items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 mr-2 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                        </svg>
                        Tareas extraídas
                        @if($meeting->tasks->count() > 0)
                            <span class="ml-2 px-2 py-1 bg-green-500/20 text-green-300 text-sm rounded-full">
                                {{ $meeting->tasks->count() }}
                            </span>
                        @endif
                    </h2>
                </div>

                @if($meeting->tasks->count() > 0)
                    <div class="space-y-3">
                        @foreach($meeting->tasks as $index => $task)
                        <div class="p-4 bg-gray-900/50 rounded-lg border border-gray-700/30 hover:border-gray-600/50 transition-all">
                            <div class="flex items-start justify-between mb-3">
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm text-gray-300 leading-relaxed">
                                        <span class="text-orange-400 font-medium">{{ $index + 1 }}.</span>
                                        {{ $task->descripcion }}
                                    </p>
                                </div>
                                <div class="flex items-center gap-2 ml-3">
                                    <!-- Dropdown de acciones -->
                                    <div class="relative" x-data="{ open: false }">
                                        <button @click="open = !open" class="p-1.5 text-gray-400 hover:text-white hover:bg-gray-700 rounded-lg transition-colors">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z"></path>
                                            </svg>
                                        </button>
                                        
                                        <div x-show="open" @click.away="open = false" 
                                             x-transition:enter="transition ease-out duration-100"
                                             x-transition:enter-start="transform opacity-0 scale-95"
                                             x-transition:enter-end="transform opacity-100 scale-100"
                                             class="absolute right-0 mt-2 w-48 bg-gray-800 rounded-lg shadow-xl border border-gray-700 z-20">
                                            
                                            <div class="p-2">
                                                <button onclick="copyTask({{ $task->id }})" class="w-full flex items-center px-3 py-2 text-sm text-gray-300 hover:bg-gray-700 rounded-lg">
                                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                                                    </svg>
                                                    Copiar tarea
                                                </button>
                                                
                                                @if(auth()->user()->integrations->where('tipo', 'slack')->count() > 0)
                                                <!-- <button onclick="sendToSlack({{ $task->id }})" class="w-full flex items-center px-3 py-2 text-sm text-gray-300 hover:bg-gray-700 rounded-lg">
                                                    <svg class="w-4 h-4 mr-2 text-green-400" fill="currentColor" viewBox="0 0 24 24">
                                                        <path d="M5.042 15.165a2.528 2.528 0 0 1-2.52 2.523A2.528 2.528 0 0 1 0 15.165a2.527 2.527 0 0 1 2.522-2.52h2.52v2.52zM6.313 15.165a2.527 2.527 0 0 1 2.521-2.52 2.527 2.527 0 0 1 2.521 2.52v6.313A2.528 2.528 0 0 1 8.834 24a2.528 2.528 0 0 1-2.521-2.522v-6.313zM8.834 5.042a2.528 2.528 0 0 1-2.521-2.52A2.528 2.528 0 0 1 8.834 0a2.528 2.528 0 0 1 2.521 2.522v2.52H8.834zM8.834 6.313a2.528 2.528 0 0 1 2.521 2.521 2.528 2.528 0 0 1-2.521 2.521H2.522A2.528 2.528 0 0 1 0 8.834a2.528 2.528 0 0 1 2.522-2.521h6.312zM18.956 8.834a2.528 2.528 0 0 1 2.522-2.521A2.528 2.528 0 0 1 24 8.834a2.528 2.528 0 0 1-2.522 2.521h-2.522V8.834zM17.688 8.834a2.528 2.528 0 0 1-2.523 2.521 2.527 2.527 0 0 1-2.52-2.521V2.522A2.527 2.527 0 0 1 15.165 0a2.528 2.528 0 0 1 2.523 2.522v6.312zM15.165 18.956a2.528 2.528 0 0 1 2.523 2.522A2.528 2.528 0 0 1 15.165 24a2.527 2.527 0 0 1-2.52-2.522v-2.522h2.52zM15.165 17.688a2.527 2.527 0 0 1-2.52-2.523 2.526 2.526 0 0 1 2.52-2.52h6.313A2.527 2.527 0 0 1 24 15.165a2.528 2.528 0 0 1-2.522 2.523h-6.313z"/>
                                                    </svg>
                                                    Enviar a Slack
                                                </button> -->
                                                @endif
                                                
                                                @if(auth()->user()->integrations->where('tipo', 'notion')->count() > 0)
                                                <!-- <button onclick="sendToNotion({{ $task->id }})" class="w-full flex items-center px-3 py-2 text-sm text-gray-300 hover:bg-gray-700 rounded-lg">
                                                    <svg class="w-4 h-4 mr-2 text-blue-400" fill="currentColor" viewBox="0 0 24 24">
                                                        <path d="M4.459 4.208c.746.606 1.026.56 2.428.466l13.215-.793c.28 0 .047-.28-.046-.326L17.86 1.968c-.42-.326-.981-.7-2.055-.607L3.01 2.295c-.466.046-.56.28-.374.466l1.823 1.447zm1.775 2.055c.187.466.187.886.187 1.727v13.775c0 .746.373 1.027 1.213 1.027.933 0 1.353-.28 1.727-1.12l7.318-11.85c.14-.186.047-.373-.094-.373l-3.896.7c-.28.047-.327.234-.187.42l4.734 7.318c.047.047.047.094.047.187v-1.727l-4.734-7.318c-.094-.14-.14-.326-.047-.514l.886-1.96c.187-.466.047-.98-.42-1.12L4.696 3.628c-.42-.14-.933 0-1.12.42l-.374.793c-.14.326-.28.7-.28 1.213L3.01 6.24c0 .466.28.653.653.653h2.147c.326-.047.42-.28.42-.606zm6.638 2.568c.187-.14.467-.047.653.093l1.12.84c.187.187.187.33.187.514v.046l-1.214 1.68-1.493-1.4c-.094-.187-.094-.33-.047-.514l.794-1.26zm-3.57 11.664c-.14.187-.047.373.094.467l1.214.84c.187.14.373.094.513-.047l2.708-3.734V8.954l-1.214 1.826-3.316 4.56z"/>
                                                    </svg>
                                                    Enviar a Notion
                                                </button> -->
                                                @endif
                                                
                                                @if(auth()->user()->integrations->where('tipo', 'google_sheets')->count() > 0)
                                               <!--  <button onclick="sendToSheets({{ $task->id }})" class="w-full flex items-center px-3 py-2 text-sm text-gray-300 hover:bg-gray-700 rounded-lg">
                                                    <svg class="w-4 h-4 mr-2 text-green-500" fill="currentColor" viewBox="0 0 24 24">
                                                        <path d="M19.11 2H4.89A2.89 2.89 0 002 4.89v14.22A2.89 2.89 0 004.89 22h14.22A2.89 2.89 0 0022 19.11V4.89A2.89 2.89 0 0019.11 2zM8 6h8v2H8V6zm0 4h8v2H8v-2zm0 4h5v2H8v-2z"/>
                                                    </svg>
                                                    Enviar a Sheets
                                                </button> -->
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-8">
                        <svg class="w-12 h-12 text-gray-600 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                        </svg>
                        <p class="text-gray-400 text-sm">No se identificaron tareas en esta reunión</p>
                    </div>
                @endif
            </div>

            <!-- Panel de acciones masivas -->
            @if($meeting->tasks->count() > 0)
            <!-- <div class="bg-gray-800/50 backdrop-blur-sm border border-gray-700/50 rounded-xl p-6">
                <h3 class="text-lg font-semibold text-white mb-4 flex items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 text-orange-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z" />
                    </svg>
                    Acciones rápidas
                </h3>

                <div class="space-y-3"> -->
                    <!-- Enviar todas las tareas -->
                    @if(auth()->user()->integrations->count() > 0)
                    <!-- <div class="bg-gray-900/50 rounded-lg p-4 border border-gray-700/30">
                        <h4 class="text-sm font-medium text-white mb-3">Enviar todas las tareas:</h4>
                        <div class="grid grid-cols-1 gap-2"> -->
                            @if(auth()->user()->integrations->where('tipo', 'slack')->count() > 0)
                            <!-- <button onclick="sendAllTasksTo('slack')" class="flex items-center justify-center px-3 py-2 bg-green-600/20 hover:bg-green-600/30 text-green-300 rounded-lg transition-colors text-sm border border-green-600/30">
                                <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M5.042 15.165a2.528 2.528 0 0 1-2.52 2.523A2.528 2.528 0 0 1 0 15.165a2.527 2.527 0 0 1 2.522-2.52h2.52v2.52zM6.313 15.165a2.527 2.527 0 0 1 2.521-2.52 2.527 2.527 0 0 1 2.521 2.52v6.313A2.528 2.528 0 0 1 8.834 24a2.528 2.528 0 0 1-2.521-2.522v-6.313z"/>
                                </svg>
                                Todas a Slack
                            </button> -->
                            @endif
                            
                            @if(auth()->user()->integrations->where('tipo', 'notion')->count() > 0)
                            <!-- <button onclick="sendAllTasksTo('notion')" class="flex items-center justify-center px-3 py-2 bg-blue-600/20 hover:bg-blue-600/30 text-blue-300 rounded-lg transition-colors text-sm border border-blue-600/30">
                                <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M4.459 4.208c.746.606 1.026.56 2.428.466l13.215-.793c.28 0 .047-.28-.046-.326L17.86 1.968c-.42-.326-.981-.7-2.055-.607L3.01 2.295c-.466.046-.56.28-.374.466l1.823 1.447z"/>
                                </svg>
                                Todas a Notion
                            </button> -->
                            @endif
                            
                            @if(auth()->user()->integrations->where('tipo', 'google_sheets')->count() > 0)
                            <!-- <button onclick="sendAllTasksTo('sheets')" class="flex items-center justify-center px-3 py-2 bg-green-600/20 hover:bg-green-600/30 text-green-300 rounded-lg transition-colors text-sm border border-green-600/30">
                                <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M19.11 2H4.89A2.89 2.89 0 002 4.89v14.22A2.89 2.89 0 004.89 22h14.22A2.89 2.89 0 0022 19.11V4.89A2.89 2.89 0 0019.11 2z"/>
                                </svg>
                                Todas a Sheets
                            </button> -->
                            @endif
                        <!-- </div>
                    </div> -->
                    @endif

                    <!-- Copiar todo -->
                   <!--  <div class="bg-gray-900/50 rounded-lg p-4 border border-gray-700/30">
                        <h4 class="text-sm font-medium text-white mb-3">Exportar contenido:</h4>
                        <div class="grid grid-cols-2 gap-2">
                            <button onclick="copyAllTasks()" class="flex items-center justify-center px-3 py-2 bg-gray-600/20 hover:bg-gray-600/30 text-gray-300 rounded-lg transition-colors text-sm border border-gray-600/30">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                                </svg>
                                Copiar lista
                            </button>
                            <button onclick="exportToMarkdown()" class="flex items-center justify-center px-3 py-2 bg-purple-600/20 hover:bg-purple-600/30 text-purple-300 rounded-lg transition-colors text-sm border border-purple-600/30">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                                Markdown
                            </button>
                        </div>
                    </div>
                </div> -->

                @if(auth()->user()->integrations->count() === 0)
               <!--  <div class="mt-4 p-4 bg-orange-500/10 border border-orange-500/20 rounded-lg">
                    <div class="flex items-start gap-3">
                        <svg class="w-5 h-5 text-orange-400 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <div>
                            <p class="text-sm text-orange-300 font-medium">Conecta tus herramientas</p>
                            <p class="text-xs text-orange-400/80 mt-1">
                                Configura Slack, Notion o Google Sheets para enviar tareas automáticamente.
                            </p>
                            <a href="{{ route('integrations.index') }}" class="inline-flex items-center mt-2 text-xs text-orange-400 hover:text-orange-300 underline">
                                Configurar integraciones
                                <svg class="w-3 h-3 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path>
                                </svg>
                            </a>
                        </div>
                    </div>
                </div> -->
                @endif
<!--             </div>
 -->            @endif

            <!-- Información del archivo -->
            <div class="bg-gray-800/50 backdrop-blur-sm border border-gray-700/50 rounded-xl p-6">
                <h3 class="text-lg font-semibold text-white mb-4 flex items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    Información del archivo
                </h3>
                <div class="space-y-3 text-sm">
                    <div class="flex justify-between">
                        <span class="text-gray-400">Nombre:</span>
                        <span class="text-gray-300 overflow-hidden">{{ basename($meeting->archivo) }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-400">Tipo:</span>
                        <span class="text-gray-300">{{ strtoupper(pathinfo($meeting->archivo, PATHINFO_EXTENSION)) }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-400">Subido:</span>
                        <span class="text-gray-300">{{ $meeting->created_at->diffForHumans() }}</span>
                    </div>
                </div>

                @if($meeting->archivo)
                <div class="mt-4 pt-4 border-t border-gray-700/50">
                    <a href="{{ Storage::url($meeting->archivo) }}" target="_blank" class="inline-flex items-center w-full justify-center px-4 py-2 bg-blue-600/20 hover:bg-blue-600/30 text-blue-300 rounded-lg transition-colors border border-blue-600/30">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        Descargar archivo original
                    </a>
                </div>
                @endif
            </div>
        </div>
    </div>
    @else
    <div class="bg-gray-800/50 border border-gray-700/50 rounded-xl p-6 text-center">
        <svg class="w-10 h-10 mx-auto text-green-400 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2"/>
        </svg>
        <p class="text-white font-semibold mb-1">Tareas automáticas</p>
        <p class="text-sm text-gray-400 mb-4">
            La extracción de tareas está disponible a partir del plan Starter
        </p>
        <a href="{{ route('subscription.manage') }}"
           class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-orange-500 to-red-500 text-white rounded-lg">
            Ver planes
        </a>
    </div>
@endif
</div>

<script>
// Copiar contenido al portapapeles
function copyToClipboard(elementId) {
    const element = document.getElementById(elementId + '-content');
    const text = element.innerText || element.textContent;
    
    navigator.clipboard.writeText(text).then(() => {
        // Mostrar notificación
        Swal.fire({
            icon: 'success',
            title: 'Copiado',
            text: 'Contenido copiado al portapapeles',
            timer: 2000,
            showConfirmButton: false,
            toast: true,
            position: 'top-end'
        });
    });
}

// Toggle transcripción
function toggleTranscript() {
    const content = document.getElementById('transcripcion-content');
    const icon = document.getElementById('transcript-toggle-icon');
    
    if (content.classList.contains('hidden')) {
        content.classList.remove('hidden');
        icon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"></path>';
    } else {
        content.classList.add('hidden');
        icon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>';
    }
}

// Toggle tarea completada
function toggleTask(taskId) {
    fetch(`/tasks/${taskId}/toggle`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload(); // Recargar para actualizar el progreso
        }
    });
}

// Pantalla completa para resumen
function toggleFullscreen(elementId) {
    const element = document.getElementById(elementId);
    
    if (!document.fullscreenElement) {
        element.requestFullscreen().catch(err => {
            console.log(`Error attempting to enable fullscreen: ${err.message}`);
        });
    } else {
        document.exitFullscreen();
    }
}

function sendMeetingByEmail() {
    fetch(`/meetings/{{ $meeting->id }}/send-email`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            Swal.fire({
                icon: 'success',
                title: 'Enviado',
                text: 'El informe se ha enviado correctamente a tu correo.',
                timer: 3000,
                toast: true,
                position: 'top-end',
                showConfirmButton: false
            });
        } else {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: data.message || 'No se pudo enviar el correo.',
                toast: true,
                position: 'top-end',
                timer: 3000,
                showConfirmButton: false
            });
        }
    });
}


// Auto-refresh si la reunión está procesando
@if(!$meeting->resumen)
setTimeout(() => {
    window.location.reload();
}, 30000); // Recargar cada 30 segundos
@endif


// Funciones para manejar envío de tareas individuales
function copyTask(taskId) {
    const task = @json($meeting->tasks);
    const taskData = task.find(t => t.id === taskId);
    
    navigator.clipboard.writeText(taskData.descripcion).then(() => {
        showNotification('success', 'Tarea copiada al portapapeles');
    });
}

function sendToSlack(taskId) {
    sendTaskTo(taskId, 'slack');
}

function sendToNotion(taskId) {
    sendTaskTo(taskId, 'notion');
}

function sendToSheets(taskId) {
    sendTaskTo(taskId, 'sheets');
}

function sendTaskTo(taskId, platform) {
    fetch(`/tasks/${taskId}/send`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({ platform: platform })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showNotification('success', `Tarea enviada a ${platform.charAt(0).toUpperCase() + platform.slice(1)}`);
        } else {
            showNotification('error', 'Error al enviar la tarea');
        }
    })
    .catch(error => {
        showNotification('error', 'Error de conexión');
    });
}

// Funciones para acciones masivas
function sendAllTasksTo(platform) {
    fetch(`/meetings/{{ $meeting->id }}/send-all-tasks`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({ platform: platform })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showNotification('success', `Todas las tareas enviadas a ${platform.charAt(0).toUpperCase() + platform.slice(1)}`);
        } else {
            showNotification('error', 'Error al enviar las tareas');
        }
    })
    .catch(error => {
        showNotification('error', 'Error de conexión');
    });
}

function copyAllTasks() {
    const tasks = @json($meeting->tasks->pluck('descripcion'));
    const taskList = tasks.map((task, index) => `${index + 1}. ${task}`).join('\n');
    
    navigator.clipboard.writeText(taskList).then(() => {
        showNotification('success', 'Lista de tareas copiada al portapapeles');
    });
}

function exportToMarkdown() {
    const tasks = @json($meeting->tasks->pluck('descripcion'));
    const meetingTitle = '{{ $meeting->titulo ?? "Reunión sin título" }}';
    
    let markdown = `# ${meetingTitle}\n\n## Tareas extraídas\n\n`;
   markdown += tasks.map((task, index) => `${index + 1}. ${task}`).join('\n');
   markdown += `\n\n---\n*Generado por Meetlyze el ${new Date().toLocaleDateString()}*`;
   
   // Crear blob y descargar
   const blob = new Blob([markdown], { type: 'text/markdown' });
   const url = window.URL.createObjectURL(blob);
   const a = document.createElement('a');
   a.href = url;
   a.download = `${meetingTitle.replace(/[^a-z0-9]/gi, '_').toLowerCase()}_tareas.md`;
   document.body.appendChild(a);
   a.click();
   document.body.removeChild(a);
   window.URL.revokeObjectURL(url);
   
   showNotification('success', 'Archivo Markdown descargado');
}

// Función helper para notificaciones
function showNotification(type, message) {
   Swal.fire({
       icon: type,
       title: type === 'success' ? 'Éxito' : 'Error',
       text: message,
       timer: 3000,
       showConfirmButton: false,
       toast: true,
       position: 'top-end'
   });
}
    
</script>

@endsection