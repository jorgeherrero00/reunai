<x-guest-layout>
    <div class="mb-6 text-center">
        <h2 class="text-2xl font-bold mb-2">Inicia sesión</h2>
        <p class="text-gray-400 text-sm">Accede a tu cuenta para gestionar tus reuniones</p>
    </div>

    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}" class="space-y-6">
        @csrf

        <!-- Email Address -->
        <div>
            <x-input-label for="email" :value="__('Email')" class="text-white" />
            <x-text-input id="email" class="block mt-1 w-full bg-gray-800 text-white border-gray-700 focus:ring-orange-500 focus:border-orange-500"
                          type="email" name="email" :value="old('email')" required autofocus autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2 text-red-400" />
        </div>

        <!-- Password -->
        <div>
            <x-input-label for="password" :value="__('Contraseña')" class="text-white" />
            <x-text-input id="password" class="block mt-1 w-full bg-gray-800 text-white border-gray-700 focus:ring-orange-500 focus:border-orange-500"
                          type="password" name="password" required autocomplete="current-password" />
            <x-input-error :messages="$errors->get('password')" class="mt-2 text-red-400" />
        </div>

        <!-- Remember Me -->
        <div class="flex items-center justify-between">
            <label for="remember_me" class="inline-flex items-center text-sm text-gray-400">
                <input id="remember_me" type="checkbox" class="rounded border-gray-600 text-orange-500 bg-gray-800 focus:ring-orange-500" name="remember">
                <span class="ml-2">Recordarme</span>
            </label>

            @if (Route::has('password.request'))
                <a class="text-sm text-orange-400 hover:text-orange-300 transition" href="{{ route('password.request') }}">
                    ¿Olvidaste tu contraseña?
                </a>
            @endif
        </div>

        <div>
            <button type="submit"
                    class="w-full py-3 px-6 text-center bg-gradient-to-r from-orange-500 to-red-500 text-white font-semibold rounded-lg transition-transform duration-300 hover:-translate-y-1 hover:shadow-lg shadow-orange-500/20">
                Entrar
            </button>
        </div>
    </form>

    <div class="mt-6 text-center text-sm text-gray-400">
        ¿No tienes cuenta?
        <a href="{{ route('register') }}" class="text-orange-400 hover:text-orange-300 font-medium">Regístrate</a>
    </div>
</x-guest-layout>
