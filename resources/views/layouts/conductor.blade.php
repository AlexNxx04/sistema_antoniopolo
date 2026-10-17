<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="theme-color" content="#059669">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <link rel="manifest" href="/pwa/manifest.json">
    <title>@yield('title', 'Conductor') - SGOA-AP</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>
        * { font-family: 'Poppins', sans-serif; }
        [x-cloak] { display: none !important; }
        body { background: #f0fdf4; }
        .btn-primary {
            background: linear-gradient(135deg, #059669 0%, #047857 100%);
            transition: all 0.3s ease;
        }
        .btn-primary:active { transform: scale(0.98); }
        .card { 
            background: white;
            border-radius: 1.5rem;
            box-shadow: 0 4px 15px rgba(5, 150, 105, 0.1);
        }
        .status-indicator {
            animation: pulse 2s infinite;
        }
        @keyframes pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.5; }
        }
    </style>
    @stack('styles')
</head>
<body class="min-h-screen">
    <div x-data="{ sidebarOpen: false }" class="min-h-screen flex flex-col">
        
        {{-- Header --}}
        <header class="bg-gradient-to-r from-emerald-700 to-emerald-600 text-white px-4 py-4 shadow-lg sticky top-0 z-50">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <img src="{{ asset('images/logo-antonio-polo.png') }}" alt="Logo" class="w-10 h-10 object-contain bg-white rounded-full p-1">
                    <div>
                        <h1 class="font-bold text-lg leading-tight">SGOA-AP</h1>
                        <p class="text-xs text-emerald-200">Conductor</p>
                    </div>
                </div>
                <div class="flex items-center gap-3">
                    @if($notificaciones->count() > 0)
                    <a href="{{ route('conductor.notificaciones') }}" class="relative p-2 bg-white/20 rounded-full hover:bg-white/30 transition-colors">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                        </svg>
                        <span class="absolute -top-1 -right-1 w-5 h-5 bg-red-500 text-white text-xs font-bold rounded-full flex items-center justify-center">{{ $notificaciones->count() }}</span>
                    </a>
                    @endif
                    <form method="POST" action="{{ route('logout') }}" class="inline">
                        @csrf
                        <button type="submit" class="p-2 bg-white/20 rounded-full hover:bg-white/30 transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                            </svg>
                        </button>
                    </form>
                </div>
            </div>
        </header>

        {{-- Main Content --}}
        <main class="flex-1 p-4 pb-24">
            @yield('content')
        </main>

        {{-- Bottom Navigation --}}
        <nav class="fixed bottom-0 left-0 right-0 bg-white border-t border-gray-200 px-4 py-2 z-50">
            <div class="flex justify-around items-center max-w-lg mx-auto">
                <a href="{{ route('conductor.index') }}" class="flex flex-col items-center gap-1 px-4 py-2 {{ request()->routeIs('conductor.index') ? 'text-emerald-600' : 'text-gray-500' }}">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                    </svg>
                    <span class="text-xs font-medium">Inicio</span>
                </a>
                <a href="{{ route('conductor.historial') }}" class="flex flex-col items-center gap-1 px-4 py-2 {{ request()->routeIs('conductor.historial') ? 'text-emerald-600' : 'text-gray-500' }}">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <span class="text-xs font-medium">Historial</span>
                </a>
                <a href="{{ route('conductor.notificaciones') }}" class="flex flex-col items-center gap-1 px-4 py-2 {{ request()->routeIs('conductor.notificaciones') ? 'text-emerald-600' : 'text-gray-500' }}">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                    </svg>
                    <span class="text-xs font-medium">Notificaciones</span>
                </a>
            </div>
        </nav>
    </div>

    <script>
        if ('serviceWorker' in navigator) {
            navigator.serviceWorker.register('/pwa/service-worker.js')
                .then(reg => console.log('SW registered'))
                .catch(err => console.log('SW registration failed:', err));
        }
    </script>
    @stack('scripts')
</body>
</html>
