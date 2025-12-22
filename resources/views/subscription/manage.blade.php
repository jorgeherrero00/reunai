@extends('layouts.app')

@section('content')
    <div class="py-12">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8">
            <!-- Header -->
            <div class="mb-8">
                <h1 class="text-3xl font-bold text-white mb-2">Gestión de suscripción</h1>
                <p class="text-gray-400">Administra tu plan, facturación y pagos</p>
            </div>

            <div class="grid lg:grid-cols-3 gap-6">
                <!-- Columna principal -->
                <div class="lg:col-span-2 space-y-6">
                    <!-- Plan actual -->
                    <div class="bg-gray-800/50 backdrop-blur-sm border border-gray-700/50 rounded-xl p-6">
                        <div class="flex items-center justify-between mb-6">
                            <div>
                               <h2 class="text-2xl font-bold text-white mb-2">
                                Plan 
                                <span class="bg-gradient-to-r from-orange-400 to-red-500 text-transparent bg-clip-text">
                                    {{ $subscription ? ucfirst($subscription->plan) : ucfirst($user->plan) }}
                                </span>
                            </h2>

                            @if($subscription)
                                @if($subscription->plan === 'free')
                                    <p class="text-gray-400 text-sm">Plan gratuito - 1 reunión por mes</p>
                                @elseif($subscription->plan === 'starter')
                                    <p class="text-gray-400 text-sm">5 reuniones mensuales - 9€/mes</p>
                                @elseif($subscription->plan === 'pro')
                                    <p class="text-gray-400 text-sm">20 reuniones mensuales - 29€/mes</p>
                                @endif
                            @else
                                <p class="text-gray-400 text-sm">Plan gratuito - 1 reunión por mes</p>
                            @endif
                            </div>
                        </div>

                        <!-- Uso del plan -->
                        @php
                            $limits = ['free' => 1, 'starter' => 5, 'pro' => 20];
                            $monthlyUploads = $user->meetings()->whereMonth('created_at', now()->month)->count();
                            $limit = $limits[$user->plan] ?? 0;
                            $percentage = $limit > 0 ? ($monthlyUploads / $limit) * 100 : 0;
                        @endphp

                        <div class="bg-gray-900/50 rounded-lg p-4">
                            <div class="flex justify-between items-center mb-2">
                                <span class="text-sm text-gray-400">Reuniones este mes</span>
                                <span class="text-sm font-medium text-white">{{ $monthlyUploads }} / {{ $limit }}</span>
                            </div>
                            <div class="w-full bg-gray-700 rounded-full h-2">
                                <div class="bg-gradient-to-r from-orange-400 to-red-500 h-2 rounded-full transition-all duration-300" 
                                     style="width: {{ min($percentage, 100) }}%"></div>
                            </div>
                            @if($monthlyUploads >= $limit)
                                <p class="text-orange-400 text-xs mt-2 flex items-center">
                                    <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                    </svg>
                                    Has alcanzado el límite de tu plan
                                </p>
                            @endif
                        </div>
                    </div>

                    <!-- Cambiar plan -->
                    @if($user->plan !== 'free')
                        <div class="bg-gray-800/50 backdrop-blur-sm border border-gray-700/50 rounded-xl p-6">
                            <h3 class="text-xl font-bold text-white mb-4">Cambiar de plan</h3>
                            
                            <div class="grid md:grid-cols-2 gap-4">
                                <!-- Plan Starter -->
                                <div class="bg-gray-900/50 border {{ $subscription && $subscription->plan === 'starter' ? 'border-orange-500' : 'border-gray-700' }} rounded-lg p-4">
                                    <div class="flex items-center justify-between mb-3">
                                        <h4 class="font-bold text-white">Starter</h4>
                                        @if($subscription && $subscription->plan === 'starter')
                                            <span class="px-2 py-1 bg-orange-500/20 text-orange-400 text-xs rounded-full">Actual</span>
                                        @endif
                                    </div>
                                    <p class="text-2xl font-bold text-white mb-2">9€<span class="text-sm text-gray-400">/mes</span></p>
                                    <p class="text-gray-400 text-sm mb-4">5 reuniones/mes</p>
                                    
                                    @if($subscription && $subscription->plan === 'pro')
                                        <form method="POST" action="{{ route('subscription.change-plan') }}">
                                            @csrf
                                            <input type="hidden" name="plan" value="starter">
                                            <button type="submit" class="w-full py-2 px-4 bg-gray-700 hover:bg-gray-600 text-white rounded-lg transition">
                                                Cambiar a Starter
                                            </button>
                                        </form>
                                    @endif
                                </div>

                                <!-- Plan Pro -->
                                <div class="bg-gray-900/50 border {{ $subscription->plan === 'pro' ? 'border-orange-500' : 'border-gray-700' }} rounded-lg p-4">
                                    <div class="flex items-center justify-between mb-3">
                                        <h4 class="font-bold text-white">Pro</h4>
                                        @if($subscription && $subscription->plan === 'pro')
                                            <span class="px-2 py-1 bg-orange-500/20 text-orange-400 text-xs rounded-full">Actual</span>
                                        @endif
                                    </div>
                                    <p class="text-2xl font-bold text-white mb-2">29€<span class="text-sm text-gray-400">/mes</span></p>
                                    <p class="text-gray-400 text-sm mb-4">20 reuniones/mes</p>

                                    @if($subscription && $subscription->plan === 'starter')
                                        <form method="POST" action="{{ route('subscription.change-plan') }}">
                                            @csrf
                                            <input type="hidden" name="plan" value="pro">
                                            <button type="submit" class="w-full py-2 px-4 bg-gradient-to-r from-orange-500 to-red-500 hover:from-orange-600 hover:to-red-600 text-white rounded-lg transition">
                                                Actualizar a Pro
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endif

                    <!-- Facturas -->
                    @if(!empty($invoices))
                        <div class="bg-gray-800/50 backdrop-blur-sm border border-gray-700/50 rounded-xl p-6">
                            <h3 class="text-xl font-bold text-white mb-4">Historial de facturas</h3>
                            
                            <div class="space-y-3">
                                @foreach($invoices as $invoice)
                                    <div class="flex items-center justify-between p-4 bg-gray-900/50 rounded-lg hover:bg-gray-900/70 transition">
                                        <div class="flex items-center gap-4">
                                            <div class="w-10 h-10 rounded-lg bg-orange-500/20 flex items-center justify-center">
                                                <svg class="w-5 h-5 text-orange-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                                </svg>
                                            </div>
                                            <div>
                                                <p class="text-white font-medium">
                                                    {{ number_format($invoice->amount_paid / 100, 2) }}€
                                                </p>
                                                <p class="text-gray-400 text-sm">
                                                    {{ date('d/m/Y', $invoice->created) }}
                                                </p>
                                            </div>
                                        </div>
                                        
                                        <div class="flex items-center gap-3">
                                            <span class="px-3 py-1 rounded-full text-xs font-medium
                                                {{ $invoice->status === 'paid' ? 'bg-green-500/20 text-green-400' : 'bg-red-500/20 text-red-400' }}">
                                                {{ $invoice->status === 'paid' ? 'Pagada' : 'Pendiente' }}
                                            </span>
                                            
                                            @if($invoice->invoice_pdf)
                                                <a href="{{ route('subscription.invoice.download', $invoice->id) }}" 
                                                   target="_blank"
                                                   class="p-2 hover:bg-gray-700 rounded-lg transition">
                                                    <svg class="w-5 h-5 text-gray-400 hover:text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                                    </svg>
                                                </a>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>

                <!-- Sidebar -->
                <div class="space-y-6">
                    <!-- Detalles de suscripción -->
                    @if($user->plan !== 'free' && $subscription)
                        <div class="bg-gray-800/50 backdrop-blur-sm border border-gray-700/50 rounded-xl p-6">
                            <h3 class="text-lg font-bold text-white mb-4">Detalles</h3>
                            
                            <div class="space-y-3">
                                <div class="flex justify-between items-center pb-3 border-b border-gray-700/50">
                                    <span class="text-gray-400 text-sm">Estado</span>
                                    <span class="px-3 py-1 rounded-full text-xs font-medium
                                        {{ $subscription->status === 'active' ? 'bg-green-500/20 text-green-400' : 'bg-gray-500/20 text-gray-400' }}">
                                        {{ $subscription->status === 'active' ? 'Activa' : ucfirst($subscription->status) }}
                                    </span>
                                </div>
                                
                                <div class="flex justify-between items-center pb-3 border-b border-gray-700/50">
                                    <span class="text-gray-400 text-sm">Inicio</span>
                                    <span class="text-white text-sm">{{ $subscription->created_at->format('d/m/Y') }}</span>
                                </div>
                                
                                @if($subscription->ends_at)
                                    <div class="flex justify-between items-center">
                                        <span class="text-gray-400 text-sm">
                                            {{ $subscription->canceled_at ? 'Finaliza' : 'Renueva' }}
                                        </span>
                                        <span class="text-white text-sm">{{ $subscription->ends_at->format('d/m/Y') }}</span>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <!-- Acciones -->
                        <div class="bg-gray-800/50 backdrop-blur-sm border border-gray-700/50 rounded-xl p-6">
                            <h3 class="text-lg font-bold text-white mb-4">Acciones</h3>
                            
                            @if(!$subscription->canceled_at)
                                <form method="POST" action="{{ route('subscription.cancel') }}" 
                                      onsubmit="return confirm('¿Estás seguro? Podrás seguir usando tu plan hasta el final del período de facturación.');">
                                    @csrf
                                    <button type="submit" 
                                            class="w-full px-4 py-2 bg-red-500/20 hover:bg-red-500/30 text-red-400 rounded-lg font-medium transition flex items-center justify-center gap-2">
                                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                        </svg>
                                        Cancelar suscripción
                                    </button>
                                </form>
                            @else
                                <form method="POST" action="{{ route('subscription.resume') }}">
                                    @csrf
                                    <button type="submit" 
                                            class="w-full px-4 py-2 bg-green-500/20 hover:bg-green-500/30 text-green-400 rounded-lg font-medium transition flex items-center justify-center gap-2">
                                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        Reactivar suscripción
                                    </button>
                                </form>
                            @endif
                        </div>
                    @else
                        <!-- Usuario Free -->
                        <div class="bg-gradient-to-br from-orange-500/20 to-red-500/20 border border-orange-500/30 rounded-xl p-6">
                            <h3 class="text-lg font-bold text-white mb-2">Desbloquea más funciones</h3>
                            <p class="text-gray-300 text-sm mb-4">
                                Actualiza tu plan para procesar más reuniones y acceder a funciones avanzadas.
                            </p>
                            <a href="{{ url('/#planes') }}" 
                               class="block w-full py-2 px-4 bg-gradient-to-r from-orange-500 to-red-500 hover:from-orange-600 hover:to-red-600 text-white text-center rounded-lg font-medium transition">
                                Ver planes
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
    @endsection