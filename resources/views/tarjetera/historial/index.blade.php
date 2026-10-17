@extends('layouts.tarjetera')

@section('title', 'Historial de Turnos')
@section('header', 'Historial de Turnos')

@section('content')

<div class="p-6">
    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4 mb-6">
        <div class="flex items-center gap-3">
            <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-yellow-400 to-orange-500 flex items-center justify-center shadow-lg">
                <i class="fas fa-history text-white text-lg"></i>
            </div>
            <div>
                <h2 class="text-2xl font-bold text-gray-800">Historial de Turnos</h2>
                <p class="text-gray-500 text-sm">{{ $puntoNombre }}</p>
            </div>
        </div>
        <div class="flex gap-3">
            <a href="{{ route('tarjetera.sorteo.index') }}" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 shadow-md transition-all text-sm">
                <i class="fas fa-arrow-left mr-2"></i> Volver al Sorteo
            </a>
            <a href="{{ route('tarjetera.historial.pdf', ['fecha' => $fecha]) }}" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 shadow-md transition-all text-sm">
                <i class="fas fa-file-pdf mr-2"></i> Descargar PDF
            </a>
        </div>
    </div>

    <form action="{{ route('tarjetera.historial.index') }}" method="GET" class="mb-6">
        <div class="flex gap-4 items-end">
            <div>
                <label class="block text-sm font-medium text-gray-600 mb-1">Buscar por fecha</label>
                <input type="date" name="fecha" value="{{ $fecha }}" class="px-4 py-2 border border-gray-300 rounded-xl focus:ring-2 focus:ring-yellow-500 focus:border-yellow-500">
            </div>
            <button type="submit" class="px-6 py-2 bg-yellow-500 text-white rounded-xl hover:bg-yellow-600 shadow-md transition-all">
                <i class="fas fa-search mr-2"></i> Buscar
            </button>
        </div>
    </form>

    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
        <div class="modern-card p-4 text-center border-l-4 border-l-yellow-500">
            <div class="text-3xl font-bold text-yellow-600">{{ $turnos->count() }}</div>
            <div class="text-sm text-gray-500 mt-1">Total</div>
        </div>
        <div class="modern-card p-4 text-center border-l-4 border-l-green-500">
            <div class="text-3xl font-bold text-green-600">{{ $turnos->where('es_descanso', 0)->count() }}</div>
            <div class="text-sm text-gray-500 mt-1">Operan</div>
        </div>
        <div class="modern-card p-4 text-center border-l-4 border-l-gray-400">
            <div class="text-3xl font-bold text-gray-600">{{ $turnos->where('es_descanso', 1)->count() }}</div>
            <div class="text-sm text-gray-500 mt-1">Descansan</div>
        </div>
        <div class="modern-card p-4 text-center border-l-4 border-l-orange-500">
            <div class="text-lg font-bold text-orange-600">{{ \Carbon\Carbon::parse($fecha)->format('d/m/Y') }}</div>
            <div class="text-sm text-gray-500 mt-1">Fecha</div>
        </div>
    </div>

    @if($turnos->count() > 0)
    <div class="modern-card overflow-hidden">
        <div class="p-4 bg-gradient-to-r from-yellow-50 to-orange-50 border-b border-gray-100">
            <h3 class="font-bold text-gray-800 flex items-center gap-2">
                <i class="fas fa-list text-yellow-600"></i>
                Detalle de Turnos
                <span class="ml-auto text-sm text-gray-500">{{ $turnos->count() }} registros</span>
            </h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-yellow-500 text-white">
                    <tr>
                        <th class="px-4 py-3 text-center text-xs font-medium uppercase tracking-wider">#</th>
                        <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider">Unidad</th>
                        <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider">Placa</th>
                        <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider">Conductor</th>
                        <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider">Teléfono</th>
                        <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider">Tipo</th>
                        <th class="px-4 py-3 text-center text-xs font-medium uppercase tracking-wider">Hora</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach($turnos as $turno)
                    <tr class="hover:bg-yellow-50/50 transition-colors">
                        <td class="px-4 py-3 text-center">
                            <span class="w-8 h-8 inline-flex items-center justify-center bg-yellow-100 text-yellow-700 rounded-lg font-bold">
                                {{ $turno->posicion }}
                            </span>
                        </td>
                        <td class="px-4 py-3">
                            <span class="px-3 py-1 bg-yellow-100 text-yellow-700 rounded-lg font-bold text-sm">
                                {{ $turno->unidad->numero_interno ?? '-' }}
                            </span>
                        </td>
                        <td class="px-4 py-3 font-semibold text-gray-800">{{ $turno->unidad->placa ?? 'N/A' }}</td>
                        <td class="px-4 py-3 text-gray-700">
                            {{ $turno->usuario->nombres ?? 'N/A' }}
                            @if(isset($turno->usuario) && $turno->usuario->apellidos)
                                <span class="text-gray-500">{{ $turno->usuario->apellidos }}</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-gray-500">{{ $turno->usuario->telefono ?? 'N/A' }}</td>
                        <td class="px-4 py-3">
                            @if($turno->es_descanso == 1)
                                <span class="px-3 py-1 bg-gray-200 text-gray-700 rounded-full text-xs font-bold">DESCANS0</span>
                            @elseif($turno->posicion == 1)
                                <span class="px-3 py-1 bg-green-100 text-green-700 rounded-full text-xs font-bold">PUNTERO 1</span>
                            @elseif($turno->posicion == 2)
                                <span class="px-3 py-1 bg-green-100 text-green-700 rounded-full text-xs font-bold">PUNTERO 2</span>
                            @else
                                <span class="px-3 py-1 bg-blue-100 text-blue-700 rounded-full text-xs font-bold">TURNO {{ $turno->posicion }}</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-center text-gray-500 text-sm">
                            {{ $turno->generado_at ? \Carbon\Carbon::parse($turno->generado_at)->format('H:i') : '-' }}
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @else
    <div class="modern-card p-12 text-center">
        <div class="w-20 h-20 mx-auto mb-4 bg-gray-100 rounded-full flex items-center justify-center">
            <i class="fas fa-calendar-times text-3xl text-gray-300"></i>
        </div>
        <p class="text-gray-500 text-lg">No hay turnos para esta fecha</p>
        <p class="text-gray-400 text-sm mt-2">{{ \Carbon\Carbon::parse($fecha)->format('d/m/Y') }}</p>
    </div>
    @endif
</div>
@endsection