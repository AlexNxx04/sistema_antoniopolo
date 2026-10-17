<!DOCTYPE html>
<html lang="es" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión - Antonio Polo S.A.</title>
    <meta name="description" content="Sistema de Gestión Operativa Automatizada - Antonio Polo S.A.">
    <meta name="author" content="Universidad Estatal de Bolívar">
    <link rel="icon" href="{{ asset('images/logo-antonio-polo.png') }}" type="image/png">
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    <style>
        * { font-family: 'Inter', sans-serif; }
        
        .gradient-bg {
            background: linear-gradient(135deg, #022c22 0%, #064e3b 25%, #047857 50%, #059669 75%, #10b981 100%);
            background-size: 400% 400%;
            animation: gradientShift 15s ease infinite;
        }
        
        @keyframes gradientShift {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }
        
        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-8px); }
        }
        
        .float-animation { animation: float 5s ease-in-out infinite; }
        
        .glass-card {
            background: rgba(255, 255, 255, 0.98);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.5);
        }
        
        .input-modern {
            transition: border-color 0.2s ease, box-shadow 0.2s ease;
            border: 1.5px solid #e5e7eb;
        }
        
        .input-modern:focus {
            border-color: #059669;
            box-shadow: 0 0 0 4px rgba(5, 150, 105, 0.15);
        }
        
        .input-modern:hover:not(:focus) {
            border-color: #a7f3d0;
        }
        
        .btn-login {
            background: linear-gradient(135deg, #059669 0%, #047857 100%);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            overflow: hidden;
        }
        
        .btn-login::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
            transition: left 0.5s;
        }
        
        .btn-login:hover::before {
            left: 100%;
        }
        
        .btn-login:hover {
            box-shadow: 0 10px 30px rgba(5, 150, 105, 0.3);
        }
        
        .btn-login:active {
            transform: scale(0.98);
        }
        
        .password-toggle {
            transition: color 0.2s ease;
        }
        
        .password-toggle:hover {
            color: #059669;
        }
        
        .brand-logo {
            filter: drop-shadow(0 10px 20px rgba(0, 0, 0, 0.2));
        }
        
        .feature-badge {
            background: linear-gradient(135deg, rgba(16, 185, 129, 0.15) 0%, rgba(5, 150, 105, 0.15) 100%);
            border: 1px solid rgba(16, 185, 129, 0.3);
        }
        
        .close-btn {
            background: rgba(156, 163, 175, 0.1);
            transition: background-color 0.2s ease;
        }
        
        .close-btn:hover {
            background: rgba(156, 163, 175, 0.2);
        }
    </style>
</head>
<body class="flex flex-col min-h-screen gradient-bg">

    {{-- Partículas decorativas de fondo --}}
    <div class="fixed inset-0 overflow-hidden pointer-events-none">
        <div class="absolute top-1/4 left-1/4 w-96 h-96 bg-emerald-500/10 rounded-full blur-3xl"></div>
        <div class="absolute bottom-1/4 right-1/4 w-80 h-80 bg-teal-500/10 rounded-full blur-3xl"></div>
    </div>

    {{-- Contenido principal --}}
    <div class="flex-1 flex items-center justify-center p-4 sm:p-6 relative z-10">
        <div class="w-full max-w-4xl" data-aos="fade-up" data-aos-duration="600">
            
            <div class="grid lg:grid-cols-5 gap-0 rounded-2xl overflow-hidden shadow-2xl">

                {{-- Panel Izquierdo --}}
                <div class="hidden lg:flex lg:col-span-2 flex-col justify-center items-center p-8 text-white relative overflow-hidden">
                    <div class="absolute inset-0 bg-gradient-to-b from-emerald-600/20 to-teal-600/20"></div>
                    
                    {{-- Círculos decorativos --}}
                    <div class="absolute -top-32 -left-32 w-64 h-64 border border-white/10 rounded-full"></div>
                    <div class="absolute -top-20 -left-20 w-48 h-48 border border-white/10 rounded-full"></div>
                    <div class="absolute -bottom-32 -right-32 w-64 h-64 border border-white/10 rounded-full"></div>
                    <div class="absolute -bottom-20 -right-20 w-48 h-48 border border-white/10 rounded-full"></div>
                    
                    <div class="relative z-10 text-center">
                        {{-- Logo con animación --}}
                        <div class="relative inline-block mb-6">
                            <div class="absolute inset-0 bg-emerald-400/30 rounded-full blur-xl"></div>
                            <img class="relative w-28 h-28 lg:w-36 lg:h-36 object-contain brand-logo float-animation" 
                                 src="{{ asset('images/logo-antonio-polo.png') }}" 
                                 alt="Logo de Antonio Polo S.A.">
                        </div>
                        
                        <h1 class="text-2xl lg:text-3xl font-bold mb-2 tracking-tight">Antonio Polo S.A.</h1>
                        <p class="text-emerald-100/80 text-sm mb-6">Sistema de Gestión Operativa Automatizada</p>
                        
                        {{-- Estadísticas rápidas --}}
                        <div class="flex justify-center gap-6">
                            <div class="text-center">
                                <div class="text-xl font-bold text-white">24+</div>
                                <div class="text-xs text-emerald-200/60">Socios</div>
                            </div>
                            <div class="text-center">
                                <div class="text-xl font-bold text-white">15</div>
                                <div class="text-xs text-emerald-200/60">Minutos</div>
                            </div>
                            <div class="text-center">
                                <div class="text-xl font-bold text-white">60</div>
                                <div class="text-xs text-emerald-200/60">Min Ruta</div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Panel Derecho --}}
                <div class="col-span-1 lg:col-span-3 glass-card p-8 lg:p-10 flex flex-col justify-center relative">

                    {{-- Botón cerrar --}}
                    <a href="{{ url('/') }}" 
                       class="absolute top-4 right-4 close-btn inline-flex items-center justify-center w-8 h-8 rounded-lg hover:bg-gray-200"
                       title="Volver al inicio"
                       aria-label="Cerrar y volver al inicio">
                        <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </a>

                    {{-- Header del formulario --}}
                    <div class="mb-6">
                        <h2 class="text-xl font-bold text-gray-800">Iniciar Sesión</h2>
                        <p class="text-gray-500 text-sm mt-1">Ingresa tus credenciales para continuar</p>
                    </div>

                    {{-- Errores de validación --}}
                    @if($errors->any())
                        <div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-xl" data-aos="shake" data-aos-duration="500">
                            <div class="flex items-center gap-3">
                                <svg class="w-5 h-5 text-red-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                <div>
                                    <p class="font-semibold text-red-800 text-sm">Error de autenticación</p>
                                    <p class="text-red-600 text-sm">{{ $errors->first() }}</p>
                                </div>
                            </div>
                        </div>
                    @endif

                    {{-- Formulario --}}
                    <form method="POST" action="{{ route('login') }}" class="space-y-5" x-data="{ showPassword: false }">
                        @csrf 

                        {{-- Cédula --}}
                        <div>
                            <label for="cedula" class="block text-sm font-medium text-gray-700 mb-2">
                                Cédula de Identidad
                            </label>
                            <div class="relative">
                                <input 
                                    type="text" 
                                    id="cedula" 
                                    name="cedula" 
                                    value="{{ old('cedula') }}" 
                                    required 
                                    autofocus 
                                    maxlength="10"
                                    pattern="[0-9]{10}"
                                    inputmode="numeric"
                                    class="input-modern w-full pl-12 pr-4 py-3.5 rounded-xl text-gray-900 bg-white text-base"
                                    placeholder="Ej: 0102030405">
                                <span class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-400">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                    </svg>
                                </span>
                            </div>
                            <p class="mt-1.5 text-xs text-gray-500">10 dígitos numéricos</p>
                        </div>

                        {{-- Contraseña --}}
                        <div>
                            <label for="password" class="block text-sm font-medium text-gray-700 mb-2">
                                Contraseña
                            </label>
                            <div class="relative">
                                <input :type="showPassword ? 'text' : 'password'" id="password" name="password" required
                                    class="input-modern w-full pl-12 pr-12 py-3.5 rounded-xl text-gray-900 bg-white text-base"
                                    placeholder="Ingresa tu contraseña">
                                <span class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-400">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                                    </svg>
                                </span>
                                <button type="button" @click="showPassword = !showPassword" 
                                        class="password-toggle absolute right-4 top-1/2 -translate-y-1/2 text-gray-400 p-1">
                                    <svg x-show="!showPassword" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                    </svg>
                                    <svg x-show="showPassword" x-cloak class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>
                                    </svg>
                                </button>
                            </div>
                        </div>

                        {{-- Recordarme y Olvidaste contraseña --}}
                        <div class="flex items-center justify-between">
                            <label class="flex items-center cursor-pointer">
                                <input type="checkbox" name="remember" id="remember" 
                                       class="w-4 h-4 text-emerald-600 border-gray-300 rounded focus:ring-emerald-500">
                                <span class="ml-2 text-sm text-gray-600">Recordarme</span>
                            </label>
                            @if (Route::has('password.request'))
                                <a href="{{ route('password.request') }}" 
                                   class="text-sm text-emerald-600 hover:text-emerald-700">
                                    ¿Olvidaste tu contraseña?
                                </a>
                            @endif
                        </div>

                        {{-- Botón submit --}}
                        <button type="submit" class="btn-login w-full py-3.5 text-white font-semibold rounded-xl text-center text-base flex items-center justify-center gap-2 mt-2">
                            <span>Iniciar Sesión</span>
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                            </svg>
                        </button>
                    </form>

                    {{-- Info adicional --}}
                    <div class="mt-6 pt-4 border-t border-gray-100">
                        <p class="text-center text-xs text-gray-400">
                            Sistema desarrollado para Antonio Polo S.A. - Salinas, Bolívar
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Pie de Página --}}
    @include('partials.footer')

    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script>
        AOS.init({ duration: 500, easing: 'ease-out', once: true });
    </script>
    <style>
        [x-cloak] { display: none !important; }
    </style>
</body>
</html>
