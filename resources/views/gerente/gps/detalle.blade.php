@extends('layouts.gerente')

@section('title', 'GPS - Detalle de Ruta')
@section('header', 'GPS - Detalle de Ruta')

@push('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<style>
    #map { height: 400px; z-index: 1; }
</style>
@endpush

@section('content')
<div class="mb-6 flex items-center gap-3">
    <a href="{{ route('gerente.gps.index') }}" class="p-2 hover:bg-gray-100 rounded-lg transition-colors">
        <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
        </svg>
    </a>
    <div>
        <h2 class="text-2xl font-bold text-gray-800">Detalle de Ruta</h2>
        <p class="text-gray-500 text-sm mt-1">{{ $salida->turno->unidad->placa ?? 'N/A' }} - {{ $salida->turno->usuario->nombres ?? 'N/A' }}</p>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
    <div class="lg:col-span-2">
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100 bg-gray-50">
                <h3 class="font-semibold text-gray-800">Mapa de la Ruta</h3>
            </div>
            <div id="map"></div>
        </div>
    </div>
    
    <div class="lg:col-span-1">
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <h3 class="font-semibold text-gray-800 mb-4">Información del Viaje</h3>
            
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-500">Unidad</label>
                    <p class="text-gray-900 font-semibold">{{ $salida->turno->unidad->placa ?? 'N/A' }}</p>
                    <p class="text-xs text-gray-500">#{{ $salida->turno->unidad->numero_interno ?? '' }}</p>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-500">Conductor</label>
                    <p class="text-gray-900">{{ $salida->turno->usuario->nombres ?? 'N/A' }} {{ $salida->turno->usuario->apellidos ?? '' }}</p>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-500">Hora de Salida</label>
                    <p class="text-gray-900">{{ $salida->hora_salida->format('d/m/Y H:i') }}</p>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-500">Origen</label>
                    <span class="px-2.5 py-1 bg-emerald-100 text-emerald-700 rounded-full text-xs font-medium">
                        {{ $salida->puntoOrigen->nombre ?? 'N/A' }}
                    </span>
                </div>
                
                @if($salida->llegada)
                <div>
                    <label class="block text-sm font-medium text-gray-500">Hora de Llegada</label>
                    <p class="text-gray-900">{{ $salida->llegada->hora_llegada->format('d/m/Y H:i') }}</p>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-500">Destino</label>
                    <span class="px-2.5 py-1 bg-blue-100 text-blue-700 rounded-full text-xs font-medium">
                        {{ $salida->llegada->puntoDestino->nombre ?? 'N/A' }}
                    </span>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-500">Tiempo de Trayecto</label>
                    <p class="text-gray-900 font-semibold">{{ $salida->llegada->tiempo_trayecto_min }} minutos</p>
                </div>
                
                <div class="pt-4 border-t">
                    <span class="px-3 py-1 bg-green-100 text-green-700 rounded-full text-sm font-medium">Viaje Completado</span>
                </div>
                @else
                <div class="pt-4 border-t">
                    <span class="px-3 py-1 bg-amber-100 text-amber-700 rounded-full text-sm font-medium">En Tránsito</span>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

<div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
    <div class="px-6 py-4 border-b border-gray-100 bg-gray-50">
        <h3 class="font-semibold text-gray-800">Puntos GPS ({{ $salida->posiciones->count() }})</h3>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-sm text-left">
            <thead class="bg-gray-50 text-gray-700">
                <tr>
                    <th class="px-4 py-3 font-semibold">#</th>
                    <th class="px-4 py-3 font-semibold">Hora</th>
                    <th class="px-4 py-3 font-semibold">Latitud</th>
                    <th class="px-4 py-3 font-semibold">Longitud</th>
                    <th class="px-4 py-3 font-semibold">Velocidad</th>
                    <th class="px-4 py-3 font-semibold">Offline</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($salida->posiciones as $pos)
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-3 text-gray-500">{{ $loop->iteration }}</td>
                    <td class="px-4 py-3 text-gray-900">{{ $pos->timestamp->format('H:i:s') }}</td>
                    <td class="px-4 py-3 text-gray-600">{{ $pos->latitud }}</td>
                    <td class="px-4 py-3 text-gray-600">{{ $pos->longitud }}</td>
                    <td class="px-4 py-3 text-gray-600">{{ $pos->velocidad ?? 'N/A' }} km/h</td>
                    <td class="px-4 py-3">
                        @if($pos->es_offline)
                            <span class="px-2 py-0.5 bg-amber-100 text-amber-700 rounded text-xs">Offline</span>
                        @else
                            <span class="px-2 py-0.5 bg-green-100 text-green-700 rounded text-xs">Online</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-4 py-8 text-center text-gray-500">
                        No hay puntos GPS registrados
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@push('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    var map = L.map('map').setView([{{ $salida->posiciones->first()->latitud ?? '-1.8312' }}, {{ $salida->posiciones->first()->longitud ?? '-79.0' }}], 13);
    
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '© OpenStreetMap'
    }).addTo(map);

    @if($salida->posiciones->count() > 0)
    var points = [
        @foreach($salida->posiciones as $pos)
        [{{ $pos->latitud }}, {{ $pos->longitud }}],
        @endforeach
    ];
    
    L.polyline(points, {color: '#10b981', weight: 4}).addTo(map);
    L.marker(points[0]).addTo(map).bindPopup('Inicio');
    L.marker(points[points.length - 1]).addTo(map).bindPopup('Fin');
    @endif
});
</script>
@endpush
@endsection
