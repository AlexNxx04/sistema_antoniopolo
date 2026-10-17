<!DOCTYPE html>
<html lang="es" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recuperar Contraseña - Antonio Polo S.A.</title>
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
        .success-animation {
            animation: checkmark 0.5s ease-out;
        }
        @keyframes checkmark {
            0% { transform: scale(0); }
            50% { transform: scale(1.2); }
            100% { transform: scale(1); }
        }
    </style>
</head>
<body class="flex flex-col min-h-screen" style="background: linear-gradient(135deg, #0f172a 0%, #064e3b 50%, #0f172a 100%);">

    <div class="flex-1 flex items-center justify-center p-4 sm:p-6 my-auto">
        <div class="w-full max-w-md rounded-2xl overflow-hidden shadow-2xl" style="background-color: #ffffff;">

            {{-- Header con botón cerrar --}}
            <div class="bg-gradient-to-br from-emerald-600 via-emerald-700 to-emerald-800 p-8 text-white relative" style="background: linear-gradient(135deg, #059669 0%, #047857 50%, #065f46 100%);">
                <a href="{{ route('login') }}" class="absolute top-4 right-4 inline-flex items-center justify-center w-10 h-10 rounded-full bg-white/20 hover:bg-white/30 text-white transition-all duration-300">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </a>
                <div class="text-center">
                    <img class="w-24 h-24 object-contain mx-auto mb-4 drop-shadow-2xl" src="{{ asset('images/logo-antonio-polo.png') }}" alt="Logo">
                    <h1 class="text-2xl font-bold mb-2">Recuperar Contraseña</h1>
                    <p class="text-emerald-100/80 text-sm">Ingresa tu correo para recibir el enlace de recuperación</p>
                </div>
            </div>

            {{-- Formulario --}}
            <div class="p-8">
                
                {{-- Session Status --}}
                @if (session('status'))
                    <div class="mb-6 p-4 bg-emerald-50 border border-emerald-200 rounded-xl text-emerald-700 text-sm flex items-center gap-3 success-animation">
                        <svg class="w-6 h-6 flex-shrink-0 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <span>{{ session('status') }}</span>
                    </div>
                @endif

                <form method="POST" action="{{ route('password.email') }}" class="space-y-5">
                    @csrf

                    <div>
                        <label for="email" class="block text-sm font-semibold text-slate-700 mb-2">Correo electrónico</label>
                        <div class="relative">
                            <input type="email" id="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username"
                                class="input-field w-full pl-11 pr-4 py-3.5 rounded-xl text-slate-900 placeholder-slate-400"
                                placeholder="correo@ejemplo.com">
                            <span class="absolute left-4 top-1/2 -translate-y-1/2 pointer-events-none">
                                <svg class="input-icon w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                            </span>
                        </div>
                        @error('email')
                            <p class="mt-2 text-red-500 text-sm flex items-center gap-1">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    <button type="submit" class="btn-primary w-full py-4 text-white font-bold rounded-xl text-center shadow-lg flex items-center justify-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                        <span>Enviar enlace de recuperación</span>
                    </button>
                </form>

                <div class="mt-6 pt-6 border-t border-slate-200">
                    <p class="text-center text-slate-500 text-sm">
                        ¿Recordaste tu contraseña?
                        <a href="{{ route('login') }}" class="font-semibold text-emerald-600 hover:text-emerald-700 hover:underline ml-1">Volver al login</a>
                    </p>
                </div>
            </div>
        </div>
    </div>

    @include('partials.footer')


</body>
</html>
