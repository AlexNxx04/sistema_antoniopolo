@extends('layouts.tarjetera')

@section('title', 'Auditoría')
@section('header', 'Auditoría')

@section('content')

<div class="p-6">
    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4 mb-6">
        <div class="flex items-center gap-3">
            <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-green-400 to-emerald-600 flex items-center justify-center shadow-lg">
                <i class="fas fa-clipboard-list text-white text-lg"></i>
            </div>
            <div>
                <h2 class="text-2xl font-bold text-gray-800">Auditoría</h2>
                <p class="text-gray-500 text-sm">{{ $puntoNombre }} - Registro de acciones</p>
            </div>
        </div>
        <div class="flex gap-3">
            <a href="{{ route('tarjetera.historial.index') }}" class="px-4 py-2 bg-yellow-500 text-white rounded-lg hover:bg-yellow-600 shadow-md transition-all text-sm">
                <i class="fas fa-calendar mr-2"></i> Historial Turnos
            </a>
            <form action="{{ route('tarjetera.auditoria.exportar') }}" method="GET" target="_blank">
                @if(request()->has('fecha_desde'))
                    <input type="hidden" name="fecha_desde" value="{{ request('fecha_desde') }}">
                @endif
                @if(request()->has('fecha_hasta'))
                    <input type="hidden" name="fecha_hasta" value="{{ request('fecha_hasta') }}">
                @endif
                @if(request()->has('tabla'))
                    <input type="hidden" name="tabla" value="{{ request('tabla') }}">
                @endif
                @if(request()->has('accion'))
                    <input type="hidden" name="accion" value="{{ request('accion') }}">
                @endif
                <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 shadow-md transition-all text-sm">
                    <i class="fas fa-file-csv mr-2"></i> Exportar CSV
                </button>
            </form>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
        <div class="modern-card p-4 text-center border-l-4 border-l-blue-500">
            <div class="text-3xl font-bold text-blue-600">{{ $auditorias->total() }}</div>
            <div class="text-sm text-gray-500 mt-1">Total</div>
        </div>
        <div class="modern-card p-4 text-center border-l-4 border-l-green-500">
            <div class="text-3xl font-bold text-green-600">{{ $auditorias->where('accion', 'crear')->count() }}</div>
            <div class="text-sm text-gray-500 mt-1">Creaciones</div>
        </div>
        <div class="modern-card p-4 text-center border-l-4 border-l-yellow-500">
            <div class="text-3xl font-bold text-yellow-600">{{ $auditorias->where('accion', 'modificar')->count() }}</div>
            <div class="text-sm text-gray-500 mt-1">Modificaciones</div>
        </div>
        <div class="modern-card p-4 text-center border-l-4 border-l-red-500">
            <div class="text-3xl font-bold text-red-600">{{ $auditorias->where('accion', 'eliminar')->count() }}</div>
            <div class="text-sm text-gray-500 mt-1">Eliminaciones</div>
        </div>
    </div>

    <form method="GET" action="{{ route('tarjetera.auditoria.index') }}" class="modern-card p-4 mb-6">
        <div class="flex flex-wrap gap-4 items-end">
            <div class="flex-1 min-w-[200px]">
                <label class="block text-sm font-medium text-gray-600 mb-1">Buscar</label>
                <input type="text" name="buscar" value="{{ request('buscar') }}" placeholder="Usuario, tabla, acción..." class="w-full px-4 py-2 border border-gray-300 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-green-500">
            </div>
            <div class="w-40">
                <label class="block text-sm font-medium text-gray-600 mb-1">Tabla</label>
                <select name="tabla" class="w-full px-4 py-2 border border-gray-300 rounded-xl focus:ring-2 focus:ring-green-500">
                    <option value="">Todas</option>
                    @foreach($tablas as $key => $valor)
                    <option value="{{ $key }}" {{ request('tabla') == $key ? 'selected' : '' }}>{{ $valor }}</option>
                    @endforeach
                </select>
            </div>
            <div class="w-36">
                <label class="block text-sm font-medium text-gray-600 mb-1">Acción</label>
                <select name="accion" class="w-full px-4 py-2 border border-gray-300 rounded-xl focus:ring-2 focus:ring-green-500">
                    <option value="">Todas</option>
                    @foreach($acciones as $key => $valor)
                    <option value="{{ $key }}" {{ request('accion') == $key ? 'selected' : '' }}>{{ $valor }}</option>
                    @endforeach
                </select>
            </div>
            <div class="w-40">
                <label class="block text-sm font-medium text-gray-600 mb-1">Desde</label>
                <input type="date" name="fecha_desde" value="{{ request('fecha_desde') }}" class="w-full px-4 py-2 border border-gray-300 rounded-xl focus:ring-2 focus:ring-green-500">
            </div>
            <div class="w-40">
                <label class="block text-sm font-medium text-gray-600 mb-1">Hasta</label>
                <input type="date" name="fecha_hasta" value="{{ request('fecha_hasta') }}" class="w-full px-4 py-2 border border-gray-300 rounded-xl focus:ring-2 focus:ring-green-500">
            </div>
            <button type="submit" class="px-6 py-2 bg-green-500 text-white rounded-xl hover:bg-green-600 shadow-md transition-all">
                <i class="fas fa-search mr-2"></i> Filtrar
            </button>
            <a href="{{ route('tarjetera.auditoria.index') }}" class="px-6 py-2 bg-gray-100 text-gray-700 rounded-xl hover:bg-gray-200 transition-all">
                <i class="fas fa-times mr-2"></i> Limpiar
            </a>
        </div>
    </form>

    @if($auditorias->count() > 0)
    <div class="modern-card overflow-hidden">
        <div class="p-4 bg-gradient-to-r from-green-50 to-emerald-50 border-b border-gray-100">
            <h3 class="font-bold text-gray-800 flex items-center gap-2">
                <i class="fas fa-list text-green-600"></i>
                Registro de Acciones
                <span class="ml-auto text-sm text-gray-500">{{ $auditorias->total() }} registros</span>
            </h3>
        </div>
        <div class="divide-y divide-gray-100">
            @foreach($auditorias as $a)
            <div class="p-4 hover:bg-green-50/30 transition-colors">
                <div class="flex items-start gap-4">
                    <div class="flex-shrink-0">
                        @if($a->accion == 'crear' || $a->accion == 'generar')
                            <span class="w-10 h-10 bg-green-100 text-green-600 rounded-full flex items-center justify-center">
                                <i class="fas fa-plus"></i>
                            </span>
                        @elseif($a->accion == 'modificar')
                            <span class="w-10 h-10 bg-yellow-100 text-yellow-600 rounded-full flex items-center justify-center">
                                <i class="fas fa-edit"></i>
                            </span>
                        @else
                            <span class="w-10 h-10 bg-red-100 text-red-600 rounded-full flex items-center justify-center">
                                <i class="fas fa-trash"></i>
                            </span>
                        @endif
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center gap-3 mb-1 flex-wrap">
                            <span class="px-2 py-1 bg-blue-100 text-blue-700 rounded text-xs font-bold">
                                {{ $tablas[$a->tabla_afectada] ?? $a->tabla_afectada }}
                            </span>
                            <span class="px-2 py-1 {{ $a->accion == 'crear' || $a->accion == 'generar' ? 'bg-green-100 text-green-700' : ($a->accion == 'modificar' ? 'bg-yellow-100 text-yellow-700' : 'bg-red-100 text-red-700') }} rounded text-xs font-bold">
                                {{ strtoupper($a->accion) }}
                            </span>
                            <span class="text-xs text-gray-400">
                                {{ $a->timestamp->format('d/m/Y H:i:s') }}
                            </span>
                        </div>
                        <p class="text-sm text-gray-800 font-medium">
                            <i class="fas fa-user mr-1 text-gray-400"></i>
                            {{ $a->usuario ? $a->usuario->nombres.' '.$a->usuario->apellidos : 'Sistema' }}
                        </p>
                        @if($a->descripcion)
                        <p class="text-sm text-gray-600 mt-1 bg-gray-50 px-3 py-2 rounded-lg">
                            <i class="fas fa-info-circle mr-1 text-gray-400"></i>
                            {{ $a->descripcion }}
                        </p>
                        @endif
                        @if($a->campo_modificado)
                        <div class="flex items-center gap-2 mt-2 text-xs">
                            @if($a->accion == 'modificar')
                                <span class="px-2 py-1 bg-red-50 text-red-600 rounded border border-red-100">{{ $a->valor_anterior ?? '-' }}</span>
                                <i class="fas fa-arrow-right text-gray-400"></i>
                                <span class="px-2 py-1 bg-green-50 text-green-600 rounded border border-green-100">{{ $a->valor_nuevo ?? '-' }}</span>
                            @else
                                <span class="px-2 py-1 bg-gray-100 text-gray-600 rounded">{{ $a->campo_modificado }}</span>
                            @endif
                        </div>
                        @endif
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        <div class="p-4 border-t border-gray-100">
            {{ $auditorias->appends(request()->except('page'))->links() }}
        </div>
    </div>
    @else
    <div class="modern-card p-12 text-center">
        <div class="w-20 h-20 mx-auto mb-4 bg-gray-100 rounded-full flex items-center justify-center">
            <i class="fas fa-clipboard-list text-3xl text-gray-300"></i>
        </div>
        <p class="text-gray-500 text-lg">No hay registros de auditoría</p>
        <p class="text-gray-400 text-sm mt-2">Las acciones que realices aparecerán aquí</p>
    </div>
    @endif
</div>
@endsection