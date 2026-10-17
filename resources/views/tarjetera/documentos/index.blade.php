@extends('layouts.tarjetera')

@section('title', 'Validar Documentos')
@section('header', 'Validar Documentos')

@section('content')

<div class="p-6">
    <div class="flex items-center gap-3 mb-6">
        <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-amber-400 to-amber-600 flex items-center justify-center shadow-lg">
            <i class="fas fa-id-card text-white text-xl"></i>
        </div>
        <div>
            <h2 class="text-2xl font-bold text-gray-800">Validar Documentos</h2>
            <p class="text-gray-500 text-sm">Conductoras y Unidades</p>
        </div>
    </div>

    @if(session('success'))
    <div class="mb-6 p-4 bg-green-100 border border-green-300 text-green-700 rounded-xl">
        <i class="fas fa-check-circle mr-2"></i> {{ session('success') }}
    </div>
    @endif

    @if($vencidos->count() > 0)
    <div class="mb-6 p-4 bg-red-100 border border-red-300 text-red-700 rounded-xl">
        <div class="flex items-center gap-2 mb-2">
            <i class="fas fa-exclamation-triangle"></i>
            <strong>{{ $vencidos->count() }} documento(s) vencido(s)</strong>
        </div>
        <div class="space-y-1 text-sm">
            @foreach($vencidos as $doc)
            <p class="text-red-600">
                <span class="font-medium">{{ $doc->usuario->nombres ?? 'N/A' }}</span> - 
                {{ $doc->tipo }} venció el {{ \Carbon\Carbon::parse($doc->fecha_vencimiento)->format('d/m/Y') }}
            </p>
            @endforeach
        </div>
    </div>
    @endif

    @if($proximos->count() > 0)
    <div class="mb-6 p-4 bg-amber-100 border border-amber-300 text-amber-700 rounded-xl">
        <div class="flex items-center gap-2 mb-2">
            <i class="fas fa-clock"></i>
            <strong>{{ $proximos->count() }} documento(s) por vencer (30 días)</strong>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3 mt-3">
            @foreach($proximos->take(6) as $doc)
            <div class="p-3 bg-white rounded-lg border border-amber-200">
                <p class="font-medium text-gray-800">{{ $doc->usuario->nombres ?? 'N/A' }}</p>
                <p class="text-sm text-amber-700">{{ $doc->tipo }}</p>
                <p class="text-xs text-gray-500">
                    Vence: {{ \Carbon\Carbon::parse($doc->fecha_vencimiento)->format('d/m/Y') }}
                </p>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    <div class="modern-card overflow-hidden">
        <table class="w-full">
            <thead class="bg-amber-50">
                <tr>
                    <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Conductor</th>
                    <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Unidad</th>
                    <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Tipo</th>
                    <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Número</th>
                    <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Vencimiento</th>
                    <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Estado</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($documentos as $documento)
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-3">
                        <p class="font-medium text-gray-800">{{ $documento->usuario->nombres ?? 'N/A' }}</p>
                        <p class="text-xs text-gray-500">{{ $documento->usuario->cedula ?? '' }}</p>
                    </td>
                    <td class="px-4 py-3">
                        <p class="text-gray-600">{{ $documento->usuario->unidad->placa ?? 'N/A' }}</p>
                    </td>
                    <td class="px-4 py-3 text-gray-600">
                        {{ $documento->tipo }}
                    </td>
                    <td class="px-4 py-3 text-gray-600 font-mono text-sm">
                        {{ $documento->numero }}
                    </td>
                    <td class="px-4 py-3">
                        @php
                            $diasRestantes = \Carbon\Carbon::now()->diffInDays(\Carbon\Carbon::parse($documento->fecha_vencimiento), false);
                        @endphp
                        <span class="@if($diasRestantes < 0) text-red-600 font-bold @elseif($diasRestantes <= 30) text-amber-600 @else text-gray-600 @endif">
                            {{ \Carbon\Carbon::parse($documento->fecha_vencimiento)->format('d/m/Y') }}
                            @if($diasRestantes >= 0 && $diasRestantes <= 30)
                                <span class="text-xs">({{ $diasRestantes }} días)</span>
                            @elseif($diasRestantes < 0)
                                <span class="text-xs">(Vencido)</span>
                            @endif
                        </span>
                    </td>
                    <td class="px-4 py-3">
                        @switch($documento->estado)
                            @case('vigente')
                                <span class="px-2 py-1 bg-green-100 text-green-700 rounded-full text-xs font-medium">
                                    <i class="fas fa-check mr-1"></i> Vigente
                                </span>
                                @break
                            @case('vencido')
                                <span class="px-2 py-1 bg-red-100 text-red-700 rounded-full text-xs font-medium">
                                    <i class="fas fa-times mr-1"></i> Vencido
                                </span>
                                @break
                            @case('en_proceso')
                                <span class="px-2 py-1 bg-blue-100 text-blue-700 rounded-full text-xs font-medium">
                                    <i class="fas fa-clock mr-1"></i> En Proceso
                                </span>
                                @break
                            @default
                                <span class="px-2 py-1 bg-gray-100 text-gray-600 rounded-full text-xs font-medium">
                                    {{ $documento->estado }}
                                </span>
                        @endswitch
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-4 py-8 text-center text-gray-500">
                        <i class="fas fa-folder-open text-4xl text-gray-300 mb-2"></i>
                        <p>No hay documentos registrados</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($documentos->hasPages())
    <div class="mt-4">
        {{ $documentos->links() }}
    </div>
    @endif
</div>
@endsection