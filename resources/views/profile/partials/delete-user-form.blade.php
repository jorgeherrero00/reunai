<section class="space-y-8">
    <!-- Header con gradientes y efectos -->
    <header class="text-center">
        <div class="inline-flex items-center bg-gradient-to-r from-red-500/30 to-orange-500/30 px-4 py-2 rounded-full backdrop-blur-sm mb-4 border border-red-500/20 shadow-lg">
            <span class="animate-pulse mr-2 h-2 w-2 bg-red-500 rounded-full"></span>
            <span class="text-red-300 font-medium tracking-wider text-xs uppercase">Zona Peligrosa</span>
        </div>
        
        <h2 class="text-3xl font-bold mb-4 bg-gradient-to-r from-white via-gray-100 to-gray-300 text-transparent bg-clip-text">
            {{ __('Delete Account') }}
        </h2>

        <p class="text-gray-400 max-w-lg mx-auto leading-relaxed">
            {{ __('Once your account is deleted, all of its resources and data will be permanently deleted. Before deleting your account, please download any data or information that you wish to retain.') }}
        </p>
    </header>

    <!-- Botón de eliminar con efectos -->
    <div class="text-center">
        <button
            x-data=""
            x-on:click.prevent="$dispatch('open-modal', 'confirm-user-deletion')"
            class="group relative overflow-hidden px-8 py-4 rounded-xl bg-gradient-to-r from-red-600 to-red-500 text-white font-bold text-lg shadow-xl shadow-red-500/20 transform transition-all duration-300 hover:-translate-y-1 hover:shadow-red-500/30 hover:from-red-700 hover:to-red-600"
        >
            <span class="relative z-10 flex items-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" />
                </svg>
                {{ __('Delete Account') }}
            </span>
            <div class="absolute inset-0 bg-gradient-to-r from-red-700 to-red-600 transform scale-x-0 group-hover:scale-x-100 transition-transform origin-left duration-300"></div>
        </button>
    </div>

    <!-- Modal estilizado -->
    <x-modal name="confirm-user-deletion" :show="$errors->userDeletion->isNotEmpty()" focusable>
        <div class="bg-gray-800/95 backdrop-blur-xl border border-gray-700/50 rounded-2xl overflow-hidden">
            <form method="post" action="{{ route('profile.destroy') }}" class="p-8">
                @csrf
                @method('delete')

                <!-- Icono de advertencia -->
                <div class="text-center mb-6">
                    <div class="mx-auto w-16 h-16 bg-red-500/20 rounded-full flex items-center justify-center mb-4">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-red-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z" />
                        </svg>
                    </div>
                </div>

                <!-- Título del modal -->
                <h2 class="text-2xl font-bold text-center mb-4 text-white">
                    {{ __('Are you sure you want to delete your account?') }}
                </h2>

                <!-- Descripción -->
                <p class="text-center text-gray-400 mb-8 leading-relaxed">
                    {{ __('Once your account is deleted, all of its resources and data will be permanently deleted. Please enter your password to confirm you would like to permanently delete your account.') }}
                </p>

                <!-- Campo de contraseña -->
                <div class="mb-8">
                    <x-input-label for="password" value="{{ __('Password') }}" class="sr-only" />
                    <x-text-input
                        id="password"
                        name="password"
                        type="password"
                        class="w-full bg-gray-700/50 border border-gray-600 rounded-lg py-3 px-4 text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-red-500/50 focus:border-red-500 transition-all"
                        placeholder="{{ __('Password') }}"
                    />
                    <x-input-error :messages="$errors->userDeletion->get('password')" class="mt-2 text-red-400" />
                </div>

                <!-- Botones -->
                <div class="flex flex-col sm:flex-row gap-3 sm:justify-end">
                    <x-secondary-button 
                        x-on:click="$dispatch('close')"
                        class="px-6 py-3 bg-gray-700/50 hover:bg-gray-700 text-gray-300 hover:text-white font-medium rounded-lg transition-all duration-300 border border-gray-600 hover:border-gray-500"
                    >
                        {{ __('Cancel') }}
                    </x-secondary-button>

                    <x-danger-button class="px-6 py-3 bg-gradient-to-r from-red-600 to-red-500 hover:from-red-700 hover:to-red-600 text-white font-bold rounded-lg transition-all duration-300 shadow-lg shadow-red-500/20 hover:shadow-red-500/30">
                        {{ __('Delete Account') }}
                    </x-danger-button>
                </div>
            </form>
        </div>
    </x-modal>

    <!-- Información adicional -->
    <div class="mt-12 text-center">
        <div class="bg-gray-800/50 backdrop-blur-sm border border-gray-700/50 rounded-xl p-6">
            <h3 class="text-lg font-semibold mb-3 text-orange-400">¿Necesitas ayuda?</h3>
            <p class="text-gray-400 text-sm mb-4">
                Si tienes problemas con tu cuenta o necesitas asistencia antes de eliminarla, nuestro equipo de soporte está aquí para ayudarte.
            </p>
            <a href="#contact" class="inline-flex items-center text-orange-400 hover:text-orange-300 font-medium transition-colors">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z" />
                </svg>
                Contactar soporte
            </a>
        </div>
    </div>
</section>