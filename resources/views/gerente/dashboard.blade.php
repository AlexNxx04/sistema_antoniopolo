@extends('layouts.gerente')

@section('title', 'Dashboard')
@section('header', 'Panel de Control')

@section('content')
<style>
.dashboard-card { background: linear-gradient(145deg, #ffffff, #f8fafc); border: 1px solid #e2e8f0; border-radius: 20px; transition: all 0.3s ease; overflow: hidden; position: relative; }
.dashboard-card::before { content: ''; position: absolute; top: 0; left: 0; right: 0; height: 4px; background: linear-gradient(90deg, #059669, #10b981); }
.dashboard-card:hover { transform: translateY(-3px); box-shadow: 0 15px 50px rgba(0,0,0,0.12); }
.section-header { display: flex; align-items: center; justify-content: space-between; padding: 20px 24px; border-bottom: 1px solid #f1f5f9; }
.section-header h3 { font-size: 18px; font-weight: 700; color: #1e293b; }
.section-header a { font-size: 13px; font-weight: 600; color: #059669; display: flex; align-items: center; gap: 6px; transition: all 0.2s; }
.section-header a:hover { color: #047857; gap: 10px; }
.action-btn { display: flex; align-items: center; gap: 14px; padding: 16px 20px; border-radius: 14px; background: #f8fafc; border: 1px solid #e2e8f0; transition: all 0.25s ease; position: relative; overflow: hidden; }
.action-btn::before { content: ''; position: absolute; top: 0; left: -100%; width: 100%; height: 100%; background: linear-gradient(135deg, #059669, #047857); transition: all 0.3s ease; }
.action-btn:hover::before { left: 0; }
.action-btn:hover { border-color: transparent; transform: translateX(6px); box-shadow: 0 8px 25px rgba(5, 150, 105, 0.25); }
.action-btn:hover span { color: white; position: relative; z-index: 1; }
.action-btn i { width: 44px; height: 44px; display: flex; align-items: center; justify-content: center; border-radius: 12px; font-size: 18px; position: relative; z-index: 1; transition: all 0.2s; }
.action-btn .icon-users { background: linear-gradient(135deg, #dbeafe, #bfdbfe); color: #2563eb; }
.action-btn .icon-bus { background: linear-gradient(135deg, #ede9fe, #ddd6fe); color: #7c3aed; }
.action-btn .icon-calendar { background: linear-gradient(135deg, #fef3c7, #fde68a); color: #d97706; }
.action-btn .icon-multa { background: linear-gradient(135deg, #fee2e2, #fecaca); color: #dc2626; }
.action-btn:hover .icon-users { background: white; color: #2563eb; }
.action-btn:hover .icon-bus { background: white; color: #7c3aed; }
.action-btn:hover .icon-calendar { background: white; color: #d97706; }
.action-btn:hover .icon-multa { background: white; color: #dc2626; }
.action-btn span { font-size: 15px; font-weight: 600; color: #334155; position: relative; z-index: 1; }
.status-pill { padding: 8px 16px; border-radius: 24px; font-size: 12px; font-weight: 700; display: inline-flex; align-items: center; gap: 6px; }
.pill-success { background: linear-gradient(135deg, #d1fae5, #a7f3d0); color: #047857; box-shadow: 0 2px 8px rgba(5, 150, 105, 0.2); }
.pill-warning { background: linear-gradient(135deg, #fef3c7, #fde68a); color: #92400e; box-shadow: 0 2px 8px rgba(217, 119, 6, 0.2); }
.pill-danger { background: linear-gradient(135deg, #fee2e2, #fecaca); color: #991b1b; box-shadow: 0 2px 8px rgba(220, 38, 38, 0.2); }
.pill-info { background: linear-gradient(135deg, #dbeafe, #bfdbfe); color: #1e40af; box-shadow: 0 2px 8px rgba(30, 64, 175, 0.2); }
.user-item { display: flex; align-items: center; gap: 16px; padding: 16px 20px; border-radius: 14px; transition: all 0.2s ease; margin: 4px 12px; }
.user-item:hover { background: linear-gradient(90deg, #ecfdf5, #d1fae5); transform: translateX(4px); box-shadow: 0 4px 15px rgba(5, 150, 105, 0.15); }
.user-avatar { width: 52px; height: 52px; border-radius: 14px; display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 16px; color: white; box-shadow: 0 4px 12px rgba(0,0,0,0.15); }
.avatar-blue { background: linear-gradient(135deg, #3b82f6, #1d4ed8); }
.avatar-green { background: linear-gradient(135deg, #10b981, #059669); }
.avatar-purple { background: linear-gradient(135deg, #8b5cf6, #6d28d9); }
.avatar-orange { background: linear-gradient(135deg, #f59e0b, #d97706); }
.stat-box { padding: 24px 20px; border-radius: 16px; text-align: center; position: relative; overflow: hidden; }
.stat-box::before { content: ''; position: absolute; top: 0; left: 0; right: 0; bottom: 0; opacity: 0.5; }
.stat-box-total { background: linear-gradient(135deg, #f1f5f9, #e2e8f0); }
.stat-box-total::before { background: linear-gradient(135deg, #94a3b8, #64748b); }
.stat-box-cobrado { background: linear-gradient(135deg, #d1fae5, #a7f3d0); }
.stat-box-cobrado::before { background: linear-gradient(135deg, #10b981, #059669); }
.stat-box p:first-child { font-size: 28px; font-weight: 800; position: relative; z-index: 1; }
.stat-box p:last-child { font-size: 13px; font-weight: 600; text-transform: uppercase; letter-spacing: 1px; opacity: 0.8; position: relative; z-index: 1; margin-top: 4px; }
.stats-grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: 16px; padding: 20px; }
.quick-link { display: flex; align-items: center; justify-content: center; gap: 8px; padding: 14px; background: #f1f5f9; border-radius: 12px; font-size: 13px; font-weight: 600; color: #475569; transition: all 0.2s; }
.quick-link:hover { background: #e2e8f0; }
.all-users-btn { display: flex; align-items: center; justify-content: center; gap: 8px; padding: 16px; background: linear-gradient(135deg, #059669, #047857); border-radius: 14px; font-size: 14px; font-weight: 600; color: white; margin: 12px; transition: all 0.3s; }
.all-users-btn:hover { transform: translateY(-2px); box-shadow: 0 8px 25px rgba(5, 150, 105, 0.35); }
</style>

{{-- Welcome Section --}}
<div class="mb-8">
    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-5">
        <div class="flex items-center gap-5">
            <div class="w-16 h-16 rounded-2xl bg-gradient-to-br from-emerald-400 to-emerald-600 flex items-center justify-center text-white text-2xl font-bold shadow-lg">
                {{ substr(Auth::user()->nombres, 0, 1) }}{{ substr(Auth::user()->apellidos ?? '', 0, 1) }}
            </div>
            <div>
                <h1 class="text-2xl font-bold text-gray-800">
                    Hola, <span class="text-emerald-600">{{ Auth::user()->nombres }}</span>
                </h1>
                <p class="text-gray-500 flex items-center gap-2">
                    <i class="fas fa-calendar-alt text-emerald-500"></i>
                    {{ $hoy->translatedFormat('l d \\d\\e F \\d\\e Y') }}
                </p>
            </div>
        </div>
        @if($cicloActivo)
        <div class="px-5 py-2.5 bg-gradient-to-r from-emerald-50 to-green-50 border border-emerald-200 rounded-xl flex items-center gap-2">
            <span class="w-2 h-2 bg-emerald-500 rounded-full animate-pulse"></span>
            <span class="text-emerald-700 font-medium">Ciclo {{ $cicloActivo->anio }} activo</span>
        </div>
        @else
        <div class="px-5 py-2.5 bg-gradient-to-r from-amber-50 to-yellow-50 border border-amber-200 rounded-xl flex items-center gap-2">
            <i class="fas fa-exclamation-circle text-amber-500"></i>
            <span class="text-amber-700 font-medium">Sin ciclo activo</span>
        </div>
        @endif
    </div>
</div>

{{-- Stats Grid --}}
<style>
.stat-card-v2 { position: relative; overflow: hidden; }
.stat-card-v2::before { content: ''; position: absolute; top: -30%; right: -30%; width: 80%; height: 80%; border-radius: 50%; opacity: 0.15; }
.stat-card-users-v2::before { background: radial-gradient(circle, #3b82f6, transparent); }
.stat-card-unidades-v2::before { background: radial-gradient(circle, #8b5cf6, transparent); }
.stat-card-turnos-v2::before { background: radial-gradient(circle, #f59e0b, transparent); }
.stat-card-salidas-v2::before { background: radial-gradient(circle, #10b981, transparent); }
.stat-icon-v2 { width: 56px; height: 56px; border-radius: 16px; display: flex; align-items: center; justify-content: center; font-size: 24px; color: white; box-shadow: 0 8px 20px rgba(0,0,0,0.2); }
.stat-icon-blue { background: linear-gradient(135deg, #3b82f6, #1d4ed8); }
.stat-icon-purple { background: linear-gradient(135deg, #8b5cf6, #6d28d9); }
.stat-icon-amber { background: linear-gradient(135deg, #f59e0b, #d97706); }
.stat-icon-green { background: linear-gradient(135deg, #10b981, #059669); }
.stat-value-v2 { font-size: 36px; font-weight: 800; color: #0f172a; line-height: 1; }
.stat-label-v2 { font-size: 12px; font-weight: 600; text-transform: uppercase; letter-spacing: 1px; opacity: 0.7; }
.stat-detail-v2 { font-size: 13px; font-weight: 600; display: flex; align-items: center; gap: 6px; margin-top: 4px; }
</style>

<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
    <div class="dashboard-card stat-card-v2 stat-card-users-v2 p-5">
        <div class="flex items-center gap-3">
            <div class="stat-icon-v2 stat-icon-blue" style="width:48px;height:48px;font-size:20px;">
                <i class="fas fa-users"></i>
            </div>
            <div>
                <p class="stat-label-v2 text-gray-500">Usuarios</p>
                <p class="stat-value-v2" style="font-size:28px;">{{ $stats['total_usuarios'] }}</p>
                <p class="text-xs text-gray-500" style="display:flex;flex-wrap:wrap;gap:4px;">
                    <span class="text-purple-600"><i class="fas fa-user-tie"></i> {{ $stats['gerentes'] }}</span>
                    <span class="text-amber-600"><i class="fas fa-credit-card"></i> {{ $stats['tarjeteras'] }}</span>
                    <span class="text-blue-600"><i class="fas fa-id-card"></i> {{ $stats['conductores'] }}</span>
                </p>
            </div>
        </div>
    </div>

    <div class="dashboard-card stat-card-v2 stat-card-unidades-v2 p-6">
        <div class="flex items-center gap-4">
            <div class="stat-icon-v2 stat-icon-purple">
                <i class="fas fa-bus"></i>
            </div>
            <div>
                <p class="stat-label-v2 text-gray-500">Unidades</p>
                <p class="stat-value-v2">{{ $stats['unidades_activas'] }}/{{ $stats['unidades_total'] }}</p>
                <p class="stat-detail-v2 {{ $stats['unidades_mantenimiento'] > 0 ? 'text-amber-600' : 'text-emerald-600' }}">
                    @if($stats['unidades_mantenimiento'] > 0)
                        <i class="fas fa-wrench"></i>{{ $stats['unidades_mantenimiento'] }} en mantenimiento
                    @else
                        <i class="fas fa-check-circle"></i>Todas operativas
                    @endif
                </p>
            </div>
        </div>
    </div>

    <div class="dashboard-card stat-card-v2 stat-card-turnos-v2 p-6">
        <div class="flex items-center gap-4">
            <div class="stat-icon-v2 stat-icon-amber">
                <i class="fas fa-calendar-check"></i>
            </div>
            <div>
                <p class="stat-label-v2 text-gray-500">Turnos Hoy</p>
                <p class="stat-value-v2">{{ $turnosOperando }}</p>
                <p class="stat-detail-v2 text-gray-500"><i class="fas fa-pause-circle"></i>{{ $turnosDescanso }} descanso</p>
            </div>
        </div>
    </div>

    <div class="dashboard-card stat-card-v2 stat-card-salidas-v2 p-6">
        <div class="flex items-center gap-4">
            <div class="stat-icon-v2 stat-icon-green">
                <i class="fas fa-route"></i>
            </div>
            <div>
                <p class="stat-label-v2 text-gray-500">Salidas</p>
                <p class="stat-value-v2">{{ $salidasCompletadas }}/{{ $salidasHoy }}</p>
                <p class="stat-detail-v2 {{ $salidasHoy > $salidasCompletadas ? 'text-amber-600' : 'text-emerald-600' }}">
                    @if($salidasHoy > $salidasCompletadas)
                        <i class="fas fa-running"></i>{{ $salidasHoy - $salidasCompletadas }} en ruta
                    @else
                        <i class="fas fa-check-double"></i>Completadas
                    @endif
                </p>
            </div>
        </div>
    </div>
</div>

{{-- Alertas --}}
<style>
.alert-card { display: flex; align-items: center; gap: 16px; padding: 20px; border-radius: 16px; transition: all 0.3s ease; position: relative; overflow: hidden; }
.alert-card::before { content: ''; position: absolute; top: 0; left: 0; right: 0; bottom: 0; opacity: 0.1; transition: all 0.3s; }
.alert-card:hover::before { opacity: 0.2; }
.alert-card:hover { transform: translateY(-3px); box-shadow: 0 15px 40px rgba(0,0,0,0.15); }
.alert-red::before { background: linear-gradient(135deg, #ef4444, #dc2626); }
.alert-amber::before { background: linear-gradient(135deg, #f59e0b, #d97706); }
.alert-icon { width: 56px; height: 56px; border-radius: 14px; display: flex; align-items: center; justify-content: center; font-size: 24px; color: white; box-shadow: 0 6px 16px rgba(0,0,0,0.2); }
.alert-red .alert-icon { background: linear-gradient(135deg, #ef4444, #dc2626); }
.alert-amber .alert-icon { background: linear-gradient(135deg, #f59e0b, #d97706); }
</style>

@if($sancionesPendientes > 0 || $documentosProximosVencer > 0 || $documentosVencidos > 0)
<div class="mb-8">
    <h3 class="text-lg font-bold text-gray-700 mb-4 flex items-center gap-2">
        <i class="fas fa-bell text-amber-500"></i> Alertas
    </h3>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        @if($sancionesPendientes > 0)
        <a href="{{ route('gerente.multas.index') }}" class="alert-card alert-red">
            <div class="alert-icon">
                <i class="fas fa-exclamation-triangle"></i>
            </div>
            <div>
                <p class="text-3xl font-bold text-gray-800">{{ $sancionesPendientes }}</p>
                <p class="text-sm font-semibold text-red-600">sanciones pendientes</p>
            </div>
        </a>
        @endif

        @if($documentosProximosVencer > 0)
        <a href="{{ route('gerente.documentos.index') }}" class="alert-card alert-amber">
            <div class="alert-icon">
                <i class="fas fa-file-alt"></i>
            </div>
            <div>
                <p class="text-3xl font-bold text-gray-800">{{ $documentosProximosVencer }}</p>
                <p class="text-sm font-semibold text-amber-600">documentos por vencer</p>
            </div>
        </a>
        @endif

        @if($documentosVencidos > 0)
        <a href="{{ route('gerente.documentos.index') }}" class="alert-card alert-red">
            <div class="alert-icon">
                <i class="fas fa-file-excel"></i>
            </div>
            <div>
                <p class="text-3xl font-bold text-gray-800">{{ $documentosVencidos }}</p>
                <p class="text-sm font-semibold text-red-600">documentos vencidos</p>
            </div>
        </a>
        @endif
    </div>
</div>
@endif



{{-- Contenido Principal --}}
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <div class="lg:col-span-2 space-y-6">
        {{-- Usuarios Recientes --}}
        <div class="dashboard-card">
            <div class="section-header">
                <h3><i class="fas fa-users text-emerald-500 mr-2"></i>Usuarios Recientes</h3>
                <a href="{{ route('gerente.usuarios.index') }}">Ver todos <i class="fas fa-arrow-right"></i></a>
            </div>
            <div class="p-3">
                @forelse($usuariosRecientes as $usuario)
                <a href="{{ route('gerente.usuarios.show', $usuario->usuario_id) }}" class="user-item">
                    <div class="user-avatar" style="{{ $usuario->rol_id == 1 ? 'background:linear-gradient(135deg,#8b5cf6,#6d28d9)' : ($usuario->rol_id == 2 ? 'background:linear-gradient(135deg,#f59e0b,#d97706)' : 'background:linear-gradient(135deg,#10b981,#059669)') }}">
                        {{ substr($usuario->nombres, 0, 1) }}{{ substr($usuario->apellidos, 0, 1) }}
                    </div>
                    <div class="flex-1">
                        <p class="font-bold text-gray-800">{{ $usuario->nombres }} {{ $usuario->apellidos }}</p>
                        <p class="text-xs text-gray-500 flex items-center gap-2">
                            <i class="fas fa-id-card"></i> {{ $usuario->cedula }}
                            <span class="text-gray-300">|</span>
                            <i class="fas fa-phone"></i> {{ $usuario->telefono ?? 'Sin teléfono' }}
                        </p>
                    </div>
                    <span class="status-pill {{ $usuario->rol_id == 1 ? 'pill-info' : ($usuario->rol_id == 2 ? 'pill-warning' : 'pill-success') }}">
                        <i class="fas {{ $usuario->rol_id == 1 ? 'fa-crown' : ($usuario->rol_id == 2 ? 'fa-credit-card' : 'fa-id-card') }}"></i>
                        {{ $usuario->rol_nombre ?? 'Sin rol' }}
                    </span>
                </a>
                @empty
                <div class="p-10 text-center">
                    <i class="fas fa-user-slash text-4xl text-gray-300 mb-3"></i>
                    <p class="text-gray-400">No hay usuarios recientes</p>
                </div>
                @endforelse
            </div>
            <a href="{{ route('gerente.usuarios.index') }}" class="all-users-btn">
                <i class="fas fa-users mr-2"></i> Ver todos los usuarios
            </a>
        </div>

        {{-- Últimas Sanciones --}}
        @if($ultimasSanciones->count() > 0)
        <div class="dashboard-card">
            <div class="section-header">
                <h3><i class="fas fa-gavel text-red-500 mr-2"></i>Últimas Sanciones</h3>
                <a href="{{ route('gerente.multas.index') }}">Ver todas <i class="fas fa-arrow-right"></i></a>
            </div>
            <div class="p-2">
                @foreach($ultimasSanciones as $sancion)
                <div class="user-item">
                    <div class="user-avatar {{ $sancion->estado === 'cobrada' ? 'avatar-green' : 'avatar-orange' }}">
                        <i class="fas {{ $sancion->estado === 'cobrada' ? 'fa-check' : 'fa-clock' }}"></i>
                    </div>
                    <div class="flex-1">
                        <p class="font-bold text-gray-800">{{ $sancion->usuario->nombres ?? 'N/A' }} {{ $sancion->usuario->apellidos ?? '' }}</p>
                        <p class="text-xs text-gray-500">{{ ucfirst(str_replace('_', ' ', $sancion->tipo)) }}</p>
                    </div>
                    <span class="status-pill {{ $sancion->estado === 'cobrada' ? 'pill-success' : 'pill-warning' }}">
                        ${{ number_format($sancion->monto, 2) }}
                    </span>
                </div>
                @endforeach
            </div>
        </div>
        @endif
    </div>

    <div class="space-y-6">
        {{-- Acciones Rápidas --}}
        <div class="dashboard-card">
            <div class="section-header">
                <h3><i class="fas fa-bolt text-amber-500 mr-2"></i>Acciones Rápidas</h3>
            </div>
            <div class="p-4 space-y-3">
                <a href="{{ route('gerente.usuarios.create') }}" class="action-btn">
                    <i class="fas fa-user-plus icon-users"></i>
                    <span>Nuevo Usuario</span>
                </a>
                <a href="{{ route('gerente.unidades.create') }}" class="action-btn">
                    <i class="fas fa-bus icon-bus"></i>
                    <span>Nueva Unidad</span>
                </a>
                <a href="{{ route('gerente.operacion.index') }}" class="action-btn">
                    <i class="fas fa-calendar-alt icon-calendar"></i>
                    <span>Ver Operación</span>
                </a>
                <a href="{{ route('gerente.multas.index') }}" class="action-btn">
                    <i class="fas fa-exclamation-circle icon-multa"></i>
                    <span>Ver Sanciones</span>
                </a>
            </div>
        </div>

        {{-- Sanciones del Mes --}}
        <div class="dashboard-card">
            <div class="section-header">
                <h3><i class="fas fa-gavel text-red-500 mr-2"></i>Sanciones del Mes</h3>
            </div>
            <div class="stats-grid">
                <div class="stat-box stat-box-total">
                    <p>${{ number_format($totalSancionesMes, 2) }}</p>
                    <p>Total</p>
                </div>
                <div class="stat-box stat-box-cobrado">
                    <p>${{ number_format($totalCobradoMes, 2) }}</p>
                    <p>Cobrado</p>
                </div>
            </div>
            <div class="px-4 pb-4">
                <a href="{{ route('gerente.multas.index') }}" class="quick-link">
                    <i class="fas fa-arrow-right"></i> Ver todas las sanciones
                </a>
            </div>
        </div>
    </div>
</div>

{{-- Ciudades / Rutas --}}
<style>
.city-card { position: relative; overflow: hidden; border-radius: 20px; cursor: pointer; box-shadow: 0 10px 40px rgba(0,0,0,0.3); }
.city-card img { width: 100%; height: 280px; object-fit: cover; transition: all 0.5s ease; }
.city-card:hover img { transform: scale(1.1); }
.city-overlay { position: absolute; inset-0; background: linear-gradient(to top, rgba(0,0,0,0.85) 0%, rgba(0,0,0,0.3) 50%, transparent 100%); }
.city-info { position: absolute; bottom: 0; left: 0; right: 0; padding: 24px; display: flex; align-items: center; gap: 16px; }
.city-icon { width: 56px; height: 56px; border-radius: 16px; background: rgba(255,255,255,0.2); backdrop-filter: blur(10px); display: flex; align-items: center; justify-content: center; font-size: 24px; color: white; }
.city-badge { position: absolute; top: 16px; right: 16px; padding: 8px 16px; background: linear-gradient(135deg, #059669, #047857); color: white; font-size: 12px; font-weight: 700; border-radius: 24px; display: flex; align-items: center; gap: 6px; box-shadow: 0 4px 15px rgba(5,150,105,0.4); }
</style>

<div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-8">
    <div class="city-card">
        <img src="{{ asset('images/guaranda.jpg') }}" alt="Guaranda">
        <div class="city-overlay"></div>
        <div class="city-info">
            <div class="city-icon"><i class="fas fa-city"></i></div>
            <div>
                <p class="text-white text-3xl font-bold">Guaranda</p>
                <p class="text-white/70 text-sm font-medium">Ciudad de Servicio</p>
            </div>
        </div>
        <div class="city-badge"><i class="fas fa-map"></i> Activo</div>
    </div>
    <div class="city-card">
        <img src="{{ asset('images/salinas.jpg') }}" alt="Salinas">
        <div class="city-overlay"></div>
        <div class="city-info">
            <div class="city-icon"><i class="fas fa-city"></i></div>
            <div>
                <p class="text-white text-3xl font-bold">Salinas</p>
                <p class="text-white/70 text-sm font-medium">Ciudad de Servicio</p>
            </div>
        </div>
        <div class="city-badge"><i class="fas fa-map"></i> Activo</div>
    </div>
</div>
@endsection