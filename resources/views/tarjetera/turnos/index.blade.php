@extends('layouts.tarjetera')

@section('title', 'Turnos del Día')
@section('header', 'Turnos del Día')

@section('content')
<div class="fade-in" x-data="turnosApp()">
    <div class="flex items-center justify-between mb-6">
        <div>
            <div class="flex items-center gap-3">
                <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-amber-400 to-amber-600 flex items-center justify-center shadow-lg">
                    <i class="fas fa-clipboard-list text-white text-xl"></i>
                </div>
                <div>
                    <h2 class="text-2xl font-bold text-gray-800">Turnos de Hoy</h2>
                    <p class="text-gray-500 text-sm">{{ now()->locale('es')->translatedFormat('l d \\d\\e F \\d\\e Y') }}</p>
                </div>
            </div>
        </div>
        <div class="flex items-center gap-3">
            <div class="px-4 py-2 bg-amber-50 border border-amber-200 rounded-xl flex items-center gap-2">
                <span class="w-3 h-3 bg-amber-500 rounded-full animate-pulse"></span>
                <span class="text-amber-700 font-semibold text-sm">{{ $turnosOperativos->count() }} Turnos</span>
            </div>
            <button onclick="showColaModal()" class="px-4 py-2 bg-yellow-500 text-white rounded-xl hover:bg-yellow-600 text-sm">
                <i class="fas fa-list mr-2"></i>Cola de Unidades
            </button>
        </div>
    </div>

    @if(session('success'))
    <div class="mb-6 p-4 bg-emerald-50 border border-emerald-200 rounded-xl flex items-center gap-3">
        <i class="fas fa-check-circle text-emerald-500"></i>
        <span class="text-emerald-700 font-medium">{{ session('success') }}</span>
    </div>
    @endif

    @if(session('error'))
    <div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-xl flex items-center gap-3">
        <i class="fas fa-exclamation-circle text-red-500"></i>
        <span class="text-red-700 font-medium">{{ session('error') }}</span>
    </div>
    @endif

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4 mb-6">
        @forelse($turnosHoy as $turno)
        <div class="modern-card p-4 hover:shadow-lg transition-all border-l-4
            @if($turno->estado === 'confirmado') border-l-emerald-500
            @elseif($turno->estado === 'no_presento' || $turno->bloqueado) border-l-red-500
            @elseif($turno->estado === 'llamado_2') border-l-orange-500
            @elseif($turno->estado === 'llamado_1') border-l-amber-400
            @else border-l-amber-500 @endif">
            
            <div class="flex items-center gap-3 mb-3">
                <div class="w-12 h-12 rounded-xl flex items-center justify-center font-bold text-white text-lg
                    @if($turno->estado === 'confirmado') bg-gradient-to-br from-emerald-400 to-emerald-600
                    @elseif($turno->estado === 'no_presento' || $turno->bloqueado) bg-gradient-to-br from-red-400 to-red-600
                    @elseif($turno->estado === 'llamado_2') bg-gradient-to-br from-orange-400 to-orange-600
                    @elseif($turno->estado === 'llamado_1') bg-gradient-to-br from-amber-400 to-amber-600
                    @else bg-gradient-to-br from-gray-400 to-gray-500 @endif">
                    #{{ str_pad($turno->posicion ?? '0', 2, '0', STR_PAD_LEFT) }}
                </div>
                <div class="flex-1 min-w-0">
                    <p class="font-bold text-gray-800 truncate">{{ $turno->usuario->nombres ?? 'Sin asignar' }}</p>
                    <p class="text-sm text-gray-500">Unidad: {{ $turno->unidad->numero_interno ?? '?' }}</p>
                </div>
            </div>
            
            <div class="text-xs text-gray-600 mb-3 space-y-1">
                @if($turno->usuario && $turno->usuario->telefono)
                <p><i class="fas fa-phone mr-1"></i>Conductor: {{ $turno->usuario->telefono }}</p>
                @endif
                @if($turno->unidad && $turno->unidad->telefono)
                <p><i class="fas fa-car mr-1"></i>Unidad (GPS): {{ $turno->unidad->telefono }}</p>
                @endif
                <p><i class="fas fa-tag mr-1"></i>Placa: {{ $turno->unidad->placa ?? 'N/A' }}</p>
            </div>
            
            <div class="flex items-center justify-between mb-3 flex-wrap gap-1">
                <span class="px-2 py-1 rounded-full text-xs font-semibold
                    @if($turno->es_puntero) bg-purple-100 text-purple-700 @endif">
                    <i class="fas fa-hashtag mr-1"></i>Turno {{ $turno->posicion ?? '-' }}
                </span>
                
@if($turno->es_puntero && $turno->estado !== 'confirmado')
                <span class="px-2 py-1 bg-red-100 text-red-600 text-xs font-bold rounded-full">
                    <i class="fas fa-exclamation-triangle mr-1"></i>Puntero - 30 min max
                </span>
                @endif
                
                @if($turno->sancion_9am)
                <span class="px-2 py-1 bg-orange-100 text-orange-600 text-xs font-bold rounded-full">
                    <i class="fas fa-clock mr-1"></i>Sale 9am
                </span>
                @endif
            </div>
            
            <div class="flex items-center justify-between mb-3">
                <span class="px-3 py-1 rounded-full text-xs font-semibold
                    @if($turno->estado === 'confirmado') bg-emerald-100 text-emerald-700
                    @elseif($turno->estado === 'no_presento') bg-red-100 text-red-700
                    @elseif($turno->estado === 'llamado_2') bg-orange-100 text-orange-700
                    @elseif($turno->estado === 'llamado_1') bg-amber-100 text-amber-700
                    @else bg-gray-100 text-gray-600 @endif">
                    @if($turno->estado === 'confirmado')
                        <i class="fas fa-check-circle mr-1"></i>Confirmado
                    @elseif($turno->estado === 'no_presento')
                        <i class="fas fa-times-circle mr-1"></i>No se presentó
                    @elseif($turno->estado === 'llamado_2')
                        <i class="fas fa-phone-volume mr-1"></i>2do Llamado
                    @elseif($turno->estado === 'llamado_1')
                        <i class="fas fa-phone mr-1"></i>1er Llamado
                    @elseif($turno->es_descanso)
                        <i class="fas fa-bed mr-1"></i>Descanso
                    @else
                        <i class="fas fa-clock mr-1"></i>En Espera
                    @endif
                </span>
            </div>
            
            @if($turno->estado !== 'confirmado' && $turno->estado !== 'no_presento' && !$turno->es_descanso)
            <div class="flex gap-2 flex-wrap">
                @if($turno->posicion == 1 || $turno->posicion == 2)
                    {{-- PUNTERO: Solo confirmar, no llamar --}}
                    <form action="{{ route('tarjetera.turnos.confirmar', $turno->turno_id) }}" method="POST" class="flex-1">
                        @csrf
                        <button type="submit" class="btn-modern btn-warning w-full text-xs py-2">
                            <i class="fas fa-check mr-1"></i>Confirmar
                        </button>
                    </form>
                @else
                    {{-- SEGUIDOR: Llamar segun estado --}}
                    @if($turno->estado === 'espera')
                    <form action="{{ route('tarjetera.turnos.llamar', $turno->turno_id) }}" method="POST" class="flex-1">
                        @csrf
                        <button type="submit" class="btn-modern bg-blue-500 hover:bg-blue-600 text-white w-full text-xs py-2">
                            <i class="fas fa-phone mr-1"></i>Llamar
                        </button>
                    </form>
                    @elseif($turno->estado === 'llamado_1')
                    <form action="{{ route('tarjetera.turnos.llamar', $turno->turno_id) }}" method="POST" class="flex-1">
                        @csrf
                        <button type="submit" class="btn-modern bg-orange-500 hover:bg-orange-600 text-white w-full text-xs py-2">
                            <i class="fas fa-phone-volume mr-1"></i>2do Llamado
                        </button>
                    </form>
                    @endif
                    @if($turno->estado !== 'llamado_2' && $turno->estado !== 'confirmado' && $turno->estado !== 'no_presento')
                    <form action="{{ route('tarjetera.turnos.confirmar', $turno->turno_id) }}" method="POST" class="flex-1">
                        @csrf
                        <button type="submit" class="btn-modern btn-warning w-full text-xs py-2">
                            <i class="fas fa-check mr-1"></i>Confirmar
                        </button>
                    </form>
                    @endif
                @endif
            </div>
            @elseif($turno->estado === 'confirmado')
            <div class="text-center p-2 bg-emerald-50 rounded-lg">
                <p class="text-xs text-emerald-600 font-medium">
                    <i class="fas fa-check-circle mr-1"></i>
                    @if($turno->sancion_9am)
                    Operando hasta 9am
                    @else
                    Operando
                    @endif
                </p>
            </div>
            @elseif($turno->estado === 'no_presento')
            <div class="text-center p-2 bg-red-50 rounded-lg">
                <p class="text-xs text-red-600 font-medium">
                    <i class="fas fa-times-circle mr-1"></i>Bloqueado
                </p>
            </div>
            @elseif($turno->es_descanso)
            <div class="text-center p-2 bg-gray-50 rounded-lg">
                <p class="text-xs text-gray-600 font-medium">
                    <i class="fas fa-bed mr-1"></i>Día de Descanso
                </p>
            </div>
            @endif
        </div>
        @empty
        <div class="col-span-full">
            <div class="modern-card p-12 text-center">
                <div class="w-20 h-20 mx-auto mb-4 bg-gray-100 rounded-full flex items-center justify-center">
                    <i class="fas fa-clipboard-list text-4xl text-gray-300"></i>
                </div>
                <h3 class="text-xl font-bold text-gray-700 mb-2">No hay turnos para hoy</h3>
                <p class="text-gray-500 mb-4">Primero ejecuta el Sorteo Diario para generar los turnos.</p>
                <a href="{{ route('tarjetera.sorteo.index') }}" class="btn-modern btn-warning">
                    <i class="fas fa-dice mr-2"></i>
                    Ir al Sorteo
                </a>
            </div>
        </div>
        @endforelse
    </div>

    <div class="modern-card p-6">
        <h3 class="text-lg font-bold text-gray-800 mb-4">
            <i class="fas fa-chart-pie text-amber-500 mr-2"></i>
            Resumen del Día
        </h3>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <div class="p-4 bg-emerald-50 rounded-xl text-center border-l-4 border-l-emerald-500">
                <p class="text-3xl font-bold text-emerald-600">{{ $turnosOperativos->where('estado', 'confirmado')->count() }}</p>
                <p class="text-sm text-emerald-700 font-medium">Confirmados</p>
            </div>
            <div class="p-4 bg-amber-50 rounded-xl text-center border-l-4 border-l-amber-500">
                <p class="text-3xl font-bold text-amber-600">{{ $turnosOperativos->whereIn('estado', ['espera', 'llamado_1'])->count() }}</p>
                <p class="text-sm text-amber-700 font-medium">En Espera</p>
            </div>
            <div class="p-4 bg-gray-50 rounded-xl text-center border-l-4 border-l-gray-400">
                <p class="text-3xl font-bold text-gray-600">{{ $turnosDescanso->count() }}</p>
                <p class="text-sm text-gray-700 font-medium">Descanso</p>
            </div>
            <div class="p-4 bg-red-50 rounded-xl text-center border-l-4 border-l-red-500">
                <p class="text-3xl font-bold text-red-600">{{ $turnosOperativos->where('estado', 'no_presento')->count() }}</p>
                <p class="text-sm text-red-700 font-medium">No Se Presentaron</p>
            </div>
        </div>
    </div>
</div>



<div id="colaModal" class="fixed inset-0 bg-black bg-opacity-50 hidden flex items-center justify-center z-50">
    <div class="bg-white rounded-2xl p-6 max-w-2xl w-full mx-4 max-h-[90vh] overflow-y-auto">
        <h3 class="text-xl font-bold text-gray-800 mb-4">
            <i class="fas fa-route text-yellow-500 mr-2"></i>
            Cola de Unidades - Control de Turnos
        </h3>
        
        <div class="grid grid-cols-4 gap-3 mb-4">
            <div class="p-3 bg-emerald-100 rounded-lg text-center">
                <p class="text-2xl font-bold text-emerald-600">{{ $turnosHoy->where('estado', 'confirmado')->count() }}</p>
                <p class="text-xs text-emerald-700">Confirmados</p>
            </div>
            <div class="p-3 bg-amber-100 rounded-lg text-center">
                <p class="text-2xl font-bold text-amber-600">{{ $turnosHoy->whereIn('estado', ['espera', 'llamado_1', 'llamado_2'])->count() }}</p>
                <p class="text-xs text-amber-700">En Espera</p>
            </div>
            <div class="p-3 bg-red-100 rounded-lg text-center">
                <p class="text-2xl font-bold text-red-600">{{ $turnosHoy->where('estado', 'no_presento')->count() }}</p>
                <p class="text-xs text-red-700">Bloqueados</p>
            </div>
            <div class="p-3 bg-orange-100 rounded-lg text-center">
                <p class="text-2xl font-bold text-orange-600">{{ $carrerasRealizadas }}</p>
                <p class="text-xs text-orange-700">Carreras</p>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-yellow-500 text-white">
                    <tr>
                        <th class="p-2 text-center">#</th>
                        <th class="p-2 text-left">Conductor</th>
                        <th class="p-2 text-center">Estado</th>
                        <th class="p-2 text-center">Tipo</th>
                        <th class="p-2 text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    @foreach($turnosHoy as $turno)
                    <tr class="hover:bg-yellow-50">
                        <td class="p-2 text-center font-bold">{{ $turno->posicion }}</td>
                        <td class="p-2">{{ $turno->usuario->nombres ?? 'N/A' }}</td>
                        <td class="p-2 text-center">
                            @if($turno->estado === 'confirmado')
                                <span class="px-2 py-1 bg-green-100 text-green-700 rounded text-xs">Confirmado</span>
                            @elseif($turno->estado === 'llamado_1')
                                <span class="px-2 py-1 bg-yellow-100 text-yellow-700 rounded text-xs">1er Llamado</span>
                            @elseif($turno->estado === 'llamado_2')
                                <span class="px-2 py-1 bg-orange-100 text-orange-700 rounded text-xs">2do Llamado</span>
                            @elseif($turno->estado === 'no_presento')
                                <span class="px-2 py-1 bg-red-100 text-red-700 rounded text-xs">Bloqueado</span>
                            @else
                                <span class="px-2 py-1 bg-gray-100 text-gray-700 rounded text-xs">Espera</span>
                            @endif
                        </td>
                        <td class="p-2 text-center">
                            @if($turno->es_descanso == 1)
                                <span class="px-2 py-1 bg-gray-200 text-gray-700 rounded text-xs">Descanso</span>
                            @elseif($turno->posicion == 1)
                                <span class="px-2 py-1 bg-purple-100 text-purple-700 rounded text-xs">Puntero 1</span>
                            @elseif($turno->posicion == 2)
                                <span class="px-2 py-1 bg-purple-100 text-purple-700 rounded text-xs">Puntero 2</span>
                            @elseif($turno->sancion_9am == 1)
                                <span class="px-2 py-1 bg-orange-100 text-orange-700 rounded text-xs">Sancionado</span>
                            @else
                                <span class="px-2 py-1 bg-blue-100 text-blue-700 rounded text-xs">Seguidor</span>
                            @endif
                        </td>
                        <td class="p-2 text-center">
                            <div class="flex gap-1 justify-center">
                                @if(($turno->posicion == 1 || $turno->posicion == 2) && $turno->sancion_9am != 1 && $turno->es_descanso == 0 && $turno->estado !== 'no_presento' && $turno->estado !== 'confirmado')
                                    <form action="{{ route('tarjetera.control-cola.sancionar', $turno->turno_id) }}" method="POST" class="inline">
                                        @csrf
                                        <button type="submit" class="px-2 py-1 bg-orange-500 text-white rounded text-xs hover:bg-orange-600" title="Sancionar - Trabaja hasta 9am">
                                            <i class="fas fa-clock mr-1"></i>Sancionar
                                        </button>
                                    </form>
                                @endif
                                
                                @if($turno->posicion > 2 && $turno->estado !== 'confirmado' && $turno->estado !== 'no_presento' && $turno->es_descanso == 0)
                                    <form action="{{ route('tarjetera.control-cola.bloquear', $turno->turno_id) }}" method="POST" class="inline">
                                        @csrf
                                        <input type="hidden" name="tipo" value="seguidor">
                                        <button type="submit" class="px-2 py-1 bg-red-500 text-white rounded text-xs hover:bg-red-600" title="Bloquear - Sin llamadas x2">
                                            <i class="fas fa-ban mr-1"></i>Bloquear
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
        
        @if($turnosDescanso->count() > 0)
        <div class="mt-6">
            <h3 class="text-lg font-bold text-gray-800 mb-3 flex items-center gap-2">
                <i class="fas fa-bed text-gray-500"></i>
                Turnos de Descanso
            </h3>
            <table class="w-full bg-white rounded-xl overflow-hidden">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="p-2 text-center">#</th>
                        <th class="p-2 text-left">Conductor</th>
                        <th class="p-2 text-center">Unidad</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    @foreach($turnosDescanso as $turno)
                    <tr class="hover:bg-gray-50">
                        <td class="p-2 text-center font-bold text-gray-500">Descanso</td>
                        <td class="p-2 text-gray-600">{{ $turno->usuario->nombres ?? 'N/A' }}</td>
                        <td class="p-2 text-center text-gray-500">{{ $turno->unidad->numero_interno ?? '-' }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif

        <div class="mt-4 bg-yellow-50 p-3 rounded-lg">
            <p class="text-sm text-yellow-700">
                <i class="fas fa-info-circle mr-1"></i>
                <strong>Sancionar</strong> = Puntero trabaja hasta 9am. 
                <strong>Bloquear</strong> = Seguidor sin llamadas x2, todo el día sin operar.
            </p>
        </div>

        <div class="mt-4 flex justify-end">
            <button type="button" onclick="closeModals()" class="btn-modern bg-gray-500 hover:bg-gray-600 text-white py-2 px-6">
                Cerrar
            </button>
        </div>
    </div>
</div>

<div id="sancionarModal" class="fixed inset-0 bg-black bg-opacity-50 hidden flex items-center justify-center z-50">
    <div class="bg-white rounded-2xl p-6 max-w-md w-full mx-4">
        <h3 class="text-xl font-bold text-gray-800 mb-4">
            <i class="fas fa-exclamation-triangle text-purple-500 mr-2"></i>
            Confirmar Sanción
        </h3>
        <p id="sancionarText" class="text-gray-600 mb-6"></p>
        <form id="sancionarForm" method="POST">
            @csrf
            <div class="flex gap-4">
                <button type="submit" class="flex-1 btn-modern bg-purple-500 hover:bg-purple-600 text-white py-2">
                    Confirmar Sanción
                </button>
                <button type="button" onclick="closeModals()" class="btn-modern bg-gray-500 hover:bg-gray-600 text-white py-2">
                    Cancelar
                </button>
            </div>
        </form>
    </div>
</div>

<div id="bloquearModal" class="fixed inset-0 bg-black bg-opacity-50 hidden flex items-center justify-center z-50">
    <div class="bg-white rounded-2xl p-6 max-w-md w-full mx-4">
        <h3 class="text-xl font-bold text-gray-800 mb-4">
            <i class="fas fa-ban text-red-500 mr-2"></i>
            Bloquear Turno
        </h3>
        <p id="bloquearText" class="text-gray-600 mb-6"></p>
        <form id="bloquearForm" method="POST">
            @csrf
            <input type="hidden" name="motivo" value="No se presentó después de 2 llamados">
            <div class="flex gap-4">
                <button type="submit" class="flex-1 btn-modern bg-red-500 hover:bg-red-600 text-white py-2">
                    Bloquear
                </button>
                <button type="button" onclick="closeModals()" class="btn-modern bg-gray-500 hover:bg-gray-600 text-white py-2">
                    Cancelar
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
function turnosApp() {
    return {
        init() {
            console.log('Turnos App Initialized');
        }
    }
}

function showSancionarModal(id, nombre, esPuntero) {
    document.getElementById('sancionarText').textContent = 
        esPuntero ? 
        nombre + ' será sancionado. Trabajará hasta las 9am y se retirará.' :
        nombre + ' será sancionado y perderá el día de trabajo.';
    document.getElementById('sancionarForm').action = '/tarjetera/turnos/' + id + '/sancionar';
    document.getElementById('sancionarModal').classList.remove('hidden');
}

function showBloquearModal(id, nombre) {
    document.getElementById('bloquearText').textContent = 
        '¿Bloquear el turno de ' + nombre + '? El conductor no trabajará todo el día.';
    document.getElementById('bloquearForm').action = '/tarjetera/turnos/' + id + '/bloquear';
    document.getElementById('bloquearModal').classList.remove('hidden');
}

function closeModals() {
    document.getElementById('sancionarModal').classList.add('hidden');
    document.getElementById('bloquearModal').classList.add('hidden');
    document.getElementById('colaModal').classList.add('hidden');
}

function showColaModal() {
    document.getElementById('colaModal').classList.remove('hidden');
}
</script>
@endpush
@endsection