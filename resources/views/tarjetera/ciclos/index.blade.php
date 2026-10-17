@extends('layouts.tarjetera')

@section('title', 'Ciclos')
@section('header', 'Ciclos Anuales')

@section('content')
<div class="fade-in">
    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-5 mb-8">
        <div>
            <div class="flex items-center gap-3 mb-2">
                <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-amber-400 to-amber-600 flex items-center justify-center shadow-lg">
                    <i class="fas fa-calendar-check text-white text-xl"></i>
                </div>
                <div>
                    <h2 class="text-3xl font-bold text-gray-800">
                        <span class="gradient-text">Ciclos Anuales</span>
                    </h2>
                    <p class="text-gray-400 text-sm mt-1">Gestiona los ciclos de operación</p>
                </div>
            </div>
        </div>
        <button type="button" onclick="abrirModal('modalCrear')" class="btn-modern btn-warning">
            <i class="fas fa-plus-circle mr-2"></i>
            Nuevo Ciclo
        </button>
    </div>

    @if(session('success'))
        <div class="mb-6 p-4 bg-gradient-to-r from-emerald-50 to-green-50 border border-emerald-200 rounded-2xl text-emerald-700 flex items-center gap-3 fade-in">
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

    @if($cicloActivo)
    <?php 
        $diasRestantes = $cicloActivo->fecha_fin ? (int)now()->diffInDays($cicloActivo->fecha_fin) : 0;
    ?>
    <div class="modern-card p-5 mb-6 fade-in delay-100 border-l-4 border-l-emerald-500">
        <div class="flex items-center gap-4">
            <div class="w-16 h-16 rounded-2xl bg-gradient-to-br from-emerald-400 to-emerald-600 flex items-center justify-center shadow-lg">
                <i class="fas fa-play-circle text-white text-2xl"></i>
            </div>
            <div class="flex-1">
                <div class="flex items-center gap-2 mb-1">
                    <span class="text-xs font-bold text-emerald-600 uppercase tracking-wider">Ciclo Activo</span>
                    <span class="badge-modern badge-success text-xs">Operando</span>
                </div>
                <p class="font-bold text-gray-800 text-2xl">Año {{ $cicloActivo->anio }}</p>
                <p class="text-sm text-gray-500 mt-1">
                    <i class="fas fa-calendar mr-1"></i>
                    {{ $cicloActivo->fecha_inicio?->format('d M Y') }} - {{ $cicloActivo->fecha_fin?->format('d M Y') }}
                </p>
            </div>
            <div class="text-right">
                <div class="text-sm text-gray-400">Días restantes</div>
                <div class="text-3xl font-bold text-emerald-600">
                    {{ $diasRestantes }} días
                </div>
            </div>
        </div>
    </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 mb-6">
        <div class="modern-card p-4 border-l-4 border-l-emerald-500">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-lg bg-emerald-100 flex items-center justify-center">
                    <i class="fas fa-play text-emerald-600"></i>
                </div>
                <div>
                    <div class="text-xs text-gray-500">Ciclos Activos</div>
                    <div class="text-xl font-bold text-gray-800">{{ $todosLosCiclos->where('activo', 1)->count() }}</div>
                </div>
            </div>
        </div>
        
        <div class="modern-card p-4 border-l-4 border-l-orange-500">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-lg bg-orange-100 flex items-center justify-center">
                    <i class="fas fa-pause text-orange-600"></i>
                </div>
                <div>
                    <div class="text-xs text-gray-500">Desactivados</div>
                    <div class="text-xl font-bold text-gray-800">{{ $todosLosCiclos->where('activo', 0)->count() }}</div>
                </div>
            </div>
        </div>
        
        <div class="modern-card p-4 border-l-4 border-l-gray-500">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-lg bg-gray-100 flex items-center justify-center">
                    <i class="fas fa-check text-gray-600"></i>
                </div>
                <div>
                    <div class="text-xs text-gray-500">Terminados</div>
                    <div class="text-xl font-bold text-gray-800">{{ $todosTerminados->count() }}</div>
                </div>
            </div>
        </div>
    </div>

    <div class="modern-card overflow-hidden fade-in delay-200">
        <div class="p-5 border-b border-gray-100 bg-gradient-to-r from-amber-50 to-white">
            <div class="flex items-center justify-between">
                <h3 class="font-bold text-gray-800 flex items-center gap-2">
                    <i class="fas fa-layer-group text-amber-500"></i>
                    Todos los Ciclos
                </h3>
                <span class="text-sm text-gray-500">{{ $todosLosCiclos->count() }} ciclos</span>
            </div>
        </div>
        <div class="overflow-x-auto">
            <table class="table-modern">
                <thead>
                    <tr>
                        <th class="px-5 py-3"><i class="fas fa-hashtag mr-1"></i> ID</th>
                        <th class="px-5 py-3"><i class="fas fa-calendar mr-1"></i> Año</th>
                        <th class="px-5 py-3"><i class="fas fa-play mr-1"></i> Inicio</th>
                        <th class="px-5 py-3"><i class="fas fa-stop mr-1"></i> Fin</th>
                        <th class="px-5 py-3"><i class="fas fa-toggle-on mr-1"></i> Estado</th>
                        <th class="px-5 py-3 text-center"><i class="fas fa-cogs mr-1"></i> Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($todosLosCiclos as $ciclo)
                    <tr class="hover:bg-amber-50/30 transition-all">
                        <td class="px-5 py-3 text-gray-500">#{{ $ciclo->ciclo_id }}</td>
                        <td class="px-5 py-3">
                            <span class="font-bold text-gray-800 text-lg">{{ $ciclo->anio }}</span>
                        </td>
                        <td class="px-5 py-3 text-gray-600">{{ $ciclo->fecha_inicio?->format('d/m/Y') }}</td>
                        <td class="px-5 py-3 text-gray-600">{{ $ciclo->fecha_fin?->format('d/m/Y') }}</td>
                        <td class="px-5 py-3">
                            @if($ciclo->activo == 1)
                                <span class="px-3 py-1 bg-emerald-100 text-emerald-700 rounded-full text-xs font-bold">
                                    <i class="fas fa-play mr-1"></i> Activo
                                </span>
                            @else
                                <span class="px-3 py-1 bg-orange-100 text-orange-700 rounded-full text-xs font-bold">
                                    <i class="fas fa-pause mr-1"></i> Desactivado
                                </span>
                            @endif
                        </td>
                        <td class="px-5 py-3">
                            <div class="flex items-center justify-center gap-1">
                                <button type="button" onclick="abrirModal('modalEditar{{ $ciclo->ciclo_id }}')" 
                                    class="p-2 text-blue-600 hover:bg-blue-50 rounded-lg transition-all" title="Editar">
                                    <i class="fas fa-edit"></i>
                                </button>
                                @if($ciclo->activo == 1)
                                    <button type="button" onclick="abrirModal('modalDesactivar{{ $ciclo->ciclo_id }}')" 
                                        class="p-2 text-red-600 hover:bg-red-50 rounded-lg transition-all" title="Desactivar">
                                        <i class="fas fa-stop"></i>
                                    </button>
                                @else
                                    <button type="button" onclick="abrirModal('modalActivar{{ $ciclo->ciclo_id }}')" 
                                        class="p-2 text-emerald-600 hover:bg-emerald-50 rounded-lg transition-all" title="Activar">
                                        <i class="fas fa-play"></i>
                                    </button>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-5 py-16 text-center">
                            <div class="w-20 h-20 mx-auto mb-4 bg-amber-50 rounded-full flex items-center justify-center">
                                <i class="fas fa-calendar-plus text-3xl text-amber-300"></i>
                            </div>
                            <p class="text-gray-500 text-lg mb-2">No hay ciclos registrados</p>
                            <button type="button" onclick="abrirModal('modalCrear')" class="btn-modern btn-warning">
                                <i class="fas fa-plus mr-2"></i> Crear primer ciclo
                            </button>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="modern-card overflow-hidden fade-in delay-300 mt-6">
        <div class="p-5 border-b border-gray-100 bg-gradient-to-r from-gray-50 to-white">
            <h3 class="font-bold text-gray-800 flex items-center gap-2">
                <i class="fas fa-history text-gray-500"></i>
                Ciclos Terminados
            </h3>
        </div>
        <div class="overflow-x-auto">
            <table class="table-modern">
                <thead>
                    <tr>
                        <th class="px-5 py-3">Año</th>
                        <th class="px-5 py-3">Período</th>
                        <th class="px-5 py-3">Duración</th>
                        <th class="px-5 py-3">Estado</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($todosTerminados as $ciclo)
                    <tr class="hover:bg-gray-50/30">
                        <td class="px-5 py-3 font-bold text-gray-700">{{ $ciclo->anio }}</td>
                        <td class="px-5 py-3 text-gray-600">{{ $ciclo->fecha_inicio?->format('d/m/Y') }} - {{ $ciclo->fecha_fin?->format('d/m/Y') }}</td>
                        <td class="px-5 py-3 text-gray-500">
                            @php
                                $duracion = '-';
                                if ($ciclo->fecha_inicio && $ciclo->fecha_fin) {
                                    $dias = (int)$ciclo->fecha_inicio->diffInDays($ciclo->fecha_fin);
                                    $duracion = $dias . ' días';
                                }
                            @endphp
                            {{ $duracion }}
                        </td>
                        <td class="px-5 py-3">
                            <span class="px-3 py-1 bg-gray-100 text-gray-600 rounded-full text-xs font-bold">
                                <i class="fas fa-check mr-1"></i> Terminado
                            </span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="px-5 py-8 text-center text-gray-400">
                            <i class="fas fa-check-circle text-2xl mb-2"></i>
                            <p>No hay ciclos terminados aún</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

@push('modales')
<div id="modalCrear" class="fixed inset-0 bg-black/50 z-50 hidden items-center justify-center">
    <div class="bg-white rounded-2xl p-6 max-w-md mx-4 shadow-2xl">
        <div class="flex items-center gap-3 mb-5">
            <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-amber-400 to-amber-600 flex items-center justify-center">
                <i class="fas fa-plus-circle text-white text-xl"></i>
            </div>
            <h3 class="font-bold text-xl text-gray-800">Crear Nuevo Ciclo</h3>
        </div>
        <form action="{{ route('tarjetera.ciclos.store') }}" method="POST" class="space-y-4">
            @csrf
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Año del Ciclo</label>
                <input type="number" name="anio" value="{{ date('Y') }}" class="input-modern w-full" placeholder="2026" required>
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Fecha Inicio</label>
                    <input type="date" name="fecha_inicio" class="input-modern w-full text-sm" required>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Fecha Fin</label>
                    <input type="date" name="fecha_fin" class="input-modern w-full text-sm" required>
                </div>
            </div>
            <div class="flex items-center gap-2 p-3 bg-emerald-50 rounded-xl border border-emerald-200">
                <input type="checkbox" name="activo" id="activo" class="w-4 h-4 text-emerald-600 rounded" checked>
                <label for="activo" class="text-sm text-emerald-700 font-medium">Activar ciclo inmediatamente</label>
            </div>
            <div class="flex gap-3 pt-2">
                <button type="button" onclick="cerrarModal('modalCrear')" class="btn-modern btn-secondary flex-1">Cancelar</button>
                <button type="submit" class="btn-modern btn-warning flex-1">Crear Ciclo</button>
            </div>
        </form>
    </div>
</div>

@foreach($todosLosCiclos as $ciclo)
<div id="modalEditar{{ $ciclo->ciclo_id }}" class="fixed inset-0 bg-black/50 z-50 hidden items-center justify-center">
    <div class="bg-white rounded-2xl p-6 max-w-md mx-4 shadow-2xl">
        <div class="flex items-center gap-3 mb-5">
            <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-blue-400 to-blue-600 flex items-center justify-center">
                <i class="fas fa-edit text-white text-xl"></i>
            </div>
            <h3 class="font-bold text-xl text-gray-800">Editar Ciclo {{ $ciclo->anio }}</h3>
        </div>
        <form action="{{ route('tarjetera.ciclos.update', $ciclo->ciclo_id) }}" method="POST" class="space-y-4">
            @csrf
            @method('PUT')
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Año del Ciclo</label>
                <input type="number" name="anio" value="{{ $ciclo->anio }}" class="input-modern w-full" required>
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Fecha Inicio</label>
                    <input type="date" name="fecha_inicio" value="{{ $ciclo->fecha_inicio }}" class="input-modern w-full text-sm" required>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Fecha Fin</label>
                    <input type="date" name="fecha_fin" value="{{ $ciclo->fecha_fin }}" class="input-modern w-full text-sm" required>
                </div>
            </div>
            <div class="flex gap-3 pt-2">
                <button type="button" onclick="cerrarModal('modalEditar{{ $ciclo->ciclo_id }}')" class="btn-modern btn-secondary flex-1">Cancelar</button>
                <button type="submit" class="btn-modern btn-warning flex-1">Guardar</button>
            </div>
        </form>
    </div>
</div>

<div id="modalDesactivar{{ $ciclo->ciclo_id }}" class="fixed inset-0 bg-black/50 z-50 hidden items-center justify-center">
    <div class="bg-white rounded-2xl p-6 max-w-md mx-4 shadow-2xl">
        <div class="text-center">
            <div class="w-20 h-20 mx-auto mb-4 bg-red-100 rounded-full flex items-center justify-center">
                <i class="fas fa-exclamation-triangle text-4xl text-red-500"></i>
            </div>
            <h3 class="font-bold text-2xl text-gray-800 mb-2">⚠️ Desactivar Ciclo {{ $ciclo->anio }}</h3>
            <div class="text-left bg-red-50 p-4 rounded-xl mb-4">
                <p class="text-red-700 font-medium mb-2">⚠️ ¿Estás seguro?</p>
                <ul class="text-sm text-red-600 space-y-1">
                    <li>• Se perderán los grupos de descanso</li>
                    <li>• Se perderán los turnos operativos</li>
                    <li>• Deberás crear un nuevo ciclo para operar</li>
                </ul>
            </div>
            <p class="text-gray-500 text-sm mb-4">Esta acción no se puede deshacer fácilmente.</p>
            <form action="{{ route('tarjetera.ciclos.desactivar', $ciclo->ciclo_id) }}" method="POST">
                @csrf
                <div class="flex gap-3">
                    <button type="button" onclick="cerrarModal('modalDesactivar{{ $ciclo->ciclo_id }}')" class="btn-modern btn-secondary flex-1">Cancelar</button>
                    <button type="submit" class="btn-modern btn-danger flex-1">Sí, Desactivar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div id="modalActivar{{ $ciclo->ciclo_id }}" class="fixed inset-0 bg-black/50 z-50 hidden items-center justify-center">
    <div class="bg-white rounded-2xl p-6 max-w-md mx-4 shadow-2xl">
        <div class="text-center">
            <div class="w-20 h-20 mx-auto mb-4 bg-emerald-100 rounded-full flex items-center justify-center">
                <i class="fas fa-play-circle text-4xl text-emerald-500"></i>
            </div>
            <h3 class="font-bold text-2xl text-gray-800 mb-2">✅ Activar Ciclo {{ $ciclo->anio }}</h3>
            <div class="text-left bg-emerald-50 p-4 rounded-xl mb-4">
                <p class="text-emerald-700 font-medium mb-2">✨ Se restablecerá:</p>
                <ul class="text-sm text-emerald-600 space-y-1">
                    <li>• Los grupos de descanso</li>
                    <li>• Los turnos operativos</li>
                    <li>• Podrás hacer el sorteo diario</li>
                </ul>
            </div>
            <p class="text-gray-500 text-sm mb-4">¿Deseas continuar?</p>
            <form action="{{ route('tarjetera.ciclos.activar', $ciclo->ciclo_id) }}" method="POST">
                @csrf
                <div class="flex gap-3">
                    <button type="button" onclick="cerrarModal('modalActivar{{ $ciclo->ciclo_id }}')" class="btn-modern btn-secondary flex-1">Cancelar</button>
                    <button type="submit" class="btn-modern btn-success flex-1">Sí, Activar</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endforeach

@endpush

@push('scripts')
<script>
function abrirModal(id) {
    document.getElementById(id).classList.remove('hidden');
    document.getElementById(id).classList.add('flex');
}
function cerrarModal(id) {
    document.getElementById(id).classList.add('hidden');
    document.getElementById(id).classList.remove('flex');
}
</script>
@endpush
@endsection