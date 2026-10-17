@extends('layouts.gerente')

@section('title', 'Nuevo Ciclo')
@section('header', 'Nuevo Ciclo')

@section('content')
<div class="mb-6 flex items-center gap-3">
    <a href="{{ route('gerente.ciclos.index') }}" class="p-2 hover:bg-gray-100 rounded-lg transition-colors">
        <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
        </svg>
    </a>
    <div>
        <h2 class="text-2xl font-bold text-gray-800">Nuevo Ciclo Anual</h2>
        <p class="text-gray-500 text-sm mt-1">Crea un nuevo ciclo de operación</p>
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

<form action="{{ route('gerente.ciclos.store') }}" method="POST" class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
    @csrf
    
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div>
            <label for="anio" class="block text-sm font-semibold text-gray-700 mb-2">
                Año <span class="text-red-500">*</span>
            </label>
            <input type="number" name="anio" id="anio" value="{{ old('anio', date('Y') + 1) }}" required min="2020" max="2100"
                   class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 @error('anio') border-red-500 @enderror">
            @error('anio')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="fecha_inicio" class="block text-sm font-semibold text-gray-700 mb-2">
                Fecha de Inicio <span class="text-red-500">*</span>
            </label>
            <input type="date" name="fecha_inicio" id="fecha_inicio" value="{{ old('fecha_inicio') }}" required
                   class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 @error('fecha_inicio') border-red-500 @enderror">
            @error('fecha_inicio')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="fecha_fin" class="block text-sm font-semibold text-gray-700 mb-2">
                Fecha de Fin <span class="text-red-500">*</span>
            </label>
            <input type="date" name="fecha_fin" id="fecha_fin" value="{{ old('fecha_fin') }}" required
                   class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 @error('fecha_fin') border-red-500 @enderror">
            @error('fecha_fin')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>
    </div>

    <div class="mt-6 p-4 bg-blue-50 border border-blue-200 rounded-xl">
        <p class="text-sm text-blue-700">
            <strong>Nota:</strong> Después de crear el ciclo, podrás asignar los grupos de descanso y sus días de descanso.
        </p>
    </div>

    <div class="mt-8 flex items-center justify-end gap-4">
        <a href="{{ route('gerente.ciclos.index') }}" class="px-6 py-3 text-gray-700 hover:bg-gray-100 rounded-xl transition-colors">
            Cancelar
        </a>
        <button type="submit" class="px-6 py-3 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl transition-colors shadow-lg flex items-center gap-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
            </svg>
            Crear Ciclo
        </button>
    </div>
</form>
@endsection
