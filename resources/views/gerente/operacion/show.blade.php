@extends('layouts.gerente')

@section('title', 'Detalle de Operación')
@section('header', 'Detalle de Turno')

@section('content')
<div class="fade-in">
    <div class="mb-6">
        <a href="{{ route('gerente.operacion.index') }}" class="btn-modern bg-gray-500 hover:bg-gray-600">
            <i class="fas fa-arrow-left mr-2"></i>
            Volver a Operación
        </a>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="modern-card p-6">
            <h3 class="font-bold text-gray-800 mb-4">
                <i class="fas fa-user mr-2 text-amber-500"></i>
                Conductor
            </h3>
            <div class="space-y-3">
                <div>
                    <span class="text-sm text-gray-500">Nombre:</span>
                    <p class="font-bold text-lg">{{ $turno->usuario->nombres ?? 'N/A' }} {{ $turno->usuario->apellidos ?? '' }}</p>
                </div>
                <div>
                    <span class="text-sm text-gray-500">Cédula:</span>
                    <p class="font-medium">{{ $turno->usuario->cedula ?? 'N/A' }}</p>
                </div>
                <div>
                    <span class="text-sm text-gray-500">Teléfono:</span>
                    <p class="font-medium">{{ $turno->usuario->telefono ?? 'N/A' }}</p>
                </div>
            </div>
        </div>

        <div class="modern-card p-6">
            <h3 class="font-bold text-gray-800 mb-4">
                <i class="fas fa-car mr-2 text-amber-500"></i>
                Unidad
            </h3>
            <div class="space-y-3">
                <div>
                    <span class="text-sm text-gray-500">Unidad:</span>
                    <p class="font-bold text-lg">{{ $turno->unidad->numero_interno ?? ($turno->es_descanso ? 'Descansa' : 'N/A') }}</p>
                </div>
                <div>
                    <span class="text-sm text-gray-500">Placa:</span>
                    <p class="font-medium">{{ $turno->unidad->placa ?? ($turno->es_descanso ? 'Descansa' : 'N/A') }}</p>
                </div>
                <div>
                    <span class="text-sm text-gray-500">Punto:</span>
                    <p class="font-medium">{{ $turno->punto->nombre ?? 'N/A' }}</p>
                </div>
            </div>
        </div>

        <div class="modern-card p-6">
            <h3 class="font-bold text-gray-800 mb-4">
                <i class="fas fa-clipboard-list mr-2 text-amber-500"></i>
                Turno
            </h3>
            <div class="space-y-3">
                <div>
                    <span class="text-sm text-gray-500">Posición:</span>
                    <p class="font-bold text-lg">#{{ $turno->posicion ?? '-' }}</p>
                </div>
                <div>
                    <span class="text-sm text-gray-500">Tipo:</span>
                    <span class="px-3 py-1 rounded-full text-sm font-semibold
                        @if($turno->es_descanso) bg-gray-100 text-gray-600
                        @elseif($turno->es_puntero) bg-purple-100 text-purple-700
                        @else bg-blue-100 text-blue-700 @endif">
                        @if($turno->es_descanso)
                            <i class="fas fa-bed mr-1"></i> Descanso
                        @elseif($turno->es_puntero)
                            <i class="fas fa-star mr-1"></i> Puntero {{ $turno->posicion <= 2 ? $turno->posicion : '' }}
                        @else
                            <i class="fas fa-user mr-1"></i> Turno {{ $turno->posicion }}
                        @endif
                    </span>
                </div>
                <div>
                    <span class="text-sm text-gray-500">Estado:</span>
                    <span class="px-3 py-1 rounded-full text-sm font-semibold
                        @if($turno->es_descanso) bg-gray-100 text-gray-600
                        @elseif($turno->estado === 'no_presento') bg-red-100 text-red-700
                        @elseif($turno->confirmado_en) bg-emerald-100 text-emerald-700
                        @else bg-amber-100 text-amber-700 @endif">
                        @if($turno->es_descanso)
                            <i class="fas fa-bed mr-1"></i> Descanso
                        @elseif($turno->estado === 'no_presento')
                            <i class="fas fa-ban mr-1"></i> Bloqueado
                        @elseif($turno->confirmado_en)
                            <i class="fas fa-check mr-1"></i> Operando
                        @else
                            <i class="fas fa-clock mr-1"></i> En Espera
                        @endif
                    </span>
                </div>
                @if($turno->confirmado_en)
                <div>
                    <span class="text-sm text-gray-500">Confirmado:</span>
                    <p class="font-medium">{{ \Carbon\Carbon::parse($turno->confirmado_en)->format('H:i') }}</p>
                </div>
                @endif
                @if($turno->estado === 'no_presento' && $turno->motivo_bloqueo)
                <div>
                    <span class="text-sm text-gray-500">Motivo:</span>
                    <p class="font-medium text-red-600">{{ $turno->motivo_bloqueo }}</p>
                </div>
                @endif
            </div>
        </div>

        <div class="modern-card p-6">
            <h3 class="font-bold text-gray-800 mb-4">
                <i class="fas fa-route mr-2 text-amber-500"></i>
                Salidas
            </h3>
            @forelse($turno->salidas as $salida)
            <div class="p-3 bg-gray-50 rounded-lg mb-2">
                <div class="flex justify-between">
                    <span class="font-medium">Salida: {{ $salida-> hora_salida }}</span>
                    @if($salida->llegada)
                    <span class="font-medium">Llegada: {{ $salida->llegada->hora_llegada }}</span>
                    @endif
                </div>
            </div>
            @empty
            <p class="text-gray-500">No hay salidas registradas</p>
            @endforelse
        </div>
    </div>
</div>
@endsection