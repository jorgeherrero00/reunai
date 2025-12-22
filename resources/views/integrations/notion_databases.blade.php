@extends('layouts.app')

@section('content')
<div class="py-12 px-6 max-w-4xl mx-auto">
    <div class="mb-8 flex items-center gap-4">
        <a href="{{ route('integrations.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-700/50 hover:bg-gray-700 text-gray-300 hover:text-white rounded-lg transition-colors">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z" clip-rule="evenodd" />
            </svg>
            Volver
        </a>
        <div class="h-6 w-px bg-gray-600"></div>
        <h1 class="text-3xl font-bold bg-gradient-to-r from-blue-400 to-purple-500 bg-clip-text text-transparent">
            Selecciona tu base de datos de Notion
        </h1>
    </div>

    @if($databases->count() > 0)
        <form action="{{ route('integrations.notion.database.save') }}" method="POST">
            @csrf
            <div class="grid gap-4 mb-8">
                @foreach($databases as $database)
                <label class="block">
                    <input type="radio" name="database_id" value="{{ $database['id'] }}" 
                           class="sr-only peer"
                           @if($loop->first) checked @endif>
                    <div class="bg-gray-800/50 border border-gray-700/50 rounded-xl p-6 cursor-pointer hover:border-blue-500/50 peer-checked:border-blue-500 peer-checked:bg-blue-500/10 transition-all">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-4">
                                <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-purple-600 rounded-xl flex items-center justify-center">
                                    <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M4.459 4.208c.746.606 1.026.56 2.428.466l13.215-.793c.28 0 .047-.28-.046-.326L17.86 1.968c-.42-.326-.981-.7-2.055-.607L3.01 2.295c-.466.046-.56.28-.374.466l1.823 1.447zm1.775 2.055c.187.466.187.886.187 1.727v13.775c0 .746.373 1.027 1.213 1.027.933 0 1.353-.28 1.727-1.12l7.318-11.85c.14-.186.047-.373-.094-.373l-3.896.7c-.28.047-.327.234-.187.42l4.734 7.318c.047.047.047.094.047.187v-1.727l-4.734-7.318c-.094-.14-.14-.326-.047-.514l.886-1.96c.187-.466.047-.98-.42-1.12L4.696 3.628c-.42-.14-.933 0-1.12.42l-.374.793c-.14.326-.28.7-.28 1.213L3.01 6.24c0 .466.28.653.653.653h2.147c.326-.047.42-.28.42-.606zm6.638 2.568c.187-.14.467-.047.653.093l1.12.84c.187.187.187.33.187.514v.046l-1.214 1.68-1.493-1.4c-.094-.187-.094-.33-.047-.514l.794-1.26zm-3.57 11.664c-.14.187-.047.373.094.467l1.214.84c.187.14.373.094.513-.047l2.708-3.734V8.954l-1.214 1.826-3.316 4.56z"/>
                                    </svg>
                                </div>
                                <div>
                                    <h3 class="text-lg font-semibold text-white">{{ $database['title'] }}</h3>
                                    <p class="text-sm text-gray-400">{{ substr($database['id'], 0, 8) }}...{{ substr($database['id'], -4) }}</p>
                                    @if($database['last_edited'])
                                        <p class="text-xs text-gray-500 mt-1">
                                            Última edición: {{ \Carbon\Carbon::parse($database['last_edited'])->diffForHumans() }}
                                        </p>
                                    @endif
                                </div>
                            </div>
                            
                            <div class="flex items-center gap-2">
                                @if($database['url'])
                                <a href="{{ $database['url'] }}" target="_blank" 
                                   class="p-2 text-gray-400 hover:text-white transition-colors rounded-lg hover:bg-gray-700"
                                   title="Abrir en Notion"
                                   onclick="event.stopPropagation();">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path>
                                    </svg>
                                </a>
                                @endif
                                
                                <div class="w-6 h-6 rounded-full border-2 border-gray-600 peer-checked:border-blue-500 peer-checked:bg-blue-500 flex items-center justify-center transition-colors">
                                    <div class="w-2.5 h-2.5 bg-white rounded-full opacity-0 peer-checked:opacity-100 transition-opacity"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </label>
                @endforeach
            </div>

            <div class="flex gap-4">
                <a href="{{ route('integrations.index') }}" 
                   class="flex-1 py-3 px-6 bg-gray-600/20 hover:bg-gray-600/30 text-gray-300 rounded-lg transition-colors border border-gray-600/30 text-center">
                    Cancelar
                </a>
                
                <button type="submit" 
                        class="flex-1 py-3 px-6 bg-gradient-to-r from-blue-600 to-purple-600 hover:from-blue-700 hover:to-purple-700 text-white rounded-lg transition-colors font-medium">
                    Guardar selección
                </button>
            </div>
        </form>
    @else
        <div class="bg-gray-800/50 border border-gray-700/50 rounded-xl p-8 text-center">
            <svg class="w-16 h-16 text-gray-600 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
            </svg>
            
            <h3 class="text-xl font-semibold text-white mb-2">No se encontraron bases de datos</h3>
            <p class="text-gray-400 mb-6">
                Verifica que tu API key de Notion tenga permisos para acceder a databases, o crea una nueva base de datos.
            </p>
            
            <div class="flex gap-4 justify-center">
                <a href="https://notion.so" target="_blank" 
                   class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path>
                    </svg>
                    Crear database en Notion
                </a>
                
                <a href="{{ route('integrations.index') }}" 
                   class="px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white rounded-lg transition-colors">
                    Volver
                </a>
            </div>
        </div>
    @endif
</div>
@endsection