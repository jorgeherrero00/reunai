<section class="space-y-8">
    <!-- Header con gradientes y efectos -->
    <header class="text-center">
        <div class="inline-flex items-center bg-gradient-to-r from-orange-500/30 to-blue-500/30 px-4 py-2 rounded-full backdrop-blur-sm mb-4 border border-orange-500/20 shadow-lg">
            <span class="animate-pulse mr-2 h-2 w-2 bg-orange-500 rounded-full"></span>
            <span class="text-orange-300 font-medium tracking-wider text-xs uppercase">Seguridad</span>
        </div>
        
        <h2 class="text-3xl font-bold mb-4 bg-gradient-to-r from-white via-gray-100 to-gray-300 text-transparent bg-clip-text">
            {{ __('Update Password') }}
        </h2>

        <p class="text-gray-400 max-w-lg mx-auto leading-relaxed">
            {{ __('Ensure your account is using a long, random password to stay secure.') }}
        </p>
    </header>

    <!-- Formulario estilizado -->
    <div class="bg-gray-800/50 backdrop-blur-sm border border-gray-700/50 rounded-xl p-8 shadow-lg hover:border-orange-500/30 transition-all duration-300">
        <form method="post" action="{{ route('password.update') }}" class="space-y-6">
            @csrf
            @method('put')

            <!-- Campo Contraseña Actual -->
            <div class="group">
                <x-input-label 
                    for="update_password_current_password" 
                    :value="__('Current Password')" 
                    class="block text-sm font-medium text-gray-300 mb-2 group-focus-within:text-orange-400 transition-colors"
                />
                <div class="relative">
                    <x-text-input 
                        id="update_password_current_password" 
                        name="current_password" 
                        type="password" 
                        class="w-full bg-gray-700/50 border border-gray-600 rounded-lg py-3 px-4 text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-orange-500/50 focus:border-orange-500 transition-all" 
                        autocomplete="current-password"
                        placeholder="Introduce tu contraseña actual"
                    />
                    <div class="absolute inset-y-0 right-0 flex items-center pr-3">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400 group-focus-within:text-orange-400 transition-colors" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                        </svg>
                    </div>
                </div>
                <x-input-error :messages="$errors->updatePassword->get('current_password')" class="mt-2 text-red-400" />
            </div>

            <!-- Campo Nueva Contraseña -->
            <div class="group">
                <x-input-label 
                    for="update_password_password" 
                    :value="__('New Password')" 
                    class="block text-sm font-medium text-gray-300 mb-2 group-focus-within:text-orange-400 transition-colors"
                />
                <div class="relative">
                    <x-text-input 
                        id="update_password_password" 
                        name="password" 
                        type="password" 
                        class="w-full bg-gray-700/50 border border-gray-600 rounded-lg py-3 px-4 text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-orange-500/50 focus:border-orange-500 transition-all" 
                        autocomplete="new-password"
                        placeholder="Introduce tu nueva contraseña"
                    />
                    <div class="absolute inset-y-0 right-0 flex items-center pr-3">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400 group-focus-within:text-orange-400 transition-colors" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                        </svg>
                    </div>
                </div>
                <x-input-error :messages="$errors->updatePassword->get('password')" class="mt-2 text-red-400" />
                
                <!-- Indicador de fortaleza de contraseña -->
                <div class="mt-2">
                    <div class="flex space-x-1">
                        <div class="h-1 w-full bg-gray-600 rounded-full overflow-hidden">
                            <div class="h-full bg-gradient-to-r from-red-500 via-yellow-500 to-green-500 w-0 transition-all duration-300" id="password-strength-bar"></div>
                        </div>
                    </div>
                    <p class="text-xs text-gray-400 mt-1">Usa al menos 8 caracteres con mayúsculas, minúsculas y números</p>
                </div>
            </div>

            <!-- Campo Confirmar Contraseña -->
            <div class="group">
                <x-input-label 
                    for="update_password_password_confirmation" 
                    :value="__('Confirm Password')" 
                    class="block text-sm font-medium text-gray-300 mb-2 group-focus-within:text-orange-400 transition-colors"
                />
                <div class="relative">
                    <x-text-input 
                        id="update_password_password_confirmation" 
                        name="password_confirmation" 
                        type="password" 
                        class="w-full bg-gray-700/50 border border-gray-600 rounded-lg py-3 px-4 text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-orange-500/50 focus:border-orange-500 transition-all" 
                        autocomplete="new-password"
                        placeholder="Confirma tu nueva contraseña"
                    />
                    <div class="absolute inset-y-0 right-0 flex items-center pr-3">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400 group-focus-within:text-orange-400 transition-colors" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                </div>
                <x-input-error :messages="$errors->updatePassword->get('password_confirmation')" class="mt-2 text-red-400" />
            </div>

            <!-- Botones y mensaje de éxito -->
            <div class="flex flex-col sm:flex-row items-center gap-4 pt-4">
                <x-primary-button class="group relative overflow-hidden px-8 py-3 rounded-lg bg-gradient-to-r from-orange-500 to-red-500 text-white font-bold shadow-lg shadow-orange-500/20 transform transition-all duration-300 hover:-translate-y-0.5 hover:shadow-orange-500/30">
                    <span class="relative z-10 flex items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                        </svg>
                        {{ __('Save') }}
                    </span>
                    <div class="absolute inset-0 bg-gradient-to-r from-orange-600 to-red-600 transform scale-x-0 group-hover:scale-x-100 transition-transform origin-left duration-300"></div>
                </x-primary-button>

                @if (session('status') === 'password-updated')
                    <div
                        x-data="{ show: true }"
                        x-show="show"
                        x-transition:enter="transition ease-out duration-300"
                        x-transition:enter-start="opacity-0 scale-90"
                        x-transition:enter-end="opacity-100 scale-100"
                        x-transition:leave="transition ease-in duration-300"
                        x-transition:leave-start="opacity-100 scale-100"
                        x-transition:leave-end="opacity-0 scale-90"
                        x-init="setTimeout(() => show = false, 3000)"
                        class="flex items-center px-4 py-2 bg-green-500/20 border border-green-500/30 rounded-lg"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-green-400 mr-2" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                        </svg>
                        <span class="text-green-400 font-medium">{{ __('Saved.') }}</span>
                    </div>
                @endif
            </div>
        </form>
    </div>

    <!-- Consejos de seguridad -->
    <div class="bg-gray-800/30 backdrop-blur-sm border border-gray-700/30 rounded-xl p-6">
        <h3 class="text-lg font-semibold mb-4 text-orange-400 flex items-center">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
            </svg>
            Consejos de seguridad
        </h3>
        <div class="grid md:grid-cols-2 gap-4 text-sm text-gray-400">
            <div class="flex items-start">
                <span class="text-orange-400 mr-2 mt-0.5">•</span>
                <span>Usa al menos 12 caracteres</span>
            </div>
            <div class="flex items-start">
                <span class="text-orange-400 mr-2 mt-0.5">•</span>
                <span>Combina mayúsculas y minúsculas</span>
            </div>
            <div class="flex items-start">
                <span class="text-orange-400 mr-2 mt-0.5">•</span>
                <span>Incluye números y símbolos</span>
            </div>
            <div class="flex items-start">
                <span class="text-orange-400 mr-2 mt-0.5">•</span>
                <span>Evita información personal</span>
            </div>
        </div>
    </div>
</section>

<script>
    // Medidor de fortaleza de contraseña
    document.addEventListener('DOMContentLoaded', function() {
        const passwordInput = document.getElementById('update_password_password');
        const strengthBar = document.getElementById('password-strength-bar');
        
        if (passwordInput && strengthBar) {
            passwordInput.addEventListener('input', function() {
                const password = this.value;
                let strength = 0;
                
                // Criterios de fortaleza
                if (password.length >= 8) strength += 20;
                if (password.length >= 12) strength += 20;
                if (/[a-z]/.test(password)) strength += 20;
                if (/[A-Z]/.test(password)) strength += 20;
                if (/[0-9]/.test(password)) strength += 10;
                if (/[^A-Za-z0-9]/.test(password)) strength += 10;
                
                strengthBar.style.width = Math.min(strength, 100) + '%';
                
                // Cambiar color según fortaleza
                if (strength < 40) {
                    strengthBar.style.background = 'linear-gradient(to right, #ef4444, #f97316)';
                } else if (strength < 70) {
                    strengthBar.style.background = 'linear-gradient(to right, #f97316, #eab308)';
                } else {
                    strengthBar.style.background = 'linear-gradient(to right, #eab308, #22c55e)';
                }
            });
        }
    });
</script>