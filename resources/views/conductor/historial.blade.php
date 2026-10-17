@extends('layouts.conductor')

@section('title', 'Historial')
@section('header', 'Mi Historial')

@section('content')
<div class="mb-6">
    <div class="flex items-center gap-4 mb-6">
        <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-blue-400 to-blue-600 flex items-center justify-center text-white shadow-lg">
            <i class="fas fa-history text-xl"></i>
        </div>
        <div>
            <h2 class="text-2xl font-bold text-gray-800">Mi Historial</h2>
            <p class="text-gray-500 text-sm">Turnos y multas registradas</p>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
    <div class="lg:col-span-2">
        <div class="dashboard-card">
            <div class="section-header">
                <h3><i class="fas fa-list text-emerald-500 mr-2"></i>Historial de Turnos</h3>
            </div>
            
            @if($turnos->count() > 0)
            <div class="divide-y divide-gray-100">
                @foreach($turnos as $turno)
                <div class="p-4 hover:bg-gray-50 transition-colors">
                    <div class="flex items-center justify-between mb-2">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-lg {{ $turno->es_descanso ? 'bg-gray-100 text-gray-600' : 'bg-emerald-100 text-emerald-600' }} flex items-center justify-center">
                                <i class="fas {{ $turno->es_descanso ? 'fa-bed' : 'fa-car' }}"></i>
                            </div>
                            <div>
                                <p class="font-semibold text-gray-800">
                                    {{ $turno->fecha->format('d/m/Y') }}
                                    @if($turno->es_descanso)
                                        <span class="ml-2 px-2 py-0.5 bg-gray-100 text-gray-600 rounded text-xs">Descanso</span>
                                    @else
                                        <span class="ml-2 px-2 py-0.5 bg-emerald-100 text-emerald-700 rounded text-xs">Operó</span>
                                    @endif
                                </p>
                                <p class="text-xs text-gray-500">{{ $turno->fecha->translatedFormat('l') }}</p>
                            </div>
                        </div>
                        <div class="text-right">
                            @if(!$turno->es_descanso)
                            <p class="font-bold text-gray-800">#{{ $turno->posicion }}</p>
                            <p class="text-xs text-gray-500">{{ $turno->unidad->placa ?? 'N/A' }}</p>
                            @else
                            <p class="text-xs text-gray-500">Grupo {{ $turno->grupo_descanso ?? 'N/A' }}</p>
                            @endif
                        </div>
                    </div>
                    @if($turno->presencia_confirmada)
                    <div class="ml-13 mt-1 flex items-center gap-2 text-xs text-green-600">
                        <i class="fas fa-check-circle"></i>
                        Presencia confirmada {{ $turno->presencia_at ? $turno->presencia_at->format('H:i') : '' }}
                        @if($turno->es_puntero_confirmado !== null)
                            - {{ $turno->es_puntero_confirmado ? 'Puntero' : 'Regular' }}
                        @endif
                    </div>
                    @endif
                </div>
                @endforeach
            </div>
            
            <div class="p-4 border-t border-gray-100">
                {{ $turnos->links() }}
            </div>
            @else
            <div class="p-8 text-center">
                <div class="w-16 h-16 mx-auto mb-4 bg-gray-100 rounded-full flex items-center justify-center">
                    <i class="fas fa-inbox text-3xl text-gray-400"></i>
                </div>
                <p class="font-bold text-gray-800 text-xl">Sin historial</p>
                <p class="text-gray-500">No hay turnos registrados aún.</p>
            </div>
            @endif
        </div>
    </div>

    <div class="lg:col-span-1">
        <div class="dashboard-card">
            <div class="section-header">
                <h3><i class="fas fa-exclamation-triangle text-red-500 mr-2"></i>Mis Multas</h3>
                <a href="{{ route('conductor.multas') }}" class="text-xs text-emerald-600">Ver todas</a>
            </div>
            
            @if($multas->count() > 0)
            <div class="divide-y divide-gray-100">
                @foreach($multas->take(5) as $multa)
                <div class="p-3 hover:bg-gray-50 transition-colors">
                    <div class="flex items-center justify-between mb-1">
                        <p class="text-sm font-medium text-gray-800">{{ $multa->tipo_display }}</p>
                        <span class="px-2 py-0.5 rounded-full text-xs font-bold {{ $multa->estado_badge_class }}">
                            {{ $multa->estado_display }}
                        </span>
                    </div>
                    <p class="text-xs text-gray-500">{{ $multa->registrado_en->format('d/m/Y') }}</p>
                    <p class="text-sm font-bold text-red-600">${{ number_format($multa->monto, 2) }}</p>
                </div>
                @endforeach
            </div>
            @if($multas->count() > 5)
            <div class="p-3 border-t border-gray-100 text-center">
                <a href="{{ route('conductor.multas') }}" class="text-sm text-emerald-600 hover:text-emerald-700">
                    Ver todas ({{ $multas->count() }}) <i class="fas fa-arrow-right ml-1"></i>
                </a>
            </div>
            @endif
            @else
            <div class="p-6 text-center">
                <div class="w-12 h-12 mx-auto mb-3 bg-green-100 rounded-full flex items-center justify-center">
                    <i class="fas fa-check text-green-600"></i>
                </div>
                <p class="text-sm text-gray-600">Sin multas</p>
            </div>
            @endif
        </div>

        <div class="dashboard-card mt-4">
            <div class="section-header">
                <h3><i class="fas fa-file-alt text-amber-500 mr-2"></i>Justificativos</h3>
                <a href="{{ route('conductor.justificativos') }}" class="text-xs text-emerald-600">Ver todos</a>
            </div>
            <div class="p-4">
                <a href="{{ route('conductor.justificativos') }}" class="block w-full py-3 bg-gradient-to-r from-amber-500 to-amber-600 text-white text-center font-semibold rounded-xl hover:from-amber-600 hover:to-amber-700 transition-all">
                    <i class="fas fa-plus-circle mr-2"></i>
                    Crear Justificativo
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
