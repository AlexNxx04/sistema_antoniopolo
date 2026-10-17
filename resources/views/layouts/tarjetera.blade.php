<!DOCTYPE html>
<html lang="es" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') - Antonio Polo S.A.</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: {
                            50: '#fffbeb',
                            100: '#fef3c7',
                            200: '#fde68a',
                            300: '#fcd34d',
                            400: '#fbbf24',
                            500: '#f59e0b',
                            600: '#d97706',
                            700: '#b45309',
                        },
                    },
                    fontFamily: {
                        sans: ['Inter', 'sans-serif'],
                    }
                }
            }
        }
    </script>
    <style>
        * { font-family: 'Inter', sans-serif; }
        [x-cloak] { display: none !important; }
        
        :root {
            --primary: #f59e0b;
        }
        
        .sidebar-gradient {
            background: linear-gradient(180deg, #b45309 0%, #d97706 50%, #b45309 100%);
        }
        
        .custom-scroll::-webkit-scrollbar {
            width: 12px;
            height: 12px;
            opacity: 0;
            transition: opacity 0.3s ease;
        }
        
        .custom-scroll:hover::-webkit-scrollbar {
            opacity: 1;
        }
        
        .custom-scroll::-webkit-scrollbar-track {
            background: rgba(0,0,0,0.1);
            border-radius: 6px;
        }
        
        .custom-scroll::-webkit-scrollbar-thumb {
            background: #f59e0b;
            border-radius: 6px;
            border: 2px solid rgba(255,255,255,0.3);
        }
        
        .custom-scroll::-webkit-scrollbar-thumb:hover {
            background: #d97706;
        }
        
        .sidebar-link {
            transition: all 0.2s ease;
            border-left: 3px solid transparent;
            position: relative;
        }
        
        .sidebar-link:hover { background: rgba(255, 255, 255, 0.06); }
        .sidebar-link.active { background: rgba(255, 255, 255, 0.12); border-left-color: #fcd34d; }
        
        .modern-card {
            background: linear-gradient(145deg, #ffffff, #f8fafc);
            border-radius: 16px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.06);
            border: 1px solid #e2e8f0;
            position: relative;
            overflow: hidden;
        }
        
        .modern-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, #f59e0b, #fbbf24);
        }
        
        .modern-card:hover { box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1); transform: translateY(-1px); transition: all 0.3s ease; }
        
        .btn-modern {
            background: linear-gradient(135deg, #f59e0b, #d97706);
            border: none;
            border-radius: 12px;
            padding: 12px 20px;
            color: white;
            font-weight: 600;
            font-size: 14px;
            transition: all 0.2s ease;
            box-shadow: 0 2px 8px rgba(245, 158, 11, 0.3);
        }
        
        .btn-modern:hover { 
            background: linear-gradient(135deg, #d97706, #b45309); 
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(245, 158, 11, 0.4);
        }
        
        .input-modern {
            border: 2px solid #e2e8f0;
            border-radius: 12px;
            padding: 14px 16px;
            width: 100%;
            font-size: 14px;
            transition: all 0.2s ease;
            background: #f8fafc;
        }
        
        .input-modern:focus { 
            border-color: #f59e0b; 
            outline: none; 
            background: #fff;
            box-shadow: 0 0 0 4px rgba(245, 158, 11, 0.1);
        }
        
        .badge-modern { padding: 6px 12px; border-radius: 20px; font-size: 12px; font-weight: 600; display: inline-flex; align-items: center; gap: 5px; }
        .badge-success { background: linear-gradient(135deg, #d1fae5, #a7f3d0); color: #047857; }
        .badge-warning { background: linear-gradient(135deg, #fef3c7, #fde68a); color: #92400e; }
        .badge-danger { background: linear-gradient(135deg, #fee2e2, #fecaca); color: #991b1b; }
        .badge-info { background: linear-gradient(135deg, #dbeafe, #bfdbfe); color: #1e40af; }
        
        .fade-in { animation: fadeIn 0.3s ease-out forwards; opacity: 0; }
        @keyframes fadeIn { from { opacity: 0; transform: translateY(5px); } to { opacity: 1; transform: translateY(0); } }
        
        .avatar {
            width: 44px;
            height: 44px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 14px;
            color: white;
        }
        
        .table-modern { width: 100%; border-collapse: separate; border-spacing: 0; }
        .table-modern thead th {
            background: linear-gradient(135deg, #f59e0b, #d97706);
            color: white;
            padding: 14px 16px;
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            border: none;
        }
        .table-modern thead th:first-child { border-radius: 12px 0 0 0; }
        .table-modern thead th:last-child { border-radius: 0 12px 0 0; }
        .table-modern tbody tr:hover { background: linear-gradient(90deg, #fffbeb, #fef3c7); }
        .table-modern tbody td { padding: 14px 16px; border-bottom: 1px solid #e5e7eb; }
        
        .notification-dot {
            position: absolute;
            top: 8px;
            right: 8px;
            width: 10px;
            height: 10px;
            background: #ef4444;
            border-radius: 50%;
            border: 2px solid white;
        }
        
        .gradient-card {
            background: linear-gradient(135deg, #f59e0b, #d97706);
            border-radius: 10px;
            padding: 14px;
        }
        
        .user-item { display: flex; align-items: center; gap: 16px; padding: 16px 20px; border-radius: 14px; transition: all 0.2s ease; margin: 4px 12px; }
        .user-item:hover { background: linear-gradient(90deg, #fffbeb, #fef3c7); transform: translateX(4px); box-shadow: 0 4px 15px rgba(245, 158, 11, 0.15); }
        .user-avatar { width: 52px; height: 52px; border-radius: 14px; display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 16px; color: white; box-shadow: 0 4px 12px rgba(0,0,0,0.15); }
        .status-pill { padding: 8px 16px; border-radius: 24px; font-size: 12px; font-weight: 700; display: inline-flex; align-items: center; gap: 6px; }
        .pill-success { background: linear-gradient(135deg, #d1fae5, #a7f3d0); color: #047857; box-shadow: 0 2px 8px rgba(5, 150, 105, 0.2); }
        .pill-warning { background: linear-gradient(135deg, #fef3c7, #fde68a); color: #92400e; box-shadow: 0 2px 8px rgba(217, 119, 6, 0.2); }
        .pill-danger { background: linear-gradient(135deg, #fee2e2, #fecaca); color: #991b1b; box-shadow: 0 2px 8px rgba(220, 38, 38, 0.2); }
    </style>
    @stack('styles')
</head>
<body class="h-full bg-slate-50 antialiased">

<div x-data="{ sidebarCollapsed: false, mobileMenuOpen: false, userMenuOpen: false, notificationOpen: false }" class="min-h-screen flex">
    
    <div x-show="mobileMenuOpen" 
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         @click="mobileMenuOpen = false" 
         class="fixed inset-0 bg-black/60 z-40 lg:hidden backdrop-blur-sm"></div>
    
    <aside :class="sidebarCollapsed ? 'w-20' : 'w-72'" 
           x-show="mobileMenuOpen || window.innerWidth >= 1024"
           class="fixed inset-y-0 left-0 z-50 sidebar-gradient text-white transition-all duration-300 flex flex-col shadow-2xl"
           :class="mobileMenuOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'">
        
        <div class="h-22 flex items-center px-5 border-b border-white/10" style="height: 90px;">
            <div class="flex items-center gap-3 w-full">
                <img src="{{ asset('images/logo-antonio-polo.png') }}" alt="Antonio Polo" 
                     class="w-14 h-14 rounded-xl object-contain bg-white/10 p-2 shadow-lg">
                <div class="overflow-hidden">
                    <h1 class="font-bold text-lg tracking-tight text-white">Antonio Polo S.A.</h1>
                    <p class="text-xs text-amber-300/80">Transporte Mixto</p>
                </div>
            </div>
        </div>

        <div x-show="!sidebarCollapsed" class="px-5 py-3 bg-white/10 border-b border-white/20">
            <p class="text-xs text-white/70 mb-1">Punto de operación</p>
            <p class="font-semibold truncate"><i class="fas fa-map-marker-alt mr-2"></i>{{ Auth::user()->punto->nombre ?? 'Sin asignar' }}</p>
        </div>

        <nav class="flex-1 py-4 overflow-y-auto custom-scroll">
            
            <div class="px-4 mb-3">
                <span x-show="!sidebarCollapsed" class="text-[10px] font-bold text-amber-300/50 uppercase tracking-[0.2em]">
                    Principal
                </span>
            </div>
            
            <a href="{{ route('tarjetera.dashboard') }}" 
               class="sidebar-link flex items-center gap-3 px-5 py-3.5 {{ request()->routeIs('tarjetera.dashboard') ? 'active' : '' }}">
                <i class="fas fa-home w-6 text-center text-lg"></i>
                <span x-show="!sidebarCollapsed" class="whitespace-nowrap font-medium">Dashboard</span>
            </a>

            <div class="px-4 mt-6 mb-3">
                <span x-show="!sidebarCollapsed" class="text-[10px] font-bold text-amber-300/50 uppercase tracking-[0.2em]">
                    Gestión
                </span>
            </div>
            
            <a href="{{ route('tarjetera.ciclos.index') }}" class="sidebar-link flex items-center gap-3 px-5 py-3.5 {{ request()->routeIs('tarjetera.ciclos.*') ? 'active' : '' }}">
                <i class="fas fa-calendar-alt w-6 text-center text-lg"></i>
                <span x-show="!sidebarCollapsed" class="whitespace-nowrap font-medium">Ciclos</span>
            </a>
            
            <a href="{{ route('tarjetera.sorteo.index') }}" class="sidebar-link flex items-center gap-3 px-5 py-3.5 {{ request()->routeIs('tarjetera.sorteo.*') ? 'active' : '' }}">
                <i class="fas fa-dice w-6 text-center text-lg"></i>
                <span x-show="!sidebarCollapsed" class="whitespace-nowrap font-medium">Sorteo Diario</span>
            </a>
            
            <a href="{{ route('tarjetera.turnos.index') }}" class="sidebar-link flex items-center gap-3 px-5 py-3.5 {{ request()->routeIs('tarjetera.turnos.*') ? 'active' : '' }}">
                <i class="fas fa-clipboard-list w-6 text-center text-lg"></i>
                <span x-show="!sidebarCollapsed" class="whitespace-nowrap font-medium">Turnos del Día</span>
            </a>
            
            <a href="{{ route('tarjetera.gps.index') }}" class="sidebar-link flex items-center gap-3 px-5 py-3.5">
                <i class="fas fa-map-marker-alt w-6 text-center text-lg"></i>
                <span x-show="!sidebarCollapsed" class="whitespace-nowrap font-medium">GPS - Tiempo Real</span>
            </a>
            
            <a href="{{ route('tarjetera.justificativos.index') }}" class="sidebar-link flex items-center gap-3 px-5 py-3.5">
                <i class="fas fa-file-signature w-6 text-center text-lg"></i>
                <span x-show="!sidebarCollapsed" class="whitespace-nowrap font-medium">Justificativos</span>
            </a>

            <div class="px-4 mt-6 mb-3">
                <span x-show="!sidebarCollapsed" class="text-[10px] font-bold text-amber-300/50 uppercase tracking-[0.2em]">
                    Gestión
                </span>
            </div>
            
            <a href="{{ route('tarjetera.multas.index') }}" class="sidebar-link flex items-center gap-3 px-5 py-3.5">
                <i class="fas fa-money-check-alt w-6 text-center text-lg"></i>
                <span x-show="!sidebarCollapsed" class="whitespace-nowrap font-medium">Multas</span>
            </a>
            
            <a href="{{ route('tarjetera.documentos.index') }}" class="sidebar-link flex items-center gap-3 px-5 py-3.5">
                <i class="fas fa-id-card w-6 text-center text-lg"></i>
                <span x-show="!sidebarCollapsed" class="whitespace-nowrap font-medium">Validar Documentos</span>
            </a>

            <div class="px-4 mt-6 mb-3">
                <span x-show="!sidebarCollapsed" class="text-[10px] font-bold text-amber-300/50 uppercase tracking-[0.2em]">
                    Información
                </span>
            </div>
            
            <a href="{{ route('tarjetera.historial.index') }}" class="sidebar-link flex items-center gap-3 px-5 py-3.5">
                <i class="fas fa-calendar w-6 text-center text-lg"></i>
                <span x-show="!sidebarCollapsed" class="whitespace-nowrap font-medium">Historial Turnos</span>
            </a>
            
            <a href="{{ route('tarjetera.conductores.index') }}" class="sidebar-link flex items-center gap-3 px-5 py-3.5">
                <i class="fas fa-users w-6 text-center text-lg"></i>
                <span x-show="!sidebarCollapsed" class="whitespace-nowrap font-medium">Conductores</span>
            </a>
            
            <a href="{{ route('tarjetera.unidades.index') }}" class="sidebar-link flex items-center gap-3 px-5 py-3.5">
                <i class="fas fa-car w-6 text-center text-lg"></i>
                <span x-show="!sidebarCollapsed" class="whitespace-nowrap font-medium">Unidades</span>
            </a>
        </nav>

        <div class="p-4 border-t border-white/10">
            <div x-data="{ open: false }" class="relative">
                <button @click="open = !open" class="w-full flex items-center gap-3 p-2.5 rounded-xl hover:bg-white/10 transition-all duration-300 group">
                    <div class="avatar bg-gradient-to-br from-amber-400 to-amber-600 group-hover:scale-110 transition-transform">
                        {{ substr(Auth::user()->nombres ?? 'A', 0, 1) }}{{ substr(Auth::user()->apellidos ?? 'U', 0, 1) }}
                    </div>
                    <div x-show="!sidebarCollapsed" class="flex-1 text-left min-w-0">
                        <p class="text-sm font-semibold truncate">{{ Auth::user()->nombres ?? 'Usuario' }} {{ Auth::user()->apellidos ?? '' }}</p>
                        <p class="text-xs text-amber-300/70 truncate">Tarjetera - {{ Auth::user()->punto->nombre ?? '' }}</p>
                    </div>
                    <i x-show="!sidebarCollapsed" x-cloak class="fas fa-chevron-up text-xs text-amber-300/70 transition-transform duration-300" :class="open ? 'rotate-180' : ''"></i>
                </button>
                
                <div x-show="open" 
                     x-cloak
                     x-transition:enter="transition ease-out duration-200"
                     x-transition:enter-start="opacity-0 transform -translate-y-2"
                     x-transition:enter-end="opacity-100 transform translate-y-0"
                     x-transition:leave="transition ease-in duration-150"
                     x-transition:leave-start="opacity-100 transform translate-y-0"
                     x-transition:leave-end="opacity-0 transform -translate-y-2"
                     @click.outside="open = false"
                     class="absolute bottom-full left-0 right-0 mb-2 bg-white rounded-2xl shadow-2xl overflow-hidden">
                    <a href="{{ route('tarjetera.perfil.index') }}" class="flex items-center gap-3 px-4 py-3.5 text-gray-700 hover:bg-amber-50 transition-colors">
                        <i class="fas fa-user text-gray-400 w-5"></i>
                        <span class="text-sm font-medium">Mi Perfil</span>
                    </a>
                    <hr class="border-gray-100">
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="w-full flex items-center gap-3 px-4 py-3.5 text-red-600 hover:bg-red-50 transition-colors">
                            <i class="fas fa-sign-out-alt w-5"></i>
                            <span class="text-sm font-medium">Cerrar Sesión</span>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </aside>

    <div :class="sidebarCollapsed ? 'lg:ml-20' : 'lg:ml-72'" class="flex-1 transition-all duration-300 min-h-screen">
        
        <header class="h-18 bg-white/80 backdrop-blur-md border-b border-gray-100 flex items-center justify-between px-6 sticky top-0 z-30 shadow-sm">
            <div class="flex items-center gap-4">
                <button @click="mobileMenuOpen = !mobileMenuOpen" class="lg:hidden p-2.5 rounded-xl hover:bg-gray-100 transition-colors">
                    <i class="fas fa-bars text-gray-600 text-lg"></i>
                </button>
                <div>
                    <h1 class="text-xl font-bold text-gray-800">@yield('header', 'Dashboard')</h1>
                    <p class="text-xs text-gray-400 hidden sm:block">
                        <i class="fas fa-bus mr-1"></i> Antonio Polo S.A.
                    </p>
                </div>
            </div>

            <div class="flex items-center gap-3">
                <div class="hidden md:flex items-center gap-2 px-4 py-2 bg-gradient-to-r from-amber-50 to-yellow-50 rounded-xl border border-amber-100">
                    <div class="w-2 h-2 bg-amber-500 rounded-full animate-pulse"></div>
                    <span class="text-xs font-semibold text-amber-700">{{ ucfirst(\Carbon\Carbon::now()->locale('es')->translatedFormat('d F Y')) }}</span>
                </div>
                
                <div x-data="{ 
                    open: false, 
                    verOpen: false, 
                    notifActual: {}, 
                    async abrirNotificacion(id, mensaje, tipo, fecha, leida, url) {
                        this.notifActual = { id, mensaje, tipo, fecha, leida, notifUrl: url };
                        this.verOpen = true;
                        this.open = false;
                        if (!leida && url !== '#') {
                            try {
                                await fetch(url, { method: 'POST', headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' } });
                                this.notifActual.leida = true;
                                window.location.reload();
                            } catch(e) {
                                console.error(e);
                            }
                        }
                    }
                }" class="relative">
                    <button @click="open = !open; notificationOpen = open" class="relative p-2.5 rounded-xl hover:bg-gray-100 transition-colors">
                        <i class="fas fa-bell text-gray-600 text-lg"></i>
                        @if(isset($notificacionesNoLeidas) && $notificacionesNoLeidas > 0)
                            <span class="notification-dot"></span>
                        @endif
                    </button>
                    
                    <div x-show="open" 
                         x-cloak
                         x-transition:enter="transition ease-out duration-200"
                         x-transition:enter-start="opacity-0 scale-95"
                         x-transition:enter-end="opacity-100 scale-100"
                         @click.outside="open = false"
                         class="absolute right-0 top-full mt-2 w-96 bg-white rounded-2xl shadow-2xl overflow-hidden z-50">
                        <div class="p-4 border-b border-gray-100 bg-gradient-to-r from-amber-50 to-white flex items-center justify-between">
                            <h3 class="font-bold text-gray-800"><i class="fas fa-bell text-amber-500 mr-2"></i>Notificaciones</h3>
                            @if(isset($notificacionesNoLeidas) && $notificacionesNoLeidas > 0)
                            <form action="{{ route('tarjetera.notificaciones.leerTodas') }}" method="POST">
                                @csrf
                                <button type="submit" class="text-xs text-amber-600 hover:text-amber-700 font-medium">
                                    Marcar todo leído
                                </button>
                            </form>
                            @endif
                        </div>
                        <div class="max-h-96 overflow-y-auto custom-scroll">
                            @if(isset($notificaciones) && $notificaciones->count() > 0)
                                @foreach($notificaciones as $notif)
                                <div @click="abrirNotificacion('{{ $notif->notif_id }}', '{{ addslashes($notif->mensaje) }}', '{{ $notif->tipo }}', '{{ $notif->enviado_at?->format("d/m/Y H:i") }}', {{ $notif->leido ? 'true' : 'false' }}, '{{ route('tarjetera.notificaciones.leer', $notif->notif_id) }}')" 
                                     class="p-4 border-b border-gray-50 hover:bg-amber-50/50 transition-colors cursor-pointer {{ !$notif->leido ? 'bg-amber-50/30' : '' }}">
                                    <div class="flex items-start gap-3">
                                        <div class="w-10 h-10 rounded-xl flex items-center justify-center 
                                            @if($notif->tipo == 'llamado_turno') bg-amber-100 text-amber-600
                                            @elseif($notif->tipo == 'sancion') bg-red-100 text-red-600
                                            @elseif($notif->tipo == 'documento_vencido') bg-red-100 text-red-600
                                            @else bg-blue-100 text-blue-600 @endif">
                                            <i class="fas 
                                                @if($notif->tipo == 'llamado_turno') fa-clipboard-check
                                                @elseif($notif->tipo == 'sancion') fa-gavel
                                                @elseif($notif->tipo == 'documento_vencido') fa-file-excel
                                                @else fa-bell @endif"></i>
                                        </div>
                                        <div class="flex-1">
                                            <p class="text-sm font-semibold text-gray-800">{{ Str::limit($notif->mensaje, 55) }}</p>
                                            <p class="text-xs text-gray-400 mt-1">
                                                <span class="text-amber-600">
                                                    @if($notif->usuario)
                                                    <i class="fas fa-user mr-1"></i>{{ $notif->usuario->nombres }}
                                                    @else
                                                    <i class="fas fa-cog mr-1"></i>Sistema
                                                    @endif
                                                </span>
                                                <span class="mx-1">•</span>
                                                <span>{{ $notif->enviado_at?->diffForHumans() }}</span>
                                            </p>
                                        </div>
                                        @if(!$notif->leido)
                                        <div class="w-2 h-2 bg-amber-500 rounded-full flex-shrink-0"></div>
                                        @endif
                                    </div>
                                </div>
                                @endforeach
                            @else
                                <div class="p-8 text-center">
                                    <i class="fas fa-bell-slash text-3xl text-gray-200 mb-2"></i>
                                    <p class="text-gray-500">No hay notificaciones</p>
                                </div>
                            @endif
                        </div>
                        <a href="#" class="block p-3 text-center text-sm font-semibold text-amber-600 hover:bg-amber-50 border-t">
                            Ver todas <i class="fas fa-arrow-right ml-1"></i>
                        </a>
                    </div>

                    <template x-teleport="body">
                        <div x-show="verOpen" 
                             x-cloak
                             x-transition:enter="transition ease-out duration-200"
                             x-transition:enter-start="opacity-0"
                             x-transition:enter-end="opacity-100"
                             x-transition:leave="transition ease-in duration-150"
                             x-transition:leave-start="opacity-100"
                             x-transition:leave-end="opacity-0"
                             class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 backdrop-blur-sm p-4"
                             @click="verOpen = false">
                            <div x-show="verOpen"
                                 x-transition:enter="transition ease-out duration-300"
                                 x-transition:enter-start="opacity-0 scale-95 translate-y-4"
                                 x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                                 @click.stop
                                 class="bg-white rounded-2xl shadow-2xl w-full max-w-lg overflow-hidden">
                                <div class="p-6 border-b border-gray-100 bg-gradient-to-r from-amber-50 to-white">
                                    <div class="flex items-center gap-3">
                                        <div class="w-12 h-12 rounded-xl flex items-center justify-center"
                                             :class="notifActual.tipo === 'llamado_turno' ? 'bg-amber-100 text-amber-600' : (notifActual.tipo === 'sancion' ? 'bg-red-100 text-red-600' : 'bg-blue-100 text-blue-600')">
                                            <i class="fas text-xl" :class="notifActual.tipo === 'llamado_turno' ? 'fa-clipboard-check' : (notifActual.tipo === 'sancion' ? 'fa-gavel' : 'fa-bell')"></i>
                                        </div>
                                        <div class="flex-1">
                                            <h3 class="font-bold text-gray-800">Detalle de Notificación</h3>
                                            <p class="text-xs text-gray-500" x-text="notifActual.fecha"></p>
                                        </div>
                                    </div>
                                </div>
                                <div class="p-6">
                                    <div class="mb-4">
                                        <label class="block text-xs font-semibold text-gray-500 uppercase mb-1">
                                            <i class="fas fa-clock text-amber-500 mr-1"></i>Fecha y Hora
                                        </label>
                                        <p class="text-gray-800 bg-gray-50 p-3 rounded-xl" x-text="notifActual.fecha"></p>
                                    </div>
                                    <div class="mb-4">
                                        <label class="block text-xs font-semibold text-gray-500 uppercase mb-1">
                                            <i class="fas fa-tag text-amber-500 mr-1"></i>Tipo de Notificación
                                        </label>
                                        <span class="badge-modern badge-info" x-text="notifActual.tipo === 'llamado_turno' ? 'Llamado de Turno' : (notifActual.tipo === 'sancion' ? 'Sanción' : (notifActual.tipo === 'documento_vencido' ? 'Documento Vencido' : 'Alerta General'))"></span>
                                    </div>
                                    <div class="mb-4">
                                        <label class="block text-xs font-semibold text-gray-500 uppercase mb-1">
                                            <i class="fas fa-envelope text-amber-500 mr-1"></i>Mensaje
                                        </label>
                                        <p class="text-gray-800 bg-amber-50 p-4 rounded-xl border border-amber-100" x-text="notifActual.mensaje"></p>
                                    </div>
                                    <div class="mb-4">
                                        <label class="block text-xs font-semibold text-gray-500 uppercase mb-1">
                                            <i class="fas fa-toggle-on text-amber-500 mr-1"></i>Estado
                                        </label>
                                        <span class="badge-modern" :class="notifActual.leida ? 'badge-success' : 'badge-warning'" x-text="notifActual.leida ? 'Leída' : 'Sin leer'"></span>
                                    </div>
                                </div>
                                <div class="p-4 border-t border-gray-100 flex justify-end gap-3">
                                    <button @click="verOpen = false" class="btn-modern">
                                        Cerrar
                                    </button>
                                </div>
                            </div>
                        </div>
                    </template>
                </div>
            </div>
        </header>

        <main class="p-6 overflow-y-auto custom-scroll" style="max-height: calc(100vh - 72px);">
            @yield('content')
            
            @stack('modales')
        </main>
    </div>
</div>

@stack('scripts')
</body>
</html>