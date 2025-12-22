@extends('layouts.app')

@section('content')
    <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6 text-center">
                    <div class="mb-6">
                        <svg class="mx-auto h-16 w-16 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </div>
                    <h2 class="text-2xl font-bold text-white mb-4">Pago cancelado</h2>
                    <p class="text-gray-400 mb-6">
                        Has cancelado el proceso de pago. Puedes volver a intentarlo cuando quieras.
                    </p>
                    <div class="flex gap-4 justify-center">
                        <a href="{{ route('dashboard') }}" class="inline-flex items-center px-6 py-3 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition">
                            Volver al Dashboard
                        </a>
                        <a href="{{ url('/#planes') }}" class="inline-flex items-center px-6 py-3 bg-orange-500 text-white rounded-lg hover:bg-orange-600 transition">
                            Ver planes
                        </a>
                    </div>
                </div>
            </div>
        </div>

@endsection