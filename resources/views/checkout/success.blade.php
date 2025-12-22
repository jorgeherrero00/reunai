@extends('layouts.app')

@section('content')
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6 text-center">
                <div class="mb-6">
                    <svg class="mx-auto h-16 w-16 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <h2 class="text-2xl font-bold text-white mb-4">¡Pago exitoso!</h2>
                <p class="text-gray-400 mb-6">
                    Has activado el plan <span class="font-bold text-orange-500">{{ ucfirst($plan ?? 'premium') }}</span> de Reunai
                </p>
                <a href="{{ route('reuniones.index') }}" class="inline-flex items-center px-6 py-3 bg-orange-500 text-white rounded-lg hover:bg-orange-600 transition">
                    Subir mi primera reunión
                </a>
            </div>
        </div>
    </div>

@endsection