@extends('layouts.tarjetera')

@section('title', 'Conductores')
@section('header', 'Conductores')

@section('content')

<div class="p-6">
    <div class="flex items-center gap-3 mb-6">
        <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-amber-400 to-amber-600 flex items-center justify-center shadow-lg">
            <i class="fas fa-users text-white text-xl"></i>
        </div>
        <div>
            <h2 class="text-2xl font-bold text-gray-800">Conductores</h2>
            <p class="text-gray-500 text-sm">{{ now()->format('d/m/Y') }}</p>
        </div>
    </div>

    <div class="modern-card overflow-hidden">
        <table class="w-full">
            <thead class="bg-amber-50">
                <tr>
                    <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">#</th>
                    <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Nombre</th>
                    <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Cédula</th>
                    <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Teléfono</th>
                    <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Unidad</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($conductores as $conductor)
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-3">
                        <span class="font-bold text-gray-600">{{ $loop->iteration }}</span>
                    </td>
                    <td class="px-4 py-3">
                        <p class="font-medium text-gray-800">{{ $conductor->nombres ?? 'N/A' }}</p>
                        <p class="text-xs text-gray-500">{{ $conductor->apellidos ?? '' }}</p>
                    </td>
                    <td class="px-4 py-3 text-gray-600 font-mono">
                        {{ $conductor->cedula ?? 'N/A' }}
                    </td>
                    <td class="px-4 py-3 text-gray-600">
                        {{ $conductor->telefono ?? 'N/A' }}
                    </td>
                    <td class="px-4 py-3">
                        @if($conductor->unidad)
                        <span class="px-2 py-1 bg-blue-100 text-blue-700 rounded text-xs font-medium">
                            {{ $conductor->unidad->numero_interno }} ({{ $conductor->unidad->placa }})
                        </span>
                        @else
                        <span class="text-gray-400">Sin unidad</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-4 py-8 text-center text-gray-500">
                        <i class="fas fa-users text-4xl text-gray-300 mb-2"></i>
                        <p>No hay conductores activos</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4 p-4 bg-amber-50 rounded-xl">
        <p class="text-sm text-amber-700">
            <i class="fas fa-info-circle mr-2"></i>
            Total: <strong>{{ $conductores->count() }}</strong> conductor(es) activo(s) en el punto.
        </p>
    </div>
</div>
@endsection