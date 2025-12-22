@extends('layouts.app')

@section('content')

@if (session('success'))
        <div class="mb-6 bg-green-500/20 border border-green-500 text-green-300 px-4 py-3 rounded-lg flex items-center">
            <svg class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
            </svg>
            {{ session('success') }}
        </div>
    @endif

    @if (session('error'))
        <div class="mb-6 bg-red-500/20 border border-red-500 text-red-300 px-4 py-3 rounded-lg flex items-center">
            <svg class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            {{ session('error') }}
        </div>
    @endif

    @if ($errors->any())
        <div class="mb-6 bg-red-500/20 border border-red-500 text-red-300 px-4 py-3 rounded-lg">
            <ul class="list-disc list-inside">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
    <div class="py-8 px-6 max-w-7xl mx-auto">
        <!-- Header personalizado -->
        <div class="mb-8">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold bg-gradient-to-r from-orange-400 to-red-500 bg-clip-text text-transparent">
                        ¡Hola, {{ Auth::user()->name }}! 👋
                    </h1>
                    <p class="text-gray-400 mt-1">Aquí tienes un resumen de tu actividad reciente</p>
                </div>
                <div class="hidden md:flex items-center space-x-4">
                    <div class="text-right">
                        <p class="text-sm text-gray-400">Hoy es</p>
                        <p class="font-medium text-white">{{ now()->locale('es')->translatedFormat('d M Y') }}</p>                    </div>
                </div>
            </div>
        </div>

        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
            <!-- Total Reuniones -->
            <div class="bg-gray-800/50 backdrop-blur-sm border border-gray-700/50 rounded-xl p-6 hover:border-orange-500/30 transition-all duration-300">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-400 text-sm">Total Reuniones</p>
                        <p class="text-2xl font-bold text-white">{{ auth()->user()->meetings()->count() }}</p>
                    </div>
                    <div class="bg-orange-500/20 p-3 rounded-lg">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-orange-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-4m-5 0H3m2 0h3M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                        </svg>
                    </div>
                </div>
                <div class="mt-4">
                    <span class="text-green-400 text-sm">
                        +{{ auth()->user()->meetings()->where('created_at', '>=', now()->subDays(7))->count() }} esta semana
                    </span>
                </div>
            </div>

            <!-- Tiempo Transcrito -->
            <div class="bg-gray-800/50 backdrop-blur-sm border border-gray-700/50 rounded-xl p-6 hover:border-orange-500/30 transition-all duration-300">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-400 text-sm">Horas Analizadas</p>
                        <p class="text-2xl font-bold text-white">
                            {{ number_format(auth()->user()->meetings()->count() * 0.75, 1) }}h
                        </p>
                    </div>
                    <div class="bg-purple-500/20 p-3 rounded-lg">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-purple-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                </div>
                <div class="mt-4">
                    <span class="text-purple-400 text-sm">Tiempo promedio: 45min</span>
                </div>
            </div>

            <!-- Insights Generados -->
            <div class="bg-gray-800/50 backdrop-blur-sm border border-gray-700/50 rounded-xl p-6 hover:border-orange-500/30 transition-all duration-300">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-400 text-sm">Insights Generados</p>
                        <p class="text-2xl font-bold text-white">{{ auth()->user()->meetings()->whereNotNull('resumen')->count() }}</p>
                    </div>
                    <div class="bg-green-500/20 p-3 rounded-lg">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-green-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z" />
                        </svg>
                    </div>
                </div>
                <div class="mt-4">
                    <span class="text-green-400 text-sm">98% precisión promedio</span>
                </div>
            </div>

            <!-- Tu Token API -->
            <div class="bg-gray-800/50 backdrop-blur-sm border border-gray-700/50 rounded-xl p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-white">Tu Token API</h3>
                    <div class="bg-blue-500/20 p-2 rounded-lg">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-blue-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="15 7a2 2 0 012 2m0 0a2 2 0 012 2m-2-2a2 2 0 00-2 2m2-2a2 2 0 002 2M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                </div>
                <p class="text-gray-300 text-sm mb-4">Para usar con la extensión oficial de Reunai</p>
                <div class="bg-gray-900/50 p-3 rounded-lg border border-gray-700 mb-4">
                    <code class="text-orange-400 text-sm break-all">
                        {{ auth()->user()->api_token ?? 'Token no generado' }}
                    </code>
                </div>
                <button onclick="copyToClipboard('{{ auth()->user()->api_token }}')" class="w-full bg-blue-600 hover:bg-blue-700 text-white py-2 px-4 rounded-lg text-sm transition-colors">
                    Copiar Token
                </button>
            </div>
        </div>
@if($stats['meetings_remaining'] <= 0)
    <div class="mb-6 bg-red-500/20 border border-red-500 text-red-300 px-4 py-3 rounded-lg flex items-start gap-2">
        <svg class="w-5 h-5 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4
                   c-.77-1.333-2.694-1.333-3.464 0L3.34 16
                   c-.77 1.333.192 3 1.732 3z"/>
        </svg>
        <div>
            <p class="font-semibold">Límite mensual alcanzado</p>
            <p class="text-sm">
                Has agotado las reuniones de tu plan.
                <a href="{{ route('subscription.manage') }}"
                   class="underline text-orange-300 ml-1">
                    Actualiza tu plan
                </a>
            </p>
        </div>
    </div>
@endif

        <!-- Zona de Drag & Drop - Destacada -->
        <div class="mb-8">
            <form id="uploadForm" action="{{ route('reuniones.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                
                <!-- Drag & Drop Zone -->
                <div id="dropZone" class="relative bg-gradient-to-br from-orange-500/10 to-red-500/10 border-2 border-dashed border-orange-500/30 rounded-xl p-8 transition-all duration-300 hover:border-orange-500/50 hover:bg-gradient-to-br hover:from-orange-500/15 hover:to-red-500/15 cursor-pointer group">
                    
                    <!-- Loading Overlay -->
                    <div id="uploadOverlay" class="absolute inset-0 bg-gray-900/90 backdrop-blur-sm rounded-xl hidden items-center justify-center z-10">
                        <div class="text-center">
                            <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-orange-500 mx-auto mb-4"></div>
                            <p class="text-white font-medium">Subiendo archivo...</p>
                            <p class="text-gray-400 text-sm mt-1">Esto puede tardar unos momentos</p>
                        </div>
                    </div>

                    <!-- Drag Over Overlay -->
                    <div id="dragOverlay" class="absolute inset-0 bg-orange-500/20 backdrop-blur-sm rounded-xl hidden items-center justify-center border-2 border-orange-500 border-dashed">
                        <div class="text-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 text-orange-400 mx-auto mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M9 12l3 3m0 0l3-3m0 0V9" />
                            </svg>
                            <p class="text-white text-xl font-semibold">¡Suelta aquí tu archivo!</p>
                            <p class="text-orange-200 text-sm mt-1">Archivo de audio o video</p>
                        </div>
                    </div>

                    <!-- Default Content -->
                    <div id="defaultContent" class="text-center">
                        <div class="bg-orange-500/20 p-4 rounded-full inline-block mb-6 group-hover:bg-orange-500/30 transition-colors">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 text-orange-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                            </svg>
                        </div>
                        
                        <h3 class="text-2xl font-bold text-white mb-2">Sube tu reunión</h3>
                        <p class="text-gray-300 mb-4">Arrastra tu archivo de audio o video aquí, o haz clic para seleccionar</p>
                        
                        <div class="flex flex-wrap justify-center gap-2 mb-6">
                            <span class="px-3 py-1 bg-gray-700/50 text-gray-300 text-xs rounded-full">MP3</span>
                            <span class="px-3 py-1 bg-gray-700/50 text-gray-300 text-xs rounded-full">WAV</span>
                            <span class="px-3 py-1 bg-gray-700/50 text-gray-300 text-xs rounded-full">MP4</span>
                            <span class="px-3 py-1 bg-gray-700/50 text-gray-300 text-xs rounded-full">MOV</span>
                            <span class="px-3 py-1 bg-gray-700/50 text-gray-300 text-xs rounded-full">WEBM</span>
                        </div>

                        <button type="button" onclick="document.getElementById('fileInput').click()" class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-orange-500 to-red-500 text-white font-medium rounded-lg hover:-translate-y-0.5 hover:shadow-lg transition-all duration-300">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                            </svg>
                            Seleccionar archivo
                        </button>
                    </div>

                    <!-- File Selected Content -->
                    <div id="fileSelectedContent" class="text-center hidden">
                        <div class="bg-green-500/20 p-4 rounded-full inline-block mb-4">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 text-green-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <h3 class="text-xl font-bold text-white mb-2">¡Archivo listo!</h3>
                        <p id="fileName" class="text-gray-300 mb-1"></p>
                        <p id="fileSize" class="text-gray-400 text-sm mb-6"></p>
                        
                        <!-- Campo de título integrado elegantemente -->
                        <div class="max-w-md mx-auto mb-6">
                            <div class="relative">
                                <input type="text" name="titulo" id="titulo" required
                                       placeholder="Ponle un nombre a esta reunión..." 
                                       class="w-full bg-gray-800/70 border-2 border-gray-600/50 rounded-xl py-4 px-6 text-white text-center text-lg placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-orange-500/50 focus:border-orange-500 transition-all duration-300 focus:bg-gray-800/90">
                                <div class="absolute inset-0 bg-gradient-to-r from-orange-500/5 to-red-500/5 rounded-xl pointer-events-none opacity-0 transition-opacity duration-300" id="titleGlow"></div>
                            </div>
                            <p class="text-gray-500 text-xs mt-2">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 inline mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                Ej: "Reunión equipo marketing", "Daily standup 24 Sep"
                            </p>
                        </div>

                     
                        
                        <div class="flex flex-col sm:flex-row gap-3 justify-center">
                            <button type="submit"
                                {{ $stats['meetings_remaining'] <= 0 ? 'disabled' : '' }}
                                class="inline-flex items-center px-8 py-4 rounded-xl font-semibold text-lg
                                    {{ $stats['meetings_remaining'] <= 0
                                        ? 'bg-gray-600 cursor-not-allowed text-gray-300'
                                        : 'bg-gradient-to-r from-orange-500 to-red-500 hover:-translate-y-1 hover:shadow-xl'
                                    }}">
                                Procesar con IA
                            </button>

                            <button type="button" onclick="resetUpload()" class="inline-flex items-center px-6 py-4 bg-gray-700/50 hover:bg-gray-700 text-gray-300 hover:text-white font-medium rounded-xl transition-all duration-300">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                                </svg>
                                Cambiar archivo
                            </button>
                        </div>
                    </div>

                    <!-- Hidden file input -->
<input type="file"
       id="fileInput"
       name="archivo"
       accept=".mp3,.wav,.m4a,.mp4,.mov,.avi,.webm"
       class="hidden"
       {{ $stats['meetings_remaining'] <= 0 ? 'disabled' : '' }}>
                </div>

                <!-- Opciones adicionales -->
                <!-- Las opciones adicionales ahora están integradas en fileSelectedContent -->
            </form>
        </div>

        <!-- Acciones Rápidas -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
            <div class="bg-gray-800/50 backdrop-blur-sm border border-gray-700/50 rounded-xl p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-xl font-semibold text-white">Acciones rápidas</h3>
                </div>
                <div class="">
                    <a href="{{ route('reuniones.index') }}" class="flex flex-col items-center p-4 bg-gray-700/30 hover:bg-gray-700/50 rounded-lg transition-colors group">
                        <div class="bg-orange-500/20 p-3 rounded-lg mb-2 group-hover:bg-orange-500/30 transition-colors">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-orange-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                            </svg>
                        </div>
                        <span class="text-white text-sm font-medium text-center">Ver todas las reuniones</span>
                    </a>
                    
                    <!-- <a href="{{ route('integrations.index') }}" class="flex flex-col items-center p-4 bg-gray-700/30 hover:bg-gray-700/50 rounded-lg transition-colors group">
                        <div class="bg-blue-500/20 p-3 rounded-lg mb-2 group-hover:bg-blue-500/30 transition-colors">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-blue-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                        </div>
                        <span class="text-white text-sm font-medium text-center">Configurar integraciones</span>
                    </a> -->
                </div>
            </div>

            <div class="bg-gray-800/50 backdrop-blur-sm border border-gray-700/50 rounded-xl p-6">
                <h3 class="text-xl font-semibold text-white mb-4">Consejos para mejores resultados</h3>
                <div class="space-y-3">
                    <div class="flex items-start space-x-3">
                        <div class="bg-green-500/20 p-1 rounded-full mt-0.5">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 text-green-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                        </div>
                        <p class="text-gray-300 text-sm">Audio claro y sin ruido de fondo</p>
                    </div>
                    <div class="flex items-start space-x-3">
                        <div class="bg-green-500/20 p-1 rounded-full mt-0.5">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 text-green-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                        </div>
                        <p class="text-gray-300 text-sm">Mencionar nombres de participantes</p>
                    </div>
                    <div class="flex items-start space-x-3">
                        <div class="bg-green-500/20 p-1 rounded-full mt-0.5">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 text-green-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                        </div>
                        <p class="text-gray-300 text-sm">Definir tareas y responsabilidades claramente</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Reuniones Recientes -->
        <div class="bg-gray-800/50 backdrop-blur-sm border border-gray-700/50 rounded-xl p-6">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-xl font-semibold text-white">Reuniones Recientes</h3>
                <a href="{{ route('reuniones.index') }}" class="text-orange-400 hover:text-orange-300 text-sm flex items-center">
                    Ver todas
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 ml-1" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10.293 5.293a1 1 0 011.414 0l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414-1.414L12.586 11H5a1 1 0 110-2h7.586l-2.293-2.293a1 1 0 010-1.414z" clip-rule="evenodd" />
                    </svg>
                </a>
            </div>

            <div class="space-y-4">
                @forelse(auth()->user()->meetings()->latest()->limit(4)->get() as $reunion)
                <div class="bg-gray-700/30 rounded-lg p-4 hover:bg-gray-700/50 transition-colors">
                    <div class="flex items-start justify-between">
                        <div class="flex-1 text-white">
                            <h4 class="font-medium text-white mb-1">{{ $reunion->titulo ?? 'Sin título' }}</h4>
                            <div class="flex items-center text-sm text-gray-400 space-x-4">
                                <span>{{ $reunion->created_at->format('j M') }}</span>
                                <span class="flex items-center">
                                    <span class="inline-block w-2 h-2 bg-{{ $reunion->resumen ? 'green' : 'orange' }}-400 rounded-full mr-1"></span>
                                    {{ $reunion->resumen ? 'Procesado' : 'Procesando...' }}
                                </span>
                            </div>
                            @if($reunion->resumen)
                            <p class="text-gray-300 text-sm mt-2 line-clamp-2">{!! Str::limit($reunion->resumen, 100) !!}</p>
                            @endif
                        </div>
                        <div class="ml-4 flex space-x-2">
                            <a href="{{ route('reuniones.show', $reunion) }}" class="p-2 text-gray-400 hover:text-white transition-colors">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                                    <path d="M10 12a2 2 0 100-4 2 2 0 000 4z" />
                                    <path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd" />
                                </svg>
                            </a>
                        </div>
                    </div>
                </div>
                @empty
                <div class="text-center py-8">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 mx-auto text-gray-600 mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                    </svg>
                    <p class="text-gray-400">No tienes reuniones todavía</p>
                    <p class="text-sm text-gray-500 mt-1">Sube tu primera reunión para empezar</p>
                </div>
                @endforelse
            </div>
        </div>
    </div>

    <script>
        const canUpload = @json($stats['meetings_remaining'] > 0);
        // Elementos del DOM
        const dropZone = document.getElementById('dropZone');
        const fileInput = document.getElementById('fileInput');
        const uploadForm = document.getElementById('uploadForm');
        const dragOverlay = document.getElementById('dragOverlay');
        const uploadOverlay = document.getElementById('uploadOverlay');
        const defaultContent = document.getElementById('defaultContent');
        const fileSelectedContent = document.getElementById('fileSelectedContent');
        const additionalOptions = document.getElementById('additionalOptions');
        const fileName = document.getElementById('fileName');
        const fileSize = document.getElementById('fileSize');

        // Formatos permitidos
        const allowedTypes = ['audio/mp3', 'audio/wav', 'audio/m4a', 'video/mp4', 'video/mov', 'video/avi', 'video/webm'];

        // Prevenir comportamiento por defecto del navegador
        ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
            dropZone.addEventListener(eventName, preventDefaults, false);
            document.body.addEventListener(eventName, preventDefaults, false);
        });

        function preventDefaults(e) {
            e.preventDefault();
            e.stopPropagation();
        }

        // Eventos de drag and drop
        dropZone.addEventListener('dragenter', handleDragEnter);
        dropZone.addEventListener('dragover', handleDragOver);
        dropZone.addEventListener('dragleave', handleDragLeave);
        dropZone.addEventListener('drop', handleDrop);

        // Evento de clic para abrir selector
        dropZone.addEventListener('click', () => {
            if (!fileInput.files.length) {
                fileInput.click();
            }
        });

        // Evento cuando se selecciona archivo
        fileInput.addEventListener('change', handleFileSelect);

        function handleDragEnter(e) {
            dropZone.classList.add('border-orange-500');
            dragOverlay.classList.remove('hidden');
            dragOverlay.classList.add('flex');
        }

        function handleDragOver(e) {
            e.dataTransfer.dropEffect = 'copy';
        }

        function handleDragLeave(e) {
            // Solo ocultar si realmente salimos del dropZone
            if (!dropZone.contains(e.relatedTarget)) {
                dropZone.classList.remove('border-orange-500');
                dragOverlay.classList.add('hidden');
                dragOverlay.classList.remove('flex');
            }
        }

        function handleDrop(e) {
            dropZone.classList.remove('border-orange-500');
            dragOverlay.classList.add('hidden');
            dragOverlay.classList.remove('flex');

            const files = e.dataTransfer.files;
            if (files.length > 0) {
                const file = files[0];
                if (isValidFile(file)) {
                    fileInput.files = files;
                    displaySelectedFile(file);
                } else {
                    showError('Tipo de archivo no válido. Solo se permiten archivos de audio y video.');
                }
            }
        }

        function handleFileSelect(e) {
            const file = e.target.files[0];
            if (file && isValidFile(file)) {
                displaySelectedFile(file);
            } else if (file) {
                showError('Tipo de archivo no válido. Solo se permiten archivos de audio y video.');
                resetUpload();
            }
        }

        function isValidFile(file) {
            // Verificar por tipo MIME y extensión
            const validMimeTypes = [
                'audio/mp3', 'audio/mpeg', 'audio/wav', 'audio/m4a',
                'video/mp4', 'video/mov', 'video/avi', 'video/webm'
            ];
            
            const validExtensions = ['mp3', 'wav', 'm4a', 'mp4', 'mov', 'avi', 'webm'];
            const fileExtension = file.name.split('.').pop().toLowerCase();
            
            return validMimeTypes.includes(file.type) || validExtensions.includes(fileExtension);
        }

        function displaySelectedFile(file) {
            defaultContent.classList.add('hidden');
            fileSelectedContent.classList.remove('hidden');
            
            fileName.textContent = file.name;
            fileSize.textContent = formatFileSize(file.size);
            
            // Cambiar el estilo del dropZone
            dropZone.classList.add('border-green-500/50', 'bg-green-500/5');
            dropZone.classList.remove('border-orange-500/30');
            
            // Enfocar el campo de título después de un breve delay
            setTimeout(() => {
                const tituloField = document.getElementById('titulo');
                tituloField.focus();
                
                // Añadir efecto de brillo al enfocar
                tituloField.addEventListener('focus', () => {
                    document.getElementById('titleGlow').style.opacity = '1';
                });
                
                tituloField.addEventListener('blur', () => {
                    document.getElementById('titleGlow').style.opacity = '0';
                });
            }, 300);
        }

        function formatFileSize(bytes) {
            if (bytes === 0) return '0 Bytes';
            const k = 1024;
            const sizes = ['Bytes', 'KB', 'MB', 'GB'];
            const i = Math.floor(Math.log(bytes) / Math.log(k));
            return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
        }

        function resetUpload() {
            fileInput.value = '';
            defaultContent.classList.remove('hidden');
            fileSelectedContent.classList.add('hidden');
            
            // Resetear estilos
            dropZone.classList.remove('border-green-500/50', 'bg-green-500/5');
            dropZone.classList.add('border-orange-500/30');
            
            // Limpiar campos
            document.getElementById('titulo').value = '';
            document.querySelector('input[name="enviar_google_sheets"]').checked = false;
        }

        function showError(message) {
            // Aquí puedes implementar tu sistema de notificaciones
            // Por ahora usamos un alert simple
            alert('Error: ' + message);
        }

        function showSuccess(message) {
            // Aquí puedes implementar tu sistema de notificaciones
            alert('Éxito: ' + message);
        }

        // Manejar envío del formulario
        uploadForm.addEventListener('submit', function(e) {
            if (!fileInput.files.length) {
                e.preventDefault();
                showError('Por favor selecciona un archivo primero.');
                return;
            }

            // Validar que el título no esté vacío
            const tituloField = document.getElementById('titulo');
            if (!tituloField.value.trim()) {
                e.preventDefault();
                
                // Destacar el campo vacío
                tituloField.classList.add('border-red-500', 'bg-red-500/10');
                tituloField.focus();
                
                // Shake animation
                tituloField.style.animation = 'shake 0.5s';
                setTimeout(() => {
                    tituloField.style.animation = '';
                }, 500);
                
                showError('Por favor, ponle un nombre a la reunión.');
                
                // Quitar estilos de error después de que el usuario empiece a escribir
                tituloField.addEventListener('input', function() {
                    this.classList.remove('border-red-500', 'bg-red-500/10');
                }, { once: true });
                
                return;
            }

            // Mostrar overlay de carga
            uploadOverlay.classList.remove('hidden');
            uploadOverlay.classList.add('flex');
            
            // Deshabilitar el botón de envío
            const submitButton = uploadForm.querySelector('button[type="submit"]');
            submitButton.disabled = true;
            submitButton.innerHTML = '<svg class="animate-spin h-6 w-6 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="m4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>Procesando con IA...';
        });

        // Función para copiar token
        function copyToClipboard(text) {
            navigator.clipboard.writeText(text).then(function() {
                showSuccess('Token copiado al portapapeles!');
            }, function(err) {
                showError('No se pudo copiar el token: ' + err);
            });
        }

        // Validación de tamaño de archivo
        fileInput.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                // Límite de 100MB
                const maxSize = 100 * 1024 * 1024; // 100MB en bytes
                if (file.size > maxSize) {
                    showError('El archivo es demasiado grande. El tamaño máximo permitido es 100MB.');
                    resetUpload();
                    return;
                }
            }
        });

        // Mostrar alertas de éxito/error desde Laravel
        @if (session('success'))
            showSuccess('{{ session('success') }}');
        @endif

        @if ($errors->any())
            showError('{{ implode(" ", $errors->all()) }}');
        @endif
    </script>

    <style>
        .line-clamp-2 {
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
        
        /* Animaciones adicionales */
        #dropZone {
            transition: all 0.3s ease;
        }
        
        #dropZone:hover {
            transform: translateY(-2px);
        }
        
        /* Loading spinner personalizado */
        @keyframes pulse-orange {
            0%, 100% {
                opacity: 1;
            }
            50% {
                opacity: .5;
            }
        }
        
        .animate-pulse-orange {
            animation: pulse-orange 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
        }
        
        /* Shake animation para campos de error */
        @keyframes shake {
            0%, 20%, 40%, 60%, 80% {
                transform: translateX(0);
            }
            10%, 30%, 50%, 70%, 90% {
                transform: translateX(-10px);
            }
        }
        
        /* Smooth transitions para el título */
        #titulo {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        
        #titulo:focus {
            transform: scale(1.02);
            box-shadow: 0 10px 25px rgba(249, 115, 22, 0.2);
        }
    </style>
@endsection