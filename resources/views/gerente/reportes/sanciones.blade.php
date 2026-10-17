@extends('layouts.gerente')

@section('title', 'Reporte de Sanciones')
@section('header', 'Reporte de Sanciones')

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
    <form method="GET" action="{{ route('gerente.reportes.sanciones') }}" class="flex flex-wrap gap-4 items-end">
        <div class="flex-1 min-w-[150px]">
            <label class="block text-sm font-medium text-gray-700 mb-1">Fecha Inicio</label>
            <input type="date" name="fecha_inicio" value="{{ $fechaInicio->format('Y-m-d') }}" 
                   class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
        </div>
        <div class="flex-1 min-w-[150px]">
            <label class="block text-sm font-medium text-gray-700 mb-1">Fecha Fin</label>
            <input type="date" name="fecha_fin" value="{{ $fechaFin->format('Y-m-d') }}" 
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

<div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-4">
        <p class="text-sm text-gray-500 mb-1">Total Generado</p>
        <p class="text-2xl font-bold text-gray-800">$ {{ number_format($totalGeneral, 2) }}</p>
    </div>
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-4">
        <p class="text-sm text-gray-500 mb-1">Total Cobrado</p>
        <p class="text-2xl font-bold text-emerald-600">$ {{ number_format($totalCobrado, 2) }}</p>
    </div>
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-4">
        <p class="text-sm text-gray-500 mb-1">Total Pendiente</p>
        <p class="text-2xl font-bold text-amber-600">$ {{ number_format($totalPendiente, 2) }}</p>
    </div>
</div>

<div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-sm text-left">
            <thead class="bg-gray-50 text-gray-700">
                <tr>
                    <th class="px-4 py-3 font-semibold">Fecha</th>
                    <th class="px-4 py-3 font-semibold">Conductor</th>
                    <th class="px-4 py-3 font-semibold">Tipo</th>
                    <th class="px-4 py-3 font-semibold">Monto</th>
                    <th class="px-4 py-3 font-semibold">Estado</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($sanciones as $sancion)
                <tr class="hover:bg-gray-50 transition-colors">
                    <td class="px-4 py-3 text-gray-600">{{ $sancion->fecha->format('d/m/Y') }}</td>
                    <td class="px-4 py-3">
                        <div class="font-medium text-gray-900">{{ $sancion->usuario->nombres ?? 'N/A' }} {{ $sancion->usuario->apellidos ?? '' }}</div>
                    </td>
                    <td class="px-4 py-3 text-gray-600">
                        @switch($sancion->tipo)
                            @case('no_presentacion_conductor')
                                No presentación
                                @break
                            @case('ausencia_puntero')
                                Ausencia puntero
                                @break
                            @case('multa_retraso')
                                Multa retraso
                                @break
                            @default
                                {{ $sancion->tipo }}
                        @endswitch
                    </td>
                    <td class="px-4 py-3 font-medium text-gray-800">$ {{ number_format($sancion->monto, 2) }}</td>
                    <td class="px-4 py-3">
                        @if($sancion->estado === 'cobrada')
                            <span class="px-2.5 py-1 bg-emerald-100 text-emerald-700 rounded-full text-xs font-medium">Cobrada</span>
                        @else
                            <span class="px-2.5 py-1 bg-amber-100 text-amber-700 rounded-full text-xs font-medium">Pendiente</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-4 py-12 text-center text-gray-500">No hay sanciones en este período</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
