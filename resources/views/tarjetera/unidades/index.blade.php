@extends('layouts.tarjetera')

@section('title', 'Unidades')
@section('header', 'Unidades')

@section('content')

<div class="p-6">
    <div class="flex items-center gap-3 mb-6">
        <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-amber-400 to-amber-600 flex items-center justify-center shadow-lg">
            <i class="fas fa-car text-white text-xl"></i>
        </div>
        <div>
            <h2 class="text-2xl font-bold text-gray-800">Unidades</h2>
            <p class="text-gray-500 text-sm">{{ now()->format('d/m/Y') }}</p>
        </div>
    </div>

    <div class="modern-card overflow-hidden">
        <table class="w-full">
            <thead class="bg-amber-50">
                <tr>
                    <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">#</th>
                    <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Placa</th>
                    <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Celular GPS</th>
                    <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Estado</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($unidades as $unidad)
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-3">
                        <span class="font-bold text-gray-600">{{ str_pad($unidad->numero_interno, 3, '0', STR_PAD_LEFT) }}</span>
                    </td>
                    <td class="px-4 py-3 font-mono text-gray-800">
                        {{ $unidad->placa }}
                    </td>
                    <td class="px-4 py-3 text-gray-600">
                        {{ $unidad->celular_gps ?? 'Sin registrar' }}
                    </td>
                    <td class="px-4 py-3">
                        @if($unidad->estado === 'activo')
                        <span class="px-2 py-1 bg-green-100 text-green-700 rounded-full text-xs font-medium">
                            <i class="fas fa-check mr-1"></i> Activo
                        </span>
                        @else
                        <span class="px-2 py-1 bg-red-100 text-red-700 rounded-full text-xs font-medium">
                            <i class="fas fa-times mr-1"></i> Inactivo
                        </span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="px-4 py-8 text-center text-gray-500">
                        <i class="fas fa-car text-4xl text-gray-300 mb-2"></i>
                        <p>No hay unidades registradas</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4 p-4 bg-amber-50 rounded-xl">
        <p class="text-sm text-amber-700">
            <i class="fas fa-info-circle mr-2"></i>
            Total: <strong>{{ $unidades->count() }}</strong> unidades activas.
        </p>
    </div>
</div>
@endsection