@extends('layouts.gerente')

@section('title', 'Documentos')
@section('header', 'Documentos')

@section('content')
<div class="fade-in">
    {{-- Encabezado --}}
    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-5 mb-8">
        <div>
            <h2 class="text-3xl font-bold text-gray-800">
                <span class="gradient-text">Documentos</span>
            </h2>
            <p class="text-gray-400 mt-1">Licencias y matrículas de conductores</p>
        </div>
        <div class="flex items-center gap-3">
            @if($alertas['vencidos'] > 0)
            <a href="{{ route('gerente.documentos.vencidos') }}" class="flex items-center gap-2 px-4 py-2 bg-gradient-to-r from-red-50 to-red-100 border border-red-200 rounded-xl text-sm hover:shadow-md transition-all">
                <i class="fas fa-exclamation-triangle text-red-500"></i>
                <span class="text-red-700 font-semibold">{{ $alertas['vencidos'] }} vencido(s)</span>
            </a>
            @endif
            @if($alertas['porVencer'] > 0)
            <div class="flex items-center gap-2 px-4 py-2 bg-gradient-to-r from-amber-50 to-amber-100 border border-amber-200 rounded-xl text-sm">
                <i class="fas fa-clock text-amber-500"></i>
                <span class="text-amber-700 font-semibold">{{ $alertas['porVencer'] }} por vencer</span>
            </div>
            @endif
        </div>
    </div>

    {{-- Filtros --}}
    <div class="modern-card p-5 mb-6 fade-in delay-100">
        <form method="GET" action="{{ route('gerente.documentos.index') }}" class="flex flex-wrap gap-4 items-end">
            <div class="flex-1 min-w-[200px]">
                <label class="block text-sm font-semibold text-gray-600 mb-2">
                    <i class="fas fa-search text-emerald-500 mr-1"></i> Buscar
                </label>
                <div class="relative">
                    <input type="text" name="buscar" value="{{ request('buscar') }}" placeholder="Buscar por conductor o número..." 
                           class="input-modern pl-11">
                    <i class="fas fa-search absolute left-4 top-1/2 -translate-y-1/2 text-gray-400"></i>
                </div>
            </div>
            <div class="w-40">
                <label class="block text-sm font-semibold text-gray-600 mb-2">Tipo</label>
                <select name="tipo" class="input-modern">
                    <option value="">Todos</option>
                    <option value="licencia" {{ request('tipo') === 'licencia' ? 'selected' : '' }}>Licencia</option>
                    <option value="matricula" {{ request('tipo') === 'matricula' ? 'selected' : '' }}>Matrícula</option>
                </select>
            </div>
            <div class="w-40">
                <label class="block text-sm font-semibold text-gray-600 mb-2">Estado</label>
                <select name="estado" class="input-modern">
                    <option value="">Todos</option>
                    <option value="vigente" {{ request('estado') === 'vigente' ? 'selected' : '' }}>Vigente</option>
                    <option value="vencido" {{ request('estado') === 'vencido' ? 'selected' : '' }}>Vencido</option>
                </select>
            </div>
            <div class="flex gap-2">
                <button type="submit" class="btn-modern">
                    <i class="fas fa-filter mr-1"></i> Filtrar
                </button>
                <a href="{{ route('gerente.documentos.index') }}" class="px-5 py-3 bg-gray-100 text-gray-600 rounded-xl font-semibold hover:bg-gray-200 transition-all">
                    <i class="fas fa-times"></i>
                </a>
            </div>
        </form>
    </div>

    {{-- Tabla --}}
    <div class="modern-card overflow-hidden fade-in delay-200">
        <div class="overflow-x-auto">
            <table class="table-modern">
                <thead>
                    <tr>
                        <th class="px-5 py-4"><i class="fas fa-user mr-1"></i> Conductor</th>
                        <th class="px-5 py-4"><i class="fas fa-file-alt mr-1"></i> Tipo</th>
                        <th class="px-5 py-4"><i class="fas fa-hashtag mr-1"></i> Número</th>
                        <th class="px-5 py-4"><i class="fas fa-calendar mr-1"></i> Vencimiento</th>
                        <th class="px-5 py-4"><i class="fas fa-toggle-on mr-1"></i> Estado</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($documentos as $documento)
                    <tr class="hover:bg-emerald-50/30 transition-all">
                        <td class="px-5 py-4">
                            <div class="flex items-center gap-3">
                                <div class="avatar bg-gradient-to-br from-emerald-400 to-emerald-600">
                                    {{ substr($documento->usuario->nombres ?? 'U', 0, 1) }}
                                </div>
                                <div>
                                    <p class="font-semibold text-gray-800">{{ $documento->usuario->nombres ?? 'N/A' }} {{ $documento->usuario->apellidos ?? '' }}</p>
                                    <p class="text-xs text-gray-400">{{ $documento->usuario->cedula ?? '' }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-5 py-4">
                            <span class="badge-modern badge-info">{{ ucfirst($documento->tipo) }}</span>
                        </td>
                        <td class="px-5 py-4 font-mono text-gray-600">{{ $documento->numero ?? 'N/A' }}</td>
                        <td class="px-5 py-4 text-gray-600">
                            {{ $documento->fecha_vencimiento?->format('d/m/Y') }}
                        </td>
                        <td class="px-5 py-4">
                            @if($documento->estado === 'vigente')
                                <span class="badge-modern badge-success"><i class="fas fa-check-circle mr-1"></i> Vigente</span>
                            @else
                                <span class="badge-modern badge-danger"><i class="fas fa-times-circle mr-1"></i> Vencido</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-5 py-16 text-center">
                            <div class="w-24 h-24 mx-auto mb-4 bg-emerald-50 rounded-full flex items-center justify-center">
                                <i class="fas fa-file-alt text-4xl text-emerald-300"></i>
                            </div>
                            <p class="text-gray-500 text-lg">No hay documentos</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($documentos->hasPages())
            <div class="px-5 py-4 border-t border-gray-100 bg-gray-50/50">
                {{ $documentos->withQueryString()->links() }}
            </div>
        @endif
    </div>
</div>
@endsection