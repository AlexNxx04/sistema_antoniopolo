@extends('layouts.tarjetera')

@section('title', 'Dashboard')
@section('header', 'Panel de Control')

@section('content')
<style>
.dashboard-card { background: linear-gradient(145deg, #ffffff, #f8fafc); border: 1px solid #e2e8f0; border-radius: 20px; transition: all 0.3s ease; overflow: hidden; position: relative; }
.dashboard-card::before { content: ''; position: absolute; top: 0; left: 0; right: 0; height: 4px; background: linear-gradient(90deg, #f59e0b, #fbbf24); }
.dashboard-card:hover { transform: translateY(-3px); box-shadow: 0 15px 50px rgba(0,0,0,0.12); }
.section-header { display: flex; align-items: center; justify-content: space-between; padding: 20px 24px; border-bottom: 1px solid #f1f5f9; }
.section-header h3 { font-size: 18px; font-weight: 700; color: #1e293b; }
.section-header a { font-size: 13px; font-weight: 600; color: #d97706; display: flex; align-items: center; gap: 6px; transition: all 0.2s; }
.section-header a:hover { color: #b45309; gap: 10px; }
.action-btn { display: flex; align-items: center; gap: 14px; padding: 16px 20px; border-radius: 14px; background: #f8fafc; border: 1px solid #e2e8f0; transition: all 0.25s ease; position: relative; overflow: hidden; }
.action-btn::before { content: ''; position: absolute; top: 0; left: -100%; width: 100%; height: 100%; background: linear-gradient(135deg, #f59e0b, #d97706); transition: all 0.3s ease; }
.action-btn:hover::before { left: 0; }
.action-btn:hover { border-color: transparent; transform: translateX(6px); box-shadow: 0 8px 25px rgba(245, 158, 11, 0.25); }
.action-btn:hover span { color: white; position: relative; z-index: 1; }
.action-btn i { width: 44px; height: 44px; display: flex; align-items: center; justify-content: center; border-radius: 12px; font-size: 18px; position: relative; z-index: 1; transition: all 0.2s; }
.action-btn .icon-turno { background: linear-gradient(135deg, #dbeafe, #bfdbfe); color: #2563eb; }
.action-btn .icon-carrera { background: linear-gradient(135deg, #d1fae5, #a7f3d0); color: #059669; }
.action-btn .icon-multa { background: linear-gradient(135deg, #fee2e2, #fecaca); color: #dc2626; }
.action-btn .icon-documento { background: linear-gradient(135deg, #fef3c7, #fde68a); color: #d97706; }
.action-btn .icon-gps { background: linear-gradient(135deg, #d1fae5, #a7f3d0); color: #059669; }
.action-btn:hover .icon-turno { background: white; color: #2563eb; }
.action-btn:hover .icon-carrera { background: white; color: #059669; }
.action-btn:hover .icon-multa { background: white; color: #dc2626; }
.action-btn:hover .icon-documento { background: white; color: #d97706; }
.action-btn:hover .icon-gps { background: white; color: #059669; }
.action-btn span { font-size: 15px; font-weight: 600; color: #334155; position: relative; z-index: 1; }
.user-item { display: flex; align-items: center; gap: 16px; padding: 16px 20px; border-radius: 14px; transition: all 0.2s ease; margin: 4px 12px; }
.user-item:hover { background: linear-gradient(90deg, #fef3c7, #fde68a); transform: translateX(4px); box-shadow: 0 4px 15px rgba(245, 158, 11, 0.15); }
.user-avatar { width: 52px; height: 52px; border-radius: 14px; display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 16px; color: white; box-shadow: 0 4px 12px rgba(0,0,0,0.15); }
.stat-box { padding: 24px 20px; border-radius: 16px; text-align: center; position: relative; overflow: hidden; }
.stat-box p:first-child { font-size: 28px; font-weight: 800; position: relative; z-index: 1; }
.stat-box p:last-child { font-size: 13px; font-weight: 600; text-transform: uppercase; letter-spacing: 1px; opacity: 0.8; position: relative; z-index: 1; margin-top: 4px; }
.stats-grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: 16px; padding: 20px; }
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

{{-- Welcome Section --}}
<div class="mb-8">
    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-5">
        <div class="flex items-center gap-5">
            <div class="w-16 h-16 rounded-2xl bg-gradient-to-br from-amber-400 to-amber-600 flex items-center justify-center text-white text-2xl font-bold shadow-lg">
                {{ substr(Auth::user()->nombres, 0, 1) }}{{ substr(Auth::user()->apellidos ?? '', 0, 1) }}
            </div>
            <div>
                <h1 class="text-2xl font-bold text-gray-800">
                    Hola, <span class="text-amber-600">{{ Auth::user()->nombres }}</span>
                </h1>
                <p class="text-gray-500 flex items-center gap-2">
                    <i class="fas fa-calendar-alt text-amber-500"></i>
                    {{ now()->locale('es')->translatedFormat('l d \\d\\e F \\d\\e Y') }}
                </p>
            </div>
        </div>
        @if(Auth::user()->punto)
        <div class="px-5 py-2.5 bg-gradient-to-r from-amber-50 to-yellow-50 border border-amber-200 rounded-xl flex items-center gap-2">
            <span class="w-2 h-2 bg-amber-500 rounded-full animate-pulse"></span>
            <span class="text-amber-700 font-medium">{{ Auth::user()->punto->nombre }}</span>
        </div>
        @endif
    </div>
</div>

{{-- Stats Grid --}}
<style>
.stat-card-v2 { position: relative; overflow: hidden; }
.stat-card-v2::before { content: ''; position: absolute; top: -30%; right: -30%; width: 80%; height: 80%; border-radius: 50%; opacity: 0.15; }
.stat-card-turnos-v2::before { background: radial-gradient(circle, #3b82f6, transparent); }
.stat-card-unidades-v2::before { background: radial-gradient(circle, #8b5cf6, transparent); }
.stat-card-multas-v2::before { background: radial-gradient(circle, #dc2626, transparent); }
.stat-card-documentos-v2::before { background: radial-gradient(circle, #f59e0b, transparent); }
.stat-icon-v2 { width: 56px; height: 56px; border-radius: 16px; display: flex; align-items: center; justify-content: center; font-size: 24px; color: white; box-shadow: 0 8px 20px rgba(0,0,0,0.2); }
.stat-icon-blue { background: linear-gradient(135deg, #3b82f6, #1d4ed8); }
.stat-icon-purple { background: linear-gradient(135deg, #8b5cf6, #6d28d9); }
.stat-icon-red { background: linear-gradient(135deg, #dc2626, #b91c1c); }
.stat-icon-amber { background: linear-gradient(135deg, #f59e0b, #d97706); }
.stat-value-v2 { font-size: 36px; font-weight: 800; color: #0f172a; line-height: 1; }
.stat-label-v2 { font-size: 12px; font-weight: 600; text-transform: uppercase; letter-spacing: 1px; opacity: 0.7; }
.stat-detail-v2 { font-size: 13px; font-weight: 600; display: flex; align-items: center; gap: 6px; margin-top: 4px; }
</style>

<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
    <div class="dashboard-card stat-card-v2 stat-card-turnos-v2 p-5">
        <div class="flex items-center gap-3">
            <div class="stat-icon-v2 stat-icon-blue" style="width:48px;height:48px;font-size:20px;">
                <i class="fas fa-calendar-check"></i>
            </div>
            <div>
                <p class="stat-label-v2 text-gray-500">Turnos Hoy</p>
                <p class="stat-value-v2" style="font-size:28px;">{{ $turnosHoy }}</p>
                <p class="stat-detail-v2 text-emerald-600">
                    <i class="fas fa-bus"></i>{{ $unidadesEnTurno }} operando
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
                <p class="stat-value-v2">{{ $unidadesActivas }}</p>
                <p class="stat-detail-v2 text-emerald-600">
                    <i class="fas fa-check-circle"></i>Todas activas
                </p>
            </div>
        </div>
    </div>

    <div class="dashboard-card stat-card-v2 stat-card-multas-v2 p-6">
        <div class="flex items-center gap-4">
            <div class="stat-icon-v2 stat-icon-red">
                <i class="fas fa-money-check-alt"></i>
            </div>
            <div>
                <p class="stat-label-v2 text-gray-500">Multas Pendientes</p>
                <p class="stat-value-v2">{{ $multasPendientes }}</p>
                <p class="stat-detail-v2 {{ $multasPendientes > 0 ? 'text-red-600' : 'text-emerald-600' }}">
                    @if($multasPendientes > 0)
                        <i class="fas fa-clock"></i>Por cobrar
                    @else
                        <i class="fas fa-check-circle"></i>Sin pendientes
                    @endif
                </p>
            </div>
        </div>
    </div>

    <div class="dashboard-card stat-card-v2 stat-card-documentos-v2 p-6">
        <div class="flex items-center gap-4">
            <div class="stat-icon-v2 stat-icon-amber">
                <i class="fas fa-id-card"></i>
            </div>
            <div>
                <p class="stat-label-v2 text-gray-500">Documentos</p>
                <p class="stat-value-v2">{{ $documentosPorVencer }}</p>
                <p class="stat-detail-v2 text-amber-600">
                    <i class="fas fa-clock"></i>Por vencer
                </p>
            </div>
        </div>
    </div>
</div>

{{-- Cola de Turno --}}
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
    {{-- Cola --}}
    <div class="dashboard-card" style="grid-column: span 2;">
        <div class="section-header">
            <h3><i class="fas fa-list-ol text-amber-500 mr-2"></i>Cola de Hoy</h3>
            <a href="#"><i class="fas fa-external-link-alt"></i>Ver todo</a>
        </div>
        <div style="display: grid; grid-template-columns: repeat(5, 1fr); gap: 12px; padding: 20px;">
            @forelse($cola as $turno)
            <div class="user-item" style="flex-direction: column; text-align: center; margin: 0;">
                <div class="user-avatar avatar-purple" style="width: 48px; height: 48px; margin: 0 auto 8px; font-size: 14px;">{{ str_pad($turno->posicion, 2, '0', STR_PAD_LEFT) }}</div>
                <p style="font-size: 13px; font-weight: 600; color: #1e293b;">{{ $turno->unidad->placa ?? 'N/A' }}</p>
                <p style="font-size: 11px; color: #64748b;">{{ $turno->usuario->nombres ?? 'N/A' }}</p>
                @if($turno->es_descanso)
                <span style="margin-top: 8px; padding: 4px 10px; border-radius: 20px; font-size: 10px; font-weight: 600; background: #fee2e2; color: #991b1b;">Descanso</span>
                @elseif($turno->bloqueado)
                <span style="margin-top: 8px; padding: 4px 10px; border-radius: 20px; font-size: 10px; font-weight: 600; background: #fee2e2; color: #991b1b;">Bloqueado</span>
                @else
                <span class="pill-success" style="margin-top: 8px; padding: 4px 10px; border-radius: 20px; font-size: 10px; font-weight: 600; background: #d1fae5; color: #047857;">{{ $turno->es_puntero ? 'Puntero' : 'Operando' }}</span>
                @endif
            </div>
            @empty
            <div style="grid-column: span 5; text-align: center; padding: 40px; color: #64748b;">
                <i class="fas fa-calendar-times text-4xl mb-3"></i>
                <p>No hay turnos asignados para hoy</p>
            </div>
            @endforelse
        </div>
    </div>

    {{-- Acciones Rápidas --}}
    <div class="dashboard-card">
        <div class="section-header">
            <h3><i class="fas fa-bolt text-amber-500 mr-2"></i>Acciones Rápidas</h3>
        </div>
        <div style="padding: 16px; display: flex; flex-direction: column; gap: 8px;">
            <a href="#" class="action-btn">
                <i class="fas fa-play-circle icon-carrera"></i>
                <span>Registrar Carrera</span>
            </a>
            <a href="#" class="action-btn">
                <i class="fas fa-user-check icon-turno"></i>
                <span>Confirmar Presencia</span>
            </a>
            <a href="#" class="action-btn">
                <i class="fas fa-money-check-alt icon-multa"></i>
                <span>Gestionar Multas</span>
            </a>
            <a href="#" class="action-btn">
                <i class="fas fa-file-signature icon-documento"></i>
                <span>Validar Documentos</span>
            </a>
            <a href="{{ route('tarjetera.gps.mapaEnVivo') }}" class="action-btn">
                <i class="fas fa-map-marked-alt icon-gps"></i>
                <span>GPS Tiempo Real</span>
                <i class="fas fa-chevron-right ml-auto text-gray-400"></i>
            </a>
        </div>
    </div>
</div>

{{-- Conductores Presentes --}}
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
    <div class="dashboard-card">
        <div class="section-header">
            <h3><i class="fas fa-user-check text-emerald-500 mr-2"></i>Presentes en el Punto</h3>
            <span class="px-3 py-1 bg-emerald-100 text-emerald-700 rounded-full text-sm font-bold">{{ $conductoresPresentes->count() }}</span>
        </div>
        <div style="padding: 12px;">
            @forelse($conductoresPresentes as $turno)
            <div class="user-item">
                <div class="user-avatar" style="background: linear-gradient(135deg, #10b981, #059669); width: 44px; height: 44px; font-size: 14px;">
                    {{ substr($turno->usuario->nombres ?? '?', 0, 1) }}{{ substr($turno->usuario->apellidos ?? '', 0, 1) }}
                </div>
                <div style="flex: 1;">
                    <p style="font-weight: 600; color: #1e293b; font-size: 14px;">{{ $turno->usuario->nombres ?? 'N/A' }}</p>
                    <p style="font-size: 12px; color: #64748b;">
                        <i class="fas fa-clock mr-1"></i>{{ $turno->presencia_at->format('H:i') }}
                        @if($turno->unidad)
                            · <i class="fas fa-car mr-1"></i>{{ $turno->unidad->placa }}
                        @endif
                    </p>
                </div>
                <div style="text-align: right;">
                    <span class="px-3 py-1 rounded-full text-xs font-bold {{ $turno->es_puntero_confirmado ? 'bg-blue-100 text-blue-700' : 'bg-gray-100 text-gray-600' }}">
                        {{ $turno->es_puntero_confirmado ? 'Puntero #' . $turno->posicion : 'Turno #' . $turno->posicion }}
                    </span>
                    <div style="font-size: 11px; color: #10b981; margin-top: 4px;">
                        <i class="fas fa-check-circle"></i> En punto
                    </div>
                </div>
            </div>
            @empty
            <div style="padding: 32px; text-align: center; color: #64748b;">
                <i class="fas fa-user-clock text-4xl mb-3" style="color: #cbd5e1;"></i>
                <p>Ningún conductor ha confirmado presencia aún</p>
            </div>
            @endforelse
        </div>
    </div>

    <div class="dashboard-card">
        <div class="section-header">
            <h3><i class="fas fa-user-clock text-amber-500 mr-2"></i>Pendientes de Llegar</h3>
            <span class="px-3 py-1 bg-amber-100 text-amber-700 rounded-full text-sm font-bold">{{ $conductoresPendientes->count() }}</span>
        </div>
        <div style="padding: 12px;">
            @forelse($conductoresPendientes as $turno)
            <div class="user-item">
                <div class="user-avatar" style="background: linear-gradient(135deg, #f59e0b, #d97706); width: 44px; height: 44px; font-size: 14px;">
                    {{ substr($turno->usuario->nombres ?? '?', 0, 1) }}{{ substr($turno->usuario->apellidos ?? '', 0, 1) }}
                </div>
                <div style="flex: 1;">
                    <p style="font-weight: 600; color: #1e293b; font-size: 14px;">{{ $turno->usuario->nombres ?? 'N/A' }}</p>
                    <p style="font-size: 12px; color: #64748b;">
                        @if($turno->unidad)
                            <i class="fas fa-car mr-1"></i>{{ $turno->unidad->placa }}
                        @endif
                        · <i class="fas fa-hashtag mr-1"></i>Turno {{ $turno->posicion }}
                    </p>
                </div>
                <div style="text-align: right;">
                    <span class="px-3 py-1 rounded-full text-xs font-bold bg-amber-100 text-amber-700">
                        {{ $turno->es_puntero ? 'Puntero' : 'Regular' }}
                    </span>
                    <div style="font-size: 11px; color: #f59e0b; margin-top: 4px;">
                        <i class="fas fa-hourglass-half"></i> Esperando
                    </div>
                </div>
            </div>
            @empty
            <div style="padding: 32px; text-align: center; color: #64748b;">
                <i class="fas fa-check-double text-4xl mb-3" style="color: #86efac;"></i>
                <p style="font-weight: 600; color: #059669;">Todos los conductores han llegado</p>
            </div>
            @endforelse
        </div>
    </div>
</div>

{{-- Horario de Operación --}}
<?php
$puntoNombre = Auth::user()->punto->nombre ?? 'Punto';
$esSalinas = str_contains(strtolower($puntoNombre), 'salinas');
?>
<div class="dashboard-card">
    <div class="section-header">
        <h3><i class="fas fa-clock text-amber-500 mr-2"></i>Horario de Operación - {{ $puntoNombre }}</h3>
    </div>
    @if($esSalinas)
    <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 20px; padding: 24px;">
        <div style="padding: 24px; background: linear-gradient(135deg, #fef3c7, #fffbeb); border-radius: 16px; text-align: center;">
            <div style="width: 56px; height: 56px; border-radius: 14px; background: linear-gradient(135deg, #f59e0b, #d97706); display: flex; align-items: center; justify-content: center; margin: 0 auto 12px;">
                <i class="fas fa-sun" style="color: white; font-size: 24px;"></i>
            </div>
            <h4 style="font-weight: 700; color: #92400e; margin-bottom: 8px; font-size: 16px;">Horario Continuo</h4>
            <p style="font-size: 24px; font-weight: 800; color: #b45309;">05:30 - 18:00</p>
            <p style="font-size: 13px; color: #d97706; margin-top: 8px; font-weight: 600;">Intervalo: 15 minutos</p>
        </div>
        <div style="padding: 24px; background: linear-gradient(135deg, #fee2e2, #fef2f2); border-radius: 16px; text-align: center;">
            <div style="width: 56px; height: 56px; border-radius: 14px; background: linear-gradient(135deg, #dc2626, #b91c1c); display: flex; align-items: center; justify-content: center; margin: 0 auto 12px;">
                <i class="fas fa-moon" style="color: white; font-size: 24px;"></i>
            </div>
            <h4 style="font-weight: 700; color: #991b1b; margin-bottom: 8px; font-size: 16px;">Noche</h4>
            <p style="font-size: 24px; font-weight: 800; color: #b91c1c;">18:00 - 19:30</p>
            <p style="font-size: 13px; color: #dc2626; margin-top: 8px; font-weight: 600;">Intervalo: 10 min o llenar</p>
        </div>
    </div>
    @else
    <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px; padding: 24px;">
        <div style="padding: 24px; background: linear-gradient(135deg, #dbeafe, #eff6ff); border-radius: 16px; text-align: center;">
            <div style="width: 56px; height: 56px; border-radius: 14px; background: linear-gradient(135deg, #3b82f6, #1d4ed8); display: flex; align-items: center; justify-content: center; margin: 0 auto 12px;">
                <i class="fas fa-sun" style="color: white; font-size: 24px;"></i>
            </div>
            <h4 style="font-weight: 700; color: #1e40af; margin-bottom: 8px; font-size: 16px;">Mañana</h4>
            <p style="font-size: 24px; font-weight: 800; color: #1d4ed8;">05:30 - 09:00</p>
            <p style="font-size: 13px; color: #3b82f6; margin-top: 8px; font-weight: 600;">Intervalo: 10 minutos</p>
        </div>
        <div style="padding: 24px; background: linear-gradient(135deg, #fef3c7, #fffbeb); border-radius: 16px; text-align: center;">
            <div style="width: 56px; height: 56px; border-radius: 14px; background: linear-gradient(135deg, #f59e0b, #d97706); display: flex; align-items: center; justify-content: center; margin: 0 auto 12px;">
                <i class="fas fa-cloud-sun" style="color: white; font-size: 24px;"></i>
            </div>
            <h4 style="font-weight: 700; color: #92400e; margin-bottom: 8px; font-size: 16px;">Tarde</h4>
            <p style="font-size: 24px; font-weight: 800; color: #b45309;">09:30 - 18:00</p>
            <p style="font-size: 13px; color: #d97706; margin-top: 8px; font-weight: 600;">Intervalo: 15 minutos</p>
        </div>
        <div style="padding: 24px; background: linear-gradient(135deg, #fee2e2, #fef2f2); border-radius: 16px; text-align: center;">
            <div style="width: 56px; height: 56px; border-radius: 14px; background: linear-gradient(135deg, #dc2626, #b91c1c); display: flex; align-items: center; justify-content: center; margin: 0 auto 12px;">
                <i class="fas fa-moon" style="color: white; font-size: 24px;"></i>
            </div>
            <h4 style="font-weight: 700; color: #991b1b; margin-bottom: 8px; font-size: 16px;">Noche</h4>
            <p style="font-size: 24px; font-weight: 800; color: #b91c1c;">18:00 - 19:30</p>
            <p style="font-size: 13px; color: #dc2626; margin-top: 8px; font-weight: 600;">Intervalo: 10 min o llenar</p>
        </div>
    </div>
    @endif
</div>

{{-- Ciudad de Operación --}}
<style>
.city-card { position: relative; overflow: hidden; border-radius: 24px; cursor: pointer; box-shadow: 0 15px 50px rgba(0,0,0,0.4); }
.city-card img { width: 100%; height: 400px; object-fit: cover; transition: all 0.5s ease; }
.city-card:hover img { transform: scale(1.05); }
.city-overlay { position: absolute; inset-0; background: linear-gradient(to top, rgba(0,0,0,0.85) 0%, rgba(0,0,0,0.2) 40%, transparent 100%); }
.city-info { position: absolute; bottom: 0; left: 0; right: 0; padding: 32px; display: flex; align-items: center; gap: 20px; }
.city-icon { width: 72px; height: 72px; border-radius: 20px; background: rgba(255,255,255,0.25); backdrop-filter: blur(10px); display: flex; align-items: center; justify-content: center; font-size: 32px; color: white; }
.city-badge { position: absolute; top: 20px; right: 20px; padding: 10px 20px; background: linear-gradient(135deg, #f59e0b, #d97706); color: white; font-size: 14px; font-weight: 700; border-radius: 24px; display: flex; align-items: center; gap: 8px; box-shadow: 0 6px 20px rgba(245,158,11,0.5); }
</style>

<div class="mt-8">
    <div class="city-card">
        <img src="{{ asset('images/' . (str_contains(strtolower($puntoNombre), 'salinas') ? 'salinas' : 'guaranda') . '.jpg') }}" alt="{{ $puntoNombre }}">
        <div class="city-overlay"></div>
        <div class="city-info">
            <div class="city-icon"><i class="fas fa-city"></i></div>
            <div>
                <p class="text-white text-4xl font-bold">{{ $puntoNombre }}</p>
                <p class="text-white/80 text-base font-medium">Ciudad de Operación</p>
            </div>
        </div>
        <div class="city-badge"><i class="fas fa-map"></i> Activo</div>
    </div>
</div>
@endsection