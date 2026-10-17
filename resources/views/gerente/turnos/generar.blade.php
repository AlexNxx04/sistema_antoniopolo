@extends('layouts.gerente')

@section('title', 'Configurar Turnos')
@section('header', 'Configurar Turnos del Ciclo')

@section('content')
<div class="mb-6 flex items-center gap-3">
    <a href="{{ route('gerente.turnos.index') }}" class="p-2 hover:bg-gray-100 rounded-lg transition-colors">
        <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
        </svg>
    </a>
    <div>
        <h2 class="text-2xl font-bold text-gray-800">Configurar Turnos del Ciclo</h2>
        <p class="text-gray-500 text-sm mt-1">El sistema generará turnos aleatorios diarios según los grupos de descanso</p>
    </div>
</div>

@if(session('success'))
<div class="mb-6 p-4 bg-emerald-50 border border-emerald-200 text-emerald-700 rounded-xl flex items-center gap-3">
    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
    </svg>
    {{ session('success') }}
</div>
@endif

@if(session('error'))
<div class="mb-6 p-4 bg-red-50 border border-red-200 text-red-700 rounded-xl flex items-center gap-3">
    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
    </svg>
    {{ session('error') }}
</div>
@endif

{{-- Selector de Fecha y Punto --}}
<div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 mb-6">
    <form method="GET" action="{{ route('gerente.turnos.generarForm') }}" class="flex flex-wrap gap-4 items-end">
        <div class="flex-1 min-w-[200px]">
            <label class="block text-sm font-medium text-gray-700 mb-1.5">Fecha</label>
            <input type="date" name="fecha" value="{{ $fecha->format('Y-m-d') }}" required
                   class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20">
        </div>
        <div class="flex-1 min-w-[200px]">
            <label class="block text-sm font-medium text-gray-700 mb-1.5">Punto de Operación</label>
            <select name="punto_id" required
                    class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20">
                <option value="">Seleccionar punto...</option>
                @foreach($puntos as $punto)
                <option value="{{ $punto->punto_id }}" {{ $puntoId == $punto->punto_id ? 'selected' : '' }}>
                    {{ $punto->nombre }}
                </option>
                @endforeach
            </select>
        </div>
        <button type="submit" class="px-6 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-sm font-medium transition-colors">
            Cargar
        </button>
    </form>
</div>

@if($puntoId && $cicloActivo)
@php
    $conductores = $conductoresDisponibles ?? collect();
    $total = $conductores->count();
@endphp

{{-- Información --}}
<div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
    <div class="bg-gradient-to-br from-emerald-500 to-emerald-600 rounded-2xl p-4 text-white shadow-lg">
        <p class="text-emerald-100 text-sm font-medium">Total Conductores</p>
        <p class="text-3xl font-bold mt-1">{{ $conductoresDisponibles->count() }}</p>
    </div>
    <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-2xl p-4 text-white shadow-lg">
        <p class="text-blue-100 text-sm font-medium">Ciclo Activo</p>
        <p class="text-lg font-bold mt-1">{{ $cicloActivo->anio }}</p>
    </div>
    <div class="bg-gradient-to-br from-purple-500 to-purple-600 rounded-2xl p-4 text-white shadow-lg">
        <p class="text-purple-100 text-sm font-medium">Estado Configuración</p>
        <p class="text-lg font-bold mt-1">{{ $yaGenerado ? 'Configurado' : 'Sin configurar' }}</p>
    </div>
</div>

@if($yaGenerado)
{{-- Ya existe configuración --}}
<div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 mb-6">
    <div class="flex items-center gap-4 mb-4">
        <div class="w-12 h-12 rounded-full bg-emerald-100 flex items-center justify-center">
            <svg class="w-6 h-6 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
            </svg>
        </div>
        <div>
            <h3 class="font-bold text-gray-800">Configuración Ya Creada</h3>
            <p class="text-sm text-gray-500">Los grupos de descanso ya están configurados. Los turnos se generan automáticamente cada día.</p>
        </div>
    </div>
    
    @if($turnoConfig)
    @php
        $config = json_decode($turnoConfig->config_json, true);
    @endphp
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mt-4">
        <div class="p-3 bg-gray-50 rounded-xl">
            <p class="text-xs text-gray-500">Días de Trabajo</p>
            <p class="font-bold text-gray-800">{{ $config['dias_trabajo'] ?? '-' }}</p>
        </div>
        <div class="p-3 bg-gray-50 rounded-xl">
            <p class="text-xs text-gray-500">Días de Descanso</p>
            <p class="font-bold text-gray-800">{{ $config['dias_descanso'] ?? '-' }}</p>
        </div>
        <div class="p-3 bg-gray-50 rounded-xl">
            <p class="text-xs text-gray-500">Total Conductores</p>
            <p class="font-bold text-gray-800">{{ $config['total_conductores'] ?? '-' }}</p>
        </div>
        <div class="p-3 bg-gray-50 rounded-xl">
            <p class="text-xs text-gray-500">Grupos Creados</p>
            <p class="font-bold text-gray-800">{{ count($config['grupos'] ?? []) }}</p>
        </div>
    </div>
    
    <div class="mt-4 p-4 bg-blue-50 border border-blue-200 rounded-xl">
        <p class="text-sm text-blue-700">
            <strong>Grupos de descanso:</strong>
            @foreach($config['grupos'] ?? [] as $index => $grupo)
                <span class="inline-block px-2 py-1 bg-blue-100 rounded text-xs ml-1">
                    Día {{ $grupo['dia_descanso'] == 0 ? 'Domingo' : ucfirst(\Carbon\Carbon::createFromFormat('w', $grupo['dia_descanso'])->locale('es')->dayName) }}: {{ count($grupo['conductores']) }} cond.
                </span>
            @endforeach
        </p>
    </div>
    @endif
    
    @if(!$turnosDelDia)
    <form action="{{ route('gerente.turnos.generarDelDia') }}" method="POST" class="mt-4">
        @csrf
        <input type="hidden" name="fecha" value="{{ $fecha->format('Y-m-d') }}">
        <input type="hidden" name="punto_id" value="{{ $puntoId }}">
        <button type="submit" class="px-6 py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-xl text-sm font-medium transition-colors">
            Generar Turnos de Hoy ({{ $fecha->format('d/m/Y') }})
        </button>
    </form>
    @else
    <div class="mt-4 p-4 bg-emerald-50 border border-emerald-200 rounded-xl">
        <p class="text-sm text-emerald-700 font-medium">
            Los turnos para hoy ya fueron generados. Puede verlos en la lista de turnos.
        </p>
    </div>
    @endif
</div>

@else
{{-- Formulario de Configuración Inicial --}}
<div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
    <div class="px-6 py-4 border-b border-gray-100 bg-gradient-to-r from-emerald-50 to-white">
        <h3 class="font-bold text-gray-800">Configurar Grupos de Descanso</h3>
        <p class="text-sm text-gray-500 mt-1">
            Seleccione cuántos días a la semana trabajan los conductores. El sistema creará grupos de descanso automáticamente.
        </p>
    </div>
    
    <form action="{{ route('gerente.turnos.generar') }}" method="POST">
        @csrf
        <input type="hidden" name="fecha" value="{{ $fecha->format('Y-m-d') }}">
        <input type="hidden" name="punto_id" value="{{ $puntoId }}">
        
        <div class="p-6">
            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-3">Días de trabajo por semana</label>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                    @for($i = 4; $i <= 7; $i++)
                    <label class="flex items-center p-4 bg-gray-50 rounded-xl border-2 border-gray-200 hover:border-emerald-300 cursor-pointer transition-colors">
                        <input type="radio" name="dias_trabajo" value="{{ $i }}" {{ $i == 6 ? 'checked' : '' }}
                               class="w-5 h-5 text-emerald-600 border-gray-300 focus:ring-emerald-500">
                        <div class="ml-3">
                            <p class="font-semibold text-gray-900">{{ $i }} días</p>
                            <p class="text-xs text-gray-500">{{ 7 - $i }} días descanso</p>
                        </div>
                    </label>
                    @endfor
                </div>
                @error('dias_trabajo')
                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
            
            <div class="p-4 bg-amber-50 border border-amber-200 rounded-xl">
                <h4 class="font-semibold text-amber-800 mb-2">¿Cómo funciona?</h4>
                <ul class="text-sm text-amber-700 space-y-1">
                    <li>1. El sistema divide los {{ $total }} conductores en {{ 7 - $diasTrabajo ?? 1 }} grupos</li>
                    <li>2. Cada grupo descansa un día diferente de la semana</li>
                    <li>3. Cada día, los conductores que NO descansan operan</li>
                    <li>4. Los 2 primeros operadores son punteros</li>
                    <li>5. Los punteros deben iniciar juntos a las 5:00 AM</li>
                </ul>
            </div>
        </div>
        
        <div class="px-6 py-4 border-t border-gray-100 flex items-center justify-end bg-gray-50">
            <button type="submit" class="px-6 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-sm font-medium transition-colors shadow-lg flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
                Guardar Configuración e Iniciar
            </button>
        </div>
    </form>
</div>
@endif

@elseif($puntoId && !$cicloActivo)
<div class="bg-yellow-50 border border-yellow-200 rounded-xl p-6 text-center">
    <svg class="w-12 h-12 text-yellow-500 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
    </svg>
    <h3 class="font-bold text-yellow-800 mb-2">No hay ciclo anual activo</h3>
    <p class="text-sm text-yellow-700">Active un ciclo anual para poder configurar los turnos.</p>
</div>
@endif
@endsection
