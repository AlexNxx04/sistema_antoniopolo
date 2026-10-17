@extends('layouts.tarjetera')

@section('title', 'GPS - Mapa en Vivo')
@section('header', 'Monitoreo GPS en Tiempo Real')

@push('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<style>
    #map { height: 500px; z-index: 1; border-radius: 20px; }
    .page-header { display: flex; align-items: center; justify-content: space-between; padding: 20px 24px; background: linear-gradient(145deg, #ffffff, #f8fafc); border-radius: 20px; border: 1px solid #e2e8f0; margin-bottom: 24px; }
    .page-header h2 { font-size: 22px; font-weight: 700; color: #1e293b; }
    .page-header h2 i { color: #059669; margin-right: 10px; }
    .status-badge { display: inline-flex; align-items: center; gap: 6px; padding: 6px 14px; border-radius: 20px; font-size: 13px; font-weight: 600; }
    .status-badge.live { background: #d1fae5; color: #047857; }
    .status-badge.live .dot { background: #10b981; }
    .status-badge .dot { width: 8px; height: 8px; border-radius: 50%; display: inline-block; }
    .unit-card { background: white; border: 1px solid #e2e8f0; border-radius: 14px; padding: 14px; }
    .unit-card .plate { font-size: 16px; font-weight: 800; color: #0f172a; }
    .unit-card .driver { font-size: 12px; color: #64748b; }
    .unit-card .info-row { display: flex; align-items: center; justify-content: space-between; margin-top: 6px; padding-top: 6px; border-top: 1px solid #f1f5f9; }
    .unit-card .speed { font-size: 13px; font-weight: 700; }
    .unit-card .last-update { font-size: 11px; color: #94a3b8; }
    .units-sidebar { max-height: 500px; overflow-y: auto; }
    .units-sidebar::-webkit-scrollbar { width: 4px; }
    .units-sidebar::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 4px; }
    .geofence-badge { font-size: 10px; padding: 2px 8px; border-radius: 12px; font-weight: 600; }
    .geofence-ok { background: #d1fae5; color: #047857; }
    .geofence-out { background: #fee2e2; color: #dc2626; }
    .alerta-item { padding: 10px; border-radius: 10px; border-left: 4px solid; margin-bottom: 6px; background: #f8fafc; }
    .alerta-item.desvio { border-left-color: #f59e0b; }
    .alerta-item.fuera { border-left-color: #ef4444; }
    .alerta-item .alerta-time { font-size: 10px; color: #94a3b8; }
    .alerta-item .alerta-msg { font-size: 12px; color: #1e293b; font-weight: 500; }
</style>
@endpush

@section('content')
<div class="fade-in">
    <div class="page-header">
        <div>
            <h2><i class="fas fa-satellite-dish"></i>{{ $puntoNombre }}</h2>
            <p style="font-size: 13px; color: #64748b; margin-top: 2px;">Monitoreo de unidades en tiempo real con geocercas</p>
        </div>
        <div class="d-flex align-items-center gap-3">
            <div class="status-badge live" id="liveBadge">
                <span class="dot"></span>
                <span id="liveText">Conectado</span>
            </div>
            <span id="countdownText" style="font-size: 12px; color: #94a3b8;">
                <i class="fas fa-sync-alt" id="refreshIcon"></i> 10s
            </span>
            <a href="{{ route('tarjetera.gps.index') }}" class="btn btn-sm btn-outline-secondary rounded-pill px-3">
                <i class="fas fa-table mr-1"></i>Ver Tabla
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-4 gap-4 mb-6">
        <div class="lg:col-span-3">
            <div id="map"></div>
        </div>
        <div class="lg:col-span-1">
            <div class="bg-white rounded-2xl border border-gray-200 overflow-hidden">
                <div class="px-4 py-3 bg-gradient-to-r from-emerald-50 to-green-50 border-b border-emerald-100">
                    <h3 class="font-bold text-emerald-800 text-sm"><i class="fas fa-truck mr-2"></i>Unidades en Ruta</h3>
                </div>
                <div class="units-sidebar p-3 space-y-2" id="unitsList">
                    <div class="text-center text-gray-400 py-6">
                        <i class="fas fa-spinner fa-spin text-xl mb-2"></i>
                        <p class="text-xs">Cargando...</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Alertas --}}
<div class="bg-white rounded-2xl border border-gray-200 overflow-hidden">
    <div class="px-4 py-2.5 bg-gradient-to-r from-red-50 to-orange-50 border-b border-red-100 flex items-center justify-between">
        <h3 class="font-bold text-sm text-gray-700"><i class="fas fa-exclamation-triangle text-red-500 mr-2"></i>Alertas</h3>
        <div class="d-flex align-items-center gap-2">
            <span id="alertasCount" class="px-2 py-0.5 bg-red-500 text-white text-xs rounded-full">0</span>
            <button onclick="marcarAlertasLeidas()" class="text-xs text-red-600 font-medium">
                <i class="fas fa-check-double"></i> Leídas
            </button>
        </div>
    </div>
    <div class="p-3 max-h-48 overflow-y-auto" id="alertasList">
        <div class="text-center text-gray-400 py-3">
            <i class="fas fa-spinner fa-spin"></i>
            <p class="text-xs mt-1">Cargando...</p>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    var map = L.map('map').setView([-1.8312, -79.0], 8);
    
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '© OpenStreetMap contributors'
    }).addTo(map);

    var unitMarkers = {};
    var geofenceCircles = {};

    var style = document.createElement('style');
    style.innerHTML = `@keyframes pulse { 0% { box-shadow: 0 0 0 0 rgba(5,150,105,0.4); } 70% { box-shadow: 0 0 0 10px rgba(5,150,105,0); } 100% { box-shadow: 0 0 0 0 rgba(5,150,105,0); } }`;
    document.head.appendChild(style);

    function drawGeofences(puntos) {
        Object.values(geofenceCircles).forEach(function(c) { map.removeLayer(c); });
        geofenceCircles = {};

        var colors = ['#059669', '#3b82f6', '#f59e0b', '#ef4444', '#8b5cf6'];
        puntos.forEach(function(p, i) {
            var color = colors[i % colors.length];
            var circle = L.circle([p.lat, p.lng], {
                radius: p.radio,
                color: color,
                fillColor: color,
                fillOpacity: 0.1,
                weight: 2,
                dashArray: '5, 8',
            }).addTo(map);
            circle.bindPopup('<b>' + p.nombre + '</b><br>Radio: ' + p.radio + 'm');
            geofenceCircles['punto_' + p.id] = circle;

            L.marker([p.lat, p.lng], {
                icon: L.divIcon({
                    html: '<div style="width:24px;height:24px;background:' + color + ';color:white;border-radius:6px;display:flex;align-items:center;justify-content:center;font-size:12px;font-weight:bold;box-shadow:0 3px 10px rgba(0,0,0,0.3);">' + p.nombre.charAt(0) + '</div>',
                    iconSize: [24, 24],
                    className: ''
                })
            }).addTo(map);
        });
    }

    function calcDistance(lat1, lng1, lat2, lng2) {
        var R = 6371000;
        var dLat = (lat2 - lat1) * Math.PI / 180;
        var dLng = (lng2 - lng1) * Math.PI / 180;
        var a = Math.sin(dLat/2) * Math.sin(dLat/2) + Math.cos(lat1 * Math.PI / 180) * Math.cos(lat2 * Math.PI / 180) * Math.sin(dLng/2) * Math.sin(dLng/2);
        var c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
        return R * c;
    }

    function getGeofenceStatus(lat, lng, puntos) {
        for (var p of puntos) {
            var d = calcDistance(lat, lng, p.lat, p.lng);
            if (d <= p.radio) return { dentro: true, punto: p.nombre, distancia: Math.round(d) };
        }
        return { dentro: false, punto: null, distancia: null };
    }

    function updateUnits(data) {
        var unidades = data.unidades;
        var puntos = data.puntos;
        var container = document.getElementById('unitsList');

        if (unidades.length === 0) {
            container.innerHTML = '<div class="text-center text-gray-400 py-6"><i class="fas fa-bed text-2xl mb-2"></i><p class="text-xs">Sin unidades en ruta</p></div>';
        } else {
            var html = '';
            unidades.forEach(function(u) {
                if (!u.lat || !u.lng) return;
                var geo = getGeofenceStatus(u.lat, u.lng, puntos);
                var badge = geo.dentro
                    ? '<span class="geofence-badge geofence-ok"><i class="fas fa-check-circle"></i> ' + geo.punto + '</span>'
                    : '<span class="geofence-badge geofence-out"><i class="fas fa-exclamation-triangle"></i> Fuera (' + geo.distancia + 'm)</span>';
                var speed = u.velocidad ? Math.round(u.velocidad) : 0;
                html += '<div class="unit-card mb-2">' +
                    '<div class="d-flex align-items-center justify-content-between">' +
                        '<span class="plate">' + u.unidad_placa + '</span>' +
                        '<span class="text-xs font-bold px-2 py-0.5 rounded-full ' + (speed > 0 ? 'bg-amber-100 text-amber-700' : 'bg-gray-100 text-gray-500') + '">' + speed + ' km/h</span>' +
                    '</div>' +
                    '<div class="driver"><i class="fas fa-user mr-1"></i>' + u.conductor + '</div>' +
                    '<div class="info-row">' +
                        '<div>' + badge + '</div>' +
                        '<div class="last-update"><i class="far fa-clock"></i> ' + (u.ultima_actualizacion || '...') + '</div>' +
                    '</div>' +
                '</div>';
            });
            container.innerHTML = html;
        }

        var activeKeys = unidades.filter(function(u) { return u.lat && u.lng; }).map(function(u) { return 'unit_' + u.salida_id; });
        Object.keys(unitMarkers).forEach(function(key) {
            if (activeKeys.indexOf(key) === -1) {
                map.removeLayer(unitMarkers[key]);
                delete unitMarkers[key];
            }
        });

        unidades.forEach(function(u) {
            if (!u.lat || !u.lng) return;
            var key = 'unit_' + u.salida_id;
            var icon = L.divIcon({
                html: '<div style="width:14px;height:14px;background:' + (u.velocidad > 0 ? '#f59e0b' : '#059669') + ';border:3px solid #fff;border-radius:50%;box-shadow:0 2px 8px rgba(0,0,0,0.3);' + (u.velocidad > 0 ? 'animation:pulse 1s infinite;' : '') + '"></div>',
                iconSize: [14, 14],
                className: ''
            });
            if (unitMarkers[key]) {
                unitMarkers[key].setLatLng([u.lat, u.lng]);
                unitMarkers[key].setIcon(icon);
            } else {
                var m = L.marker([u.lat, u.lng], { icon: icon }).addTo(map);
                m.bindPopup('<b>' + u.unidad_placa + '</b><br>' + u.conductor + '<br>' + speed + ' km/h<br>' + (u.ultima_actualizacion || ''));
                unitMarkers[key] = m;
            }
        });

        drawGeofences(puntos);

        if (unidades.length > 0) {
            var bounds = [];
            unidades.forEach(function(u) {
                if (u.lat && u.lng) bounds.push([u.lat, u.lng]);
            });
            puntos.forEach(function(p) {
                bounds.push([p.lat, p.lng]);
            });
            if (bounds.length > 0) map.fitBounds(bounds, { padding: [50, 50], maxZoom: 13 });
        }
    }

    var alertMarkers = {};

    function cargarAlertas() {
        fetch('{{ route('tarjetera.gps.alertas') }}')
            .then(function(r) { return r.json(); })
            .then(function(data) {
                var container = document.getElementById('alertasList');
                document.getElementById('alertasCount').textContent = data.no_leidas;

                Object.values(alertMarkers).forEach(function(m) { map.removeLayer(m); });
                alertMarkers = {};

                if (data.alertas.length === 0) {
                    container.innerHTML = '<div class="text-center text-gray-400 py-3"><i class="fas fa-check-circle text-green-400 text-xl mb-1"></i><p class="text-xs">Sin alertas</p></div>';
                } else {
                    var html = '';
                    data.alertas.forEach(function(a) {
                        var tipoClass = a.tipo.indexOf('desvio') !== -1 ? 'desvio' : 'fuera';
                        var color = a.tipo.indexOf('desvio') !== -1 ? '#f59e0b' : '#ef4444';
                        html += '<div class="alerta-item ' + tipoClass + ' ' + (a.leido ? 'opacity-50' : '') + '">' +
                            '<div class="d-flex align-items-center justify-content-between">' +
                                '<span class="alerta-time"><i class="fas fa-truck mr-1"></i>' + a.unidad_placa + '</span>' +
                                '<span class="alerta-time">' + a.registrado_at + '</span>' +
                            '</div>' +
                            '<div class="alerta-msg">' + a.mensaje + '</div>' +
                        '</div>';

                        if (a.lat && a.lng && !a.leido) {
                            var m = L.marker([a.lat, a.lng], {
                                icon: L.divIcon({
                                    html: '<div style="width:18px;height:18px;background:' + color + ';color:white;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:10px;font-weight:bold;box-shadow:0 2px 8px rgba(0,0,0,0.4);">!</div>',
                                    iconSize: [18, 18],
                                    className: ''
                                })
                            }).addTo(map);
                            m.bindPopup('<b>⚠️ ' + a.tipo.replace(/_/g, ' ') + '</b><br>' + a.mensaje);
                            alertMarkers['alerta_' + a.alerta_id] = m;
                        }
                    });
                    container.innerHTML = html;
                }
            })
            .catch(function() {});
    }

    function marcarAlertasLeidas() {
        fetch('{{ route('tarjetera.gps.alertas.leidas') }}', {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content }
        }).then(function() { cargarAlertas(); document.getElementById('alertasCount').textContent = '0'; });
    }

    function cargarDatos() {
        document.getElementById('refreshIcon').className = 'fas fa-sync-alt fa-spin';
        fetch('{{ route('tarjetera.gps.posicionesEnVivo') }}')
            .then(function(r) { return r.json(); })
            .then(function(data) {
                updateUnits(data);
                document.getElementById('refreshIcon').className = 'fas fa-sync-alt';
                document.getElementById('liveBadge').className = 'status-badge live';
                document.getElementById('liveText').textContent = 'Conectado';
            })
            .catch(function() {
                document.getElementById('refreshIcon').className = 'fas fa-sync-alt';
                document.getElementById('liveBadge').className = 'status-badge';
                document.getElementById('liveText').textContent = 'Sin conexión';
            });
        cargarAlertas();
    }

    var countdown = 10;
    setInterval(function() {
        countdown--;
        document.getElementById('countdownText').innerHTML = '<i class="fas fa-sync-alt" id="refreshIcon"></i> ' + countdown + 's';
        if (countdown <= 0) { cargarDatos(); countdown = 10; }
    }, 1000);

    cargarDatos();
});
</script>
@endpush