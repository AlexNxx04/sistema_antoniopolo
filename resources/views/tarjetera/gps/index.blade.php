@extends('layouts.tarjetera')

@section('title', 'GPS - Tiempo Real')
@section('header', 'GPS - Tiempo Real')

@push('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
@endpush

@push('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
@endpush

@section('content')

<div class="p-6">
    <div class="flex items-center justify-between mb-6">
        <div class="flex items-center gap-3">
            <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-amber-400 to-amber-600 flex items-center justify-center shadow-lg">
                <i class="fas fa-map-marker-alt text-white text-xl"></i>
            </div>
            <div>
                <h2 class="text-2xl font-bold text-gray-800">GPS - Tiempo Real</h2>
                <p class="text-gray-500 text-sm">{{ $puntoNombre }} - {{ now()->format('d/m/Y') }}</p>
            </div>
        </div>
        <div class="flex items-center gap-2">
            <span class="px-3 py-1 bg-green-100 text-green-700 rounded-full text-sm font-medium">
                <i class="fas fa-circle text-xs animate-pulse mr-1"></i>
                En línea
            </span>
            <button onclick="actualizarMapa()" class="px-3 py-1 bg-amber-500 text-white rounded-lg text-sm hover:bg-amber-600">
                <i class="fas fa-sync mr-1"></i> Actualizar
            </button>
        </div>
    </div>

    @if(session('success'))
    <div class="mb-6 p-4 bg-green-100 border border-green-300 text-green-700 rounded-xl">
        <i class="fas fa-check-circle mr-2"></i> {{ session('success') }}
    </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2">
            <div class="modern-card p-4 border-l-4 border-amber-500 mb-6">
                <h3 class="font-bold text-gray-800 mb-3 flex items-center gap-2">
                    <i class="fas fa-map text-amber-600"></i>
                    Mapa de Unidades en Tiempo Real
                </h3>
                <div id="mapa-unidades" class="w-full h-[500px] rounded-xl border border-gray-200"></div>
                <div class="flex items-center justify-center gap-6 mt-3 text-sm">
                    <div class="flex items-center gap-2">
                        <span class="w-3 h-3 bg-green-500 rounded-full"></span>
                        <span class="text-gray-600">En ruta</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="w-3 h-3 bg-amber-500 rounded-full"></span>
                        <span class="text-gray-600">En parada</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="w-3 h-3 bg-red-500 rounded-full"></span>
                        <span class="text-gray-600">Detenido</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="w-3 h-3 bg-gray-400 rounded-full"></span>
                        <span class="text-gray-600">Fuera de servicio</span>
                    </div>
                </div>
            </div>

            <div class="modern-card p-4 border-l-4 border-emerald-500">
                <h3 class="font-bold text-gray-800 mb-4 flex items-center gap-2">
                    <i class="fas fa-route text-emerald-600"></i>
                    Unidades en Recorrido ({{ $turnosConfirmados->count() }})
                </h3>
                <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-5 gap-3">
                    @foreach($turnosConfirmados as $turno)
                    <div class="p-3 bg-emerald-50 rounded-lg border border-emerald-200 text-center hover:shadow-md transition-shadow">
                        <span class="font-bold text-emerald-700 text-lg">#{{ $turno->posicion }}</span>
                        <p class="text-sm text-gray-600 truncate">{{ $turno->usuario->nombres ?? 'N/A' }}</p>
                        <p class="text-xs text-gray-500">{{ $turno->unidad->placa ?? 'N/A' }}</p>
                        <div class="mt-1">
                            <span class="inline-block w-2 h-2 bg-green-500 rounded-full animate-pulse"></span>
                        </div>
                    </div>
                    @endforeach
                    @if($turnosConfirmados->isEmpty())
                    <div class="col-span-full text-center py-8 text-gray-500">
                        <i class="fas fa-bus text-4xl text-gray-300 mb-2"></i>
                        <p>No hay unidades en recorrido</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="space-y-4">
            <div class="modern-card p-4 border-l-4 border-amber-500">
                <h3 class="font-bold text-gray-800 mb-3 flex items-center gap-2">
                    <i class="fas fa-car text-amber-600"></i>
                    Registrar Salida
                </h3>
                <form action="{{ route('tarjetera.gps.salida') }}" method="POST" class="space-y-3">
                    @csrf
                    <div>
                        <label class="block text-sm font-medium text-gray-600 mb-1">Unidad</label>
                        <select name="turno_id" class="w-full px-3 py-2 border border-gray-300 rounded-lg" required>
                            <option value="">-- Seleccionar --</option>
                            @foreach($turnosConfirmados as $turno)
                            <option value="{{ $turno->turno_id }}">
                                #{{ $turno->posicion }} - {{ $turno->usuario->nombres ?? 'N/A' }}
                            </option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-600 mb-1">Tipo de Salida</label>
                        <select name="tipo" class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                            <option value="normal">Normal</option>
                            <option value="anticipada">Anticipada</option>
                            <option value="llena">Unidad Llena</option>
                            <option value="carrera">Carrera Especial</option>
                        </select>
                    </div>
                    <button type="submit" class="w-full px-4 py-2 bg-green-500 text-white rounded-lg hover:bg-green-600">
                        <i class="fas fa-play mr-2"></i> Registrar Salida
                    </button>
                </form>
            </div>

            @if($carrerasActivas->count() > 0)
            <div class="modern-card p-4 border-l-4 border-orange-500">
                <h3 class="font-bold text-gray-800 mb-3 flex items-center gap-2">
                    <i class="fas fa-running text-orange-600"></i>
                    Carreras Activas
                </h3>
                <div class="space-y-2">
                    @foreach($carrerasActivas as $carrera)
                    <div class="p-3 bg-orange-50 rounded-lg border border-orange-200">
                        <div class="flex justify-between items-center">
                            <div>
                                <span class="font-bold text-orange-700">#{{ $carrera->posicion }}</span>
                                <p class="text-sm text-gray-600">{{ $carrera->nombres ?? 'N/A' }}</p>
                            </div>
                            <form action="{{ route('tarjetera.gps.finalizar', $carrera->carrera_id) }}" method="POST">
                                @csrf
                                <button type="submit" class="px-3 py-1 bg-red-500 text-white rounded text-xs hover:bg-red-600">
                                    <i class="fas fa-stop mr-1"></i> Llegada
                                </button>
                            </form>
                        </div>
                        <p class="text-xs text-gray-500 mt-1">
                            <i class="fas fa-clock mr-1"></i> {{ \Carbon\Carbon::parse($carrera->hora_salida_especial)->format('H:i') }}
                        </p>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

            <div class="modern-card p-4 border-l-4 border-blue-500">
                <h3 class="font-bold text-gray-800 mb-3 flex items-center gap-2">
                    <i class="fas fa-mobile-alt text-blue-600"></i>
                    GPS Unidades ({{ $unidades->count() }})
                </h3>
                <div class="space-y-2 max-h-[300px] overflow-y-auto">
                    @foreach($unidades as $unidad)
                    <div class="p-2 bg-blue-50 rounded-lg border border-blue-100">
                        <div class="flex justify-between items-center">
                            <div>
                                <span class="font-bold text-blue-700">#{{ str_pad($unidad->numero_interno, 3, '0', STR_PAD_LEFT) }}</span>
                                <span class="text-xs text-gray-500 ml-2">{{ $unidad->placa }}</span>
                            </div>
                            <span class="text-xs px-2 py-0.5 bg-green-100 text-green-700 rounded">
                                {{ $turnosConfirmados->where('unidad_id', $unidad->unidad_id)->first() ? 'Activo' : 'Inactivo' }}
                            </span>
                        </div>
                        <p class="text-xs text-gray-500 mt-1">
                            <i class="fas fa-phone mr-1"></i> {{ $unidad->celular_gps ?? 'Sin GPS' }}
                        </p>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>

<script>
let mapa = null;
let marcadores = [];
let intervalActualizacion = null;

function inicializarMapa() {
    const coordenadas = [-1.59106, -78.99903];

    mapa = L.map('mapa-unidades').setView(coordenadas, 13);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '© OpenStreetMap contributors'
    }).addTo(mapa);

    agregarMarcadores();
}

function agregarMarcadores() {
    marcadores.forEach(m => mapa.removeLayer(m));
    marcadores = [];

    const turnos = @json($turnosConfirmados);
    const coordGuaranda = [-1.8264, -79.4885];
    const coordSalinas = [-2.2058, -80.8512];
    const baseCoords = [-1.59106, -78.99903];

    turnos.forEach((turno, index) => {
        const offset = 0.003;
        const angle = (index / turnos.length) * 2 * Math.PI;
        const lat = baseCoords[0] + offset * Math.cos(angle) + (Math.random() - 0.5) * 0.002;
        const lng = baseCoords[1] + offset * Math.sin(angle) + (Math.random() - 0.5) * 0.002;

        const colores = {
            'en_ruta': '#22c55e',
            'en_parada': '#f59e0b',
            'detenido': '#ef4444'
        };
        const estado = Math.random() > 0.7 ? 'detenido' : (Math.random() > 0.5 ? 'en_ruta' : 'en_parada');

        const icono = L.divIcon({
            className: 'custom-marker',
            html: `<div style="background:${colores[estado]};width:30px;height:30px;border-radius:50%;border:3px solid white;display:flex;align-items:center;justify-content:center;color:white;font-weight:bold;font-size:12px;box-shadow:0 2px 5px rgba(0,0,0,0.3);">${turno.posicion}</div>`,
            iconSize: [30, 30],
            iconAnchor: [15, 15]
        });

        const marcador = L.marker([lat, lng], { icon: icono })
            .addTo(mapa)
            .bindPopup(`
                <div style="min-width:150px;">
                    <strong>#${turno.posicion}</strong><br/>
                    ${turno.usuario?.nombres || 'N/A'}<br/>
                    <span style="color:#666;">${turno.unidad?.placa || 'N/A'}</span><br/>
                    <span style="font-size:11px;color:${colores[estado]};">● ${estado.replace('_', ' ')}</span>
                </div>
            `);
        marcadores.push(marcador);
    });

    if (turnos.length === 0) {
        const marcador = L.marker(baseCoords)
            .addTo(mapa)
            .bindPopup('Centro de operaciones');
        marcadores.push(marcador);
    }
}

function actualizarMapa() {
    if (mapa) {
        agregarMarcadores();
    }
}

document.addEventListener('DOMContentLoaded', function() {
    inicializarMapa();
    intervalActualizacion = setInterval(actualizarMapa, 30000);
});

window.addEventListener('beforeunload', function() {
    if (intervalActualizacion) {
        clearInterval(intervalActualizacion);
    }
});
</script>

<style>
.custom-marker {
    background: transparent !important;
    border: none !important;
}
.leaflet-popup-content-wrapper {
    border-radius: 8px;
}
</style>
@endsection