{{-- resources/views/reuniones/index.blade.php --}}
@extends('layouts.app')

@section('content')
<div class="py-12 px-6 max-w-6xl mx-auto">
    <!-- Header section -->
    <div class="mb-10 text-center">
        <h1 class="text-3xl md:text-4xl font-bold bg-gradient-to-r from-orange-400 to-red-500 bg-clip-text text-transparent mb-4">
            {{ __('My Meetings') }}
        </h1>
        <p class="text-gray-400 max-w-2xl mx-auto">
            {{ __('Manage your conversations and get intelligent summaries instantly') }}
        </p>
    </div>

    {{-- ====== CARD DE STATS DE USO ====== --}}
    <div class="mb-8 bg-gradient-to-br from-gray-800/70 to-gray-900/70 backdrop-blur-sm border border-gray-700/50 rounded-2xl p-6 shadow-xl">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-6">
            <!-- Plan info -->
            <div class="flex-1">
                <div class="flex items-center gap-3 mb-4">
                    <div class="bg-gradient-to-br from-orange-500/20 to-red-500/20 p-3 rounded-xl">
                        <svg class="w-6 h-6 text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z" />
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-sm text-gray-400 uppercase tracking-wide">{{ __('Current plan') }}</h3>
                        <p class="text-2xl font-bold text-white capitalize flex items-center gap-2">

                            @if($stats['plan'] === 'free')
                                {{ __('Free') }}
                                <span class="text-xs bg-gray-700 text-gray-300 px-2 py-1 rounded-full">{{ __('Free') }}</span>
                            @elseif($stats['plan'] === 'starter')
                                Starter
                                <span class="text-xs bg-orange-500/20 text-orange-300 px-2 py-1 rounded-full">€9/mes</span>
                            @else
                                Pro
                                <span class="text-xs bg-gradient-to-r from-orange-500 to-red-500 text-white px-2 py-1 rounded-full">€29/mes</span>
                            @endif
                        </p>
                    </div>
                </div>
                
                <!-- Progress bar -->
                <div class="space-y-3">
                    <div class="flex justify-between items-center text-sm">
                        <span class="text-gray-400">{{ __('Meetings this month') }}</span>
                        <span class="text-white font-semibold">
                            {{ $stats['meetings_used'] }} / {{ $stats['meetings_limit'] }}
                        </span>
                    </div>
                    
                    <div class="relative w-full bg-gray-700/50 rounded-full h-3 overflow-hidden">
                        <div class="absolute inset-0 bg-gradient-to-r from-orange-500/20 to-red-500/20"></div>
                        <div class="relative bg-gradient-to-r from-orange-500 to-red-500 h-3 rounded-full transition-all duration-700 ease-out shadow-lg shadow-orange-500/50"
                             style="width: {{ min($stats['usage_percentage'], 100) }}%">
                            <div class="absolute inset-0 bg-white/20 animate-pulse"></div>
                        </div>
                    </div>
                    
                    @if($stats['meetings_remaining'] > 0)
                        <div class="flex items-center justify-between text-xs">
                            <span class="text-gray-500">
                                {{ __('remaining') }} <span class="text-orange-400 font-bold">{{ $stats['meetings_remaining'] }}</span>
                                {{ $stats['meetings_remaining'] == 1 ? __('meeting') : __('meetings') }}
                            </span>
                            <span class="text-gray-500">
                                {{ __('Reset') }}: {{ $stats['next_reset'] }}
                            </span>
                        </div>
                    @else
                        <div class="bg-red-500/10 border border-red-500/30 rounded-lg p-3 flex items-start gap-2">
                            <svg class="w-5 h-5 text-red-400 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                            </svg>
                            <div>
                                <p class="text-sm text-red-300 font-medium">{{ __('Limit reached') }}</p>
                                <p class="text-xs text-white mt-1">
                                    {{ __('Next reset') }}: {{ $stats['next_reset'] }}
                                </p>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
            
            <!-- Features + CTA -->
            <div class="md:w-80 space-y-4">
                <!-- Features del plan -->
                <div class="bg-gray-900/50 rounded-xl p-4 space-y-2">
                    <p class="text-xs text-gray-400 uppercase tracking-wide mb-3">{{ __('Features') }}</p>
                    <div class="space-y-2 text-sm">
                        <div class="flex items-center gap-2">
                            @if($stats['features']['sentiment_analysis'])
                                <svg class="w-4 h-4 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                                <span class="text-gray-300">{{ __('Sentiment analysis') }}</span>
                            @else
                                <svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                                <span class="text-gray-600 line-through">{{ __('Sentiment analysis') }}</span>
                            @endif
                        </div>

                        <div class="flex items-center gap-2">
                            @if($stats['features']['integrations'])
                                <svg class="w-4 h-4 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                                <span class="text-gray-300">{{ __('Integrations (Slack, etc.)') }}</span>
                            @else
                                <svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                                <span class="text-gray-600 line-through">{{ __('Integrations (Slack, etc.)') }}</span>
                            @endif
                        </div>

                        <div class="flex items-center gap-2 text-xs text-gray-500">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            {{ __('Max') }} {{ $stats['max_duration'] }} {{ __('min per meeting') }}
                        </div>
                    </div>
                </div>
                
                <!-- CTA Button -->
                @if($stats['plan'] !== 'pro')
                    <a href="{{ route('subscription.manage') }}"
                       class="block w-full py-3 px-6 text-center bg-gradient-to-r from-orange-500 to-red-500 text-white font-semibold rounded-xl shadow-lg shadow-orange-500/30 hover:shadow-orange-500/50 hover:-translate-y-0.5 transition-all duration-300">
                        @if($stats['plan'] === 'free')
                            {{ __('Upgrade to Starter') }}
                        @else
                            {{ __('Upgrade to Pro') }}
                        @endif
                    </a>
                @else
                    <a href="{{ route('subscription.manage') }}"
                       class="block w-full py-3 px-6 text-center bg-gray-700/50 text-gray-300 font-medium rounded-xl hover:bg-gray-700 transition-colors">
                        {{ __('Manage subscription') }}
                    </a>
                @endif
            </div>
        </div>
    </div>

    {{-- ====== ALERTS ====== --}}
    @if (session('success'))
        <div class="mb-6 bg-green-500/20 border border-green-500 text-green-300 px-4 py-3 rounded-lg flex items-center animate-fade-in">
            <svg class="h-5 w-5 mr-2 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
            </svg>
            {{ session('success') }}
        </div>
    @endif

    @if (session('error'))
        <div class="mb-6 bg-red-500/20 border border-red-500 text-red-300 px-4 py-3 rounded-lg flex items-center animate-fade-in">
            <svg class="h-5 w-5 mr-2 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            {{ session('error') }}
        </div>
    @endif

    @if ($errors->any())
        <div class="mb-6 bg-red-500/20 border border-red-500 text-red-300 px-4 py-3 rounded-lg animate-fade-in">
            <div class="flex items-start gap-2">
                <svg class="h-5 w-5 flex-shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <div class="flex-1">
                    @foreach ($errors->all() as $error)
                        <p>{{ $error }}</p>
                    @endforeach
                    @if(session('upgrade_needed'))
                        <button onclick="showUpgradeModal()" 
                                class="mt-2 text-sm font-semibold text-orange-300 hover:text-orange-200 underline">
                            Ver planes disponibles →
                        </button>
                    @endif
                </div>
            </div>
        </div>
    @endif

    {{-- ====== UPLOAD FORM ====== --}}
    <div class="mb-12 bg-gray-800/50 backdrop-blur-sm border border-gray-700/50 rounded-xl p-6 md:p-8 shadow-lg hover:border-orange-500/30 transition-all duration-300">
        <h2 class="text-xl font-semibold mb-6 flex items-center text-white">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 text-orange-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
            </svg>
            {{ __('Upload new meeting') }}
            @if($stats['meetings_remaining'] <= 0)
                <span class="ml-auto text-xs bg-red-500/20 text-red-300 px-3 py-1 rounded-full">
                    {{ __('Limit reached') }}
                </span>
            @endif
        </h2>
        
        <form id="uploadForm" 
              action="{{ route('reuniones.store') }}" 
              method="POST" 
              enctype="multipart/form-data" 
              class="space-y-6">
            @csrf
            
            <div class="grid md:grid-cols-2 gap-6">
                <div>
                    <label for="titulo" class="block text-sm text-gray-400 mb-2">{{ __('Title (optional)') }}</label>
                    <input type="text"
                           name="titulo"
                           id="titulo"
                           placeholder="{{ __('E.g.: Marketing team meeting') }}"
                           class="w-full bg-gray-700/50 border border-gray-600 rounded-lg py-3 px-4 text-white focus:outline-none focus:ring-2 focus:ring-orange-500/50 focus:border-orange-500 transition-all"
                           {{ $stats['meetings_remaining'] <= 0 ? 'disabled' : '' }}>
                </div>

                <div>
                    <label for="archivo" class="block text-sm text-gray-400 mb-2">{{ __('Audio/video file') }}</label>
                    <input type="file"
                           name="archivo"
                           id="archivo"
                           required
                           accept=".mp4,.mov,.webm,.mp3,.wav,.ogg,.m4a"
                           class="w-full bg-gray-700/50 border border-gray-600 rounded-lg py-3 px-4 text-white focus:outline-none focus:ring-2 focus:ring-orange-500/50 focus:border-orange-500 transition-all file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:bg-orange-500 file:text-white hover:file:bg-orange-600 disabled:opacity-50 disabled:cursor-not-allowed"
                           {{ $stats['meetings_remaining'] <= 0 ? 'disabled' : '' }}>
                    <p class="mt-2 text-xs text-gray-500">
                        {{ __('Maximum') }} {{ $stats['max_duration'] }} {{ __('min per meeting') }} • {{ __('Up to 100MB') }}
                    </p>
                </div>
            </div>
            
            <!-- Progress bar (hidden by default) -->
            <div id="progressContainer" class="hidden space-y-4">
                <div class="bg-gray-700/30 rounded-lg p-4 border border-gray-600/50">
                    <div class="flex justify-between text-sm mb-2">
                        <span class="text-gray-300 font-medium">{{ __('Uploading file...') }}</span>
                        <span id="progressText" class="text-orange-400 font-bold">0%</span>
                    </div>
                    <div class="w-full bg-gray-700 rounded-full h-3 mb-3 overflow-hidden">
                        <div id="progressBar"
                             class="bg-gradient-to-r from-orange-500 to-red-500 h-3 rounded-full transition-all duration-300 shadow-lg"
                             style="width: 0%"></div>
                    </div>
                    <div class="flex justify-between text-xs text-gray-400">
                        <span id="sizeText">0 MB de 0 MB</span>
                        <span id="speedText">0 MB/s</span>
                    </div>
                    <p id="statusText" class="text-sm text-gray-300 mt-2 flex items-center">
                        <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-orange-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        {{ __('Preparing upload...') }}
                    </p>
                </div>
            </div>
            
            <div class="flex justify-end">
                <button type="submit" 
                        id="submitBtn" 
                        class="group relative overflow-hidden px-6 py-3 rounded-lg bg-gradient-to-r from-orange-500 to-red-500 text-white font-medium shadow-md transition-all duration-300 hover:shadow-lg hover:shadow-orange-500/30 hover:-translate-y-0.5 disabled:opacity-50 disabled:cursor-not-allowed disabled:transform-none"
                        {{ $stats['meetings_remaining'] <= 0 ? 'disabled' : '' }}>
                    <span id="buttonText" class="relative z-10 flex items-center">
                        @if($stats['meetings_remaining'] <= 0)
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                            </svg>
                            {{ __('Limit reached') }}
                        @else
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M3 17a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zM6.293 6.707a1 1 0 010-1.414l3-3a1 1 0 011.414 0l3 3a1 1 0 01-1.414 1.414L11 5.414V13a1 1 0 11-2 0V5.414L7.707 6.707a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                            </svg>
                            {{ __('Upload meeting') }}
                        @endif
                    </span>
                    <span class="absolute inset-0 bg-gradient-to-r from-orange-600 to-red-600 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></span>
                </button>
            </div>
        </form>
    </div>

    {{-- ====== REUNIONS LIST ====== --}}
    <div>
        <h2 class="text-2xl font-semibold mb-6 flex items-center text-white">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 mr-2 text-orange-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
            </svg>
            {{ __('Meeting history') }}
        </h2>

        @if(count($reuniones) > 0)
            <div class="grid gap-6">
                @foreach($reuniones as $reunion)
                <div class="bg-gray-800/40 backdrop-blur-sm border border-gray-700/50 rounded-xl p-6 hover:border-orange-500/30 transition-all duration-300 transform hover:-translate-y-1">
                    <div class="flex flex-col md:flex-row md:justify-between md:items-center gap-4">
                        <div class="flex-1">
                            <h3 class="text-xl font-medium text-white">
                                {{ $reunion->titulo ?? __('Untitled') }}
                            </h3>
                            <div class="mt-2 flex flex-wrap gap-3">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-orange-500/20 text-orange-300">
                                    {{ ucfirst($reunion->formato_origen) }}
                                </span>
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-700 text-gray-300">
                                    {{ \Carbon\Carbon::parse($reunion->created_at)->diffForHumans() }}
                                </span>
                                @if($reunion->tasks && $reunion->tasks->count() > 0)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-500/20 text-blue-300">
                                        {{ $reunion->tasks->count() }} {{ $reunion->tasks->count() == 1 ? __('task') : __('tasks') }}
                                    </span>
                                @endif
                            </div>
                        </div>
                        <div class="flex gap-2">
                            <a href="{{ route('reuniones.show', $reunion) }}" 
                               class="p-2 rounded-lg bg-gray-700/50 hover:bg-gray-700 text-gray-300 hover:text-white transition-colors" 
                               title="Ver detalles">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                    <path d="M10 12a2 2 0 100-4 2 2 0 000 4z" />
                                    <path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd" />
                                </svg>
                            </a>
                        </div>
                    </div>
                    
                    @if($reunion->resumen)
                    <div class="mt-4 pt-4 border-t border-gray-700/50 text-white">
                        <h4 class="text-sm font-medium text-gray-400 mb-2">{{ __('Summary:') }}</h4>
                        <p class="text-white text-sm line-clamp-3">
                            {!! Str::limit($reunion->resumen, 200) !!}

                        </p>
                    </div>
                    @endif
                </div>
                @endforeach
            </div>
        @else
            <div class="text-center py-16 bg-gray-800/20 rounded-xl border border-gray-700/50">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 mx-auto text-gray-600 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                </svg>
                <p class="text-gray-400">{{ __('You don\'t have any meetings yet') }}</p>
                <p class="text-sm text-gray-500 mt-2">{{ __('Upload your first meeting to get an intelligent summary') }}</p>
            </div>
        @endif
    </div>
</div>

{{-- ====== UPGRADE MODAL ====== --}}

{{-- ====== JAVASCRIPT ====== --}}
<script>
// Función para mostrar modal de upgrade
function showUpgradeModal() {
    const stats = @json($stats);
    Alpine.store('upgrade').show({
        message: `Has alcanzado el límite de ${stats.meetings_limit} reuniones/mes del plan ${stats.plan}.`,
        plan: stats.plan,
        current: stats.meetings_used,
        limit: stats.meetings_limit,
        next_reset: stats.next_reset
    });
}

// Form upload con progress bar
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('uploadForm');
    const progressContainer = document.getElementById('progressContainer');
    const progressBar = document.getElementById('progressBar');
    const progressText = document.getElementById('progressText');
    const sizeText = document.getElementById('sizeText');
    const speedText = document.getElementById('speedText');
    const statusText = document.getElementById('statusText');
    const submitBtn = document.getElementById('submitBtn');
    const buttonText = document.getElementById('buttonText');
    
    let startTime, lastLoaded = 0, lastTime;

    form.addEventListener('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        const fileInput = document.getElementById('archivo');
        
        if (!fileInput.files[0]) {
            Swal.fire({
                icon: 'warning',
                title: '{{ __('File required') }}',
                text: '{{ __('Please select a file') }}',
                confirmButtonColor: '#f97316'
            });
            return;
        }

        // Mostrar progress
        progressContainer.classList.remove('hidden');
        submitBtn.disabled = true;
        buttonText.innerHTML = `
            <svg class="animate-spin -ml-1 mr-2 h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            {{ __('Uploading...') }}
        `;
        
        startTime = Date.now();
        lastTime = startTime;
        
        const xhr = new XMLHttpRequest();
        
        xhr.upload.addEventListener('progress', function(e) {
            if (e.lengthComputable) {
                const currentTime = Date.now();
                const percentComplete = Math.round((e.loaded / e.total) * 100);
                
                progressBar.style.width = percentComplete + '%';
                progressText.textContent = percentComplete + '%';
                
                const loaded = (e.loaded / 1024 / 1024).toFixed(1);
                const total = (e.total / 1024 / 1024).toFixed(1);
                sizeText.textContent = `${loaded} MB de ${total} MB`;
                
                if (currentTime > lastTime + 500) {
                    const timeDiff = (currentTime - lastTime) / 1000;
                    const loadedDiff = e.loaded - lastLoaded;
                    const speed = (loadedDiff / timeDiff / 1024 / 1024).toFixed(1);
                    speedText.textContent = `${speed} MB/s`;
                    lastLoaded = e.loaded;
                    lastTime = currentTime;
                }
                
                if (percentComplete < 100) {
                    statusText.innerHTML = `
                        <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-orange-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        {{ __('Uploading file...') }}
                    `;
                } else {
                    statusText.innerHTML = `
                        <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-blue-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        {{ __('Processing... This may take a few minutes.') }}
                    `;
                    progressBar.classList.add('animate-pulse');
                }
            }
        });
        
        xhr.addEventListener('load', function() {
            if (xhr.status === 200) {
                try {
                    const response = JSON.parse(xhr.responseText);
                    
                    if (response.upgrade_needed) {
                        // Mostrar modal de upgrade
                        showUpgradeModal();
                        resetForm();
                    } else {
                        statusText.innerHTML = `
                            <svg class="mr-2 h-4 w-4 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                            {{ __('File uploaded. Processing...') }}
                        `;
                        setTimeout(() => window.location.href = '/reuniones', 2000);
                    }
                } catch(e) {
                    // Respuesta HTML (success redirect)
                    statusText.innerHTML = `{{ __('Success! Redirecting...') }}`;
                    setTimeout(() => window.location.href = '/reuniones', 1500);
                }
            } else {
                handleError('{{ __('Error uploading. Code:') }} ' + xhr.status);
            }
        });

        xhr.addEventListener('error', () => handleError('{{ __('Connection error') }}'));
        xhr.addEventListener('timeout', () => handleError('{{ __('Timeout') }}'));
        
        xhr.open('POST', '{{ route("reuniones.store") }}');
        xhr.setRequestHeader('X-CSRF-TOKEN', '{{ csrf_token() }}');
        xhr.timeout = 300000;
        xhr.send(formData);
    });
    
    function resetForm() {
        progressContainer.classList.add('hidden');
        submitBtn.disabled = false;
        buttonText.innerHTML = `
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M3 17a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zM6.293 6.707a1 1 0 010-1.414l3-3a1 1 0 011.414 0l3 3a1 1 0 01-1.414 1.414L11 5.414V13a1 1 0 11-2 0V5.414L7.707 6.707a1 1 0 01-1.414 0z" clip-rule="evenodd" />
            </svg>
            {{ __('Upload meeting') }}
        `;
    }
    
    function handleError(message) {
        statusText.innerHTML = `❌ ${message}`;
        resetForm();
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: message,
            confirmButtonColor: '#f43f5e'
        });
    }
});
</script>

<style>
@keyframes fade-in {
    from { opacity: 0; transform: translateY(-10px); }
    to { opacity: 1; transform: translateY(0); }
}
.animate-fade-in {
    animation: fade-in 0.3s ease-out;
}
</style>
@endsection
