@extends('layouts.tarjetera')

@section('title', 'Justificativos')
@section('header', 'Justificativos')

@section('content')

<div class="p-6">
    <div class="flex items-center gap-3 mb-6">
        <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-amber-400 to-amber-600 flex items-center justify-center shadow-lg">
            <i class="fas fa-file-signature text-white text-xl"></i>
        </div>
        <div>
            <h2 class="text-2xl font-bold text-gray-800">Justificativos</h2>
            <p class="text-gray-500 text-sm">{{ now()->format('d/m/Y') }}</p>
        </div>
    </div>

    @if(session('success'))
    <div class="mb-6 p-4 bg-green-100 border border-green-300 text-green-700 rounded-xl">
        <i class="fas fa-check-circle mr-2"></i> {{ session('success') }}
    </div>
    @endif

    @if(session('info'))
    <div class="mb-6 p-4 bg-blue-100 border border-blue-300 text-blue-700 rounded-xl">
        <i class="fas fa-info-circle mr-2"></i> {{ session('info') }}
    </div>
    @endif

    @if($pendientes > 0)
    <div class="mb-6 p-4 bg-amber-100 border border-amber-300 text-amber-700 rounded-xl">
        <i class="fas fa-exclamation-circle mr-2"></i>
        <strong>{{ $pendientes }}</strong> justificativo(s) pendiente(s) de revisión
    </div>
    @endif

    <div class="space-y-4">
        @forelse($justificativos as $justificativo)
        <div class="modern-card p-4 @if($justificativo->estado === 'aprobado') border-l-4 border-green-500 @elseif($justificativo->estado === 'rechazado') border-l-4 border-red-500 @else border-l-4 border-amber-500 @endif">
            <div class="flex justify-between items-start">
                <div class="flex-1">
                    <div class="flex items-center gap-2 mb-2">
                        <span class="text-lg font-bold text-gray-800">
                            @if($justificativo->turno->es_puntero)
                            <span class="px-2 py-1 bg-purple-100 text-purple-700 rounded text-xs">
                                <i class="fas fa-star mr-1"></i> Puntero #{{ $justificativo->turno->posicion }}
                            </span>
                            @else
                            <span class="px-2 py-1 bg-blue-100 text-blue-700 rounded text-xs">
                                #{{ $justificativo->turno->posicion }}
                            </span>
                            @endif
                        </span>
                        <span class="font-medium">{{ $justificativo->turno->usuario->nombres ?? 'N/A' }}</span>
                        <span class="text-gray-500">({{ $justificativo->turno->unidad->placa ?? 'N/A' }})</span>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-2 text-sm">
                        <div>
                            <span class="text-gray-500">Tipo:</span>
                            <span class="font-medium text-gray-700">
                                @switch($justificativo->tipo)
                                    @case('sustitucion') Sustitución @break
                                    @case('atraso') Atraso @break
                                    @case('inasistencia') Inasistencia @break
                                    @default {{ $justificativo->tipo }}
                                @endswitch
                            </span>
                        </div>
                        <div>
                            <span class="text-gray-500">Hora:</span>
                            <span class="text-gray-700">
                                {{ \Carbon\Carbon::parse($justificativo->registrado_at)->format('H:i') }}
                            </span>
                        </div>
                    </div>
                    
                    @if($justificativo->motivo)
                    <div class="mt-2 p-2 bg-gray-50 rounded-lg text-sm">
                        <span class="text-gray-500">Motivo:</span>
                        <p class="text-gray-700">{{ $justificativo->motivo }}</p>
                    </div>
                    @endif
                    
                    @if($justificativo->tipo === 'sustitucion' && $justificativo->sustituto_nombre)
                    <div class="mt-2 p-2 bg-purple-50 rounded-lg text-sm">
                        <span class="text-purple-600"><i class="fas fa-user-plus mr-1"></i> Sustituto:</span>
                        <span class="text-gray-700">{{ $justificativo->sustituto_nombre }}</span>
                        <span class="text-gray-500">({{ $justificativo->sustituto_cedula }})</span>
                    </div>
                    @endif
                </div>
                
                <div class="ml-4">
                    @if($justificativo->estado === 'aprobado')
                    <span class="px-3 py-1 bg-green-100 text-green-700 rounded-full text-sm font-medium">
                        <i class="fas fa-check mr-1"></i> Aprobado
                    </span>
                    @elseif($justificativo->estado === 'rechazado')
                    <span class="px-3 py-1 bg-red-100 text-red-700 rounded-full text-sm font-medium">
                        <i class="fas fa-times mr-1"></i> Rechazado
                    </span>
                    @else
                    <div class="flex gap-2">
                        <form action="{{ route('tarjetera.justificativos.aprobar', $justificativo->justificativo_id) }}" method="POST">
                            @csrf
                            <button type="submit" class="px-3 py-1 bg-green-500 text-white rounded text-sm hover:bg-green-600">
                                <i class="fas fa-check mr-1"></i> Aprobar
                            </button>
                        </form>
                        <form action="{{ route('tarjetera.justificativos.rechazar', $justificativo->justificativo_id) }}" method="POST">
                            @csrf
                            <button type="submit" class="px-3 py-1 bg-red-500 text-white rounded text-sm hover:bg-red-600">
                                <i class="fas fa-times mr-1"></i> Rechazar
                            </button>
                        </form>
                    </div>
                    @endif
                </div>
            </div>
        </div>
        @empty
        <div class="modern-card p-8 text-center">
            <i class="fas fa-file-signature text-4xl text-gray-300 mb-2"></i>
            <p class="text-gray-500">No hay justificativos registrados hoy</p>
        </div>
        @endforelse
    </div>
</div>
@endsection