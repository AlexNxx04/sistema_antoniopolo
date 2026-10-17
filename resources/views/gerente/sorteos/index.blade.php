@extends('layouts.gerente')

@section('title', 'Sorteos')
@section('header', 'Sorteos')

@section('content')
<div class="fade-in">
    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-5 mb-8">
        <div>
            <h2 class="text-3xl font-bold text-gray-800">
                <span class="gradient-text">Sorteos</span>
            </h2>
            <p class="text-gray-400 mt-1">Gestión de turnos y ciclos anuales</p>
        </div>
    </div>

    @if(session('success'))
        <div class="mb-6 p-4 bg-gradient-to-r from-emerald-50 to-green-50 border border-emerald-200 rounded-2xl text-emerald-700 flex items-center gap-3 fade-in">
            <i class="fas fa-check-circle text-xl"></i>
            {{ session('success') }}
        </div>
    @endif

    {{-- Ciclos Activos --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
        <div class="modern-card p-5 fade-in delay-100">
            <div class="flex items-center justify-between mb-4">
                <h3 class="font-bold text-gray-800 flex items-center gap-2">
                    <i class="fas fa-calendar text-emerald-500"></i>
                    Ciclos Activos
                </h3>
                <span class="badge-modern badge-success">{{ $ciclos->where('activo', 1)->count() }}</span>
            </div>
            <div class="space-y-3">
                @forelse($ciclos as $ciclo)
                <div class="flex items-center justify-between p-3 bg-emerald-50 rounded-xl">
                    <div>
                        <p class="font-semibold text-gray-800">Año {{ $ciclo->anio }}</p>
                        <p class="text-xs text-gray-500">{{ $ciclo->fecha_inicio?->format('d/m/Y') }} - {{ $ciclo->fecha_fin?->format('d/m/Y') }}</p>
                    </div>
                    @if($ciclo->activo)
                        <span class="badge-modern badge-success">Activo</span>
                    @endif
                </div>
                @empty
                <p class="text-gray-500 text-center py-4">No hay ciclos</p>
                @endforelse
            </div>
        </div>

        <div class="modern-card p-5 fade-in delay-200">
            <div class="flex items-center justify-between mb-4">
                <h3 class="font-bold text-gray-800 flex items-center gap-2">
                    <i class="fas fa-history text-blue-500"></i>
                    Historial de Sorteos
                </h3>
            </div>
            <div class="space-y-3">
                @forelse($historial as $item)
                <div class="flex items-center justify-between p-3 bg-gray-50 rounded-xl">
                    <div>
                        <p class="font-semibold text-gray-700">{{ $item['fecha'] }}</p>
                        <p class="text-xs text-gray-400">{{ $item['puntos'] }} puntos</p>
                    </div>
                    <span class="badge-modern badge-info">{{ $item['turnos'] }} turnos</span>
                </div>
                @empty
                <p class="text-gray-500 text-center py-4">No hay historial</p>
                @endforelse
            </div>
        </div>
    </div>

    {{-- Tabla de Turnos --}}
    <div class="modern-card overflow-hidden fade-in delay-300">
        <div class="p-5 border-b border-gray-100 bg-gradient-to-r from-emerald-50 to-white">
            <h3 class="font-bold text-gray-800 flex items-center gap-2">
                <i class="fas fa-list text-emerald-500"></i>
                Turnos Recientes
            </h3>
        </div>
        <div class="overflow-x-auto">
            <table class="table-modern">
                <thead>
                    <tr>
                        <th class="px-5 py-4"><i class="fas fa-calendar mr-1"></i> Fecha</th>
                        <th class="px-5 py-4"><i class="fas fa-map-marker-alt mr-1"></i> Punto</th>
                        <th class="px-5 py-4"><i class="fas fa-users mr-1"></i> Conductor</th>
                        <th class="px-5 py-4"><i class="fas fa-car mr-1"></i> Unidad</th>
                        <th class="px-5 py-4"><i class="fas fa-stream mr-1"></i> Posición</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($turnos as $turno)
                    <tr class="hover:bg-emerald-50/30">
                        <td class="px-5 py-4 text-gray-600">{{ $turno->fecha?->format('d/m/Y') }}</td>
                        <td class="px-5 py-4">{{ $turno->punto->nombre ?? 'N/A' }}</td>
                        <td class="px-5 py-4">
                            <div class="flex items-center gap-2">
                                <div class="avatar bg-gradient-to-br from-emerald-400 to-emerald-600 text-xs">
                                    {{ substr($turno->usuario->nombres ?? 'U', 0, 1) }}
                                </div>
                                {{ $turno->usuario->nombres ?? 'N/A' }}
                            </div>
                        </td>
                        <td class="px-5 py-4">{{ $turno->unidad->placa ?? 'Sin unidad' }}</td>
                        <td class="px-5 py-4">
                            <span class="font-bold text-gray-800">#{{ $turno->posicion }}</span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-5 py-16 text-center">
                            <div class="w-24 h-24 mx-auto mb-4 bg-emerald-50 rounded-full flex items-center justify-center">
                                <i class="fas fa-ticket-alt text-4xl text-emerald-300"></i>
                            </div>
                            <p class="text-gray-500 text-lg">No hay turnos</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection