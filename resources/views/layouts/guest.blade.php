<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <title>{{ config('app.name', 'Reunai') }}</title>

    <!-- Fonts & Styles -->
    <link rel="preconnect" href="https://fonts.bunny.net" />
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        body::before {
            content: "";
            position: fixed;
            inset: 0;
            background: radial-gradient(ellipse at top left, rgba(0,0,0,0.6), transparent),
                        linear-gradient(135deg, #110f17 0%, #0b0b0f 100%);
            z-index: -1;
        }
    </style>
</head>
<body class="bg-[#0a0a0f] text-white font-sans antialiased">
    <div class="min-h-screen flex flex-col justify-center items-center px-6 sm:px-0">
        <div class="text-3xl font-extrabold bg-gradient-to-r from-orange-400 via-orange-500 to-red-500 text-transparent bg-clip-text mb-6">
            <img src="/logos/logo.png" alt="{{ config('app.name', 'Reunai') }}" class="h-10 inline-block mr-2" />
            {{ config('app.name', 'Reunai') }}
        </div>

        <div class="w-full sm:max-w-md  backdrop-blur-md  shadow-xl rounded-xl p-8">
            {{ $slot }}
        </div>
    </div>
</body>
</html>
