@extends('layouts.gerente')

@section('title', 'Verificación del Sistema')
@section('header', 'Verificación del Sistema')

@section('content')
<div class="mb-6">
    <p class="text-gray-600">Verificación de todos los módulos y su conexión con la base de datos.</p>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
    @foreach($resultados as $modulo => $datos)
    <div class="bg-white rounded-2xl border border-gray-100 overflow-hidden">
        <div class="p-5 border-b border-gray-100">
            <div class="flex items-center justify-between">
                <h3 class="font-bold text-gray-900 capitalize">{{ str_replace('_', ' ', $modulo) }}</h3>
                @if($datos['estado'] === 'ok')
                    <span class="px-3 py-1 bg-emerald-100 text-emerald-700 rounded-full text-xs font-semibold">✓ OK</span>
                @elseif($datos['estado'] === 'advertencia')
                    <span class="px-3 py-1 bg-amber-100 text-amber-700 rounded-full text-xs font-semibold">⚠ Advertencia</span>
                @else
                    <span class="px-3 py-1 bg-red-100 text-red-700 rounded-full text-xs font-semibold">✗ Error</span>
                @endif
            </div>
        </div>
        <div class="p-5">
            <p class="text-sm text-gray-600 mb-3">{{ $datos['mensaje'] }}</p>
            @if(!empty($datos['datos']) && is_array($datos['datos']) && count($datos['datos']) > 0)
                <details class="mt-2">
                    <summary class="text-xs text-emerald-600 cursor-pointer hover:text-emerald-700">Ver datos ({{ count($datos['datos']) }} registros)</summary>
                    <pre class="mt-2 p-2 bg-gray-50 rounded text-xs overflow-auto max-h-40">{{ json_encode($datos['datos'], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                </details>
            @elseif($datos['estado'] === 'ok' && empty($datos['datos']))
                <p class="text-xs text-gray-400 italic">Sin datos registrados</p>
            @endif
        </div>
    </div>
    @endforeach
</div>

<div class="mt-8 flex gap-4">
    <a href="{{ route('gerente.dashboard') }}" class="px-6 py-3 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-xl font-medium transition-colors">
        ← Volver al Dashboard
    </a>
    <a href="{{ route('gerente.verificar') }}" class="px-6 py-3 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl font-medium transition-colors">
        🔄 Actualizar Verificación
    </a>
</div>
@endsection
