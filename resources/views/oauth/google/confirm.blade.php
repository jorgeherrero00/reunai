<!DOCTYPE html>
<html lang="es" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Conectar con Google Sheets - Reunai</title>
    
    <!-- Fonts & Styles -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    
    <style>
        body::before {
            content: "";
            position: fixed;
            inset: 0;
            background: radial-gradient(ellipse at top left, rgba(0,0,0,0.6), transparent),
                        linear-gradient(135deg, #110f17 0%, #0b0b0f 100%);
            z-index: -1;
        }
        
        .gradient-border {
            background: linear-gradient(135deg, #f97316, #ef4444) padding-box,
                        linear-gradient(135deg, #f97316, #ef4444) border-box;
            border: 2px solid transparent;
        }
        
        .google-icon {
            background: linear-gradient(135deg, #4285f4, #34a853, #fbbc05, #ea4335);
            -webkit-background-clip: text;
            background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        
        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-10px); }
        }
        
        .float-animation {
            animation: float 3s ease-in-out infinite;
        }
        
        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .slide-in {
            animation: slideIn 0.6s ease-out forwards;
        }
    </style>
</head>

<body class="bg-[#0a0a0f] text-white font-sans antialiased min-h-screen flex items-center justify-center p-6">
    <!-- Background effects -->
    <div class="fixed inset-0 overflow-hidden pointer-events-none">
        <div class="absolute top-1/4 left-1/5 w-96 h-96 bg-orange-500/10 rounded-full filter blur-[80px] animate-pulse"></div>
        <div class="absolute bottom-1/3 right-1/4 w-80 h-80 bg-blue-500/10 rounded-full filter blur-[100px] animate-pulse" style="animation-delay: 1s;"></div>
    </div>

    <!-- Main content -->
    <div class="relative z-10 w-full max-w-md">
        <!-- Logo -->
        <div class="text-center mb-8 slide-in">
            <div class="inline-flex items-center space-x-2 mb-6">
                <div class="relative">
                    <div class="absolute -inset-1 bg-gradient-to-r from-orange-500 to-red-500 rounded-full blur-md opacity-70 animate-pulse"></div>
                    <div class="relative text-2xl font-extrabold bg-gradient-to-r from-orange-400 via-orange-500 to-red-500 text-transparent bg-clip-text">Reunai</div>
                </div>
            </div>
        </div>

        <!-- Main card -->
        <div class="bg-gray-800/50 backdrop-blur-xl border border-gray-700/50 rounded-2xl p-8 shadow-2xl slide-in" style="animation-delay: 0.2s;">
            <!-- Success icon -->
            <div class="text-center mb-6">
                <div class="inline-flex items-center justify-center w-16 h-16 bg-green-500/20 rounded-full mb-4 float-animation">
                    <svg class="w-8 h-8 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                </div>
                <h2 class="text-2xl font-bold text-white mb-2">¡Conexión exitosa!</h2>
                <p class="text-gray-400 text-sm">Tu cuenta de Google se ha vinculado correctamente</p>
            </div>

            <!-- Google Sheets info -->
            <div class="bg-gray-900/50 rounded-xl p-4 mb-6 border border-gray-600/30">
                <div class="flex items-center space-x-3 mb-3">
                    <div class="w-10 h-10 bg-white rounded-lg flex items-center justify-center">
                        <svg class="w-6 h-6" viewBox="0 0 24 24">
                            <path fill="#0F9D58" d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2z"/>
                            <path fill="white" d="M14 17H7v-2h7v2zm3-4H7v-2h10v2zm0-4H7V7h10v2z"/>
                        </svg>
                    </div>
                    <div>
                        <h3 class="font-semibold text-white">Google Sheets</h3>
                        <p class="text-xs text-gray-400">Permisos de lectura y escritura</p>
                    </div>
                </div>
                
                <div class="space-y-2">
                    <div class="flex items-center text-xs text-gray-300">
                        <svg class="w-4 h-4 text-green-400 mr-2" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                        </svg>
                        Crear y editar hojas de cálculo
                    </div>
                    <div class="flex items-center text-xs text-gray-300">
                        <svg class="w-4 h-4 text-green-400 mr-2" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                        </svg>
                        Exportar resúmenes automáticamente
                    </div>
                    <div class="flex items-center text-xs text-gray-300">
                        <svg class="w-4 h-4 text-green-400 mr-2" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                        </svg>
                        Acceso seguro con tokens cifrados
                    </div>
                </div>
            </div>

            <!-- Security note -->
            <div class="bg-blue-500/10 border border-blue-500/20 rounded-lg p-3 mb-6">
                <div class="flex items-start space-x-2">
                    <svg class="w-5 h-5 text-blue-400 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                    </svg>
                    <div>
                        <p class="text-blue-300 text-sm font-medium">Conexión segura</p>
                        <p class="text-blue-400 text-xs mt-1">Tus credenciales se almacenan de forma cifrada y solo se usan para las funciones que autorices.</p>
                    </div>
                </div>
            </div>

            <!-- Action buttons -->
            <form method="POST" action="{{ route('oauth.google.store') }}" class="space-y-4">
                @csrf
                <input type="hidden" name="access_token" value="{{ $access_token }}">
                <input type="hidden" name="refresh_token" value="{{ $refresh_token }}">
                
                <button type="submit" class="group relative w-full overflow-hidden py-4 px-6 rounded-xl bg-gradient-to-r from-green-500 to-emerald-500 text-white font-bold text-lg shadow-xl shadow-green-500/20 transform transition-all duration-300 hover:-translate-y-1 hover:shadow-green-500/30">
                    <span class="relative z-10 flex items-center justify-center space-x-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        <span>Sí, guardar conexión</span>
                    </span>
                    <div class="absolute inset-0 bg-gradient-to-r from-green-600 to-emerald-600 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                </button>
            </form>

            <a href="{{ route('dashboard') }}" class="group relative block w-full py-3 px-6 text-center rounded-xl bg-gray-700/50 hover:bg-gray-700 text-gray-300 hover:text-white font-medium transition-all duration-300 mt-3 border border-gray-600/50 hover:border-gray-500">
                <span class="flex items-center justify-center space-x-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                    <span>No, omitir por ahora</span>
                </span>
            </a>

            <!-- Footer note -->
            <p class="text-center text-xs text-gray-500 mt-4">
                Puedes desconectar esta integración en cualquier momento desde tu panel de configuración.
            </p>
        </div>

        <!-- Bottom help -->
        <div class="text-center mt-6 slide-in" style="animation-delay: 0.4s;">
            <p class="text-gray-400 text-sm">¿Necesitas ayuda?</p>
            <a href="#help" class="inline-flex items-center px-4 py-2 mt-2 bg-white/10 hover:bg-white/20 backdrop-blur-sm border border-white/20 rounded-full text-orange-400 transition-colors text-sm">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                Centro de ayuda
            </a>
        </div>
    </div>
</body>
</html>