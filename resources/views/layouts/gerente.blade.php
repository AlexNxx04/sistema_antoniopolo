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
                            50: '#ecfdf5',
                            100: '#d1fae5',
                            200: '#a7f3d0',
                            300: '#6ee7b7',
                            400: '#34d399',
                            500: '#10b981',
                            600: '#059669',
                            700: '#047857',
                            800: '#065f46',
                            900: '#064e3b',
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
            --primary: #059669;
        }
        
        /* Sidebar - Verde profesional */
        .sidebar-gradient {
            background: linear-gradient(180deg, #065f46 0%, #047857 50%, #034d3a 100%);
        }
        
        /* Scrollbar */
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
            background: #10b981;
            border-radius: 6px;
            border: 2px solid rgba(255,255,255,0.3);
        }
        
        .custom-scroll::-webkit-scrollbar-thumb:hover {
            background: #059669;
        }
        
        /* Sidebar links */
        .sidebar-link {
            transition: all 0.2s ease;
            border-left: 3px solid transparent;
            position: relative;
        }
        
        .sidebar-link:hover { background: rgba(255, 255, 255, 0.06); }
        .sidebar-link.active { background: rgba(255, 255, 255, 0.12); border-left-color: #34d399; }
        
        /* Cards - Con borde verde arriba vibrantes */
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
            background: linear-gradient(90deg, #059669, #10b981);
        }
        
        .modern-card:hover { box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1); transform: translateY(-1px); transition: all 0.3s ease; }
        
        /* Buttons - Verde profesional vibrantes */
        .btn-modern {
            background: linear-gradient(135deg, #059669, #047857);
            border: none;
            border-radius: 12px;
            padding: 12px 20px;
            color: white;
            font-weight: 600;
            font-size: 14px;
            transition: all 0.2s ease;
            box-shadow: 0 2px 8px rgba(5, 150,105, 0.3);
        }
        
        .btn-modern:hover { 
            background: linear-gradient(135deg, #047857, #065f46); 
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(5,150,105, 0.4);
        }
        
        /* Inputs modernos */
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
            border-color: #059669; 
            outline: none; 
            background: #fff;
            box-shadow: 0 0 0 4px rgba(5,150,105, 0.1);
        }
        
        /* Badges - Verde profesional (UNIFICADO con gradientes) */
        .badge-modern { padding: 6px 12px; border-radius: 20px; font-size: 12px; font-weight: 600; display: inline-flex; align-items: center; gap: 5px; }
        .badge-success { background: linear-gradient(135deg, #d1fae5, #a7f3d0); color: #047857; }
        .badge-warning { background: linear-gradient(135deg, #fef3c7, #fde68a); color: #92400e; }
        .badge-danger { background: linear-gradient(135deg, #fee2e2, #fecaca); color: #991b1b; }
        .badge-info { background: linear-gradient(135deg, #dbeafe, #bfdbfe); color: #1e40af; }
        
        /* Animations profesionales */
        .fade-in { animation: fadeIn 0.3s ease-out forwards; opacity: 0; }
        @keyframes fadeIn { from { opacity: 0; transform: translateY(5px); } to { opacity: 1; transform: translateY(0); } }
        
        /* Avatar */
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
        
        /* Table - Verde profesional (UNIFICADO) */
        .table-modern, .table-striped { width: 100%; border-collapse: separate; border-spacing: 0; }
        .table-modern thead th, .table-striped thead th {
            background: linear-gradient(135deg, #059669, #047857);
            color: white;
            padding: 14px 16px;
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            border: none;
        }
        .table-modern thead th:first-child, .table-striped thead th:first-child { border-radius: 12px 0 0 0; }
        .table-modern thead th:last-child, .table-striped thead th:last-child { border-radius: 0 12px 0 0; }
        .table-modern tbody tr:hover, .table-striped tbody tr:hover { background: linear-gradient(90deg, #ecfdf5, #f0fdf4); }
        .table-modern tbody td, .table-striped tbody td { padding: 14px 16px; border-bottom: 1px solid #e5e7eb; }
        
        /* Gradient text */
        .gradient-text { color: #059669; font-weight: 700; }
        
        /* Notification badge */
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
        
        /* Gradient card */
        .gradient-card {
            background: linear-gradient(135deg, #059669, #047857);
            border-radius: 10px;
            padding: 14px;
        }
        
        /* Glass card */
        .glass-card {
            background: white;
            border-radius: 12px;
            border: 1px solid #e5e7eb;
            box-shadow: 0 1px 3px rgba(0,0,0,0.05);
        }
        
        /* Button gradient */
        .btn-gradient {
            background: linear-gradient(135deg, #059669, #047857);
            border: none;
            border-radius: 8px;
            padding: 10px 18px;
            color: white;
            font-weight: 500;
        }
        
        .btn-gradient:hover { background: linear-gradient(135deg, #047857, #065f46); }
        
        /* Badge gradient */
        .badge-gradient {
            background: linear-gradient(135deg, #d1fae5, #a7f3d0);
            color: #047857;
            padding: 4px 10px;
            border-radius: 6px;
            font-weight: 500;
        }
            background: #d1fae5;
            color: #047857;
            padding: 3px 8px;
            border-radius: 4px;
            font-size: 11px;
        }
        
        /* Table striped */
        .table-striped {
            width: 100%;
            border-collapse: collapse;
        }
        
        .table-striped thead th {
            background: #f3f4f6;
            color: #374151;
            padding: 10px;
            font-size: 12px;
            font-weight: 600;
            border-bottom: 1px solid #d1d5db;
        }
        
        .table-striped tbody tr:hover { background: #f3f4f6; }
        .table-striped tbody td { padding: 10px; border-bottom: 1px solid #e5e7eb; }
        
        /* Table unificada */
        .dashboard-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
        }
        
        .dashboard-table thead th {
            background: linear-gradient(135deg, #059669, #047857);
            color: white;
            padding: 14px 16px;
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            border: none;
        }
        
        .dashboard-table thead th:first-child {
            border-radius: 12px 0 0 0;
        }
        
        .dashboard-table thead th:last-child {
            border-radius: 0 12px 0 0;
        }
        
        .dashboard-table tbody tr {
            transition: all 0.2s ease;
        }
        
        .dashboard-table tbody tr:hover {
            background: linear-gradient(90deg, #ecfdf5, #f0fdf4);
        }
        
        .dashboard-table tbody td {
            padding: 14px 16px;
            border-bottom: 1px solid #e5e7eb;
            font-size: 14px;
            color: #374151;
        }
        
        .dashboard-table tbody tr:last-child td {
            border-bottom: none;
        }
        
        .dashboard-table tbody tr:last-child td:first-child {
            border-radius: 0 0 0 12px;
        }
        
        .dashboard-table tbody tr:last-child td:last-child {
            border-radius: 0 0 12px 0;
        }
        
        /* Badge unificado */
        .badge-unified {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
        }
        
        .badge-unified-success {
            background: linear-gradient(135deg, #d1fae5, #a7f3d0);
            color: #047857;
        }
        
        .badge-unified-warning {
            background: linear-gradient(135deg, #fef3c7, #fde68a);
            color: #92400e;
        }
        
        .badge-unified-danger {
            background: linear-gradient(135deg, #fee2e2, #fecaca);
            color: #991b1b;
        }
        
        .badge-unified-info {
            background: linear-gradient(135deg, #dbeafe, #bfdbfe);
            color: #1e40af;
        }
        
        .badge-unified-neutral {
            background: linear-gradient(135deg, #f3f4f6, #e5e7eb);
            color: #374151;
        }
    </style>
    @stack('styles')
</head>
<body class="h-full bg-slate-50 antialiased">

<div x-data="{ sidebarCollapsed: false, mobileMenuOpen: false, userMenuOpen: false, notificationOpen: false }" class="min-h-screen flex">
    
    {{-- Overlay móvil --}}
    <div x-show="mobileMenuOpen" 
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         @click="mobileMenuOpen = false" 
         class="fixed inset-0 bg-black/60 z-40 lg:hidden backdrop-blur-sm"></div>
    
    {{-- Sidebar --}}
    <aside :class="sidebarCollapsed ? 'w-20' : 'w-72'" 
           x-show="mobileMenuOpen || window.innerWidth >= 1024"
           class="fixed inset-y-0 left-0 z-50 sidebar-gradient text-white transition-all duration-300 flex flex-col shadow-2xl"
           :class="mobileMenuOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'">
        
        {{-- Logo --}}
        <div class="h-22 flex items-center px-5 border-b border-white/10" style="height: 90px;">
            <div class="flex items-center gap-3 w-full">
                <img src="{{ asset('images/logo-antonio-polo.png') }}" alt="Antonio Polo" 
                     class="w-14 h-14 rounded-xl object-contain bg-white/10 p-2 shadow-lg">
                <div class="overflow-hidden">
                    <h1 class="font-bold text-lg tracking-tight text-white">Antonio Polo S.A.</h1>
                    <p class="text-xs text-emerald-300/80">Transporte Mixto</p>
                </div>
            </div>
        </div>

        {{-- Navegación --}}
        <nav class="flex-1 py-4 overflow-y-auto custom-scroll">
            
            <div class="px-4 mb-3">
                <span x-show="!sidebarCollapsed" class="text-[10px] font-bold text-emerald-300/50 uppercase tracking-[0.2em]">
                    Principal
                </span>
            </div>
            
            <a href="{{ route('gerente.dashboard') }}" 
               class="sidebar-link flex items-center gap-3 px-5 py-3.5 {{ request()->routeIs('gerente.dashboard') ? 'active' : '' }}">
                <i class="fas fa-home w-6 text-center text-lg"></i>
                <span x-show="!sidebarCollapsed" class="whitespace-nowrap font-medium">Inicio</span>
            </a>

            <div class="px-4 mt-6 mb-3">
                <span x-show="!sidebarCollapsed" class="text-[10px] font-bold text-emerald-300/50 uppercase tracking-[0.2em]">
                    Gestión
                </span>
            </div>

            <a href="{{ route('gerente.usuarios.index') }}" 
               class="sidebar-link flex items-center gap-3 px-5 py-3.5 {{ request()->routeIs('gerente.usuarios.*') ? 'active' : '' }}">
                <i class="fas fa-users w-6 text-center text-lg"></i>
                <span x-show="!sidebarCollapsed" class="whitespace-nowrap font-medium">Usuarios</span>
            </a>

            <a href="{{ route('gerente.unidades.index') }}" 
               class="sidebar-link flex items-center gap-3 px-5 py-3.5 {{ request()->routeIs('gerente.unidades.*') ? 'active' : '' }}">
                <i class="fas fa-bus w-6 text-center text-lg"></i>
                <span x-show="!sidebarCollapsed" class="whitespace-nowrap font-medium">Unidades</span>
            </a>

            <div class="px-4 mt-6 mb-3">
                <span x-show="!sidebarCollapsed" class="text-[10px] font-bold text-emerald-300/50 uppercase tracking-[0.2em]">
                    Operación
                </span>
            </div>

            <a href="{{ route('gerente.operacion.index') }}" 
               class="sidebar-link flex items-center gap-3 px-5 py-3.5 {{ request()->routeIs('gerente.operacion.*') || request()->routeIs('gerente.turnos.*') ? 'active' : '' }}">
                <i class="fas fa-calendar-check w-6 text-center text-lg"></i>
                <span x-show="!sidebarCollapsed" class="whitespace-nowrap font-medium">Operación Diaria</span>
            </a>

            <a href="{{ route('gerente.gps.mapa') }}" 
               class="sidebar-link flex items-center gap-3 px-5 py-3.5 {{ request()->routeIs('gerente.gps.*') ? 'active' : '' }}">
                <i class="fas fa-map-marker-alt w-6 text-center text-lg"></i>
                <span x-show="!sidebarCollapsed" class="whitespace-nowrap font-medium">GPS - Tiempo Real</span>
            </a>

            <a href="{{ route('gerente.multas.index') }}" 
               class="sidebar-link flex items-center gap-3 px-5 py-3.5 {{ request()->routeIs('gerente.multas.*') ? 'active' : '' }}">
                <i class="fas fa-exclamation-triangle w-6 text-center text-lg"></i>
                <span x-show="!sidebarCollapsed" class="whitespace-nowrap font-medium">Multas</span>
            </a>

            <a href="{{ route('gerente.reportes.index') }}" 
               class="sidebar-link flex items-center gap-3 px-5 py-3.5 {{ request()->routeIs('gerente.reportes.index') ? 'active' : '' }}">
                <i class="fas fa-chart-bar w-6 text-center text-lg"></i>
                <span x-show="!sidebarCollapsed" class="whitespace-nowrap font-medium">Reportes</span>
            </a>

            <a href="{{ route('gerente.auditoria.index') }}" 
               class="sidebar-link flex items-center gap-3 px-5 py-3.5 {{ request()->routeIs('gerente.auditoria.*') ? 'active' : '' }}">
                <i class="fas fa-history w-6 text-center text-lg"></i>
                <span x-show="!sidebarCollapsed" class="whitespace-nowrap font-medium">Historial</span>
            </a>

            <div class="px-4 mt-6 mb-3">
                <span x-show="!sidebarCollapsed" class="text-[10px] font-bold text-emerald-300/50 uppercase tracking-[0.2em]">
                    Sistema
                </span>
            </div>

            <a href="{{ route('gerente.parametros.index') }}" 
               class="sidebar-link flex items-center gap-3 px-5 py-3.5 {{ request()->routeIs('gerente.parametros.*') ? 'active' : '' }}">
                <i class="fas fa-cogs w-6 text-center text-lg"></i>
                <span x-show="!sidebarCollapsed" class="whitespace-nowrap font-medium">Parámetros</span>
            </a>

            <a href="{{ route('gerente.notificaciones.index') }}" 
               class="sidebar-link flex items-center gap-3 px-5 py-3.5 {{ request()->routeIs('gerente.notificaciones.*') ? 'active' : '' }}">
                <i class="fas fa-bell w-6 text-center text-lg"></i>
                <span x-show="!sidebarCollapsed" class="whitespace-nowrap font-medium">Notificaciones</span>
            </a>
        </nav>

        {{-- Usuario --}}
        <div class="p-4 border-t border-white/10">
            <div x-data="{ open: false }" class="relative">
                <button @click="open = !open" class="w-full flex items-center gap-3 p-2.5 rounded-xl hover:bg-white/10 transition-all duration-300 group">
                    <div class="avatar bg-gradient-to-br from-emerald-400 to-emerald-600 group-hover:scale-110 transition-transform">
                        {{ substr(Auth::user()->nombres ?? 'A', 0, 1) }}{{ substr(Auth::user()->apellidos ?? 'U', 0, 1) }}
                    </div>
                    <div x-show="!sidebarCollapsed" class="flex-1 text-left min-w-0">
                        <p class="text-sm font-semibold truncate">{{ Auth::user()->nombres ?? 'Usuario' }} {{ Auth::user()->apellidos ?? '' }}</p>
                        <p class="text-xs text-emerald-300/70 truncate">{{ Auth::user()->rol->nombre ?? 'Gerente' }}</p>
                    </div>
                    <i x-show="!sidebarCollapsed" x-cloak class="fas fa-chevron-up text-xs text-emerald-300/70 transition-transform duration-300" :class="open ? 'rotate-180' : ''"></i>
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
                    <a href="{{ route('gerente.perfil.index') }}" class="flex items-center gap-3 px-4 py-3.5 text-gray-700 hover:bg-emerald-50 transition-colors">
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

    {{-- Contenido Principal --}}
    <div :class="sidebarCollapsed ? 'lg:ml-20' : 'lg:ml-72'" class="flex-1 transition-all duration-300 min-h-screen">
        
        {{-- Top Bar --}}
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
                {{-- Fecha actual --}}
                <div class="hidden md:flex items-center gap-2 px-4 py-2 bg-gradient-to-r from-emerald-50 to-green-50 rounded-xl border border-emerald-100">
                    <div class="w-2 h-2 bg-emerald-500 rounded-full animate-pulse"></div>
                    <span class="text-xs font-semibold text-emerald-700">{{ ucfirst(\Carbon\Carbon::now()->locale('es')->translatedFormat('d F Y')) }}</span>
                </div>
                
                {{-- Notificaciones --}}
                <div x-data="{ 
                    open: false, 
                    verOpen: false, 
                    notifActual: {}, 
                    async abrirNotificacion(id, mensaje, tipo, fecha, leida, url) {
                        this.notifActual = { id, mensaje, tipo, fecha, leida, notifUrl: url };
                        this.verOpen = true;
                        this.open = false;
                        if (!leida) {
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
                        <div class="p-4 border-b border-gray-100 bg-gradient-to-r from-emerald-50 to-white flex items-center justify-between">
                            <h3 class="font-bold text-gray-800"><i class="fas fa-bell text-emerald-500 mr-2"></i>Notificaciones</h3>
                            @if(isset($notificacionesNoLeidas) && $notificacionesNoLeidas > 0)
                            <form action="{{ route('gerente.notificaciones.leerTodas') }}" method="POST">
                                @csrf
                                <button type="submit" class="text-xs text-emerald-600 hover:text-emerald-700 font-medium">
                                    Marcar todo leído
                                </button>
                            </form>
                            @endif
                        </div>
                        <div class="max-h-96 overflow-y-auto custom-scroll">
                            @if(isset($notificaciones) && $notificaciones->count() > 0)
                                @foreach($notificaciones as $notif)
                                <div @click="abrirNotificacion('{{ $notif->notif_id }}', '{{ addslashes($notif->mensaje) }}', '{{ $notif->tipo }}', '{{ $notif->enviado_at?->format("d/m/Y H:i") }}', {{ $notif->leido ? 'true' : 'false' }}, '{{ route('gerente.notificaciones.leer', $notif->notif_id) }}')" 
                                     class="p-4 border-b border-gray-50 hover:bg-emerald-50/50 transition-colors cursor-pointer {{ !$notif->leido ? 'bg-emerald-50/30' : '' }}">
                                    <div class="flex items-start gap-3">
                                        <div class="w-10 h-10 rounded-xl flex items-center justify-center 
                                            @if($notif->tipo == 'documento_vencido') bg-red-100 text-red-600
                                            @elseif($notif->tipo == 'sancion') bg-amber-100 text-amber-600
                                            @else bg-blue-100 text-blue-600 @endif">
                                            <i class="fas 
                                                @if($notif->tipo == 'documento_vencido') fa-file-excel
                                                @elseif($notif->tipo == 'sancion') fa-gavel
                                                @else fa-bell @endif"></i>
                                        </div>
                                        <div class="flex-1">
                                            <p class="text-sm font-semibold text-gray-800">{{ Str::limit($notif->mensaje, 55) }}</p>
                                            <p class="text-xs text-gray-400 mt-1">
                                                <span class="text-emerald-600">
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
                                        <div class="w-2 h-2 bg-emerald-500 rounded-full flex-shrink-0"></div>
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
                        <a href="{{ route('gerente.notificaciones.index') }}" class="block p-3 text-center text-sm font-semibold text-emerald-600 hover:bg-emerald-50 border-t">
                            Ver todas <i class="fas fa-arrow-right ml-1"></i>
                        </a>
                    </div>

                    {{-- Modal de ver notificación --}}
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
                                <div class="p-6 border-b border-gray-100 bg-gradient-to-r from-emerald-50 to-white">
                                    <div class="flex items-center gap-3">
                                        <div class="w-12 h-12 rounded-xl flex items-center justify-center"
                                             :class="notifActual.tipo === 'documento_vencido' ? 'bg-red-100 text-red-600' : (notifActual.tipo === 'sancion' ? 'bg-amber-100 text-amber-600' : 'bg-blue-100 text-blue-600')">
                                            <i class="fas text-xl" :class="notifActual.tipo === 'documento_vencido' ? 'fa-file-excel' : (notifActual.tipo === 'sancion' ? 'fa-gavel' : 'fa-bell')"></i>
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
                                            <i class="fas fa-clock text-emerald-500 mr-1"></i>Fecha y Hora
                                        </label>
                                        <p class="text-gray-800 bg-gray-50 p-3 rounded-xl" x-text="notifActual.fecha"></p>
                                    </div>
                                    <div class="mb-4">
                                        <label class="block text-xs font-semibold text-gray-500 uppercase mb-1">
                                            <i class="fas fa-tag text-emerald-500 mr-1"></i>Tipo de Notificación
                                        </label>
                                        <span class="badge-modern badge-info" x-text="notifActual.tipo === 'llamado_turno' ? 'Llamado de Turno' : (notifActual.tipo === 'sancion' ? 'Sanción' : (notifActual.tipo === 'documento_vencido' ? 'Documento Vencido' : 'Alerta General'))"></span>
                                    </div>
                                    <div class="mb-4">
                                        <label class="block text-xs font-semibold text-gray-500 uppercase mb-1">
                                            <i class="fas fa-envelope text-emerald-500 mr-1"></i>Mensaje
                                        </label>
                                        <p class="text-gray-800 bg-emerald-50 p-4 rounded-xl border border-emerald-100" x-text="notifActual.mensaje"></p>
                                    </div>
                                    <div class="mb-4">
                                        <label class="block text-xs font-semibold text-gray-500 uppercase mb-1">
                                            <i class="fas fa-toggle-on text-emerald-500 mr-1"></i>Estado
                                        </label>
                                        <span class="badge-modern" :class="notifActual.leida ? 'badge-success' : 'badge-warning'" x-text="notifActual.leida ? 'Leída' : 'Sin leer'"></span>
                                    </div>
                                </div>
                                <div class="p-4 border-t border-gray-100 flex justify-end gap-3">
                                    <button @click="verOpen = false" class="btn-modern">
                                        Cerrar
                                    </button>
                                    <form x-show="!notifActual.leida" :action="notifActual.notifUrl" method="POST" class="inline">
                                        @csrf
                                        <button type="submit" class="btn-modern flex items-center gap-2">
                                            <i class="fas fa-check"></i> Marcar leída
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </template>
                </div>
            </div>
        </header>

        {{-- Contenido Página --}}
        <main class="p-6 overflow-y-auto custom-scroll" style="max-height: calc(100vh - 72px);">
            @yield('content')
        </main>
    </div>
</div>

@stack('scripts')
</body>
</html>