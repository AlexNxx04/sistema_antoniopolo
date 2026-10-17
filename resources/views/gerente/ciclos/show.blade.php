@extends('layouts.gerente')

@section('title', 'Ciclo ' . $ciclo->anio)
@section('header', 'Ciclo ' . $ciclo->anio)

@section('content')
<div class="mb-6 flex items-center justify-between">
    <div class="flex items-center gap-3">
        <a href="{{ route('gerente.ciclos.index') }}" class="p-2 hover:bg-gray-100 rounded-lg transition-colors">
            <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
        </a>
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Ciclo {{ $ciclo->anio }}</h1>
            <p class="text-gray-500 text-sm">{{ $ciclo->fecha_inicio->format('d/m/Y') }} - {{ $ciclo->fecha_fin->format('d/m/Y') }}</p>
        </div>
    </div>
    <div class="flex items-center gap-2">
        @if(!$ciclo->activo)
            <form action="{{ route('gerente.ciclos.activar', $ciclo->ciclo_id) }}" method="POST">
                @csrf
                <button type="submit" class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-xl text-sm font-medium transition-colors">
                    Activar Ciclo
                </button>
            </form>
        @else
            <span class="px-4 py-2 bg-green-100 text-green-700 rounded-xl text-sm font-semibold">Ciclo Activo</span>
        @endif
    </div>
</div>

@if(session('success'))
<div class="mb-4 p-4 bg-emerald-50 border border-emerald-200 text-emerald-700 rounded-xl flex items-center gap-3">
    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
    </svg>
    {{ session('success') }}
</div>
@endif

<div class="mb-6 flex justify-end">
    <a href="{{ route('gerente.grupos.create', $ciclo->ciclo_id) }}" 
       class="inline-flex items-center px-4 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-medium rounded-xl transition-colors shadow-lg">
        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
        </svg>
        Nuevo Grupo de Descanso
    </a>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
    @forelse($ciclo->grupos as $grupo)
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-bold text-gray-800">{{ $grupo->nombre }}</h3>
            <span class="px-3 py-1 bg-blue-100 text-blue-700 rounded-full text-xs font-semibold">
                {{ $grupo->dia_nombre }}
            </span>
        </div>
        
        <div class="space-y-2 mb-4">
            @forelse($grupo->unidades as $unidad)
            <div class="flex items-center justify-between p-2 bg-gray-50 rounded-lg">
                <div>
                    <p class="font-medium text-gray-900">{{ $unidad->placa }}</p>
                    <p class="text-xs text-gray-500">{{ $unidad->punto->nombre ?? 'Sin punto' }}</p>
                </div>
                <span class="text-xs text-gray-400">#{{ $unidad->numero_interno }}</span>
            </div>
            @empty
            <p class="text-gray-400 text-sm text-center py-2">Sin unidades asignadas</p>
            @endforelse
        </div>

        <div class="flex items-center justify-between pt-4 border-t border-gray-100">
            <span class="text-xs text-gray-500">{{ $grupo->unidades->count() }} unidades</span>
            <div class="flex gap-1">
                <a href="{{ route('gerente.grupos.edit', [$ciclo->ciclo_id, $grupo->grupo_id]) }}" 
                   class="p-2 text-emerald-600 hover:bg-emerald-50 rounded-lg">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                    </svg>
                </a>
                <form action="{{ route('gerente.grupos.destroy', [$ciclo->ciclo_id, $grupo->grupo_id]) }}" method="POST" class="inline" onsubmit="return confirm('¿Eliminar este grupo?');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="p-2 text-red-600 hover:bg-red-50 rounded-lg">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                        </svg>
                    </button>
                </form>
            </div>
        </div>
    </div>
    @empty
    <div class="col-span-full">
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-12 text-center">
            <svg class="w-16 h-16 mx-auto text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
            </svg>
            <p class="text-gray-500 font-medium">No hay grupos de descanso</p>
            <p class="text-gray-400 text-sm mt-1">Crea grupos para gestionar los descansos rotativos</p>
        </div>
    </div>
    @endforelse
</div>
@endsection
