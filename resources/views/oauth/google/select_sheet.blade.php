@extends('layouts.app')
@section('content')
<div class="relative z-10 max-w-4xl mx-auto">
    <!-- Header section -->
    <div class="mb-8 text-center">
        <div class="inline-flex items-center bg-gradient-to-r from-blue-500/30 to-green-500/30 px-4 py-2 rounded-full backdrop-blur-sm mb-4 border border-blue-500/20 shadow-lg">
            <span class="animate-pulse mr-2 h-2 w-2 bg-blue-500 rounded-full"></span>
            <span class="text-blue-300 font-medium tracking-wider text-sm">GOOGLE SHEETS</span>
        </div>
        <h2 class="text-3xl font-bold mb-2 bg-gradient-to-r from-white via-gray-100 to-gray-300 text-transparent bg-clip-text">Selecciona tu <span class="bg-gradient-to-r from-blue-400 to-green-500 text-transparent bg-clip-text">Google Sheet</span></h2>
        <p class="text-gray-400 text-sm max-w-lg mx-auto">Elige la hoja de cálculo donde quieres que se guarden automáticamente tus resúmenes de reuniones</p>
    </div>

    @if(empty($files))
        <div class="bg-yellow-500/10 border border-yellow-500/20 rounded-xl p-6 mb-8">
            <div class="flex items-center gap-3 mb-3">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-yellow-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z" />
                </svg>
                <h3 class="text-yellow-400 font-semibold">No se encontraron Google Sheets</h3>
            </div>
            <p class="text-gray-300 mb-4">No tienes hojas de cálculo de Google en tu cuenta o no se pueden acceder.</p>
            <div class="flex gap-3">
                <a href="https://docs.google.com/spreadsheets/create" target="_blank" 
                   class="inline-flex items-center px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg transition-colors">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    Crear nuevo Google Sheet
                </a>
                <button onclick="window.location.reload()" 
                        class="inline-flex items-center px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white rounded-lg transition-colors">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                    </svg>
                    Actualizar lista
                </button>
            </div>
        </div>
    @else
        <div class="bg-gray-800/50 backdrop-blur-sm border border-gray-700/50 rounded-xl p-6 mb-8">
            <form action="{{ route('oauth.google.sheets.save') }}" method="POST" class="space-y-6">
                @csrf
                
                <div>
                    <label for="spreadsheet_id" class="block text-white font-medium mb-3">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 inline mr-2 text-green-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        Selecciona tu hoja de cálculo:
                    </label>
                    
                    <div class="space-y-3 max-h-96 overflow-y-auto">
                        @foreach($files as $file)
                            <label class="flex items-center p-4 bg-gray-700/50 hover:bg-gray-700/70 rounded-lg border border-gray-600/50 hover:border-green-500/30 transition-all duration-300 cursor-pointer group">
                                <input type="radio" name="spreadsheet_id" value="{{ $file['id'] }}" 
                                       class="w-4 h-4 text-green-500 border-gray-600 focus:ring-green-500 focus:ring-2 mr-4">
                                
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center gap-2 mb-1">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-green-400 flex-shrink-0" fill="currentColor" viewBox="0 0 24 24">
                                            <path d="M19.993 8.448c-.006-.12-.012-.24-.018-.358H8v5.01h7.34c-.305 1.59-1.533 2.962-3.296 3.638v3.033h5.35c3.12-2.867 4.917-7.08 4.917-12.03 0-1.131-.099-2.242-.29-3.323z" fill="#4285F4"/>
                                            <path d="M8 21c4.468 0 8.223-1.456 10.957-3.933l-5.35-3.027c-1.48.995-3.38 1.585-5.607 1.585-4.295 0-7.934-2.898-9.235-6.8H-6.58v3.127C-3.825 17.735 1.816 21 8 21z" fill="#34A853"/>
                                        </svg>
                                        <h3 class="text-white font-medium truncate group-hover:text-green-300 transition-colors">
                                            {{ $file['name'] }}
                                        </h3>
                                    </div>
                                    <p class="text-xs text-gray-400 font-mono truncate">ID: {{ $file['id'] }}</p>
                                    @if(isset($file['webViewLink']))
                                        <a href="{{ $file['webViewLink'] }}" target="_blank" 
                                           class="text-xs text-blue-400 hover:text-blue-300 flex items-center gap-1 mt-1">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                                            </svg>
                                            Ver en Google Sheets
                                        </a>
                                    @endif
                                </div>
                            </label>
                        @endforeach
                    </div>
                </div>

                <div class="flex justify-between items-center pt-4 border-t border-gray-700/50">
                    <a href="{{ route('integrations.index') }}" 
                       class="inline-flex items-center px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white rounded-lg transition-colors">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                        </svg>
                        Volver a integraciones
                    </a>
                    
                    <button type="submit" 
                            class="group relative overflow-hidden px-6 py-3 rounded-lg bg-gradient-to-r from-green-500 to-blue-500 text-white font-bold shadow-lg shadow-green-500/20 transform transition-all duration-300 hover:-translate-y-1 hover:shadow-green-500/30">
                        <span class="relative z-10 flex items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                            Guardar selección
                        </span>
                        <div class="absolute inset-0 bg-gradient-to-r from-green-600 to-blue-600 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                    </button>
                </div>
            </form>
        </div>
    @endif

    <!-- Info section -->
    <div class="bg-blue-500/10 border border-blue-500/20 rounded-xl p-6">
        <div class="flex items-start gap-3">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-blue-400 flex-shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <div>
                <h3 class="text-blue-400 font-semibold mb-2">¿Cómo funciona?</h3>
                <ul class="text-gray-300 text-sm space-y-1">
                    <li>• Cada vez que proceses una reunión, se añadirá automáticamente una fila a tu Google Sheet</li>
                    <li>• Se incluirá: fecha, título, resumen y lista de tareas</li>
                    <li>• Las cabeceras se crearán automáticamente la primera vez</li>
                    <li>• Puedes cambiar la hoja seleccionada en cualquier momento</li>
                </ul>
            </div>
        </div>
    </div>
</div>

<!-- Background decorative elements -->
<div class="fixed inset-0 -z-10 overflow-hidden">
    <div class="absolute top-1/4 left-1/5 w-96 h-96 bg-blue-500/10 rounded-full filter blur-[80px] animate-pulse"></div>
    <div class="absolute bottom-1/3 right-1/4 w-80 h-80 bg-green-600/10 rounded-full filter blur-[100px] animate-pulse" style="animation-delay: 1s;"></div>
</div>
@endsection