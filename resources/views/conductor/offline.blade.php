@extends('layouts.conductor')

@section('title', 'Sin conexión')

@section('content')
<div class="min-h-[70vh] flex flex-col items-center justify-center text-center">
    <div class="w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mb-6">
        <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636a9 9 0 010 12.728m0 0l-2.829-2.829m2.829 2.829L21 21M15.536 8.464a5 5 0 010 7.072m0 0l-2.829-2.829m-4.243 2.829a4.978 4.978 0 01-1.414-2.83m-1.414 5.658a9 9 0 01-2.167-9.238m7.824 2.167a1 1 0 111.414 1.414m-1.414-1.414L3 3m8.293 8.293l1.414 1.414"/>
        </svg>
    </div>
    <h2 class="text-xl font-bold text-gray-800 mb-2">Sin conexión a internet</h2>
    <p class="text-gray-500 mb-6 max-w-xs">No hay conexión a internet. Por favor verifica tu conexión y vuelve a intentar.</p>
    <button onclick="location.reload()" class="px-6 py-3 btn-primary text-white font-semibold rounded-xl shadow-lg">
        Reintentar
    </button>
</div>
@endsection
