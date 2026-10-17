@extends('layouts.gerente')

@section('title', 'Ciclos')
@section('header', 'Ciclos Anuales')

@section('content')
<div class="fade-in">
    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-5 mb-8">
        <div>
            <h2 class="text-3xl font-bold text-gray-800">
                <span class="gradient-text">Ciclos Anuales</span>
            </h2>
            <p class="text-gray-400 mt-1">Gestión de ciclos de operación</p>
        </div>
        <a href="{{ route('gerente.ciclos.create') }}" class="btn-modern inline-flex items-center gap-2">
            <i class="fas fa-plus"></i>
            Nuevo Ciclo
        </a>
    </div>

    @if(session('success'))
        <div class="mb-6 p-4 bg-gradient-to-r from-emerald-50 to-green-50 border border-emerald-200 rounded-2xl text-emerald-700 flex items-center gap-3 fade-in">
            <i class="fas fa-check-circle text-xl"></i>
            {{ session('success') }}
        </div>
    @endif

    @if($cicloActivo)
    <div class="modern-card p-5 mb-6 fade-in delay-100 border-l-4 border-l-emerald-500">
        <div class="flex items-center gap-4">
            <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-emerald-400 to-emerald-600 flex items-center justify-center shadow-lg">
                <i class="fas fa-calendar-check text-white text-2xl"></i>
            </div>
            <div>
                <p class="font-bold text-gray-800">Ciclo Activo: {{ $cicloActivo->anio }}</p>
                <p class="text-sm text-gray-500">{{ $cicloActivo->fecha_inicio?->format('d/m/Y') }} - {{ $cicloActivo->fecha_fin?->format('d/m/Y') }}</p>
            </div>
            <span class="badge-modern badge-success ml-auto">Activo</span>
        </div>
    </div>
    @endif

    <div class="modern-card overflow-hidden fade-in delay-200">
        <div class="overflow-x-auto">
            <table class="table-modern">
                <thead>
                    <tr>
                        <th class="px-5 py-4"><i class="fas fa-calendar mr-1"></i> Año</th>
                        <th class="px-5 py-4"><i class="fas fa-play mr-1"></i> Inicio</th>
                        <th class="px-5 py-4"><i class="fas fa-stop mr-1"></i> Fin</th>
                        <th class="px-5 py-4"><i class="fas fa-toggle-on mr-1"></i> Estado</th>
                        <th class="px-5 py-4 text-center"><i class="fas fa-cogs mr-1"></i> Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($ciclos as $ciclo)
                    <tr class="hover:bg-emerald-50/30 transition-all">
                        <td class="px-5 py-4">
                            <span class="font-bold text-gray-800 text-lg">{{ $ciclo->anio }}</span>
                        </td>
                        <td class="px-5 py-4 text-gray-600">{{ $ciclo->fecha_inicio?->format('d/m/Y') }}</td>
                        <td class="px-5 py-4 text-gray-600">{{ $ciclo->fecha_fin?->format('d/m/Y') }}</td>
                        <td class="px-5 py-4">
                            @if($ciclo->activo)
                                <span class="badge-modern badge-success"><i class="fas fa-check-circle mr-1"></i> Activo</span>
                            @else
                                <span class="badge-modern bg-gray-100 text-gray-600"><i class="fas fa-times-circle mr-1"></i> Inactivo</span>
                            @endif
                        </td>
                        <td class="px-5 py-4">
                            <div class="flex items-center justify-center gap-1">
                                <a href="{{ route('gerente.ciclos.show', $ciclo->ciclo_id) }}" 
                                   class="p-2.5 text-blue-600 hover:bg-blue-50 rounded-xl transition-all" title="Ver">
                                    <i class="fas fa-eye"></i>
                                </a>
                                @if(!$ciclo->activo)
                                <form action="{{ route('gerente.ciclos.activar', $ciclo->ciclo_id) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="p-2.5 text-emerald-600 hover:bg-emerald-50 rounded-xl transition-all" title="Activar">
                                        <i class="fas fa-play"></i>
                                    </button>
                                </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-5 py-16 text-center">
                            <div class="w-24 h-24 mx-auto mb-4 bg-emerald-50 rounded-full flex items-center justify-center">
                                <i class="fas fa-calendar text-4xl text-emerald-300"></i>
                            </div>
                            <p class="text-gray-500 text-lg">No hay ciclos</p>
                            <a href="{{ route('gerente.ciclos.create') }}" class="btn-modern inline-flex items-center gap-2 mt-4">
                                <i class="fas fa-plus"></i> Crear ciclo
                            </a>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection