@extends('layouts.gerente')

@section('title', 'Reporte GPS')
@section('header', 'Reporte GPS')

@section('content')
<div class="mb-6 flex items-center justify-between">
    <a href="{{ route('gerente.reportes.index') }}" class="flex items-center gap-2 px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-xl text-sm font-medium transition-colors">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
        </svg>
        Volver al Menú
    </a>
</div>

<div class="mb-6 bg-white rounded-2xl shadow-sm border border-gray-100 p-4">
    <form method="GET" action="{{ route('gerente.reportes.gps') }}" class="flex flex-wrap gap-4 items-end">
        <div class="flex-1 min-w-[200px]">
            <label class="block text-sm font-medium text-gray-700 mb-1">Fecha</label>
            <input type="date" name="fecha" value="{{ $fecha->format('Y-m-d') }}" 
                   class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
        </div>
        <div class="flex gap-2">
            <button type="submit" class="px-4 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-sm font-medium transition-colors">
                Filtrar
            </button>
            <button type="submit" name="formato" value="pdf" class="px-4 py-2.5 bg-red-600 hover:bg-red-700 text-white rounded-xl text-sm font-medium transition-colors flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                Exportar PDF
            </button>
        </div>
    </form>
</div>

<div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-4 text-center">
        <p class="text-3xl font-bold text-emerald-600">{{ $totalSalidas }}</p>
        <p class="text-sm text-gray-500 mt-1">Total Salidas</p>
    </div>
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-4 text-center">
        <p class="text-3xl font-bold text-blue-600">{{ $completadas }}</p>
        <p class="text-sm text-gray-500 mt-1">Completadas</p>
    </div>
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-4 text-center">
        <p class="text-3xl font-bold text-amber-600">{{ $enRuta }}</p>
        <p class="text-sm text-gray-500 mt-1">En Ruta</p>
    </div>
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-4 text-center">
        <p class="text-3xl font-bold text-purple-600">{{ $tiempoPromedio }} <span class="text-lg">min</span></p>
        <p class="text-sm text-gray-500 mt-1">Tiempo Promedio</p>
    </div>
</div>

<div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-sm text-left">
            <thead class="bg-gray-50 text-gray-700">
                <tr>
                    <th class="px-4 py-3 font-semibold">Hora</th>
                    <th class="px-4 py-3 font-semibold">Conductor</th>
                    <th class="px-4 py-3 font-semibold">Unidad</th>
                    <th class="px-4 py-3 font-semibold">Origen</th>
                    <th class="px-4 py-3 font-semibold">Destino</th>
                    <th class="px-4 py-3 font-semibold">Tiempo</th>
                    <th class="px-4 py-3 font-semibold">Estado</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($salidas as $salida)
                <tr class="hover:bg-gray-50 transition-colors">
                    <td class="px-4 py-3 font-mono text-gray-600">{{ $salida->hora_salida->format('H:i') }}</td>
                    <td class="px-4 py-3">
                        <div class="font-medium text-gray-900">{{ $salida->turno?->usuario?->nombres ?? 'N/A' }} {{ $salida->turno?->usuario?->apellidos ?? '' }}</div>
                    </td>
                    <td class="px-4 py-3 text-gray-600">{{ $salida->turno?->unidad?->placa ?? 'N/A' }}</td>
                    <td class="px-4 py-3 text-gray-600">{{ $salida->puntoOrigen->nombre ?? 'N/A' }}</td>
                    <td class="px-4 py-3 text-gray-600">{{ $salida->llegada?->puntoDestino?->nombre ?? '-' }}</td>
                    <td class="px-4 py-3 text-gray-600">
                        @if($salida->llegada)
                            {{ $salida->llegada->tiempo_trayecto_min }} min
                        @else
                            -
                        @endif
                    </td>
                    <td class="px-4 py-3">
                        @if($salida->llegada)
                            <span class="px-2.5 py-1 bg-emerald-100 text-emerald-700 rounded-full text-xs font-medium">Completada</span>
                        @else
                            <span class="px-2.5 py-1 bg-amber-100 text-amber-700 rounded-full text-xs font-medium">En Ruta</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="px-4 py-12 text-center text-gray-500">No hay salidas registradas para esta fecha</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
