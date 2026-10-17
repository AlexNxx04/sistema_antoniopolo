@extends('layouts.tarjetera')

@section('title', 'Control de Cola')
@section('header', 'Control de Cola')

@section('content')

<div class="p-6">
    <div class="flex items-center gap-3 mb-6">
        <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-yellow-400 to-orange-500 flex items-center justify-center shadow-lg">
            <i class="fas fa-route text-white text-lg"></i>
        </div>
        <div>
            <h2 class="text-2xl font-bold text-gray-800">Control de Cola</h2>
            <p class="text-gray-500 text-sm">Gestión dinámica de turnos del día</p>
        </div>
    </div>

    @if(session('success'))
        <div class="mb-6 p-4 bg-green-100 border border-green-300 text-green-700 rounded-xl">
            <i class="fas fa-check-circle mr-2"></i> {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="mb-6 p-4 bg-red-100 border border-red-300 text-red-700 rounded-xl">
            <i class="fas fa-exclamation-circle mr-2"></i> {{ session('error') }}
        </div>
    @endif

    <div class="grid grid-cols-2 md:grid-cols-5 gap-4 mb-6">
        <div class="modern-card p-4 text-center border-l-4 border-l-blue-500">
            <div class="text-2xl font-bold text-blue-600">{{ $estadisticas['total'] }}</div>
            <div class="text-sm text-gray-500">Total</div>
        </div>
        <div class="modern-card p-4 text-center border-l-4 border-l-green-500">
            <div class="text-2xl font-bold text-green-600">{{ $estadisticas['confirmados'] }}</div>
            <div class="text-sm text-gray-500">Confirmados</div>
        </div>
        <div class="modern-card p-4 text-center border-l-4 border-l-yellow-500">
            <div class="text-2xl font-bold text-yellow-600">{{ $estadisticas['en_espera'] }}</div>
            <div class="text-sm text-gray-500">En Espera</div>
        </div>
        <div class="modern-card p-4 text-center border-l-4 border-l-red-500">
            <div class="text-2xl font-bold text-red-600">{{ $estadisticas['bloqueados'] }}</div>
            <div class="text-sm text-gray-500">Bloqueados</div>
        </div>
        <div class="modern-card p-4 text-center border-l-4 border-l-orange-500">
            <div class="text-2xl font-bold text-orange-600">{{ $estadisticas['sancionados'] }}</div>
            <div class="text-sm text-gray-500">Sancionados</div>
        </div>
    </div>

    @if($carrerasActivas->count() > 0)
    <div class="modern-card p-4 mb-6 bg-orange-50 border border-orange-200">
        <h3 class="font-bold text-orange-800 mb-3 flex items-center gap-2">
            <i class="fas fa-car text-orange-600"></i>
            Carreras Especiales en Curso
        </h3>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
            @foreach($carrerasActivas as $carrera)
            <div class="flex items-center justify-between p-3 bg-white rounded-lg border border-orange-300">
                <div>
                    <span class="font-bold text-orange-700">Turno {{ $carrera->posicion_antes }}</span>
                    <p class="text-sm text-gray-600">Carrera especial</p>
                </div>
                <span class="px-3 py-1 bg-orange-200 text-orange-700 rounded-full text-sm">En carrera</span>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    <div class="modern-card overflow-hidden">
        <div class="p-4 bg-gradient-to-r from-yellow-50 to-orange-50 border-b border-gray-100">
            <h3 class="font-bold text-gray-800 flex items-center gap-2">
                <i class="fas fa-list text-yellow-600"></i>
                Turnos del Día
                <span class="ml-auto text-sm text-gray-500">{{ $turnosDia->count() }} turnos</span>
            </h3>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-yellow-500 text-white">
                    <tr>
                        <th class="px-4 py-3 text-center text-xs">Pos</th>
                        <th class="px-4 py-3 text-left text-xs">Unidad</th>
                        <th class="px-4 py-3 text-left text-xs">Conductor</th>
                        <th class="px-4 py-3 text-center text-xs">Estado</th>
                        <th class="px-4 py-3 text-center text-xs">Tipo</th>
                        <th class="px-4 py-3 text-center text-xs">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach($turnosDia as $turno)
                    <tr class="hover:bg-yellow-50/50 {{ $turno->estado === 'no_presento' ? 'bg-red-50' : '' }} {{ $turno->sancion_9am == 1 ? 'bg-orange-50' : '' }}">
                        <td class="px-4 py-3 text-center">
                            <span class="w-8 h-8 inline-flex items-center justify-center bg-yellow-100 text-yellow-700 rounded-lg font-bold text-sm">
                                {{ $turno->posicion }}
                            </span>
                        </td>
                        <td class="px-4 py-3">
                            <span class="px-2 py-1 bg-yellow-100 text-yellow-700 rounded font-bold text-sm">
                                {{ $turno->unidad->numero_interno ?? '-' }}
                            </span>
                            <span class="text-sm text-gray-500 ml-2">{{ $turno->unidad->placa ?? 'N/A' }}</span>
                        </td>
                        <td class="px-4 py-3 font-medium text-gray-800">
                            {{ $turno->usuario->nombres ?? 'N/A' }}
                        </td>
                        <td class="px-4 py-3 text-center">
                            @if($turno->estado === 'confirmado')
                                <span class="px-3 py-1 bg-green-100 text-green-700 rounded-full text-xs font-bold">Confirmado</span>
                            @elseif($turno->estado === 'llamado_1')
                                <span class="px-3 py-1 bg-yellow-100 text-yellow-700 rounded-full text-xs font-bold">1er Llamado</span>
                            @elseif($turno->estado === 'llamado_2')
                                <span class="px-3 py-1 bg-orange-100 text-orange-700 rounded-full text-xs font-bold">2do Llamado</span>
                            @elseif($turno->estado === 'no_presento')
                                <span class="px-3 py-1 bg-red-100 text-red-700 rounded-full text-xs font-bold">Bloqueado</span>
                            @else
                                <span class="px-3 py-1 bg-gray-100 text-gray-700 rounded-full text-xs font-bold">En Espera</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-center">
                            @if($turno->es_descanso == 1)
                                <span class="px-3 py-1 bg-gray-200 text-gray-700 rounded-full text-xs font-bold">Descanso</span>
                            @elseif($turno->posicion == 1)
                                <span class="px-3 py-1 bg-purple-100 text-purple-700 rounded-full text-xs font-bold">Puntero 1</span>
                            @elseif($turno->posicion == 2)
                                <span class="px-3 py-1 bg-purple-100 text-purple-700 rounded-full text-xs font-bold">Puntero 2</span>
                            @elseif($turno->sancion_9am == 1)
                                <span class="px-3 py-1 bg-orange-100 text-orange-700 rounded-full text-xs font-bold">Sancionado</span>
                            @else
                                <span class="px-3 py-1 bg-blue-100 text-blue-700 rounded-full text-xs font-bold">Seguidor</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-center">
                            <div class="flex gap-2 justify-center">
                                @if($turno->estado !== 'confirmado' && $turno->estado !== 'no_presento' && $turno->es_descanso == 0)
                                    <form action="{{ route('tarjetera.control-cola.bloquear', $turno->turno_id) }}" method="POST" class="inline">
                                        @csrf
                                        <input type="hidden" name="tipo" value="seguidor">
                                        <button type="submit" class="px-2 py-1 bg-red-500 text-white rounded text-xs hover:bg-red-600">
                                            <i class="fas fa-ban"></i> Bloquear
                                        </button>
                                    </form>
                                @endif
                                
                                @if(($turno->posicion == 1 || $turno->posicion == 2) && $turno->sancion_9am != 1 && $turno->es_descanso == 0)
                                    <form action="{{ route('tarjetera.control-cola.sancionar', $turno->turno_id) }}" method="POST" class="inline">
                                        @csrf
                                        <button type="submit" class="px-2 py-1 bg-orange-500 text-white rounded text-xs hover:bg-orange-600">
                                            <i class="fas fa-clock"></i> Sancionar 9am
                                        </button>
                                    </form>
                                @endif
                                
                                @if($turno->estado === 'no_presento')
                                    <form action="{{ route('tarjetera.turnos.liberar', $turno->turno_id) }}" method="POST" class="inline">
                                        @csrf
                                        <button type="submit" class="px-2 py-1 bg-green-500 text-white rounded text-xs hover:bg-green-600">
                                            <i class="fas fa-unlock"></i> Liberar
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection