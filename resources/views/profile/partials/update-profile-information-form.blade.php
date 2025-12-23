<section class="space-y-8">
    <!-- Header con gradientes y efectos -->
    <header class="text-center">
        <div class="inline-flex items-center bg-gradient-to-r from-orange-400 to-red-500 px-4 py-2 rounded-full backdrop-blur-sm mb-4 border border-orange-500/20 shadow-lg">
            <span class="animate-pulse mr-2 h-2 w-2 bg-white rounded-full"></span>
            <span class="text-white font-medium tracking-wider text-xs uppercase">{{ __('Personal Information') }}</span>
        </div>
        
        <h2 class="text-3xl font-bold mb-4 bg-gradient-to-r from-white via-gray-100 to-gray-300 text-transparent bg-clip-text">
            {{ __('Profile Information') }}
        </h2>

        <p class="text-gray-400 max-w-lg mx-auto leading-relaxed">
            {{ __("Update your account's profile information and email address.") }}
        </p>
    </header>

    <!-- Formulario de verificación oculto -->
    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <!-- Formulario principal estilizado -->
    <div class="bg-gray-800/50 backdrop-blur-sm border border-orange-500/20 rounded-xl p-8 shadow-lg hover:border-orange-500/30 transition-all duration-300">
        <form method="post" action="{{ route('profile.update') }}" class="space-y-6">
            @csrf
            @method('patch')

            <!-- Campo Nombre -->
            <div class="group">
                <x-input-label 
                    for="name" 
                    :value="__('Name')" 
                    class="block text-sm font-medium text-gray-300 mb-2 group-focus-within:text-orange-400 transition-colors"
                />
                <div class="relative">
                    <x-text-input 
                        id="name" 
                        name="name" 
                        type="text" 
                        class="w-full bg-gray-700/50 border border-gray-600 rounded-lg py-3 px-4 pl-12 text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-orange-400 focus:border-orange-400 transition-all" 
                        :value="old('name', $user->name)"
                        required
                        autofocus
                        autocomplete="name"
                        placeholder="{{ __('Enter your full name') }}"
                    />
                    <div class="absolute inset-y-0 left-0 flex items-center pl-3">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400 group-focus-within:text-orange-400 transition-colors" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                    </div>
                </div>
                <x-input-error class="mt-2 text-red-400" :messages="$errors->get('name')" />
            </div>

            <!-- Campo Email -->
            <div class="group">
                <x-input-label 
                    for="email" 
                    :value="__('Email')" 
                    class="block text-sm font-medium text-gray-300 mb-2 group-focus-within:text-orange-400 transition-colors"
                />
                <div class="relative">
                    <x-text-input 
                        id="email" 
                        name="email" 
                        type="email" 
                        class="w-full bg-gray-700/50 border border-gray-600 rounded-lg py-3 px-4 pl-12 text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-orange-400 focus:border-orange-400 transition-all" 
                        :value="old('email', $user->email)"
                        required
                        autocomplete="username"
                        placeholder="{{ __('your@email.com') }}"
                    />
                    <div class="absolute inset-y-0 left-0 flex items-center pl-3">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400 group-focus-within:text-orange-400 transition-colors" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                        </svg>
                    </div>
                </div>
                <x-input-error class="mt-2 text-red-400" :messages="$errors->get('email')" />

                <!-- Verificación de email -->
                @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                    <div class="mt-4 p-4 bg-yellow-500/10 border border-yellow-500/20 rounded-lg">
                        <div class="flex items-start">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-yellow-400 mr-3 mt-0.5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z" />
                            </svg>
                            <div>
                                <p class="text-sm text-yellow-200 mb-2">
                                    {{ __('Your email address is unverified.') }}
                                </p>
                                <button 
                                    form="send-verification" 
                                    class="inline-flex items-center px-4 py-2 bg-yellow-500/20 hover:bg-yellow-500/30 border border-yellow-500/30 rounded-lg text-yellow-300 hover:text-yellow-200 font-medium transition-all duration-300 text-sm"
                                >
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
                                    </svg>
                                    {{ __('Click here to re-send the verification email.') }}
                                </button>
                            </div>
                        </div>

                        @if (session('status') === 'verification-link-sent')
                            <div class="mt-3 p-3 bg-green-500/10 border border-green-500/20 rounded-lg">
                                <div class="flex items-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-green-400 mr-2" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                    </svg>
                                    <p class="text-sm font-medium text-green-400">
                                        {{ __('A new verification link has been sent to your email address.') }}
                                    </p>
                                </div>
                            </div>
                        @endif
                    </div>
                @else
                    <!-- Email verificado -->
                    <div class="mt-2 flex items-center text-sm text-green-400">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                        </svg>
                        <span>{{ __('Email verified') }}</span>
                    </div>
                @endif
            </div>

            <!-- Botones y mensaje de éxito -->
            <div class="flex flex-col sm:flex-row items-center gap-4 pt-4">
                <x-primary-button class="group relative overflow-hidden px-8 py-3 rounded-lg bg-gradient-to-r from-orange-500 to-red-500 text-white font-bold shadow-lg shadow-orange-500/20 transform transition-all duration-300 ">
                    <span class="relative z-10 flex items-center text-white">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                        </svg>
                        {{ __('Save') }}
                    </span>
                </x-primary-button>

                @if (session('status') === 'profile-updated')
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

    <!-- Información adicional del perfil -->
    <div class="grid md:grid-cols-2 gap-6">
        <!-- Estado de la cuenta -->
        <div class="bg-gray-800/30 backdrop-blur-sm border border-gray-700/30 rounded-xl p-6">
            <h3 class="text-lg font-semibold mb-4 text-orange-400 flex items-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-6-3a2 2 0 11-4 0 2 2 0 014 0zm-2 4a5 5 0 00-4.546 2.916A5.986 5.986 0 0010 16a5.986 5.986 0 004.546-2.084A5 5 0 0010 11z" clip-rule="evenodd" />
                </svg>
                {{ __('Account status') }}
            </h3>
            <div class="space-y-3 text-sm">
                <div class="flex justify-between items-center">
                    <span class="text-gray-400">{{ __('Account created:') }}</span>
                    <span class="text-white">{{ $user->created_at->format('d/m/Y') }}</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-gray-400">{{ __('Last access:') }}</span>
                    <span class="text-white">{{ $user->updated_at->diffForHumans() }}</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-gray-400">{{ __('Email verified:') }}</span>
                    @if($user->hasVerifiedEmail())
                        <span class="flex items-center text-green-400">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                            </svg>
                            {{ __('Yes') }}
                        </span>
                    @else
                        <span class="flex items-center text-yellow-400">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                            </svg>
                            {{ __('Pending') }}
                        </span>
                    @endif
                </div>
            </div>
        </div>

        <!-- Consejos de perfil -->
        <div class="bg-gray-800/30 backdrop-blur-sm border border-gray-700/30 rounded-xl p-6">
            <h3 class="text-lg font-semibold mb-4 text-orange-400 flex items-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                </svg>
                {{ __('Tips') }}
            </h3>
            <div class="space-y-2 text-sm text-gray-400">
                <div class="flex items-start">
                    <span class="text-orange-400 mr-2 mt-0.5">•</span>
                    <span>{{ __('Use your real name for better identification') }}</span>
                </div>
                <div class="flex items-start">
                    <span class="text-orange-400 mr-2 mt-0.5">•</span>
                    <span>{{ __('Verify your email for greater security') }}</span>
                </div>
                <div class="flex items-start">
                    <span class="text-orange-400 mr-2 mt-0.5">•</span>
                    <span>{{ __('Keep your information up to date') }}</span>
                </div>
            </div>
        </div>
    </div>
</section>