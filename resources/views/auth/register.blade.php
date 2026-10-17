<!DOCTYPE html>
<html lang="es" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrarse - Antonio Polo S.A.</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        * { font-family: 'Poppins', sans-serif; }
        html, body {
            background: linear-gradient(135deg, #0f172a 0%, #064e3b 50%, #0f172a 100%) !important;
        }
        .input-field { 
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            border: 1.5px solid #e2e8f0;
            background: #f8fafc;
        }
        .input-field:focus { 
            border-color: #10b981;
            background: #ffffff;
            box-shadow: 0 0 0 4px rgba(16, 185, 129, 0.1);
            outline: none;
        }
        .input-field:hover:not(:focus) {
            border-color: #cbd5e1;
        }
        .btn-primary {
            background: linear-gradient(135deg, #059669 0%, #047857 100%);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            overflow: hidden;
        }
        .btn-primary::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
            transition: left 0.5s;
        }
        .btn-primary:hover::before {
            left: 100%;
        }
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 12px 35px rgba(5, 150, 105, 0.4);
        }
        .input-icon {
            color: #94a3b8;
            transition: color 0.3s ease;
        }
    </style>
</head>
<body class="flex flex-col min-h-screen" style="background: linear-gradient(135deg, #0f172a 0%, #064e3b 50%, #0f172a 100%);">

    <div class="flex-1 flex items-center justify-center p-4 sm:p-6 my-auto">
        <div class="w-full max-w-4xl grid lg:grid-cols-5 gap-0 rounded-2xl overflow-hidden shadow-2xl">

            {{-- Panel Izquierdo - Branding --}}
            <div class="hidden lg:flex lg:col-span-2 flex-col justify-center items-center p-8 bg-gradient-to-br from-emerald-600 via-emerald-700 to-emerald-800 text-white relative overflow-hidden" style="background: linear-gradient(135deg, #059669 0%, #047857 50%, #065f46 100%);">
                <div class="absolute inset-0 opacity-10">
                    <svg width="100%" height="100%"><defs><pattern id="grid" width="40" height="40" patternUnits="userSpaceOnUse"><path d="M 40 0 L 0 0 0 40" fill="none" stroke="white" stroke-width="0.5"/></pattern></defs><rect width="100%" height="100%" fill="url(#grid)"/></svg>
                </div>
                <div class="absolute -top-20 -left-20 w-64 h-64 bg-emerald-400/30 rounded-full blur-3xl"></div>
                <div class="absolute -bottom-20 -right-20 w-72 h-72 bg-teal-400/20 rounded-full blur-3xl"></div>
                
                <div class="relative z-10 text-center">
                    <img class="w-44 h-44 object-contain mx-auto drop-shadow-2xl" src="{{ asset('images/logo-antonio-polo.png') }}" alt="Logo">
                    <h1 class="text-3xl font-bold mb-3 mt-4">Antonio Polo S.A.</h1>
                    <p class="text-emerald-100/80 text-base">Compañía de Transporte Mixto</p>
                </div>
            </div>

            {{-- Panel Derecho - Formulario --}}
            <div class="col-span-1 lg:col-span-3 bg-white p-8 lg:p-12 flex flex-col justify-center relative" style="background-color: #ffffff;">
                
                <a href="/" class="absolute top-6 right-6 lg:top-8 lg:right-8 inline-flex items-center justify-center w-10 h-10 rounded-full bg-slate-100 hover:bg-emerald-50 text-slate-400 hover:text-emerald-600 transition-all duration-300">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </a>

                <div class="mb-8">
                    <h2 class="text-2xl lg:text-3xl font-bold text-slate-800 mb-2">Crear Cuenta</h2>
                    <p class="text-slate-500">Completa tus datos para registrarte en el sistema</p>
                </div>

                <x-auth-session-status class="mb-6 p-4 bg-emerald-50 border border-emerald-200 rounded-xl text-emerald-700 text-sm flex items-center gap-3" :status="session('status')">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </x-auth-session-status>

                <form method="POST" action="{{ route('register') }}" class="space-y-5">
                    @csrf

                    <div>
                        <label for="name" class="block text-sm font-semibold text-slate-700 mb-2">Nombre completo</label>
                        <div class="relative">
                            <input type="text" id="name" name="name" value="{{ old('name') }}" required autofocus autocomplete="name"
                                class="input-field w-full pl-11 pr-4 py-3.5 rounded-xl text-slate-900 placeholder-slate-400"
                                placeholder="Ingresa tu nombre completo">
                            <span class="absolute left-4 top-1/2 -translate-y-1/2 pointer-events-none">
                                <svg class="input-icon w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                            </span>
                        </div>
                        <x-input-error :messages="$errors->get('name')" class="mt-2 text-red-500 text-sm flex items-center gap-1" />
                    </div>

                    <div>
                        <label for="email" class="block text-sm font-semibold text-slate-700 mb-2">Correo electrónico</label>
                        <div class="relative">
                            <input type="email" id="email" name="email" value="{{ old('email') }}" required autocomplete="username"
                                class="input-field w-full pl-11 pr-4 py-3.5 rounded-xl text-slate-900 placeholder-slate-400"
                                placeholder="correo@ejemplo.com">
                            <span class="absolute left-4 top-1/2 -translate-y-1/2 pointer-events-none">
                                <svg class="input-icon w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                            </span>
                        </div>
                        <x-input-error :messages="$errors->get('email')" class="mt-2 text-red-500 text-sm flex items-center gap-1" />
                    </div>

                    <div class="grid sm:grid-cols-2 gap-5">
                        <div>
                            <label for="password" class="block text-sm font-semibold text-slate-700 mb-2">Contraseña</label>
                            <div class="relative">
                                <input type="password" id="password" name="password" required autocomplete="new-password"
                                    class="input-field w-full pl-11 pr-12 py-3.5 rounded-xl text-slate-900 placeholder-slate-400"
                                    placeholder="Mínimo 8 caracteres">
                                <span class="absolute left-4 top-1/2 -translate-y-1/2 pointer-events-none">
                                    <svg class="input-icon w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                                </span>
                                <button type="button" onclick="togglePassword('password')" class="absolute right-4 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600">
                                    <svg id="eye-password" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                </button>
                            </div>
                            <x-input-error :messages="$errors->get('password')" class="mt-2 text-red-500 text-sm flex items-center gap-1" />
                        </div>

                        <div>
                            <label for="password_confirmation" class="block text-sm font-semibold text-slate-700 mb-2">Confirmar contraseña</label>
                            <div class="relative">
                                <input type="password" id="password_confirmation" name="password_confirmation" required autocomplete="new-password"
                                    class="input-field w-full pl-11 pr-12 py-3.5 rounded-xl text-slate-900 placeholder-slate-400"
                                    placeholder="Repite la contraseña">
                                <span class="absolute left-4 top-1/2 -translate-y-1/2 pointer-events-none">
                                    <svg class="input-icon w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                </span>
                                <button type="button" onclick="togglePassword('password_confirmation')" class="absolute right-4 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600">
                                    <svg id="eye-password_confirmation" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                </button>
                            </div>
                            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2 text-red-500 text-sm flex items-center gap-1" />
                        </div>
                    </div>

                    <div class="flex items-start gap-3 p-4 bg-slate-50 rounded-xl border border-slate-200">
                        <input type="checkbox" required id="terms" class="w-5 h-5 mt-0.5 text-emerald-600 rounded border-slate-300 focus:ring-emerald-500 cursor-pointer">
                        <label for="terms" class="text-sm text-slate-600 cursor-pointer">
                            Acepto los <a href="#" class="text-emerald-600 hover:text-emerald-700 font-medium hover:underline">términos y condiciones</a> y la <a href="#" class="text-emerald-600 hover:text-emerald-700 font-medium hover:underline">política de privacidad</a>
                        </label>
                    </div>

                    <button type="submit" class="btn-primary w-full py-4 text-white font-bold rounded-xl text-center shadow-lg flex items-center justify-center gap-2">
                        <span>Crear Cuenta</span>
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                    </button>
                </form>

                <div class="mt-8 pt-6 border-t border-slate-200">
                    <p class="text-center text-slate-500 text-sm">
                        ¿Ya tienes cuenta?
                        <a href="{{ route('login') }}" class="font-semibold text-emerald-600 hover:text-emerald-700 hover:underline ml-1">Iniciar sesión</a>
                    </p>
                </div>
            </div>
        </div>
    </div>

    @include('partials.footer')


    <script>
        function togglePassword(inputId) {
            const input = document.getElementById(inputId);
            const eyeIcon = document.getElementById('eye-' + inputId);
            if (input.type === 'password') {
                input.type = 'text';
                eyeIcon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>';
            } else {
                input.type = 'password';
                eyeIcon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>';
            }
        }
    </script>

</body>
</html>
