@extends('layouts.tarjetera')

@section('title', 'Registrar Carrera')
@section('header', 'Registrar Carrera')

@section('content')

<div class="p-6">
    <div class="flex items-center gap-3 mb-6">
        <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-yellow-400 to-orange-500 flex items-center justify-center shadow-lg">
            <i class="fas fa-play-circle text-white text-lg"></i>
        </div>
        <div>
            <h2 class="text-2xl font-bold text-gray-800">Registrar Carrera Especial</h2>
            <p class="text-gray-500 text-sm">Carreras fuera de turno normal</p>
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

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="modern-card p-6">
            <h3 class="font-bold text-gray-800 mb-4 flex items-center gap-2">
                <i class="fas fa-list text-yellow-600"></i>
                Turnos Disponibles
            </h3>
            
            <form action="{{ route('tarjetera.carrera.registrar') }}" method="POST">
                @csrf
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-600 mb-2">Seleccionar Turno</label>
                    <select name="turno_id" class="w-full px-4 py-2 border border-gray-300 rounded-xl" required>
                        <option value="">-- Seleccionar --</option>
                        @foreach($turnosActivos as $turno)
                        <option value="{{ $turno->turno_id }}">
                            {{ $turno->posicion }} - {{ $turno->usuario->nombres ?? 'N/A' }} ({{ $turno->unidad->placa ?? 'N/A' }})
                        </option>
                        @endforeach
                    </select>
                </div>
                
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-600 mb-2">Tipo de Carrera</label>
                    <select name="tipo" class="w-full px-4 py-2 border border-gray-300 rounded-xl">
                        <option value="carrera_especial">Carrera Especial</option>
                        <option value="urgente">Urgente</option>
                        <option value="intervento">Intervento</option>
                    </select>
                </div>
                
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-600 mb-2">Observaciones</label>
                    <textarea name="observaciones" class="w-full px-4 py-2 border border-gray-300 rounded-xl" rows="3" placeholder="Detalles de la carrera..."></textarea>
                </div>
                
                <button type="submit" class="w-full px-4 py-3 bg-yellow-500 text-white rounded-xl hover:bg-yellow-600 font-medium">
                    <i class="fas fa-play mr-2"></i> Registrar Salida
                </button>
            </form>
        </div>

        <div class="space-y-6">
            <div class="modern-card p-6">
                <h3 class="font-bold text-gray-800 mb-4 flex items-center gap-2">
                    <i class="fas fa-spinner text-orange-600"></i>
                    Carreras en Curso
                </h3>
                
                @if($carrerasActivas->count() > 0)
                <div class="space-y-3">
                    @foreach($carrerasActivas as $carrera)
                    <div class="flex items-center justify-between p-3 bg-orange-50 rounded-xl border border-orange-200">
                        <div>
                            <span class="font-bold text-orange-700">Posición {{ $carrera->posicion_antes }}</span>
                            <p class="text-sm text-gray-600">{{ $carrera->nombres ?? 'N/A' }} - {{ $carrera->placa ?? 'N/A' }}</p>
                        </div>
                        <form action="{{ route('tarjetera.carrera.finalizar', $carrera->carrera_id) }}" method="POST">
                            @csrf
                            <button type="submit" class="px-4 py-2 bg-green-500 text-white rounded-lg hover:bg-green-600 text-sm">
                                <i class="fas fa-stop mr-1"></i> Finalizar
                            </button>
                        </form>
                    </div>
                    @endforeach
                </div>
                @else
                <p class="text-gray-500 text-center py-4">No hay carreras activas</p>
                @endif
            </div>

            <div class="modern-card p-6">
                <h3 class="font-bold text-gray-800 mb-4 flex items-center gap-2">
                    <i class="fas fa-history text-gray-600"></i>
                    Carreras Finalizadas
                </h3>
                
                @if($carrerasFinalizadas->count() > 0)
                <div class="space-y-2">
                    @foreach($carrerasFinalizadas as $carrera)
                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                        <div>
                            <span class="font-semibold text-gray-700">Posición {{ $carrera->posicion_antes }}</span>
                            <p class="text-xs text-gray-500">{{ $carrera->nombres ?? 'N/A' }}</p>
                            <p class="text-xs text-gray-400">
                                Salida: {{ \Carbon\Carbon::parse($carrera->hora_salida_especial)->format('H:i') }} - 
                                {{ $carrera->hora_regreso ? \Carbon\Carbon::parse($carrera->hora_regreso)->format('H:i') : 'En curso' }}
                            </p>
                        </div>
                    </div>
                    @endforeach
                </div>
                @else
                <p class="text-gray-500 text-center py-4">Sin carreras finalizadas</p>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection