<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>404 - Página no encontrada | Reunai</title>
    
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700" rel="stylesheet" />
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
        
        .float-animation {
            animation: float 6s ease-in-out infinite;
        }
        
        .float-delayed {
            animation: float 6s ease-in-out infinite;
            animation-delay: 2s;
        }
        
        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-20px); }
        }
        
        .glitch-text {
            position: relative;
            color: #f97316;
        }
        
        .glitch-text::before,
        .glitch-text::after {
            content: attr(data-text);
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
        }
        
        .glitch-text::before {
            animation: glitch-1 2s infinite;
            color: #ef4444;
            z-index: -1;
        }
        
        .glitch-text::after {
            animation: glitch-2 2s infinite;
            color: #3b82f6;
            z-index: -2;
        }
        
        @keyframes glitch-1 {
            0%, 14%, 16%, 18%, 20%, 32%, 34%, 36%, 38%, 40%, 42%, 44%, 46%, 56%, 58%, 60%, 62%, 68%, 70%, 72%, 74%, 76%, 78%, 80%, 82%, 84%, 86%, 88%, 90%, 92%, 94%, 96%, 98%, 100% {
                transform: translate(0);
            }
            15%, 17%, 19%, 33%, 35%, 37%, 39%, 41%, 43%, 45%, 57%, 59%, 61%, 69%, 71%, 73%, 75%, 77%, 79%, 81%, 83%, 85%, 87%, 89%, 91%, 93%, 95%, 97% {
                transform: translate(-2px, 0);
            }
        }
        
        @keyframes glitch-2 {
            0%, 20%, 22%, 24%, 26%, 28%, 30%, 32%, 34%, 36%, 38%, 40%, 42%, 44%, 46%, 48%, 50%, 52%, 54%, 56%, 58%, 60%, 62%, 64%, 66%, 68%, 70%, 72%, 74%, 76%, 78%, 80%, 82%, 84%, 86%, 88%, 90%, 92%, 94%, 96%, 98%, 100% {
                transform: translate(0);
            }
            21%, 23%, 25%, 27%, 29%, 31%, 33%, 35%, 37%, 39%, 41%, 43%, 45%, 47%, 49%, 51%, 53%, 55%, 57%, 59%, 61%, 63%, 65%, 67%, 69%, 71%, 73%, 75%, 77%, 79%, 81%, 83%, 85%, 87%, 89%, 91%, 93%, 95%, 97%, 99% {
                transform: translate(2px, 0);
            }
        }
    </style>
</head>
<body class="min-h-screen bg-[#0a0a0f] text-white font-sans antialiased">
    <!-- Background animated elements -->
    <div class="fixed inset-0 z-0">
        <div class="absolute top-1/4 left-1/5 w-96 h-96 bg-orange-500/20 rounded-full filter blur-[80px] animate-pulse float-animation"></div>
        <div class="absolute bottom-1/3 right-1/4 w-80 h-80 bg-purple-600/20 rounded-full filter blur-[100px] animate-pulse float-delayed" style="animation-delay: 1s;"></div>
    </div>

    <!-- Main content -->
    <div class="relative z-10 min-h-screen flex flex-col justify-center items-center px-6">
        <!-- Logo/Brand -->
        <div class="mb-8">
            <div class="relative">
                <div class="absolute -inset-1 bg-gradient-to-r from-orange-500 to-red-500 rounded-full blur-md opacity-70 animate-pulse"></div>
                <div class="relative text-3xl font-extrabold bg-gradient-to-r from-orange-400 via-orange-500 to-red-500 text-transparent bg-clip-text">
                    Reunai
                </div>
            </div>
        </div>

        <!-- 404 Error -->
        <div class="text-center max-w-4xl mx-auto">
            <!-- Large 404 -->
            <h1 class="glitch-text text-8xl md:text-9xl font-extrabold mb-6" data-text="404">
                404
            </h1>
            
            <!-- Error message -->
            <div class="inline-flex items-center bg-gradient-to-r from-orange-500/30 to-red-500/30 px-4 py-2 rounded-full backdrop-blur-sm mb-6 border border-orange-500/20 shadow-lg">
                <span class="animate-pulse mr-2 h-2 w-2 bg-orange-500 rounded-full"></span>
                <span class="text-orange-300 font-medium tracking-wider text-sm">REUNIÓN NO ENCONTRADA</span>
            </div>
            
            <h2 class="text-3xl md:text-4xl font-bold mb-6 leading-tight bg-gradient-to-br from-white via-gray-100 to-gray-300 text-transparent bg-clip-text">
                Esta página se perdió en el <span class="bg-gradient-to-r from-orange-400 to-red-500 text-transparent bg-clip-text">análisis</span>
            </h2>
            
            <p class="text-xl text-gray-400 max-w-2xl mx-auto mb-12 leading-relaxed">
                Parece que la página que buscas no existe o se ha movido. Pero no te preocupes, podemos ayudarte a encontrar lo que necesitas.
            </p>
            
            <!-- Action buttons -->
            <div class="flex flex-col sm:flex-row justify-center gap-6 mb-16">
                <a href="{{ url('/') }}" class="group relative overflow-hidden px-8 py-4 rounded-xl bg-gradient-to-r from-orange-500 to-red-500 font-bold text-lg shadow-xl shadow-orange-500/20 transform transition-all duration-300 hover:-translate-y-1 hover:shadow-orange-500/30">
                    <span class="relative z-10 flex items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                            <path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z" />
                        </svg>
                        Volver al inicio
                    </span>
                    <div class="absolute inset-0 bg-gradient-to-r from-orange-600 to-red-600 transform scale-x-0 group-hover:scale-x-100 transition-transform origin-left duration-300"></div>
                </a>
                
                <a href="{{ route('reuniones.index') }}" class="group relative px-8 py-4 rounded-xl bg-white/10 backdrop-blur-sm border border-white/20 font-bold text-lg transition-all duration-300 hover:bg-white/20 hover:-translate-y-1">
                    <span class="flex items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                        </svg>
                        Ver mis reuniones
                    </span>
                </a>
            </div>

            <!-- Fun fact -->
            <div class="backdrop-blur-md bg-white/5 px-6 py-4 rounded-xl border border-white/10 inline-block">
                <div class="flex items-center gap-3">
                    <div class="h-8 w-8 rounded-full bg-gradient-to-r from-orange-500 to-red-500 flex items-center justify-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <span class="text-gray-300 text-sm">
                        <strong>¿Sabías que?</strong> El 73% de las reuniones podrían ser más eficientes con un buen análisis.
                    </span>
                </div>
            </div>
        </div>
    </div>

    <!-- Error code for debugging (hidden) -->
    <div class="fixed bottom-4 right-4 text-xs text-gray-600">
        ERROR_404_Reunai
    </div>
</body>
</html>