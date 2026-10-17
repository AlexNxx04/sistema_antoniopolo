@extends('layouts.gerente')

@section('title', 'Editar Documento')
@section('header', 'Editar Documento')

@section('content')
<div class="mb-6 flex items-center gap-3">
    <a href="{{ route('gerente.documentos.index') }}" class="p-2 hover:bg-gray-100 rounded-lg transition-colors">
        <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
        </svg>
    </a>
    <div>
        <h2 class="text-2xl font-bold text-gray-800">Editar Documento</h2>
        <p class="text-gray-500 text-sm mt-1">Modifica los datos del documento</p>
    </div>
</div>

@if($errors->any())
<div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-xl">
    <div class="flex items-center gap-2 mb-2">
        <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        <p class="font-semibold text-red-800">Corrija los siguientes errores:</p>
    </div>
    <ul class="list-disc list-inside text-sm text-red-700">
        @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
        @endforeach
    </ul>
</div>
@endif

<form action="{{ route('gerente.documentos.update', $documento->documento_id) }}" method="POST" class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
    @csrf
    @method('PUT')
    
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div>
            <label for="usuario_id" class="block text-sm font-semibold text-gray-700 mb-2">
                Conductor <span class="text-red-500">*</span>
            </label>
            <select name="usuario_id" id="usuario_id" required
                    class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 @error('usuario_id') border-red-500 @enderror">
                @foreach($conductores as $conductor)
                    <option value="{{ $conductor->usuario_id }}" {{ old('usuario_id', $documento->usuario_id) == $conductor->usuario_id ? 'selected' : '' }}>
                        {{ $conductor->nombres }} {{ $conductor->apellidos }} ({{ $conductor->cedula }})
                    </option>
                @endforeach
            </select>
            @error('usuario_id')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="tipo" class="block text-sm font-semibold text-gray-700 mb-2">
                Tipo de Documento <span class="text-red-500">*</span>
            </label>
            <select name="tipo" id="tipo" required
                    class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 @error('tipo') border-red-500 @enderror">
                <option value="licencia" {{ old('tipo', $documento->tipo) === 'licencia' ? 'selected' : '' }}>Licencia de Conducir</option>
                <option value="matricula" {{ old('tipo', $documento->tipo) === 'matricula' ? 'selected' : '' }}>Matrícula Vehicular</option>
            </select>
            @error('tipo')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="numero" class="block text-sm font-semibold text-gray-700 mb-2">
                Número de Documento
            </label>
            <input type="text" name="numero" id="numero" value="{{ old('numero', $documento->numero) }}"
                   class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500"
                   placeholder="Ej: 1234567890">
            @error('numero')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="fecha_emision" class="block text-sm font-semibold text-gray-700 mb-2">
                Fecha de Emisión
            </label>
            <input type="date" name="fecha_emision" id="fecha_emision" value="{{ old('fecha_emision', $documento->fecha_emision?->format('Y-m-d')) }}"
                   class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
            @error('fecha_emision')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="fecha_vencimiento" class="block text-sm font-semibold text-gray-700 mb-2">
                Fecha de Vencimiento <span class="text-red-500">*</span>
            </label>
            <input type="date" name="fecha_vencimiento" id="fecha_vencimiento" value="{{ old('fecha_vencimiento', $documento->fecha_vencimiento?->format('Y-m-d')) }}" required
                   class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 @error('fecha_vencimiento') border-red-500 @enderror">
            @error('fecha_vencimiento')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
            <p class="mt-1 text-xs text-gray-500">El estado se actualizará automáticamente según la fecha</p>
        </div>
    </div>

    <div class="mt-8 flex items-center justify-end gap-4">
        <a href="{{ route('gerente.documentos.index') }}" class="px-6 py-3 text-gray-700 hover:bg-gray-100 rounded-xl transition-colors">
            Cancelar
        </a>
        <button type="submit" class="px-6 py-3 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl transition-colors shadow-lg flex items-center gap-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
            </svg>
            Guardar Cambios
        </button>
    </div>
</form>
@endsection
