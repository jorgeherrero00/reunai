<div x-data="{ showUpgrade: false }" 
     @show-upgrade.window="showUpgrade = true"
     x-show="showUpgrade"
     x-cloak
     class="fixed inset-0 z-50 overflow-y-auto"
     style="display: none;">
    
    <!-- Backdrop -->
    <div class="fixed inset-0 bg-black/60 backdrop-blur-sm transition-opacity"
         @click="showUpgrade = false"></div>

    <!-- Modal -->
    <div class="flex min-h-screen items-center justify-center p-4">
        <div class="relative bg-gradient-to-br from-gray-900 to-gray-800 rounded-2xl shadow-2xl max-w-md w-full border border-gray-700/50"
             @click.away="showUpgrade = false"
             x-transition:enter="ease-out duration-300"
             x-transition:enter-start="opacity-0 scale-95"
             x-transition:enter-end="opacity-100 scale-100">
            
            <!-- Close button -->
            <button @click="showUpgrade = false" 
                    class="absolute top-4 right-4 text-gray-400 hover:text-white transition-colors">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>

            <div class="p-8">
                <!-- Icon -->
                <div class="flex justify-center mb-6">
                    <div class="bg-orange-500/20 p-4 rounded-full">
                        <svg class="w-12 h-12 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                        </svg>
                    </div>
                </div>

                <!-- Content -->
                <div class="text-center mb-8">
                    <h3 class="text-2xl font-bold text-white mb-3">
                        Límite alcanzado
                    </h3>
                    <p class="text-gray-300 mb-4" x-text="$store.upgrade.message"></p>
                    
                    <div class="bg-gray-800/50 rounded-lg p-4 mb-6">
                        <div class="flex justify-between items-center text-sm">
                            <span class="text-gray-400">Plan actual:</span>
                            <span class="text-white font-semibold uppercase" x-text="$store.upgrade.plan"></span>
                        </div>
                        <div class="flex justify-between items-center text-sm mt-2">
                            <span class="text-gray-400">Reuniones usadas:</span>
                            <span class="text-white">
                                <span x-text="$store.upgrade.current"></span> / 
                                <span x-text="$store.upgrade.limit"></span>
                            </span>
                        </div>
                        <div class="flex justify-between items-center text-sm mt-2">
                            <span class="text-gray-400">Próximo reinicio:</span>
                            <span class="text-white" x-text="$store.upgrade.nextReset"></span>
                        </div>
                    </div>
                </div>

                <!-- Actions -->
                <div class="space-y-3">
                    <a href="{{ route('subscription.manage') }}" 
                       class="block w-full py-3 px-6 text-center bg-gradient-to-r from-orange-500 to-red-500 text-white font-semibold rounded-lg transition-transform hover:-translate-y-0.5 hover:shadow-lg hover:shadow-orange-500/30">
                        Ver planes
                    </a>
                    
                    <button @click="showUpgrade = false" 
                            class="w-full py-3 px-6 text-center bg-gray-700/50 text-gray-300 font-medium rounded-lg hover:bg-gray-700 transition-colors">
                        Cerrar
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('alpine:init', () => {
    Alpine.store('upgrade', {
        message: '',
        plan: '',
        current: 0,
        limit: 0,
        nextReset: '',
        
        show(data) {
            this.message = data.message || '';
            this.plan = data.plan || 'free';
            this.current = data.current || 0;
            this.limit = data.limit || 1;
            this.nextReset = data.next_reset || '';
            window.dispatchEvent(new CustomEvent('show-upgrade'));
        }
    });
});
</script>