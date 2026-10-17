<?php $routePrefix = $routePrefix ?? ''; ?>
{{-- Plantilla uniforme para todas las vistas de índice --}}
<div class="fade-in">
    {{-- Encabezado --}}
    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-5 mb-8">
        <div>
            <h2 class="text-3xl font-bold text-gray-800">
                <span class="gradient-text">{{ $titulo }}</span>
            </h2>
            <p class="text-gray-400 mt-1">{{ $descripcion }}</p>
        </div>
        @if(isset($rutaCrear))
        <a href="{{ $rutaCrear }}" class="btn-modern inline-flex items-center gap-2">
            <i class="fas fa-plus"></i>
            {{ $textoCrear ?? 'Nuevo' }}
        </a>
        @endif
    </div>

    {{-- Alertas --}}
    @if(session('success'))
        <div class="mb-6 p-4 bg-gradient-to-r from-emerald-50 to-green-50 border border-emerald-200 rounded-2xl text-emerald-700 flex items-center gap-3 fade-in">
            <i class="fas fa-check-circle text-xl"></i>
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="mb-6 p-4 bg-gradient-to-r from-red-50 to-red-50 border border-red-200 rounded-2xl text-red-700 flex items-center gap-3 fade-in">
            <i class="fas fa-exclamation-circle text-xl"></i>
            {{ session('error') }}
        </div>
    @endif

    {{-- Filtros --}}
    @if(isset($filtros))
    <div class="modern-card p-5 mb-6 fade-in delay-100">
        {{ $filtros }}
    </div>
    @endif

    {{-- Tabla o Contenido --}}
    <div class="modern-card overflow-hidden fade-in delay-200">
        {{ $slot }}
    </div>
</div>