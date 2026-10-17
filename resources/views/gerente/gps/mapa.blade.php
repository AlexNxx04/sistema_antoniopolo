@extends('layouts.gerente')

@section('title', 'GPS - Mapa en Vivo')
@section('header', 'GPS - Monitoreo en Tiempo Real')

@push('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<style>
    #map { height: 550px; z-index: 1; border-radius: 20px; }
    .leaflet-container { font-family: inherit; }
    .page-header { display: flex; align-items: center; justify-content: space-between; padding: 24px; background: linear-gradient(145deg, #ffffff, #f8fafc); border-radius: 20px; border: 1px solid #e2e8f0; margin-bottom: 24px; }
    .page-header h2 { font-size: 24px; font-weight: 700; color: #1e293b; }
    .page-header h2 i { color: #059669; margin-right: 10px; }
    .page-header p { color: #64748b; font-size: 14px; margin-top: 4px; }
    .btn-volver { padding: 12px 20px; background: linear-gradient(135deg, #059669, #047857); color: white; border-radius: 12px; font-weight: 600; display: inline-flex; align-items: center; gap: 8px; }
    .status-badge { display: inline-flex; align-items: center; gap: 6px; padding: 6px 14px; border-radius: 20px; font-size: 13px; font-weight: 600; }
    .status-badge.live { background: #d1fae5; color: #047857; }
    .status-badge.live .dot { background: #10b981; }
    .status-badge.offline { background: #fee2e2; color: #991b1b; }
    .status-badge .dot { width: 8px; height: 8px; border-radius: 50%; display: inline-block; }
    .unit-card { background: white; border: 1px solid #e2e8f0; border-radius: 16px; padding: 16px; transition: all 0.2s; }
    .unit-card:hover { box-shadow: 0 8px 30px rgba(0,0,0,0.1); transform: translateY(-2px); }
    .unit-card .plate { font-size: 18px; font-weight: 800; color: #0f172a; }
    .unit-card .driver { font-size: 13px; color: #64748b; }
    .unit-card .status-row { display: flex; align-items: center; justify-content: space-between; margin-top: 8px; padding-top: 8px; border-top: 1px solid #f1f5f9; }
    .unit-card .speed { font-size: 14px; font-weight: 700; color: #059669; }
    .unit-card .last-update { font-size: 11px; color: #94a3b8; }
    .unit-card .geofence-badge { font-size: 11px; padding: 3px 8px; border-radius: 12px; font-weight: 600; }
    .geofence-ok { background: #d1fae5; color: #047857; }
    .geofence-out { background: #fee2e2; color: #dc2626; }
    .units-sidebar { max-height: 550px; overflow-y: auto; padding-right: 4px; }
    .units-sidebar::-webkit-scrollbar { width: 4px; }
    .units-sidebar::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 4px; }
    .refresh-indicator { font-size: 12px; color: #94a3b8; display: flex; align-items: center; gap: 6px; }
    .alerta-item { padding: 12px; border-radius: 12px; border-left: 4px solid; margin-bottom: 8px; background: #f8fafc; }
    .alerta-item.desvio { border-left-color: #f59e0b; }
    .alerta-item.fuera { border-left-color: #ef4444; }
    .alerta-item .alerta-time { font-size: 11px; color: #94a3b8; }
    .alerta-item .alerta-msg { font-size: 13px; color: #1e293b; font-weight: 500; }
    .alerta-item .alerta-unit { font-size: 12px; color: #64748b; }
</style>
@endpush

@section('content')
<div class="fade-in">
    <div class="page-header">
        <div>
            <h2><i class="fas fa-satellite-dish"></i>Monitoreo en Tiempo Real</h2>
            <p>Ubicación de unidades activas con geocercas</p>
        </div>
        <div class="d-flex align-items-center gap-3">
            <div class="status-badge live" id="liveBadge">
                <span class="dot status-indicator"></span>
                <span id="liveText">Conectado</span>
            </div>
            <div class="refresh-indicator">
                <i class="fas fa-sync-alt" id="refreshIcon"></i>
                <span id="countdownText">Actualizando en 10s</span>
            </div>
            <a href="{{ route('gerente.gps.index') }}" class="btn-volver">
                <i class="fas fa-table"></i> Ver Tabla
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-4 gap-6 mb-6">
        <div class="lg:col-span-3">
            <div id="map"></div>
        </div>
        <div class="lg:col-span-1">
            <div class="bg-white rounded-2xl border border-gray-200 overflow-hidden">
                <div class="px-4 py-3 bg-gradient-to-r from-emerald-50 to-green-50 border-b border-emerald-100 flex items-center justify-between">
                    <h3 class="font-bold text-emerald-800"><i class="fas fa-truck mr-2"></i>Unidades en Ruta</h3>
                    <span id="unitCount" class="px-2 py-0.5 bg-emerald-500 text-white text-xs rounded-full">0</span>
                </div>
                <div class="units-sidebar p-3 space-y-3" id="unitsList">
                    <div class="text-center text-gray-400 py-8">
                        <i class="fas fa-spinner fa-spin text-2xl mb-2"></i>
                        <p class="text-sm">Cargando unidades...</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-2xl border border-gray-200 overflow-hidden">
        <div class="px-5 py-3 bg-gradient-to-r from-gray-50 to-slate-50 border-b border-gray-200 flex items-center justify-between">
            <h3 class="font-bold text-gray-700"><i class="fas fa-map-marked-alt text-emerald-500 mr-2"></i>Geocercas</h3>
            <span class="text-xs text-gray-500">Círculos = radio de geofence configurado en cada punto</span>
        </div>
        <div class="p-4">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4" id="puntosList">
                <div class="text-center text-gray-400 py-4">
                    <i class="fas fa-spinner fa-spin"></i>
                    <p class="text-sm mt-2">Cargando puntos...</p>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Alertas --}}
<div class="bg-white rounded-2xl border border-gray-200 overflow-hidden mt-6">
    <div class="px-5 py-3 bg-gradient-to-r from-red-50 to-orange-50 border-b border-red-100 flex items-center justify-between">
        <h3 class="font-bold text-gray-700"><i class="fas fa-exclamation-triangle text-red-500 mr-2"></i>Alertas de Geocerca</h3>
        <div class="flex items-center gap-3">
            <span id="alertasCount" class="px-2 py-0.5 bg-red-500 text-white text-xs rounded-full">0</span>
            <button onclick="marcarAlertasLeidas()" class="text-xs text-red-600 hover:text-red-800 font-medium">
                <i class="fas fa-check-double"></i> Marcar leídas
            </button>
        </div>
    </div>
    <div class="p-4 max-h-64 overflow-y-auto" id="alertasList">
        <div class="text-center text-gray-400 py-4">
            <i class="fas fa-spinner fa-spin"></i>
            <p class="text-sm mt-2">Cargando alertas...</p>
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
    var routeLines = {};
    var geofenceCircles = {};

    var unitIcon = L.divIcon({
        html: '<div style="width:16px;height:16px;background:#059669;border:3px solid #fff;border-radius:50%;box-shadow:0 2px 8px rgba(0,0,0,0.3);"></div>',
        iconSize: [16, 16],
        className: ''
    });

    var unitIconMoving = L.divIcon({
        html: '<div style="width:18px;height:18px;background:#f59e0b;border:3px solid #fff;border-radius:50%;box-shadow:0 2px 8px rgba(0,0,0,0.3);animation:pulse 1s infinite;"></div>',
        iconSize: [18, 18],
        className: ''
    });

    var style = document.createElement('style');
    style.innerHTML = `@keyframes pulse { 0% { box-shadow: 0 0 0 0 rgba(5,150,105,0.4); } 70% { box-shadow: 0 0 0 10px rgba(5,150,105,0); } 100% { box-shadow: 0 0 0 0 rgba(5,150,105,0); } }`;
    document.head.appendChild(style);

    var puntoMarkers = {};

    function drawGeofences(puntos) {
        Object.values(geofenceCircles).forEach(c => map.removeLayer(c));
        geofenceCircles = {};
        Object.values(puntoMarkers).forEach(m => map.removeLayer(m));
        puntoMarkers = {};

        const colors = ['#059669', '#3b82f6', '#f59e0b', '#ef4444', '#8b5cf6'];

        puntos.forEach((p, i) => {
            var color = colors[i % colors.length];

            var circle = L.circle([p.lat, p.lng], {
                radius: p.radio,
                color: color,
                fillColor: color,
                fillOpacity: 0.08,
                weight: 2,
                dashArray: '5, 8',
            }).addTo(map);
            circle.bindPopup(`
                <b>${p.nombre}</b><br>
                Radio: ${p.radio}m<br>
                Lat: ${p.lat}<br>
                Lng: ${p.lng}
            `);
            geofenceCircles['punto_' + p.id] = circle;

            var pm = L.marker([p.lat, p.lng], {
                icon: L.divIcon({
                    html: `<div style="width:28px;height:28px;background:${color};color:white;border-radius:8px;display:flex;align-items:center;justify-content:center;font-size:14px;font-weight:bold;box-shadow:0 3px 10px rgba(0,0,0,0.3);">${p.nombre.charAt(0)}</div>`,
                    iconSize: [28, 28],
                    className: ''
                })
            }).addTo(map);
            pm.bindPopup(`<b>${p.nombre}</b><br>Radio geocerca: ${p.radio}m`);
            puntoMarkers['punto_' + p.id] = pm;
        });
    }

    function getGeofenceStatus(lat, lng, puntos) {
        for (var p of puntos) {
            var d = calcDistance(lat, lng, p.lat, p.lng);
            if (d <= p.radio) {
                return { dentro: true, punto: p.nombre, distancia: Math.round(d) };
            }
        }
        return { dentro: false, punto: null, distancia: null };
    }

    function calcDistance(lat1, lng1, lat2, lng2) {
        var R = 6371000;
        var dLat = (lat2 - lat1) * Math.PI / 180;
        var dLng = (lng2 - lng1) * Math.PI / 180;
        var a = Math.sin(dLat/2) * Math.sin(dLat/2) +
                Math.cos(lat1 * Math.PI / 180) * Math.cos(lat2 * Math.PI / 180) *
                Math.sin(dLng/2) * Math.sin(dLng/2);
        var c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
        return R * c;
    }

    function updateUnits(data) {
        var unidades = data.unidades;
        var puntos = data.puntos;

        var unitsContainer = document.getElementById('unitsList');
        var puntosContainer = document.getElementById('puntosList');
        document.getElementById('unitCount').textContent = unidades.length;

        if (unidades.length === 0) {
            unitsContainer.innerHTML = `
                <div class="text-center text-gray-400 py-8">
                    <i class="fas fa-bed text-3xl mb-2"></i>
                    <p class="text-sm font-medium">Sin unidades en ruta</p>
                    <p class="text-xs mt-1">No hay salidas activas en este momento</p>
                </div>`;
        } else {
            var html = '';
            unidades.forEach(function(u) {
                var geo = u.lat && u.lng ? getGeofenceStatus(u.lat, u.lng, puntos) : null;
                var geoBadge = '';
                if (geo) {
                    if (geo.dentro) {
                        geoBadge = `<span class="geofence-badge geofence-ok"><i class="fas fa-check-circle"></i> ${geo.punto}</span>`;
                    } else {
                        geoBadge = `<span class="geofence-badge geofence-out"><i class="fas fa-exclamation-triangle"></i> Fuera de zona (${geo.distancia}m)</span>`;
                    }
                }
                var speedKm = u.velocidad ? Math.round(u.velocidad) : 0;
                html += `
                    <div class="unit-card">
                        <div class="d-flex align-items-center justify-content-between">
                            <span class="plate">${u.unidad_placa}</span>
                            <span class="text-xs font-bold px-2 py-0.5 rounded-full ${speedKm > 0 ? 'bg-amber-100 text-amber-700' : 'bg-gray-100 text-gray-500'}">${speedKm} km/h</span>
                        </div>
                        <div class="driver"><i class="fas fa-user mr-1"></i>${u.conductor}</div>
                        <div class="driver"><i class="fas fa-hashtag mr-1"></i>Turno #${u.turno_posicion} · ${u.punto_origen}</div>
                        <div class="status-row">
                            <div>
                                ${geoBadge}
                            </div>
                            <div class="last-update">
                                <i class="far fa-clock"></i> ${u.ultima_actualizacion || '...'}
                            </div>
                        </div>
                    </div>`;
            });
            unitsContainer.innerHTML = html;
        }

        if (puntos.length > 0) {
            var ph = '';
            puntos.forEach(function(p) {
                ph += `
                    <div class="p-3 bg-gray-50 rounded-xl border border-gray-100">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-lg bg-emerald-100 flex items-center justify-center text-emerald-600 font-bold text-xs">${p.nombre.charAt(0)}</div>
                            <div>
                                <p class="font-semibold text-gray-800 text-sm">${p.nombre}</p>
                                <p class="text-xs text-gray-500">Radio: ${p.radio}m · Lat: ${p.lat} · Lng: ${p.lng}</p>
                            </div>
                        </div>
                    </div>`;
            });
            puntosContainer.innerHTML = ph;
        }

        var idsActuales = unidades.filter(function(u) { return u.lat && u.lng; }).map(function(u) { return 'unit_' + u.salida_id; });
        Object.keys(unitMarkers).forEach(function(key) {
            if (idsActuales.indexOf(key) === -1) {
                map.removeLayer(unitMarkers[key]);
                delete unitMarkers[key];
            }
        });
        Object.keys(routeLines).forEach(function(key) {
            if (idsActuales.indexOf(key) === -1) {
                map.removeLayer(routeLines[key]);
                delete routeLines[key];
            }
        });

        unidades.forEach(function(u) {
            if (!u.lat || !u.lng) return;
            var key = 'unit_' + u.salida_id;

            var icon = u.velocidad > 0 ? unitIconMoving : unitIcon;
            if (unitMarkers[key]) {
                unitMarkers[key].setLatLng([u.lat, u.lng]);
                unitMarkers[key].setIcon(icon);
            } else {
                var marker = L.marker([u.lat, u.lng], { icon: icon }).addTo(map);
                marker.bindPopup(`
                    <b>${u.unidad_placa}</b><br>
                    Conductor: ${u.conductor}<br>
                    Velocidad: ${Math.round(u.velocidad)} km/h<br>
                    Última actualización: ${u.ultima_actualizacion}<br>
                    Origen: ${u.punto_origen} · Turno #${u.turno_posicion}
                `);
                unitMarkers[key] = marker;
            }

            if (u.punto_origen && !routeLines[key]) {
                var punto = puntos.find(function(p) { return p.nombre === u.punto_origen; });
                if (punto) {
                    var line = L.polyline([[punto.lat, punto.lng], [u.lat, u.lng]], {
                        color: '#059669',
                        weight: 2,
                        opacity: 0.4,
                        dashArray: '4, 6',
                    }).addTo(map);
                    routeLines[key] = line;
                }
            } else if (routeLines[key] && puntos.length > 0) {
                var linea = routeLines[key];
                var latlngs = linea.getLatLngs();
                if (latlngs.length >= 1) {
                    linea.setLatLngs([latlngs[0], [u.lat, u.lng]]);
                }
            }
        });

        drawGeofences(puntos);
    }

    var alertMarkers = {};

    function cargarAlertas() {
        fetch('{{ route('gerente.gps.alertas') }}')
            .then(function(r) { return r.json(); })
            .then(function(data) {
                var container = document.getElementById('alertasList');
                var countEl = document.getElementById('alertasCount');
                countEl.textContent = data.no_leidas;

                Object.values(alertMarkers).forEach(function(m) { map.removeLayer(m); });
                alertMarkers = {};

                if (data.alertas.length === 0) {
                    container.innerHTML = '<div class="text-center text-gray-400 py-4"><i class="fas fa-check-circle text-green-400 text-2xl mb-2"></i><p class="text-sm">Sin alertas</p></div>';
                } else {
                    var html = '';
                    data.alertas.forEach(function(a) {
                        var tipoClass = a.tipo.indexOf('desvio') !== -1 ? 'desvio' : 'fuera';
                        var iconColor = a.tipo.indexOf('desvio') !== -1 ? '#f59e0b' : '#ef4444';
                        var leidoClass = a.leido ? 'opacity-50' : '';
                        html += '<div class="alerta-item ' + tipoClass + ' ' + leidoClass + '">' +
                            '<div class="d-flex align-items-center justify-content-between">' +
                                '<span class="alerta-unit"><i class="fas fa-truck mr-1"></i>' + a.unidad_placa + '</span>' +
                                '<span class="alerta-time">' + a.registrado_at + '</span>' +
                            '</div>' +
                            '<div class="alerta-msg">' + a.mensaje + '</div>' +
                            '<div class="alerta-time">' + a.fecha + ' · ' + a.punto_nombre + ' · ' + Math.round(a.distancia_m) + 'm</div>' +
                        '</div>';

                        if (a.lat && a.lng && !a.leido) {
                            var alertIcon = L.divIcon({
                                html: '<div style="width:20px;height:20px;background:' + iconColor + ';color:white;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:11px;font-weight:bold;box-shadow:0 2px 8px rgba(0,0,0,0.4);animation:pulse 1s infinite;">!</div>',
                                iconSize: [20, 20],
                                className: ''
                            });
                            var m = L.marker([a.lat, a.lng], { icon: alertIcon }).addTo(map);
                            m.bindPopup('<b>⚠️ ' + a.tipo.replace(/_/g, ' ') + '</b><br>' + a.mensaje + '<br>' + a.registrado_at);
                            alertMarkers['alerta_' + a.alerta_id] = m;
                        }
                    });
                    container.innerHTML = html;
                }
            })
            .catch(function() {});
    }

    function marcarAlertasLeidas() {
        fetch('{{ route('gerente.gps.alertas.leidas') }}', {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content }
        }).then(function() {
            cargarAlertas();
            document.getElementById('alertasCount').textContent = '0';
        });
    }

    function cargarDatos() {
        var refreshIcon = document.getElementById('refreshIcon');
        refreshIcon.className = 'fas fa-sync-alt fa-spin';

        fetch('{{ route('gerente.gps.posicionesEnVivo') }}')
            .then(function(r) { return r.json(); })
            .then(function(data) {
                updateUnits(data);
                refreshIcon.className = 'fas fa-sync-alt';
                document.getElementById('liveBadge').className = 'status-badge live';
                document.getElementById('liveText').textContent = 'Conectado';
            })
            .catch(function() {
                refreshIcon.className = 'fas fa-sync-alt';
                document.getElementById('liveBadge').className = 'status-badge offline';
                document.getElementById('liveText').textContent = 'Sin conexión';
            });

        cargarAlertas();
    }

    var countdown = 10;
    setInterval(function() {
        countdown--;
        document.getElementById('countdownText').textContent = 'Actualizando en ' + countdown + 's';
        if (countdown <= 0) {
            cargarDatos();
            countdown = 10;
        }
    }, 1000);

    cargarDatos();
});
</script>
@endpush