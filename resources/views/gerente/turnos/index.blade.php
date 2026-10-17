@extends('layouts.gerente')

@section('title', 'Turnos Diarios')
@section('header', 'Turnos Diarios')

@section('content')
<div class="mb-6 flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
    <div>
        <h2 class="text-2xl font-bold text-gray-800">Turnos Diarios</h2>
        <p class="text-gray-500 text-sm mt-1">Gestiona los turnos de operación por fecha y punto</p>
    </div>
    @if($cicloActivo)
    <a href="{{ route('gerente.turnos.generarForm', ['fecha' => $fecha->format('Y-m-d'), 'punto_id' => $puntoId]) }}" 
       class="inline-flex items-center gap-2 px-5 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-medium rounded-xl transition-colors">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
        </svg>
        Generar / Editar Turnos
    </a>
    @else
    <div class="p-4 bg-yellow-50 border border-yellow-200 rounded-xl">
        <p class="text-yellow-700 text-sm">
            <strong>Activa un ciclo anual</strong> para generar turnos
        </p>
    </div>
    @endif
</div>

@if(session('success'))
<div class="mb-4 p-4 bg-emerald-50 border border-emerald-200 text-emerald-700 rounded-xl flex items-center gap-3">
    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
    </svg>
    {{ session('success') }}
</div>
@endif

@if($errors->any())
<div class="mb-4 p-4 bg-red-50 border border-red-200 rounded-xl">
    @foreach($errors->all() as $error)
        <p class="text-red-700 text-sm">{{ $error }}</p>
    @endforeach
</div>
@endif

{{-- Estadísticas --}}
<div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-4 mb-6">
    <div class="bg-white rounded-xl p-4 border border-gray-100 text-center">
        <p class="text-gray-500 text-xs">Total</p>
        <p class="text-2xl font-bold text-gray-800">{{ $estadisticas['total'] }}</p>
    </div>
    <div class="bg-white rounded-xl p-4 border border-gray-100 text-center">
        <p class="text-gray-500 text-xs">Operando</p>
        <p class="text-2xl font-bold text-green-600">{{ $estadisticas['operando'] }}</p>
    </div>
    <div class="bg-white rounded-xl p-4 border border-gray-100 text-center">
        <p class="text-gray-500 text-xs">Bloqueados</p>
        <p class="text-2xl font-bold text-red-600">{{ $estadisticas['bloqueados'] }}</p>
    </div>
    <div class="bg-white rounded-xl p-4 border border-gray-100 text-center">
        <p class="text-gray-500 text-xs">Descansando</p>
        <p class="text-2xl font-bold text-purple-600">{{ $estadisticas['descansando'] }}</p>
    </div>
    <div class="bg-white rounded-xl p-4 border border-gray-100 text-center">
        <p class="text-gray-500 text-xs">Punteros</p>
        <p class="text-2xl font-bold text-amber-600">{{ $estadisticas['punteros'] }}</p>
    </div>
    <div class="bg-white rounded-xl p-4 border border-gray-100 flex items-center justify-center gap-2">
        <a href="{{ route('gerente.turnos.index', ['fecha' => $fecha->copy()->subDay()->format('Y-m-d'), 'punto_id' => $puntoId]) }}" 
           class="p-2 hover:bg-gray-100 rounded-lg transition-colors">
            <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
        </a>
        <span class="font-semibold text-gray-700 text-sm min-w-[80px] text-center">{{ $fecha->format('d/m/Y') }}</span>
        <a href="{{ route('gerente.turnos.index', ['fecha' => $fecha->copy()->addDay()->format('Y-m-d'), 'punto_id' => $puntoId]) }}" 
           class="p-2 hover:bg-gray-100 rounded-lg transition-colors">
            <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
            </svg>
        </a>
    </div>
</div>

@php
    $turnosFiltrados = $puntoId ? $turnos->where('punto_id', $puntoId) : $turnos;
    $punteros = $turnosFiltrados->where('es_puntero', 1);
    $conductores = $turnosFiltrados->filter(function($t) {
        return !$t->es_puntero && !$t->es_descanso;
    });
    $descanso = $turnosFiltrados->where('es_descanso', 1);
    $bloqueados = $turnosFiltrados->where('bloqueado', 1);
@endphp

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
    
    {{-- PUNTEROS --}}
    <div class="bg-white rounded-2xl border border-gray-100 overflow-hidden">
        <div class="px-4 py-3 border-b border-gray-100 bg-gradient-to-r from-amber-50 to-white">
            <h3 class="font-bold text-amber-800 flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
                PUNTEROS (2 por punto)
                <span class="ml-auto px-2 py-0.5 bg-amber-200 text-amber-800 rounded-full text-xs font-bold">
                    {{ $punteros->count() }}
                </span>
            </h3>
            <p class="text-xs text-amber-600 mt-1">Deben iniciar juntos. Si uno falta, opera hasta las 9 AM.</p>
        </div>
        <div class="p-4 max-h-80 overflow-y-auto">
            @forelse($punteros as $turno)
            <div class="mb-3 p-3 rounded-xl border {{ $turno->bloqueado ? 'bg-red-50 border-red-200' : ($turno->hora_limite ? 'bg-amber-50 border-amber-200' : 'bg-green-50 border-green-200') }}">
                <div class="flex items-center justify-between mb-2">
                    <div class="flex items-center gap-2">
                        <span class="w-8 h-8 rounded-full bg-amber-500 text-white flex items-center justify-center font-bold text-sm">
                            {{ $turno->posicion }}
                        </span>
                        <div>
                            <p class="font-semibold text-gray-900">{{ $turno->usuario->nombres ?? 'N/A' }}</p>
                            <p class="text-xs text-gray-500">{{ $turno->usuario->cedula ?? '' }}</p>
                        </div>
                    </div>
                    @if($turno->bloqueado)
                        <span class="px-2 py-1 bg-red-500 text-white rounded text-xs font-bold">BLOQUEADO</span>
                    @elseif($turno->hora_limite)
                        <span class="px-2 py-1 bg-amber-500 text-white rounded text-xs font-bold">Hasta 9 AM</span>
                    @else
                        <span class="px-2 py-1 bg-green-500 text-white rounded text-xs font-bold">ACTIVO</span>
                    @endif
                </div>
                @if($turno->bloqueado)
                    <p class="text-xs text-red-600 italic">{{ $turno->motivo_bloqueo }}</p>
                @elseif($turno->hora_limite)
                    <p class="text-xs text-amber-600 italic">{{ $turno->justificacion_retraso ?? 'Sin justificación' }}</p>
                @endif
                @if(!$turno->bloqueado)
                <button onclick="mostrarJustificacion({{ $turno->turno_id }}, '{{ $turno->usuario->nombres ?? '' }}')"
                        class="mt-2 w-full px-3 py-1.5 bg-amber-100 hover:bg-amber-200 text-amber-700 rounded-lg text-xs font-medium transition-colors">
                    {{ $turno->hora_limite ? 'Editar Justificación' : 'Registrar Retraso' }}
                </button>
                @endif
            </div>
            @empty
            <p class="text-center text-gray-400 py-4">No hay punteros asignados</p>
            @endforelse
        </div>
    </div>

    {{-- CONDUCTORES --}}
    <div class="bg-white rounded-2xl border border-gray-100 overflow-hidden">
        <div class="px-4 py-3 border-b border-gray-100 bg-gradient-to-r from-emerald-50 to-white">
            <h3 class="font-bold text-emerald-800 flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/>
                </svg>
                CONDUCTORES
                <span class="ml-auto px-2 py-0.5 bg-emerald-200 text-emerald-800 rounded-full text-xs font-bold">
                    {{ $conductores->count() }}
                </span>
            </h3>
            <p class="text-xs text-emerald-600 mt-1">Si no se presenta, se bloquea el turno.</p>
        </div>
        <div class="p-4 max-h-80 overflow-y-auto">
            @forelse($conductores as $turno)
            <div class="mb-3 p-3 rounded-xl border {{ $turno->bloqueado ? 'bg-red-50 border-red-200' : 'bg-white border-gray-200' }}">
                <div class="flex items-center justify-between mb-2">
                    <div class="flex items-center gap-2">
                        <span class="w-8 h-8 rounded-full {{ $turno->bloqueado ? 'bg-red-500 text-white' : 'bg-emerald-500 text-white' }} flex items-center justify-center font-bold text-sm">
                            {{ $turno->posicion }}
                        </span>
                        <div>
                            <p class="font-semibold text-gray-900">{{ $turno->usuario->nombres ?? 'N/A' }}</p>
                            <p class="text-xs text-gray-500">{{ $turno->usuario->cedula ?? '' }}</p>
                        </div>
                    </div>
                    <span class="px-2 py-1 {{ $turno->bloqueado ? 'bg-red-500 text-white' : 'bg-emerald-500 text-white' }} rounded text-xs font-bold">
                        {{ $turno->bloqueado ? 'BLOQUEADO' : 'ACTIVO' }}
                    </span>
                </div>
                @if($turno->bloqueado)
                    <p class="text-xs text-red-600 italic mb-2">{{ $turno->motivo_bloqueo }}</p>
                    <form action="{{ route('gerente.turnos.desbloquear', $turno->turno_id) }}" method="POST" class="inline">
                        @csrf
                        <button type="submit" class="px-3 py-1.5 bg-blue-100 hover:bg-blue-200 text-blue-700 rounded-lg text-xs font-medium transition-colors">
                            Desbloquear
                        </button>
                    </form>
                @else
                    <button onclick="mostrarBloqueo({{ $turno->turno_id }}, '{{ $turno->usuario->nombres ?? '' }}')"
                            class="mt-2 w-full px-3 py-1.5 bg-red-100 hover:bg-red-200 text-red-700 rounded-lg text-xs font-medium transition-colors">
                        Bloquear Turno
                    </button>
                @endif
            </div>
            @empty
            <p class="text-center text-gray-400 py-4">No hay conductores</p>
            @endforelse
        </div>
    </div>

    {{-- DESCANSAN / BLOQUEADOS --}}
    <div class="bg-white rounded-2xl border border-gray-100 overflow-hidden">
        <div class="px-4 py-3 border-b border-gray-100 bg-gradient-to-r from-purple-50 to-white">
            <h3 class="font-bold text-purple-800 flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/>
                </svg>
                DESCANSAN
                <span class="ml-auto px-2 py-0.5 bg-purple-200 text-purple-800 rounded-full text-xs font-bold">
                    {{ $descanso->count() }}
                </span>
            </h3>
            <p class="text-xs text-purple-600 mt-1">Grupo de descanso semanal.</p>
        </div>
        <div class="p-4 max-h-80 overflow-y-auto">
            @forelse($descanso as $turno)
            <div class="mb-3 p-3 rounded-xl border bg-gray-50 border-gray-200">
                <div class="flex items-center gap-2">
                    <span class="w-8 h-8 rounded-full bg-purple-500 text-white flex items-center justify-center font-bold text-sm">
                        {{ $turno->posicion }}
                    </span>
                    <div>
                        <p class="font-semibold text-gray-900">{{ $turno->unidad->placa ?? 'N/A' }}</p>
                        <p class="text-xs text-gray-500">Descanso semanal</p>
                    </div>
                </div>
            </div>
            @empty
            <p class="text-center text-gray-400 py-4">Nadie descansa</p>
            @endforelse
        </div>
    </div>
</div>

{{-- Modal para Justificación de Puntero --}}
<div id="modalJustificacion" class="fixed inset-0 bg-black/50 z-50 hidden items-center justify-center">
    <div class="bg-white rounded-2xl p-6 w-full max-w-md mx-4 shadow-2xl">
        <h3 class="text-lg font-bold text-gray-900 mb-4">Registrar Retraso de Puntero</h3>
        <form action="{{ route('gerente.turnos.justificar', 0) }}" method="POST" id="formJustificacion">
            @csrf
            <input type="hidden" name="turno_id" id="justificacion_turno_id">
            <div class="mb-4">
                <p class="text-sm text-gray-600 mb-2">Conductor: <strong id="justificacion_nombre"></strong></p>
            </div>
            <div class="mb-4">
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" name="tiene_justificacion" value="1" id="tiene_justificacion" 
                           class="w-4 h-4 text-emerald-600 border-gray-300 rounded focus:ring-emerald-500">
                    <span class="text-sm text-gray-700">Tiene justificación válida</span>
                </label>
                <p class="text-xs text-gray-500 mt-1 ml-6">Si no tiene justificación, solo podrá operar hasta las 9:00 AM</p>
            </div>
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Justificación</label>
                <textarea name="justificacion" id="justificacion_texto" rows="3" required
                          class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500"
                          placeholder="Ej: Tráfico, emergencia familiar, etc."></textarea>
            </div>
            <div class="flex items-center justify-end gap-3">
                <button type="button" onclick="cerrarModal('modalJustificacion')" 
                        class="px-4 py-2 text-gray-700 hover:bg-gray-100 rounded-xl transition-colors">
                    Cancelar
                </button>
                <button type="submit" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl transition-colors">
                    Guardar
                </button>
            </div>
        </form>
    </div>
</div>

{{-- Modal para Bloquear Turno --}}
<div id="modalBloqueo" class="fixed inset-0 bg-black/50 z-50 hidden items-center justify-center">
    <div class="bg-white rounded-2xl p-6 w-full max-w-md mx-4 shadow-2xl">
        <h3 class="text-lg font-bold text-red-700 mb-4">Bloquear Turno</h3>
        <form action="{{ route('gerente.turnos.bloquear', 0) }}" method="POST" id="formBloqueo">
            @csrf
            <input type="hidden" name="turno_id" id="bloqueo_turno_id">
            <div class="mb-4">
                <p class="text-sm text-gray-600 mb-2">Conductor: <strong id="bloqueo_nombre"></strong></p>
                <div class="p-3 bg-red-50 border border-red-200 rounded-xl">
                    <p class="text-sm text-red-700">
                        <strong>Nota:</strong> Al bloquear, el conductor <strong>NO OPERARÁ EN TODO EL DÍA</strong>. Esta acción queda registrada.
                    </p>
                </div>
            </div>
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Motivo del bloqueo <span class="text-red-500">*</span></label>
                <select name="motivo" id="bloqueo_motivo" required
                        class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
                    <option value="">Seleccionar motivo...</option>
                    <option value="No se presentó después de 2 llamados">No se presentó después de 2 llamados</option>
                    <option value="Falta sin justificación">Falta sin justificación</option>
                    <option value="Renuncia temporal">Renuncia temporal</option>
                    <option value="Suspensión">Suspensión</option>
                    <option value="Otro">Otro</option>
                </select>
            </div>
            <div class="flex items-center justify-end gap-3">
                <button type="button" onclick="cerrarModal('modalBloqueo')" 
                        class="px-4 py-2 text-gray-700 hover:bg-gray-100 rounded-xl transition-colors">
                    Cancelar
                </button>
                <button type="submit" class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-xl transition-colors">
                    Bloquear Turno
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function mostrarJustificacion(turnoId, nombre) {
    document.getElementById('justificacion_turno_id').value = turnoId;
    document.getElementById('justificacion_nombre').textContent = nombre;
    document.getElementById('formJustificacion').action = '/gerente/turnos/' + turnoId + '/justificar';
    document.getElementById('modalJustificacion').classList.remove('hidden');
    document.getElementById('modalJustificacion').classList.add('flex');
}

function mostrarBloqueo(turnoId, nombre) {
    document.getElementById('bloqueo_turno_id').value = turnoId;
    document.getElementById('bloqueo_nombre').textContent = nombre;
    document.getElementById('formBloqueo').action = '/gerente/turnos/' + turnoId + '/bloquear';
    document.getElementById('modalBloqueo').classList.remove('hidden');
    document.getElementById('modalBloqueo').classList.add('flex');
}

function cerrarModal(modalId) {
    document.getElementById(modalId).classList.add('hidden');
    document.getElementById(modalId).classList.remove('flex');
}

document.getElementById('modalJustificacion').addEventListener('click', function(e) {
    if (e.target === this) cerrarModal('modalJustificacion');
});

document.getElementById('modalBloqueo').addEventListener('click', function(e) {
    if (e.target === this) cerrarModal('modalBloqueo');
});
</script>
@endsection
