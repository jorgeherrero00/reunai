<nav x-data="{ open: false }" class="bg-white dark:bg-black border-gray-100 dark:border-gray-700">
    <!-- Primary Navigation Menu -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <!-- Logo -->
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('dashboard') }}">
                        <img class="h-9 w-auto rounded" src="{{ asset('logos/logo.png') }}" alt="Logo">
                    </a>
                </div>

                <!-- Navigation Links -->
                <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                    <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                        {{ __('Dashboard') }}
                    </x-nav-link>
                    <x-nav-link :href="route('reuniones.index')" :active="request()->routeIs('reuniones.index')" >
                        {{ __('My Meetings') }}
                    </x-nav-link>
                   <!--  <x-nav-link :href="route('integrations.index')" :active="request()->routeIs('integrations.index')">
                        {{ __('My Api Keys') }}
                    </x-nav-link> -->
                </div>
            </div>

            <!-- Settings Dropdown -->
            <div class="hidden sm:flex sm:items-center sm:ms-6">
    <x-dropdown align="right" width="48">
        <x-slot name="trigger">
            <button class="group inline-flex items-center px-4 py-2 border border-gray-600 text-sm leading-4 font-medium rounded-lg text-gray-300 bg-gray-800/50 backdrop-blur-sm hover:text-white hover:border-orange-500/50 focus:outline-none focus:ring-2 focus:ring-orange-500/50 transition-all duration-300">
                <div class="flex items-center gap-2">
                    <!-- Avatar placeholder -->
                    <div class="w-7 h-7 rounded-full bg-gradient-to-r from-orange-400 to-red-500 flex items-center justify-center text-white font-bold text-xs">
                        {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                    </div>
                    <span>{{ Auth::user()->name }}</span>
                </div>

                <div class="ms-2">
                    <svg class="fill-current h-4 w-4 text-gray-400 group-hover:text-orange-400 transition-colors duration-300" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                    </svg>
                </div>
            </button>
        </x-slot>

        <x-slot name="content">
            <div class="bg-black backdrop-blur-xl border border-gray-700/50 rounded-xl shadow-xl shadow-black/20 overflow-hidden">
                <!-- User info header -->
                <div class="px-4 py-3 border-b border-gray-700/50 bg-gradient-to-r from-orange-500/10 to-red-500/10">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-full bg-gradient-to-r from-orange-400 to-red-500 flex items-center justify-center text-white font-bold text-sm">
                            {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                        </div>
                        <div>
                            <div class="text-white font-medium text-sm">{{ Auth::user()->name }}</div>
                            <div class="text-gray-400 text-xs">{{ Auth::user()->email }}</div>
                        </div>
                    </div>
                </div>

                <!-- Menu items -->
                <div class="py-1">
                    <x-dropdown-link :href="route('profile.edit')" 
                        class="group flex items-center px-4 py-3 text-sm text-gray-300 hover:text-white hover:bg-orange-500/10 transition-colors duration-200">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-3 text-gray-400 group-hover:text-orange-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                        {{ __('Profile') }}
                    </x-dropdown-link>

                  <x-dropdown-link :href="route('subscription.manage')" 
                    class="group flex items-center px-4 py-3 text-sm text-gray-300 hover:text-white hover:bg-orange-500/10 transition-colors duration-200">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-3 text-gray-400 group-hover:text-orange-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                    </svg>
                    Mi suscripción
                </x-dropdown-link>

                <!-- Divider -->
                <div class="border-t border-gray-700/50 my-1"></div>

                    <!-- Authentication -->
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <x-dropdown-link :href="route('logout')"
                                class="group flex items-center px-4 py-3 text-sm text-gray-300 hover:text-red-400 hover:bg-red-500/10 transition-colors duration-200"
                                onclick="event.preventDefault(); this.closest('form').submit();">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-3 text-gray-400 group-hover:text-red-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                            </svg>
                            {{ __('Log Out') }}
                        </x-dropdown-link>
                    </form>
                </div>
            </div>
        </x-slot>
    </x-dropdown>
</div>

            <!-- Hamburger -->
            <div class="-me-2 flex items-center sm:hidden">
                <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 dark:text-gray-500 hover:text-gray-500 dark:hover:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-900 focus:outline-none focus:bg-gray-100 dark:focus:bg-gray-900 focus:text-gray-500 dark:focus:text-gray-400 transition duration-150 ease-in-out">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Responsive Navigation Menu -->
    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden">
        <div class="pt-2 pb-3 space-y-1">
            <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                {{ __('Dashboard') }}
            </x-responsive-nav-link>
        </div>

        <!-- Responsive Settings Options -->
        <div class="pt-4 pb-1 border-t border-gray-200 dark:border-gray-600">
            <div class="px-4">
                <div class="font-medium text-base text-gray-800 dark:text-gray-200">{{ Auth::user()->name }}</div>
                <div class="font-medium text-sm text-gray-500">{{ Auth::user()->email }}</div>
            </div>

            <div class="mt-3 space-y-1">
                <x-responsive-nav-link :href="route('profile.edit')">
                    {{ __('Profile') }}
                </x-responsive-nav-link>

                <!-- Authentication -->
                <form method="POST" action="{{ route('logout') }}">
                    @csrf

                    <x-responsive-nav-link :href="route('logout')"
                            onclick="event.preventDefault();
                                        this.closest('form').submit();">
                        {{ __('Log Out') }}
                    </x-responsive-nav-link>
                </form>
            </div>
        </div>
    </div>
</nav>
