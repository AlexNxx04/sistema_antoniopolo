@extends('layouts.conductor')

@section('title', 'Dashboard')
@section('header', 'Panel de Conductor')

@section('content')
<style>
.dashboard-card { background: linear-gradient(145deg, #ffffff, #f0fdf4); border: 1px solid #d1fae5; border-radius: 20px; transition: all 0.3s ease; overflow: hidden; position: relative; }
.dashboard-card::before { content: ''; position: absolute; top: 0; left: 0; right: 0; height: 4px; background: linear-gradient(90deg, #059669, #10b981); }
.dashboard-card:hover { transform: translateY(-3px); box-shadow: 0 15px 50px rgba(0,0,0,0.12); }
.section-header { display: flex; align-items: center; justify-content: space-between; padding: 20px 24px; border-bottom: 1px solid #ecfdf5; }
.section-header h3 { font-size: 18px; font-weight: 700; color: #1e293b; }
.section-header a { font-size: 13px; font-weight: 600; color: #059669; display: flex; align-items: center; gap: 6px; transition: all 0.2s; }
.section-header a:hover { color: #047857; gap: 10px; }
.action-btn { display: flex; align-items: center; gap: 14px; padding: 16px 20px; border-radius: 14px; background: #f0fdf4; border: 1px solid #d1fae5; transition: all 0.25s ease; position: relative; overflow: hidden; }
.action-btn::before { content: ''; position: absolute; top: 0; left: -100%; width: 100%; height: 100%; background: linear-gradient(135deg, #059669, #047857); transition: all 0.3s ease; }
.action-btn:hover::before { left: 0; }
.action-btn:hover { border-color: transparent; transform: translateX(6px); box-shadow: 0 8px 25px rgba(5, 150, 105, 0.25); }
.action-btn:hover span { color: white; position: relative; z-index: 1; }
.action-btn i { width: 44px; height: 44px; display: flex; align-items: center; justify-content: center; border-radius: 12px; font-size: 18px; position: relative; z-index: 1; transition: all 0.2s; }
.action-btn .icon-gps { background: linear-gradient(135deg, #d1fae5, #a7f3d0); color: #059669; }
.action-btn .icon-history { background: linear-gradient(135deg, #dbeafe, #bfdbfe); color: #2563eb; }
.action-btn .icon-notif { background: linear-gradient(135deg, #fef3c7, #fde68a); color: #d97706; }
.action-btn .icon-multa { background: linear-gradient(135deg, #fee2e2, #fecaca); color: #dc2626; }
.action-btn .icon-justif { background: linear-gradient(135deg, #ede9fe, #ddd6fe); color: #7c3aed; }
.action-btn:hover .icon-gps { background: white; color: #059669; }
.action-btn:hover .icon-history { background: white; color: #2563eb; }
.action-btn:hover .icon-notif { background: white; color: #d97706; }
.action-btn:hover .icon-multa { background: white; color: #dc2626; }
.action-btn:hover .icon-justif { background: white; color: #7c3aed; }
.action-btn span { font-size: 15px; font-weight: 600; color: #334155; position: relative; z-index: 1; }
.user-item { display: flex; align-items: center; gap: 16px; padding: 16px 20px; border-radius: 14px; transition: all 0.2s ease; margin: 4px 12px; }
.user-item:hover { background: linear-gradient(90deg, #ecfdf5, #d1fae5); transform: translateX(4px); box-shadow: 0 4px 15px rgba(5, 150, 105, 0.15); }
.user-avatar { width: 52px; height: 52px; border-radius: 14px; display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 16px; color: white; box-shadow: 0 4px 12px rgba(0,0,0,0.15); background: linear-gradient(135deg, #059669, #047857); }
.stat-box { padding: 24px 20px; border-radius: 16px; text-align: center; position: relative; overflow: hidden; }
.stat-box::before { content: ''; position: absolute; top: 0; left: 0; right: 0; bottom: 0; opacity: 0.5; }
.stat-box-turno::before { background: linear-gradient(135deg, #d1fae5, #a7f3d0); }
.stat-box-salidas::before { background: linear-gradient(135deg, #dbeafe, #bfdbfe); }
.stat-box p:first-child { font-size: 28px; font-weight: 800; position: relative; z-index: 1; }
.stat-box p:last-child { font-size: 13px; font-weight: 600; text-transform: uppercase; letter-spacing: 1px; opacity: 0.8; position: relative; z-index: 1; margin-top: 4px; }
.stats-grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: 16px; padding: 20px; }
.alert-card { display: flex; align-items: center; gap: 16px; padding: 20px; border-radius: 16px; transition: all 0.3s ease; position: relative; overflow: hidden; }
.alert-card::before { content: ''; position: absolute; top: 0; left: 0; right: 0; bottom: 0; opacity: 0.1; transition: all 0.3s; }
.alert-card:hover::before { opacity: 0.2; }
.alert-card:hover { transform: translateY(-3px); box-shadow: 0 15px 40px rgba(0,0,0,0.15); }
.alert-green::before { background: linear-gradient(135deg, #10b981, #059669); }
.alert-amber::before { background: linear-gradient(135deg, #f59e0b, #d97706); }
.alert-red::before { background: linear-gradient(135deg, #ef4444, #dc2626); }
.alert-icon { width: 56px; height: 56px; border-radius: 14px; display: flex; align-items: center; justify-content: center; font-size: 24px; color: white; box-shadow: 0 6px 16px rgba(0,0,0,0.2); }
.alert-green .alert-icon { background: linear-gradient(135deg, #10b981, #059669); }
.alert-amber .alert-icon { background: linear-gradient(135deg, #f59e0b, #d97706); }
.alert-red .alert-icon { background: linear-gradient(135deg, #ef4444, #dc2626); }
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
                    {{ now()->locale('es')->translatedFormat('l d \\d\\e F \\d\\e Y') }}
                </p>
            </div>
        </div>
        @if($turnoHoy)
        <div class="px-5 py-2.5 bg-gradient-to-r from-emerald-50 to-green-50 border border-emerald-200 rounded-xl flex items-center gap-2">
            <span class="w-2 h-2 bg-emerald-500 rounded-full animate-pulse"></span>
            <span class="text-emerald-700 font-medium">En Turno - {{ $turnoHoy->punto->nombre ?? 'Sin punto' }}</span>
        </div>
        @else
        <div class="px-5 py-2.5 bg-gradient-to-r from-gray-50 to-gray-100 border border-gray-200 rounded-xl flex items-center gap-2">
            <i class="fas fa-bed text-gray-500"></i>
            <span class="text-gray-700 font-medium">Sin Turno Hoy (Descanso)</span>
        </div>
        @endif
    </div>
</div>

{{-- Stats Grid --}}
<style>
.stat-card-v2 { position: relative; overflow: hidden; }
.stat-card-v2::before { content: ''; position: absolute; top: -30%; right: -30%; width: 80%; height: 80%; border-radius: 50%; opacity: 0.15; }
.stat-card-turno-v2::before { background: radial-gradient(circle, #10b981, transparent); }
.stat-card-salidas-v2::before { background: radial-gradient(circle, #3b82f6, transparent); }
.stat-card-notif-v2::before { background: radial-gradient(circle, #f59e0b, transparent); }
.stat-card-unidad-v2::before { background: radial-gradient(circle, #8b5cf6, transparent); }
.stat-card-multas-v2::before { background: radial-gradient(circle, #ef4444, transparent); }
.stat-icon-v2 { width: 56px; height: 56px; border-radius: 16px; display: flex; align-items: center; justify-content: center; font-size: 24px; color: white; box-shadow: 0 8px 20px rgba(0,0,0,0.2); }
.stat-icon-green { background: linear-gradient(135deg, #10b981, #059669); }
.stat-icon-blue { background: linear-gradient(135deg, #3b82f6, #1d4ed8); }
.stat-icon-amber { background: linear-gradient(135deg, #f59e0b, #d97706); }
.stat-icon-purple { background: linear-gradient(135deg, #8b5cf6, #6d28d9); }
.stat-icon-red { background: linear-gradient(135deg, #ef4444, #dc2626); }
.stat-value-v2 { font-size: 36px; font-weight: 800; color: #0f172a; line-height: 1; }
.stat-label-v2 { font-size: 12px; font-weight: 600; text-transform: uppercase; letter-spacing: 1px; opacity: 0.7; }
.stat-detail-v2 { font-size: 13px; font-weight: 600; display: flex; align-items: center; gap: 6px; margin-top: 4px; }
</style>

<div class="grid grid-cols-2 lg:grid-cols-5 gap-4 mb-8">
    <div class="dashboard-card stat-card-v2 stat-card-turno-v2 p-5">
        <div class="flex items-center gap-3">
            <div class="stat-icon-v2 stat-icon-green" style="width:48px;height:48px;font-size:20px;">
                <i class="fas fa-calendar-check"></i>
            </div>
            <div>
                <div class="stat-value-v2">{{ $turnoHoy ? ($turnoHoy->es_descanso ? 'Descanso' : 'Opera') : 'No' }}</div>
                <div class="stat-label-v2">Estado Hoy</div>
                @if($turnoHoy && !$turnoHoy->es_descanso)
                <div class="stat-detail-v2 text-emerald-600">
                    <i class="fas fa-hashtag text-xs"></i>
                    {{ $turnoHoy->posicion }}
                </div>
                @endif
            </div>
        </div>
    </div>

    <div class="dashboard-card stat-card-v2 stat-card-salidas-v2 p-5">
        <div class="flex items-center gap-3">
            <div class="stat-icon-v2 stat-icon-blue" style="width:48px;height:48px;font-size:20px;">
                <i class="fas fa-route"></i>
            </div>
            <div>
                <div class="stat-value-v2">{{ $enRuta ? 'En Ruta' : 'En Base' }}</div>
                <div class="stat-label-v2">Estado</div>
                @if($ultimaSalida)
                <div class="stat-detail-v2 text-blue-600">
                    <i class="fas fa-clock text-xs"></i>
                    {{ $ultimaSalida->hora_salida->format('H:i') }}
                </div>
                @endif
            </div>
        </div>
    </div>

    <div class="dashboard-card stat-card-v2 stat-card-notif-v2 p-5">
        <div class="flex items-center gap-3">
            <div class="stat-icon-v2 stat-icon-amber" style="width:48px;height:48px;font-size:20px;">
                <i class="fas fa-bell"></i>
            </div>
            <div>
                <div class="stat-value-v2">{{ $notificaciones->count() }}</div>
                <div class="stat-label-v2">Notificaciones</div>
                <div class="stat-detail-v2 text-amber-600">
                    <i class="fas fa-circle text-xs"></i>
                    Sin leer
                </div>
            </div>
        </div>
    </div>

    <div class="dashboard-card stat-card-v2 stat-card-unidad-v2 p-5">
        <div class="flex items-center gap-3">
            <div class="stat-icon-v2 stat-icon-purple" style="width:48px;height:48px;font-size:20px;">
                <i class="fas fa-car"></i>
            </div>
            <div>
                <div class="stat-value-v2">{{ $turnoHoy && $turnoHoy->unidad ? $turnoHoy->unidad->numero_interno : '-' }}</div>
                <div class="stat-label-v2">Unidad</div>
                @if($turnoHoy && $turnoHoy->unidad)
                <div class="stat-detail-v2 text-purple-600">
                    <i class="fas fa-id-card text-xs"></i>
                    {{ $turnoHoy->unidad->placa }}
                </div>
                @endif
            </div>
        </div>
    </div>

    <div class="dashboard-card stat-card-v2 stat-card-multas-v2 p-5">
        <div class="flex items-center gap-3">
            <div class="stat-icon-v2 stat-icon-red" style="width:48px;height:48px;font-size:20px;">
                <i class="fas fa-exclamation-triangle"></i>
            </div>
            <div>
                <div class="stat-value-v2">{{ $multasPendientes ?? 0 }}</div>
                <div class="stat-label-v2">Multas Pend.</div>
                <div class="stat-detail-v2 text-red-600">
                    <i class="fas fa-dollar-sign text-xs"></i>
                    Por pagar
                </div>
            </div>
        </div>
    </div>
</div>

@if($turnoHoy && !$turnoHoy->es_descanso)
{{-- Presence Confirmation --}}
@if(!isset($turnoHoy->presencia_confirmada) || !$turnoHoy->presencia_confirmada)
<div class="dashboard-card mb-6 p-5">
    <div class="flex items-center gap-4 mb-4">
        <div class="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center">
            <i class="fas fa-user-check text-blue-600 text-xl"></i>
        </div>
        <div class="flex-1">
            <h3 class="font-bold text-gray-800">Confirmar Presencia</h3>
            <p class="text-sm text-gray-500">Confirma tu presencia para el turno de hoy</p>
        </div>
    </div>
    <div class="flex gap-3">
        <button @click="confirmarPresencia(true)" 
                :disabled="!gpsEnabled || loadingPresencia"
                class="flex-1 py-3 bg-gradient-to-r from-blue-500 to-blue-600 text-white font-semibold rounded-xl hover:from-blue-600 hover:to-blue-700 disabled:opacity-50 disabled:cursor-not-allowed transition-all flex items-center justify-center gap-2">
            <svg x-show="!loadingPresencia" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"/>
            </svg>
            <span x-text="loadingPresencia ? 'Confirmando...' : 'Soy Puntero'"></span>
        </button>
        <button @click="confirmarPresencia(false)" 
                :disabled="!gpsEnabled || loadingPresencia"
                class="flex-1 py-3 bg-gradient-to-r from-gray-500 to-gray-600 text-white font-semibold rounded-xl hover:from-gray-600 hover:to-gray-700 disabled:opacity-50 disabled:cursor-not-allowed transition-all flex items-center justify-center gap-2">
            <svg x-show="!loadingPresencia" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
            </svg>
            <span x-text="loadingPresencia ? 'Confirmando...' : 'No soy Puntero'"></span>
        </button>
    </div>
</div>
@else
<div class="dashboard-card mb-6 p-5 bg-green-50 border-green-200">
    <div class="flex items-center gap-4">
        <div class="w-12 h-12 bg-green-100 rounded-xl flex items-center justify-center">
            <i class="fas fa-check-circle text-green-600 text-xl"></i>
        </div>
        <div>
            <h3 class="font-bold text-green-800">Presencia Confirmada</h3>
            <p class="text-sm text-green-600">
                Confirmado a las {{ $turnoHoy->presencia_at ? $turnoHoy->presencia_at->format('H:i') : 'N/A' }}
                @if($turnoHoy->es_puntero_confirmado !== null)
                    - {{ $turnoHoy->es_puntero_confirmado ? 'Puntero' : 'Regular' }}
                @endif
            </p>
        </div>
    </div>
</div>
@endif

{{-- Turno Info Card --}}
<div class="dashboard-card mb-6">
    <div class="section-header">
        <h3><i class="fas fa-info-circle text-emerald-500 mr-2"></i>Información del Turno</h3>
        <span class="px-3 py-1 rounded-full text-xs font-bold {{ $enRuta ? 'bg-blue-100 text-blue-700' : 'bg-emerald-100 text-emerald-700' }}">
            {{ $enRuta ? 'En Ruta' : 'En Base' }}
        </span>
    </div>
    <div class="stats-grid">
        <div class="stat-box stat-box-turno">
            <p class="text-emerald-800">{{ $turnoHoy->fecha->format('d/m/Y') }}</p>
            <p class="text-emerald-600">Fecha</p>
        </div>
        <div class="stat-box stat-box-turno">
            <p class="text-emerald-800">#{{ $turnoHoy->posicion }}</p>
            <p class="text-emerald-600">Posición</p>
        </div>
        <div class="stat-box stat-box-salidas">
            <p class="text-blue-800">{{ $turnoHoy->punto->nombre ?? 'N/A' }}</p>
            <p class="text-blue-600">Punto</p>
        </div>
        <div class="stat-box stat-box-salidas">
            <p class="text-blue-800">{{ $turnoHoy->unidad->placa ?? 'N/A' }}</p>
            <p class="text-blue-600">Unidad</p>
        </div>
    </div>
</div>

{{-- GPS Status & Actions --}}
<div class="dashboard-card p-5 mb-6">
    <div class="flex items-center justify-between mb-4">
        <h3 class="font-bold text-gray-800 flex items-center gap-2">
            <i class="fas fa-map-marker-alt text-emerald-500"></i>
            GPS en Tiempo Real
        </h3>
        <div class="flex items-center gap-2">
            <span x-show="gpsEnabled" class="flex items-center gap-1 text-xs text-emerald-600">
                <span class="w-2 h-2 bg-emerald-500 rounded-full status-indicator"></span>
                Activo
            </span>
            <span x-show="!gpsEnabled" class="flex items-center gap-1 text-xs text-red-500">
                <span class="w-2 h-2 bg-red-500 rounded-full"></span>
                Inactivo
            </span>
        </div>
    </div>

    <div x-show="gpsEnabled" class="mb-4 p-3 bg-gray-50 rounded-xl">
        <div class="grid grid-cols-3 gap-2 text-center">
            <div>
                <p class="text-xs text-gray-500">Latitud</p>
                <p class="font-mono text-sm font-medium" x-text="currentPosition.lat || '...'"></p>
            </div>
            <div>
                <p class="text-xs text-gray-500">Longitud</p>
                <p class="font-mono text-sm font-medium" x-text="currentPosition.lng || '...'"></p>
            </div>
            <div>
                <p class="text-xs text-gray-500">Velocidad</p>
                <p class="font-mono text-sm font-medium" x-text="currentPosition.speed ? currentPosition.speed + ' km/h' : '...'"></p>
            </div>
        </div>
    </div>

    <div x-show="!gpsEnabled" class="mb-4 p-3 bg-red-50 rounded-xl text-center">
        <p class="text-sm text-red-600">GPS deshabilitado. Por favor active la ubicación.</p>
    </div>

    {{-- Action Buttons --}}
    <div class="space-y-3">
        @if(!$enRuta)
            @if($puedeRegistrar)
            <button @click="registrarSalida()" 
                    :disabled="!gpsEnabled || loading"
                    class="w-full py-4 btn-primary text-white font-semibold rounded-2xl shadow-lg flex items-center justify-center gap-2 disabled:opacity-50 disabled:cursor-not-allowed">
                <svg x-show="!loading" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18"/>
                </svg>
                <svg x-show="loading" class="w-6 h-6 animate-spin" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                <span x-text="loading ? 'Registrando...' : 'Registrar Salida'"></span>
            </button>
            @else
            <button disabled
                    class="w-full py-4 bg-gray-400 text-white font-semibold rounded-2xl shadow-lg flex items-center justify-center gap-2 cursor-not-allowed">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                </svg>
                Tarjetera Registra
            </button>
            @endif
        @elseif(!$puedeRegistrar && $ultimaSalida && !$ultimaSalida->es_excepcion)
        <div class="bg-emerald-50 border border-emerald-200 rounded-xl p-4 mb-3">
            <div class="flex items-center gap-3">
                <div class="w-3 h-3 bg-emerald-500 rounded-full status-indicator"></div>
                <div>
                    <p class="font-semibold text-emerald-800">En Ruta</p>
                    <p class="text-sm text-emerald-600">
                        @if($ultimaSalida)
                            Salió de {{ $ultimaSalida->puntoOrigen->nombre ?? 'Punto de origen' }}
                            a las {{ $ultimaSalida->hora_salida->format('H:i') }}
                        @endif
                    </p>
                </div>
            </div>
        </div>
        <button @click="registrarLlegada()" 
                :disabled="!gpsEnabled || loading"
                class="w-full py-4 bg-gradient-to-r from-amber-500 to-amber-600 hover:from-amber-600 hover:to-amber-700 text-white font-semibold rounded-2xl shadow-lg flex items-center justify-center gap-2 disabled:opacity-50 disabled:cursor-not-allowed transition-all">
            <svg x-show="!loading" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"/>
            </svg>
            <svg x-show="loading" class="w-6 h-6 animate-spin" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            <span x-text="loading ? 'Registrando...' : 'Registrar Llegada'"></span>
        </button>
        @endif
    </div>
</div>
@elseif($turnoHoy && $turnoHoy->es_descanso)
{{-- Rest Day Card --}}
<div class="dashboard-card mb-6">
    <div class="p-8 text-center">
        <div class="w-24 h-24 mx-auto mb-4 bg-gray-100 rounded-full flex items-center justify-center">
            <i class="fas fa-bed text-5xl text-gray-400"></i>
        </div>
        <p class="font-bold text-gray-800 text-2xl mb-2">Día de Descanso</p>
        <p class="text-gray-500 mb-4">Hoy no tienes turno asignado. ¡Disfruta tu descanso!</p>
        <div class="inline-flex items-center gap-2 px-4 py-2 bg-green-50 border border-green-200 rounded-xl">
            <i class="fas fa-check-circle text-green-500"></i>
            <span class="text-green-700 font-medium">Grupo: {{ $turnoHoy->grupo_descanso ?? 'N/A' }}</span>
        </div>
    </div>
</div>
@else
{{-- No Turno Card --}}
<div class="dashboard-card mb-6">
    <div class="p-8 text-center">
        <div class="w-24 h-24 mx-auto mb-4 bg-gray-100 rounded-full flex items-center justify-center">
            <i class="fas fa-calendar-times text-5xl text-gray-400"></i>
        </div>
        <p class="font-bold text-gray-800 text-2xl mb-2">Sin Turno Asignado</p>
        <p class="text-gray-500 mb-4">No tienes un turno asignado para hoy.</p>
        @if($notificaciones->count() > 0)
        <a href="{{ route('conductor.notificaciones') }}" class="btn-modern bg-emerald-500 hover:bg-emerald-600 text-white px-6 py-3 rounded-xl font-medium inline-flex items-center gap-2">
            <i class="fas fa-bell"></i>
            Ver Notificaciones ({{ $notificaciones->count() }})
        </a>
        @endif
    </div>
</div>
@endif

{{-- Quick Actions --}}
<div class="dashboard-card mb-6">
    <div class="section-header">
        <h3><i class="fas fa-bolt text-emerald-500 mr-2"></i>Acciones Rápidas</h3>
    </div>
    <div class="p-4 space-y-3">
        <a href="{{ route('conductor.historial') }}" class="action-btn">
            <i class="fas fa-history icon-history"></i>
            <span>Ver Historial</span>
            <i class="fas fa-chevron-right ml-auto text-gray-400"></i>
        </a>
        <a href="{{ route('conductor.multas') }}" class="action-btn">
            <i class="fas fa-exclamation-triangle icon-multa"></i>
            <span>Mis Multas</span>
            @if(isset($multasPendientes) && $multasPendientes > 0)
            <span class="ml-auto px-2 py-0.5 bg-red-500 text-white text-xs rounded-full">{{ $multasPendientes }}</span>
            @else
            <i class="fas fa-chevron-right ml-auto text-gray-400"></i>
            @endif
        </a>
        <a href="{{ route('conductor.justificativos') }}" class="action-btn">
            <i class="fas fa-file-alt icon-justif"></i>
            <span>Justificativos</span>
            <i class="fas fa-chevron-right ml-auto text-gray-400"></i>
        </a>
        <a href="{{ route('conductor.notificaciones') }}" class="action-btn">
            <i class="fas fa-bell icon-notif"></i>
            <span>Notificaciones</span>
            @if($notificaciones->count() > 0)
            <span class="ml-auto px-2 py-0.5 bg-red-500 text-white text-xs rounded-full">{{ $notificaciones->count() }}</span>
            @else
            <i class="fas fa-chevron-right ml-auto text-gray-400"></i>
            @endif
        </a>
    </div>
</div>

{{-- Notifications Preview --}}
@if($notificaciones->count() > 0)
<div class="dashboard-card mb-6">
    <div class="section-header">
        <h3><i class="fas fa-bell text-emerald-500 mr-2"></i>Últimas Notificaciones</h3>
        <a href="{{ route('conductor.notificaciones') }}">Ver todas <i class="fas fa-arrow-right"></i></a>
    </div>
    <div class="p-4 space-y-2">
        @foreach($notificaciones->take(3) as $notif)
        <div class="p-3 bg-gray-50 rounded-xl flex items-start gap-3">
            <div class="w-2 h-2 bg-emerald-500 rounded-full mt-2"></div>
            <div class="flex-1 min-w-0">
                <p class="text-sm text-gray-800 truncate">{{ $notif->mensaje }}</p>
                <p class="text-xs text-gray-500 mt-1">{{ $notif->enviado_at->diffForHumans() }}</p>
            </div>
        </div>
        @endforeach
    </div>
</div>
@endif

{{-- Toast Messages --}}
<div x-show="toast.show" 
     x-transition:enter="transition ease-out duration-300"
     x-transition:enter-start="translate-y-full opacity-0"
     x-transition:enter-end="translate-y-0 opacity-100"
     x-transition:leave="transition ease-in duration-200"
     x-transition:leave-start="translate-y-0 opacity-100"
     x-transition:leave-end="translate-y-full opacity-0"
     :class="toast.type === 'success' ? 'bg-emerald-600' : 'bg-red-600'"
     class="fixed bottom-20 left-4 right-4 mx-auto max-w-md p-4 rounded-2xl text-white shadow-xl z-50">
    <p class="text-center font-medium" x-text="toast.message"></p>
</div>
@endsection

@section('scripts')
<script>
function conductorApp() {
    return {
        gpsEnabled: false,
        currentPosition: { lat: null, lng: null, speed: null },
        loading: false,
        loadingPresencia: false,
        watchId: null,
        salidaId: null,
        turnoId: {{ $turnoHoy && !$turnoHoy->es_descanso ? $turnoHoy->turno_id : 'null' }},
        toast: { show: false, message: '', type: 'success' },
        
        init() {
            this.checkGps();
            @if($enRuta && $ultimaSalida)
                this.salidaId = {{ $ultimaSalida ? $ultimaSalida->salida_id : 'null' }};
            @endif
        },
        
        checkGps() {
            if ('geolocation' in navigator) {
                this.gpsEnabled = true;
                this.startTracking();
            } else {
                this.gpsEnabled = false;
            }
        },
        
        startTracking() {
            this.watchId = navigator.geolocation.watchPosition(
                (position) => {
                    this.gpsEnabled = true;
                    this.currentPosition = {
                        lat: position.coords.latitude.toFixed(6),
                        lng: position.coords.longitude.toFixed(6),
                        speed: position.coords.speed ? Math.round(position.coords.speed * 3.6) : 0
                    };
                    
                    if (this.salidaId && !this.loading) {
                        this.enviarPosicion();
                    }
                },
                (error) => {
                    console.log('GPS error:', error);
                    this.gpsEnabled = false;
                },
                { enableHighAccuracy: true, maximumAge: 10000 }
            );
        },
        
        async enviarPosicion() {
            try {
                await fetch('{{ route('conductor.registrarPosicion') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({
                        salida_id: this.salidaId,
                        latitud: this.currentPosition.lat,
                        longitud: this.currentPosition.lng,
                        velocidad: this.currentPosition.speed
                    })
                });
            } catch (e) {
                console.log('Error enviando posición');
            }
        },
        
        async confirmarPresencia(esPuntero) {
            if (!this.gpsEnabled) {
                this.showToast('Active el GPS para continuar', 'error');
                return;
            }
            
            this.loadingPresencia = true;
            try {
                const response = await fetch('{{ route('conductor.confirmarPresencia') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({
                        latitud: this.currentPosition.lat,
                        longitud: this.currentPosition.lng,
                        es_puntero: esPuntero
                    })
                });
                
                const data = await response.json();
                
                if (response.ok) {
                    this.showToast(data.message, 'success');
                    setTimeout(() => location.reload(), 1500);
                } else {
                    this.showToast(data.error || 'Error al confirmar', 'error');
                }
            } catch (e) {
                this.showToast('Error de conexión', 'error');
            }
            this.loadingPresencia = false;
        },
        
        async registrarSalida() {
            if (!this.gpsEnabled) {
                this.showToast('Active el GPS para continuar', 'error');
                return;
            }
            
            this.loading = true;
            try {
                const response = await fetch('{{ route('conductor.registrarSalida') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({
                        turno_id: this.turnoId,
                        latitud: this.currentPosition.lat,
                        longitud: this.currentPosition.lng,
                        velocidad: this.currentPosition.speed
                    })
                });
                
                const data = await response.json();
                
                if (response.ok) {
                    this.salidaId = data.salida_id;
                    this.showToast(data.mensaje, 'success');
                    setTimeout(() => location.reload(), 1500);
                } else {
                    this.showToast(data.error || 'Error al registrar', 'error');
                }
            } catch (e) {
                this.showToast('Error de conexión', 'error');
            }
            this.loading = false;
        },
        
        async registrarLlegada() {
            if (!this.gpsEnabled) {
                this.showToast('Active el GPS para continuar', 'error');
                return;
            }
            
            this.loading = true;
            try {
                const response = await fetch('{{ route('conductor.registrarLlegada') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({
                        salida_id: this.salidaId,
                        latitud: this.currentPosition.lat,
                        longitud: this.currentPosition.lng,
                        velocidad: this.currentPosition.speed
                    })
                });
                
                const data = await response.json();
                
                if (response.ok) {
                    this.showToast(data.mensaje, 'success');
                    setTimeout(() => location.reload(), 2000);
                } else {
                    this.showToast(data.error || 'Error al registrar', 'error');
                }
            } catch (e) {
                this.showToast('Error de conexión', 'error');
            }
            this.loading = false;
        },
        
        showToast(message, type = 'success') {
            this.toast = { show: true, message, type };
            setTimeout(() => {
                this.toast.show = false;
            }, 3000);
        }
    };
}
</script>
@endsection
