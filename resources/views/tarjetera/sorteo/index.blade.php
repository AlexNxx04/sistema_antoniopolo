@extends('layouts.tarjetera')

@section('title', 'Sorteo')
@section('header', 'Sorteo Diario')

@section('content')
<div class="fade-in">
    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-5 mb-8">
        <div>
            <div class="flex items-center gap-3 mb-2">
                <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-yellow-400 to-yellow-600 flex items-center justify-center shadow-lg">
                    <i class="fas fa-dice text-white text-xl"></i>
                </div>
                <div>
                    <h2 class="text-3xl font-bold text-gray-800">Sorteo Diario</h2>
                    <p class="text-gray-400 text-sm mt-1">
                        @if($puntoId == 1)
                            Guaranda: 19 operan + 4 descanso
                        @else
                            Salinas: 18 operan + 3 descanso
                        @endif
                    </p>
                </div>
            </div>
        </div>
        <div class="flex items-center gap-3">
            <div class="px-4 py-2 bg-yellow-50 rounded-xl border border-yellow-300">
                <span class="text-sm text-yellow-700 font-medium">
                    <i class="fas fa-calendar-day mr-1"></i>
                    {{ now()->format('d/m/Y') }}
                </span>
            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="mb-6 p-4 bg-gradient-to-r from-yellow-50 to-yellow-50 border border-yellow-200 rounded-2xl text-yellow-700 flex items-center gap-3 fade-in">
            <i class="fas fa-check-circle text-xl"></i>
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="mb-6 p-4 bg-gradient-to-r from-red-50 to-pink-50 border border-red-200 rounded-2xl text-red-700 flex items-center gap-3 fade-in">
            <i class="fas fa-exclamation-circle text-xl"></i>
            {{ session('error') }}
        </div>
    @endif

    @if(!$cicloActivo)
        <div class="modern-card p-8 mb-6 fade-in delay-100 border-l-4 border-l-red-500">
            <div class="text-center">
                <div class="w-24 h-24 mx-auto mb-4 bg-red-100 rounded-full flex items-center justify-center">
                    <i class="fas fa-calendar-times text-5xl text-red-500"></i>
                </div>
                <p class="font-bold text-gray-800 text-2xl mb-2">No Hay Ciclo Activo</p>
                <p class="text-gray-500 mb-4">Necesitas activar un ciclo para hacer el sorteo</p>
                <a href="{{ route('tarjetera.ciclos.index') }}" class="btn-modern btn-warning">
                    <i class="fas fa-calendar-plus mr-2"></i>
                    Ir a Ciclos
                </a>
            </div>
        </div>
    @else
        @php
            $semanaCiclo = ((now()->weekOfYear - 1) % 20) + 1;
            $vueltaActual = $cicloActivo->vuelta_actual ?? 0;
            $posicionRotativa = intval((($semanaCiclo - 1) / 5)) % 4;
            $grupoPrestaVacante = (($semanaCiclo - 1) % 5) + 1;
            $today = now()->format('N');

            $nombresPosicion = [1 => '1ro', 2 => '2do', 3 => '3ro', 4 => '4to'];
            $posicionTexto = $nombresPosicion[$posicionRotativa + 1] ?? 'N/A';

            $grupoVacanteData = null;
            $grupoPrestaData = null;
            foreach ($gruposData as $g) {
                if (isset($g['grupo']->es_vacante) && $g['grupo']->es_vacante == 1) {
                    $grupoVacanteData = $g;
                }
                if ($g['grupo']->nombre == 'Grupo ' . $grupoPrestaVacante) {
                    $grupoPrestaData = $g;
                }
            }

            $conductorVacante = null;
            if ($grupoPrestaData && $posicionRotativa < $grupoPrestaData['miembros']->count()) {
                $conductorVacante = $grupoPrestaData['miembros'][$posicionRotativa];
            }
        @endphp

        @if($puntoId == 1 && !empty($gruposData))
        <div class="modern-card p-4 mb-6 fade-in delay-100 border-l-4 border-l-orange-500">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-4 flex-wrap">
                    <div class="flex items-center gap-2">
                        <span class="px-3 py-1 bg-orange-100 text-orange-700 rounded-full text-sm font-bold">
                            <i class="fas fa-sync-alt mr-1"></i>
                            Semana {{ $semanaCiclo }}/20
                        </span>
                    </div>
                    <div class="text-sm">
                        <span class="text-gray-500">Grupo prestando vacante:</span>
                        <span class="font-bold text-orange-600">Grupo {{ $grupoPrestaVacante }}</span>
                    </div>
                    <div class="text-sm">
                        <span class="text-gray-500">Posicion rotando:</span>
                        <span class="font-bold text-green-600">{{ $posicionTexto }} miembro</span>
                    </div>
                    @if($conductorVacante)
                    <div class="text-sm">
                        <span class="text-gray-500">Conductor en G6:</span>
                        <span class="font-bold text-green-600">{{ $conductorVacante->nombres }}</span>
                    </div>
                    @endif
                </div>
            </div>
        </div>
        @endif

        @if(empty($gruposData))
        <div class="modern-card p-6 mb-6 fade-in delay-100 border-l-4 border-l-orange-500">
            <div class="text-center">
                <div class="w-16 h-16 mx-auto mb-3 bg-orange-100 rounded-full flex items-center justify-center">
                    <i class="fas fa-users text-3xl text-orange-500"></i>
                </div>
                <p class="font-bold text-gray-800 text-xl mb-2">Generar Grupos de Descanso</p>
                <p class="text-gray-500 mb-4">
                    @if($puntoId == 1)
                        Guaranda: 5 grupos de 4 (Lun-Sab) + 1 grupo con 3 fijos + vacante (Sabado)
                    @else
                        Salinas: 7 grupos de 3 personas cada uno
                    @endif
                </p>
                <form action="{{ route('tarjetera.sorteo.crearGrupos') }}" method="POST">
                    @csrf
                    <input type="hidden" name="ciclo_id" value="{{ $cicloActivo->ciclo_id }}">
                    <button type="submit" class="btn-modern btn-warning">
                        <i class="fas fa-users mr-2"></i>
                        Generar Grupos de Descanso
                    </button>
                </form>
            </div>
        </div>
        @else
        <div class="modern-card p-4 mb-6 fade-in delay-100 border-l-4 border-l-yellow-500">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-lg bg-gradient-to-br from-yellow-400 to-yellow-600 flex items-center justify-center">
                        <i class="fas fa-check text-white"></i>
                    </div>
                    <div>
                        <div class="text-sm text-gray-500">Grupos de Descanso</div>
                        <div class="font-bold text-gray-800">{{ count($gruposData) }} grupos</div>
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    <button type="button" onclick="document.getElementById('modalEliminar').classList.remove('hidden')" class="btn-modern bg-red-500 hover:bg-red-600 text-white px-4 py-2 text-sm">
                        <i class="fas fa-trash mr-2"></i>
                        Recrear
                    </button>
                    <form action="{{ route('tarjetera.sorteo.randomizarDias') }}" method="POST">
                        @csrf
                        <input type="hidden" name="ciclo_id" value="{{ $cicloActivo->ciclo_id }}">
                        <button type="submit" class="btn-modern bg-yellow-500 hover:bg-yellow-600 text-white px-4 py-2 text-sm">
                            <i class="fas fa-random mr-2"></i>
                            Randomizar Dias
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 mb-8">
            @foreach($gruposData as $data)
            @php
            $dias = ['', 'Lunes', 'Martes', 'Miercoles', 'Jueves', 'Viernes', 'Sabado', 'Domingo'];
            $diaNombre = $dias[$data['grupo']->dia_descanso] ?? 'Dia ' . $data['grupo']->dia_descanso;
            $tieneVacante = isset($data['grupo']->es_vacante) && $data['grupo']->es_vacante == 1;
            $esDominical = isset($data['grupo']->es_dominical) && $data['grupo']->es_dominical == true;
            $esPrestaVacante = $data['grupo']->nombre == 'Grupo ' . $grupoPrestaVacante && !$esDominical;
            $descansaDomingo = $esPrestaVacante && $today == 7;

            $miembrosFijos = [];
            $miembroVacante = null;
            if ($tieneVacante) {
                foreach ($data['miembros'] as $idx => $m) {
                    if ($idx < 3) {
                        $miembrosFijos[] = $m;
                    } else {
                        $miembroVacante = $m;
                    }
                }
            }
            @endphp
            <div class="modern-card p-4 border-l-4 {{ $esDominical ? 'border-l-red-600 bg-red-50' : ($tieneVacante ? 'border-l-orange-500' : ($esPrestaVacante ? 'border-l-red-500' : 'border-l-purple-500')) }}">
                <div class="flex items-center justify-between mb-3">
                    <div class="flex items-center gap-2 flex-wrap">
                        <span class="text-lg font-bold text-gray-800">{{ $data['grupo']->nombre }}</span>
                        @if($esDominical)
                        <span class="px-2 py-0.5 bg-red-600 text-white rounded text-xs font-bold">DESCANSO DOMINGO</span>
                        @endif
                        @if($tieneVacante)
                        <span class="px-2 py-0.5 bg-orange-100 text-orange-700 rounded text-xs font-bold">3 FIJOS + VACANTE</span>
                        @endif
                        @if($esPrestaVacante && $puntoId == 1)
                        <span class="px-2 py-0.5 bg-red-100 text-red-700 rounded text-xs font-bold">PRESTA VACANTE</span>
                        @endif
                        @if($descansaDomingo)
                        <span class="px-2 py-0.5 bg-yellow-100 text-yellow-700 rounded text-xs font-bold">+DESCANSA DOMINGO</span>
                        @endif
                    </div>
                    <span class="px-3 py-1 {{ $esDominical ? 'bg-red-600 text-white' : 'bg-yellow-100 text-yellow-700' }} rounded-full text-sm font-bold">
                        {{ $esDominical ? 'Domingo' : $diaNombre }}
                    </span>
                </div>
                @if($esDominical)
                <div class="text-xs text-red-600 mb-2">Grupo {{ $data['grupo_presta'] ?? $grupoPrestaVacante }} prestando vacante</div>
                @endif
                <div class="space-y-2">
                    @if($tieneVacante)
                        <div class="text-xs text-gray-500 mb-1">Fijos:</div>
                        @foreach($miembrosFijos as $idx => $miembro)
                        <div class="flex items-center gap-2 text-sm">
                            <i class="fas fa-user text-gray-400"></i>
                            <span class="text-gray-600">{{ $miembro->nombres }}</span>
                        </div>
                        @endforeach
                        <div class="text-xs text-gray-500 mt-2 mb-1">Vacante ({{ $posicionTexto }} de {{ $grupoPrestaVacante }}):</div>
                        @if($conductorVacante)
                        <div class="flex items-center gap-2 text-sm text-blue-600 bg-blue-50 px-2 py-1 rounded">
                            <i class="fas fa-user-plus"></i>
                            <span class="font-medium">{{ $conductorVacante->nombres }}</span>
                        </div>
                        @endif
                    @else
                        @foreach($data['miembros'] as $miembro)
                        <div class="flex items-center gap-2 text-sm">
                            <i class="fas fa-user text-gray-400"></i>
                            <span class="text-gray-600">{{ $miembro->nombres }}</span>
                        </div>
                        @endforeach
                    @endif
                </div>
            </div>
            @endforeach
        </div>
        @endif

        @if($yaSorteado)
        <div class="modern-card p-5 mb-6 fade-in delay-100 border-l-4 border-l-yellow-500">
            <div class="flex items-center gap-4">
                <div class="w-16 h-16 rounded-2xl bg-gradient-to-br from-yellow-400 to-yellow-600 flex items-center justify-center shadow-lg">
                    <i class="fas fa-check-circle text-white text-2xl"></i>
                </div>
                <div class="flex-1">
                    <p class="font-bold text-gray-800 text-xl">Turnos de Hoy Asignados</p>
                    <p class="text-sm text-gray-500 mt-1">
                        <i class="fas fa-clock mr-1"></i>
                        {{ $turnosHoy->first()?->generado_at?->format('H:i') ?? '' }}
                    </p>
                </div>
                <div class="text-right">
                    <div class="text-sm text-gray-400">Unidades</div>
                    <div class="text-3xl font-bold text-yellow-600">{{ $turnosHoy->count() }}</div>
                </div>
            </div>
        </div>

        <div class="modern-card overflow-hidden fade-in delay-200">
            <div class="p-5 border-b border-gray-100 bg-gradient-to-r from-yellow-50 to-white">
                <div class="flex items-center justify-between">
                    <h3 class="font-bold text-gray-800 flex items-center gap-2">
                        <i class="fas fa-list text-yellow-500"></i>
                        Turnos Asignados
                    </h3>
                    <span class="text-sm text-gray-500">{{ $turnosHoy->count() }} unidades</span>
                </div>
            </div>
            <div class="overflow-x-auto">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-3 p-2">
                    @forelse($turnosHoy->sortBy('posicion') as $turno)
                    <div class="bg-white rounded-xl border border-gray-100 shadow-sm hover:shadow-md transition-all p-4">
                        <div class="flex items-center justify-between mb-3">
                            @if($turno->es_descanso == 1)
                                <span class="px-3 py-1 bg-gray-100 text-gray-600 rounded-full text-sm font-bold">Descanso</span>
                            @elseif($turno->posicion == 1)
                                <span class="px-3 py-1 bg-yellow-100 text-yellow-700 rounded-full text-sm font-bold">Puntero 1</span>
                            @elseif($turno->posicion == 2)
                                <span class="px-3 py-1 bg-yellow-100 text-yellow-700 rounded-full text-sm font-bold">Puntero 2</span>
                            @else
                                <span class="px-3 py-1 bg-blue-100 text-yellow-700 rounded-full text-sm font-bold">Turno {{ $turno->posicion }}</span>
                            @endif
                            <div class="w-10 h-10 rounded-lg bg-gradient-to-br from-yellow-400 to-yellow-600 flex items-center justify-center text-white font-bold">
                                {{ $turno->unidad->numero_interno ?? '-' }}
                            </div>
                        </div>
                        <div class="space-y-2">
                            <div class="flex items-center gap-2">
                                <i class="fas fa-car text-gray-400"></i>
                                <span class="font-semibold text-gray-800">{{ $turno->unidad->placa ?? 'N/A' }}</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <i class="fas fa-user text-gray-400"></i>
                                <span class="text-gray-600">{{ $turno->usuario->nombres ?? 'Sin asignar' }}</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <i class="fas fa-phone text-gray-400"></i>
                                <span class="text-gray-500">{{ $turno->usuario->telefono ?? 'N/A' }}</span>
                            </div>
                        </div>
                    </div>
                    @empty
                    <div class="col-span-full py-16 text-center">
                        <div class="w-20 h-20 mx-auto mb-4 bg-gray-100 rounded-full flex items-center justify-center">
                            <i class="fas fa-inbox text-3xl text-gray-300"></i>
                        </div>
                        <p class="text-gray-500">No hay turnos</p>
                    </div>
                    @endforelse
                </div>
            </div>
        </div>

        <div class="flex justify-center gap-4 mt-6">
            <button type="button" onclick="document.getElementById('modalSorteo').classList.remove('hidden')" class="btn-modern btn-warning text-lg px-8 py-3">
                <i class="fas fa-dice mr-2"></i>
                Ejecutar Sorteo
            </button>
            @if($turnosHoy->count() > 0)
            <button type="button" onclick="document.getElementById('modalLimpiarTurnos').classList.remove('hidden')" class="btn-modern btn-danger text-lg px-8 py-3">
                <i class="fas fa-trash mr-2"></i>
                Limpiar Turnos
            </button>
            @endif
        </div>
        @else
        <div class="modern-card p-6 fade-in delay-100">
            <div class="text-center mb-6">
                <p class="font-bold text-gray-800 text-2xl mb-2">Sorteo Diario</p>
                <p class="text-gray-500">
                    @if($puntoId == 1) 19 operan + 4 descanso = 23 conductores @else 18 operan + 3 descanso = 21 conductores @endif
                </p>
            </div>

            <div class="modern-card p-5 mb-6">
                <div class="grid grid-cols-2 gap-4 text-center">
                    <div class="p-4 bg-yellow-50 rounded-xl">
                        <div class="text-3xl font-bold text-yellow-600">{{ $puntoId == 1 ? 19 : 18 }}</div>
                        <div class="text-sm text-yellow-700">Operan</div>
                    </div>
                    <div class="p-4 bg-gray-100 rounded-xl">
                        <div class="text-3xl font-bold text-gray-600">{{ $puntoId == 1 ? 4 : 3 }}</div>
                        <div class="text-sm text-gray-600">Descansan</div>
                    </div>
                </div>
            </div>

            <div class="flex justify-center mt-6">
                <button type="button" onclick="document.getElementById('modalSorteo').classList.remove('hidden')" class="btn-modern btn-warning text-lg px-8 py-3">
                    <i class="fas fa-dice mr-2"></i>
                    Ejecutar Sorteo
                </button>
            </div>
        </div>
        @endif
    @endif
</div>

<div id="modalSorteo" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50 flex items-center justify-center">
    <div class="bg-white rounded-2xl p-6 max-w-sm mx-4 shadow-2xl">
        <div class="text-center">
            <div class="w-16 h-16 mx-auto mb-4 bg-yellow-100 rounded-full flex items-center justify-center">
                <i class="fas fa-dice text-3xl text-yellow-600"></i>
            </div>
            <h3 class="text-xl font-bold text-gray-800 mb-2">¿Ejecutar Sorteo?</h3>
            <p class="text-gray-500 mb-6">Se asignarán los turnos del día de forma aleatoria.</p>
            <div class="flex gap-3">
                <button type="button" onclick="document.getElementById('modalSorteo').classList.add('hidden')" class="flex-1 px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-700 rounded-xl font-medium">
                    Cancelar
                </button>
                <form action="{{ route('tarjetera.sorteo.ejecutar') }}" method="POST" class="flex-1">
                    @csrf
                    <input type="hidden" name="ciclo_id" value="{{ $cicloActivo->ciclo_id }}">
                    <button type="submit" class="w-full px-4 py-2 bg-yellow-500 hover:bg-yellow-600 text-white rounded-xl font-medium">
                        Ejecutar
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<div id="modalEliminar" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50 flex items-center justify-center">
    <div class="bg-white rounded-2xl p-6 max-w-sm mx-4 shadow-2xl">
        <div class="text-center">
            <div class="w-16 h-16 mx-auto mb-4 bg-red-100 rounded-full flex items-center justify-center">
                <i class="fas fa-exclamation-triangle text-3xl text-red-500"></i>
            </div>
            <h3 class="text-xl font-bold text-gray-800 mb-2">¿Recrear Grupos?</h3>
            <p class="text-gray-500 mb-6">Se eliminarán los grupos actuales y se crearán nuevos grupos con distribución aleatoria.</p>
            <div class="flex gap-3">
                <button type="button" onclick="document.getElementById('modalEliminar').classList.add('hidden')" class="flex-1 px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-700 rounded-xl font-medium">
                    Cancelar
                </button>
                <form action="{{ route('tarjetera.sorteo.eliminarGrupos') }}" method="POST" class="flex-1">
                    @csrf
                    <input type="hidden" name="ciclo_id" value="{{ $cicloActivo->ciclo_id }}">
                    <button type="submit" class="w-full px-4 py-2 bg-red-500 hover:bg-red-600 text-white rounded-xl font-medium">
                        Recrear
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<div id="modalLimpiarTurnos" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50 flex items-center justify-center">
    <div class="bg-white rounded-2xl p-6 max-w-sm mx-4 shadow-2xl">
        <div class="text-center">
            <div class="w-16 h-16 mx-auto mb-4 bg-red-100 rounded-full flex items-center justify-center">
                <i class="fas fa-broom text-3xl text-red-500"></i>
            </div>
            <h3 class="text-xl font-bold text-gray-800 mb-2">¿Limpiar Turnos?</h3>
            <p class="text-gray-500 mb-6">Se eliminarán los turnos asignados hoy. Podrás ejecutar un nuevo sorteo.</p>
            <div class="flex gap-3">
                <button type="button" onclick="document.getElementById('modalLimpiarTurnos').classList.add('hidden')" class="flex-1 px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-700 rounded-xl font-medium">
                    Cancelar
                </button>
                <form action="{{ route('tarjetera.sorteo.limpiarSorteos') }}" method="POST" class="flex-1">
                    @csrf
                    <button type="submit" class="w-full px-4 py-2 bg-red-500 hover:bg-red-600 text-white rounded-xl font-medium">
                        Limpiar
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
