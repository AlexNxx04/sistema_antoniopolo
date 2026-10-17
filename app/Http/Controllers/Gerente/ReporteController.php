<?php

namespace App\Http\Controllers\Gerente;

use App\Http\Controllers\Controller;
use App\Models\Multa;
use App\Models\Salida;
use App\Models\Turno;
use App\Models\Unidad;
use App\Models\User;
use Carbon\Carbon;
use Dompdf\Dompdf;
use Dompdf\Options;
use Illuminate\Http\Request;

class ReporteController extends Controller
{
    public function index()
    {
        return view('gerente.reportes.index');
    }

    public function unidades(Request $request)
    {
        $formato = $request->formato ?? 'html';
        $unidades = Unidad::with(['conductor'])
            ->orderBy('numero_interno')
            ->get();

        if ($formato === 'pdf') {
            return $this->generarPdfUnidades($unidades);
        }

        return view('gerente.reportes.unidades', compact('unidades'));
    }

    public function multas(Request $request)
    {
        $fechaInicio = $request->filled('fecha_inicio') ? Carbon::parse($request->fecha_inicio) : Carbon::today()->startOfMonth();
        $fechaFin = $request->filled('fecha_fin') ? Carbon::parse($request->fecha_fin) : Carbon::today();
        $formato = $request->formato ?? 'html';

        $multas = Multa::with(['usuario'])
            ->whereBetween('registrado_en', [$fechaInicio->startOfDay(), $fechaFin->endOfDay()])
            ->orderByDesc('registrado_en')
            ->get();

        $totalGeneral = $multas->sum('monto');
        $totalCobrado = $multas->where('estado', 'cobrada')->sum('monto');
        $totalPendiente = $multas->where('estado', 'pendiente')->sum('monto');

        if ($formato === 'pdf') {
            return $this->generarPdfMultas($multas, $fechaInicio, $fechaFin, $totalGeneral, $totalCobrado, $totalPendiente);
        }

        return view('gerente.reportes.multas', compact('multas', 'fechaInicio', 'fechaFin', 'totalGeneral', 'totalCobrado', 'totalPendiente'));
    }

    public function conductores(Request $request)
    {
        $formato = $request->formato ?? 'html';

        $conductores = User::where('rol_id', 3)
            ->where('activo', true)
            ->with(['turnos' => function ($q) {
                $q->whereDate('fecha', Carbon::today());
            }, 'documentos'])
            ->get();

        $documentosProximos = [];
        foreach ($conductores as $conductor) {
            $venciendo = $conductor->documentos
                ->where('estado', 'vigente')
                ->filter(fn ($doc) => $doc->fecha_vencimiento->diffInDays(now()) <= 30);
            if ($venciendo->count() > 0) {
                $documentosProximos[$conductor->usuario_id] = $venciendo;
            }
        }

        if ($formato === 'pdf') {
            return $this->generarPdfConductores($conductores);
        }

        return view('gerente.reportes.conductores', compact('conductores', 'documentosProximos'));
    }

    public function turnos(Request $request)
    {
        $fecha = $request->filled('fecha') ? Carbon::parse($request->fecha) : Carbon::today();
        $formato = $request->formato ?? 'html';

        $turnos = Turno::with(['usuario', 'unidad', 'punto'])
            ->whereDate('fecha', $fecha)
            ->orderBy('posicion')
            ->get();

        if ($formato === 'pdf') {
            return $this->generarPdfTurnos($turnos, $fecha);
        }

        return view('gerente.reportes.turnos', compact('turnos', 'fecha'));
    }

    public function sanciones(Request $request)
    {
        $fechaInicio = $request->filled('fecha_inicio') ? Carbon::parse($request->fecha_inicio) : Carbon::today()->startOfMonth();
        $fechaFin = $request->filled('fecha_fin') ? Carbon::parse($request->fecha_fin) : Carbon::today();
        $formato = $request->formato ?? 'html';

        $sanciones = Multa::with(['usuario', 'registrador'])
            ->whereBetween('registrado_en', [$fechaInicio, $fechaFin])
            ->orderByDesc('registrado_en')
            ->get();

        $totalGeneral = $sanciones->sum('monto');
        $totalCobrado = $sanciones->where('estado', 'cobrada')->sum('monto');
        $totalPendiente = $sanciones->where('estado', 'pendiente')->sum('monto');

        if ($formato === 'pdf') {
            return $this->generarPdfSanciones($sanciones, $fechaInicio, $fechaFin, $totalGeneral, $totalCobrado, $totalPendiente);
        }

        return view('gerente.reportes.sanciones', compact('sanciones', 'fechaInicio', 'fechaFin', 'totalGeneral', 'totalCobrado', 'totalPendiente'));
    }

    public function gps(Request $request)
    {
        $fecha = $request->filled('fecha') ? Carbon::parse($request->fecha) : Carbon::today();
        $formato = $request->formato ?? 'html';

        $salidas = Salida::with(['turno.usuario', 'turno.unidad', 'puntoOrigen', 'llegada.puntoDestino'])
            ->whereDate('hora_salida', $fecha)
            ->orderBy('hora_salida')
            ->get();

        $totalSalidas = $salidas->count();
        $completadas = $salidas->filter(fn ($s) => $s->llegada)->count();
        $enRuta = $totalSalidas - $completadas;

        $tiempoPromedio = 0;
        $conLlegada = $salidas->filter(fn ($s) => $s->llegada);
        if ($conLlegada->count() > 0) {
            $tiempoPromedio = round($conLlegada->avg(fn ($s) => $s->llegada->tiempo_trayecto_min));
        }

        if ($formato === 'pdf') {
            return $this->generarPdfGps($salidas, $fecha, $totalSalidas, $completadas, $enRuta, $tiempoPromedio);
        }

        return view('gerente.reportes.gps', compact('salidas', 'fecha', 'totalSalidas', 'completadas', 'enRuta', 'tiempoPromedio'));
    }

    private function generarPdfTurnos($turnos, $fecha)
    {
        $html = $this->getHtmlReportes('turnos', [
            'turnos' => $turnos,
            'fecha' => $fecha,
            'title' => 'Reporte de Turnos',
            'subtitle' => 'Fecha: '.$fecha->format('d/m/Y'),
        ]);

        return $this->generarPdf($html, 'reporte_turnos_'.$fecha->format('Y-m-d').'.pdf');
    }

    private function generarPdfSanciones($sanciones, $fechaInicio, $fechaFin, $totalGeneral, $totalCobrado, $totalPendiente)
    {
        $html = $this->getHtmlReportes('sanciones', [
            'sanciones' => $sanciones,
            'fechaInicio' => $fechaInicio,
            'fechaFin' => $fechaFin,
            'totalGeneral' => $totalGeneral,
            'totalCobrado' => $totalCobrado,
            'totalPendiente' => $totalPendiente,
            'title' => 'Reporte de Sanciones',
            'subtitle' => 'Desde: '.$fechaInicio->format('d/m/Y').' Hasta: '.$fechaFin->format('d/m/Y'),
        ]);

        return $this->generarPdf($html, 'reporte_sanciones_'.date('Y-m-d').'.pdf');
    }

    private function generarPdfGps($salidas, $fecha, $totalSalidas, $completadas, $enRuta, $tiempoPromedio)
    {
        $html = $this->getHtmlReportes('gps', [
            'salidas' => $salidas,
            'fecha' => $fecha,
            'totalSalidas' => $totalSalidas,
            'completadas' => $completadas,
            'enRuta' => $enRuta,
            'tiempoPromedio' => $tiempoPromedio,
            'title' => 'Reporte GPS',
            'subtitle' => 'Fecha: '.$fecha->format('d/m/Y'),
        ]);

        return $this->generarPdf($html, 'reporte_gps_'.$fecha->format('Y-m-d').'.pdf');
    }

    private function getHtmlReportes($tipo, $data)
    {
        $css = "
            <style>
                * { margin: 0; padding: 0; box-sizing: border-box; }
                body { font-family: 'Helvetica', Arial, sans-serif; font-size: 11px; color: #1f2937; }
                .header { background: #059669; color: white; padding: 20px; text-align: center; margin-bottom: 20px; }
                .header h1 { font-size: 18px; margin-bottom: 5px; }
                .header h2 { font-size: 14px; font-weight: normal; }
                .subtitle { text-align: center; color: #6b7280; margin-bottom: 20px; font-size: 12px; }
                .stats { display: flex; justify-content: center; gap: 20px; margin-bottom: 20px; }
                .stat-box { background: #f0fdf4; border: 1px solid #d1fae5; padding: 10px 20px; border-radius: 8px; text-align: center; }
                .stat-box .value { font-size: 20px; font-weight: bold; color: #059669; }
                .stat-box .label { font-size: 10px; color: #6b7280; }
                table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
                th { background: #059669; color: white; padding: 8px 10px; text-align: left; font-size: 10px; }
                td { padding: 8px 10px; border-bottom: 1px solid #e5e7eb; }
                tr:nth-child(even) { background: #f9fafb; }
                .badge { padding: 2px 8px; border-radius: 10px; font-size: 9px; }
                .badge-success { background: #d1fae5; color: #065f46; }
                .badge-warning { background: #fef3c7; color: #92400e; }
                .badge-danger { background: #fee2e2; color: #991b1b; }
                .badge-info { background: #dbeafe; color: #1e40af; }
                .footer { text-align: center; font-size: 9px; color: #9ca3af; margin-top: 30px; padding-top: 10px; border-top: 1px solid #e5e7eb; }
                .totals { display: flex; justify-content: flex-end; gap: 30px; margin-bottom: 20px; }
                .total-item { text-align: right; }
                .total-item .label { font-size: 10px; color: #6b7280; }
                .total-item .value { font-size: 14px; font-weight: bold; color: #059669; }
            </style>
        ";

        $html = $css.$this->getReportContent($tipo, $data);

        return $html;
    }

    private function getReportContent($tipo, $data)
    {
        $title = $data['title'] ?? 'Reporte';
        $subtitle = $data['subtitle'] ?? '';

        $content = '';

        switch ($tipo) {
            case 'turnos':
                $content = "
                    <div class='header'>
                        <h1>SGOA-AP - {$title}</h1>
                        <h2>Antonio Polo S.A.</h2>
                    </div>
                    <div class='subtitle'>{$subtitle}</div>
                    <table>
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Conductor</th>
                                <th>Cédula</th>
                                <th>Unidad</th>
                                <th>Punto</th>
                                <th>Estado</th>
                            </tr>
                        </thead>
                        <tbody>
                ";
                foreach ($data['turnos'] as $turno) {
                    $estado = $turno->es_descanso ? '<span class="badge badge-warning">Descanso</span>' : '<span class="badge badge-success">Operando</span>';
                    $content .= "
                        <tr>
                            <td>{$turno->posicion}</td>
                            <td>{$turno->usuario->nombres} {$turno->usuario->apellidos}</td>
                            <td>{$turno->usuario->cedula}</td>
                            <td>{$turno->unidad->placa}</td>
                            <td>{$turno->punto->nombre}</td>
                            <td>{$estado}</td>
                        </tr>
                    ";
                }
                $content .= '</tbody></table>';
                break;

            case 'sanciones':
                $content = "
                    <div class='header'>
                        <h1>SGOA-AP - {$title}</h1>
                        <h2>Antonio Polo S.A.</h2>
                    </div>
                    <div class='subtitle'>{$subtitle}</div>
                    <div class='totals'>
                        <div class='total-item'>
                            <div class='label'>Total Generado</div>
                            <div class='value'>$ ".number_format($data['totalGeneral'], 2)."</div>
                        </div>
                        <div class='total-item'>
                            <div class='label'>Total Cobrado</div>
                            <div class='value'>$ ".number_format($data['totalCobrado'], 2)."</div>
                        </div>
                        <div class='total-item'>
                            <div class='label'>Total Pendiente</div>
                            <div class='value'>$ ".number_format($data['totalPendiente'], 2).'</div>
                        </div>
                    </div>
                    <table>
                        <thead>
                            <tr>
                                <th>Fecha</th>
                                <th>Conductor</th>
                                <th>Tipo</th>
                                <th>Monto</th>
                                <th>Estado</th>
                            </tr>
                        </thead>
                        <tbody>
                ';
                foreach ($data['sanciones'] as $sancion) {
                    $tipoNombre = $sancion->tipo_display;
                    $estadoBadge = $sancion->estado === 'cobrada'
                        ? '<span class="badge badge-success">Cobrada</span>'
                        : '<span class="badge badge-warning">Pendiente</span>';
                    $content .= "
                            <tr>
                                <td>{$sancion->registrado_en->format('d/m/Y')}</td>
                                <td>{$sancion->usuario->nombres} {$sancion->usuario->apellidos}</td>
                                <td>{$tipoNombre}</td>
                                <td>$ ".number_format($sancion->monto, 2)."</td>
                                <td>{$estadoBadge}</td>
                            </tr>
                        ";
                }
                $content .= '</tbody></table>';
                break;

            case 'gps':
                $content = "
                    <div class='header'>
                        <h1>SGOA-AP - {$title}</h1>
                        <h2>Antonio Polo S.A.</h2>
                    </div>
                    <div class='subtitle'>{$subtitle}</div>
                    <div class='stats'>
                        <div class='stat-box'>
                            <div class='value'>{$data['totalSalidas']}</div>
                            <div class='label'>Total Salidas</div>
                        </div>
                        <div class='stat-box'>
                            <div class='value'>{$data['completadas']}</div>
                            <div class='label'>Completadas</div>
                        </div>
                        <div class='stat-box'>
                            <div class='value'>{$data['enRuta']}</div>
                            <div class='label'>En Ruta</div>
                        </div>
                        <div class='stat-box'>
                            <div class='value'>{$data['tiempoPromedio']} min</div>
                            <div class='label'>Tiempo Promedio</div>
                        </div>
                    </div>
                    <table>
                        <thead>
                            <tr>
                                <th>Hora</th>
                                <th>Conductor</th>
                                <th>Unidad</th>
                                <th>Origen</th>
                                <th>Destino</th>
                                <th>Tiempo</th>
                            </tr>
                        </thead>
                        <tbody>
                ";
                foreach ($data['salidas'] as $salida) {
                    $destino = $salida->llegada?->puntoDestino?->nombre ?? '-';
                    $tiempo = $salida->llegada?->tiempo_trayecto_min
                        ? $salida->llegada->tiempo_trayecto_min.' min'
                        : '-';
                    $conductor = $salida->turno?->usuario?->nombres.' '.($salida->turno?->usuario?->apellidos ?? '');
                    $unidad = $salida->turno?->unidad?->placa ?? '-';
                    $content .= "
                        <tr>
                            <td>{$salida->hora_salida->format('H:i')}</td>
                            <td>{$conductor}</td>
                            <td>{$unidad}</td>
                            <td>{$salida->puntoOrigen->nombre}</td>
                            <td>{$destino}</td>
                            <td>{$tiempo}</td>
                        </tr>
                    ";
                }
                $content .= '</tbody></table>';
                break;

            case 'conductores':
                $content = "
                    <div class='header'>
                        <h1>SGOA-AP - {$title}</h1>
                        <h2>Antonio Polo S.A.</h2>
                    </div>
                    <div class='subtitle'>{$subtitle}</div>
                    <table>
                        <thead>
                            <tr>
                                <th>Cédula</th>
                                <th>Nombre</th>
                                <th>Teléfono</th>
                                <th>Estado</th>
                            </tr>
                        </thead>
                        <tbody>
                ";
                foreach ($data['conductores'] as $conductor) {
                    $content .= "
                        <tr>
                            <td>{$conductor->cedula}</td>
                            <td>{$conductor->nombres} {$conductor->apellidos}</td>
                            <td>{$conductor->telefono}</td>
                            <td><span class='badge badge-success'>Activo</span></td>
                        </tr>
                    ";
                }
                $content .= '</tbody></table>';
                break;

            case 'unidades':
                $content = "
                    <div class='header'>
                        <h1>SGOA-AP - {$title}</h1>
                        <h2>Antonio Polo S.A.</h2>
                    </div>
                    <div class='subtitle'>{$subtitle}</div>
                    <table>
                        <thead>
                            <tr>
                                <th># Interno</th>
                                <th>Placa</th>
                                <th>Conductor</th>
                                <th>Estado</th>
                            </tr>
                        </thead>
                        <tbody>
                ";
                foreach ($data['unidades'] as $unidad) {
                    $conductor = $unidad->conductor?->nombres.' '.($unidad->conductor?->apellidos ?? '');
                    $conductor = $conductor ?: 'Sin asignar';
                    $estadoUnidad = $unidad->estado === 'activo' ? 'Activo' : ucfirst($unidad->estado);
                    $badgeClass = $unidad->estado === 'activo' ? 'badge-success' : 'badge-warning';
                    $content .= "
                        <tr>
                            <td>{$unidad->numero_interno}</td>
                            <td>{$unidad->placa}</td>
                            <td>{$conductor}</td>
                            <td><span class='badge {$badgeClass}'>{$estadoUnidad}</span></td>
                        </tr>
                    ";
                }
                $content .= '</tbody></table>';
                break;
        }

        $content .= "
            <div class='footer'>
                <p>Sistema de Gestión Operativa Automatizada - Antonio Polo S.A.</p>
                <p>Generado el: ".now()->format('d/m/Y H:i:s').'</p>
            </div>
        ';

        return $content;
    }

    private function generarPdfConductores($conductores)
    {
        $html = $this->getHtmlReportes('conductores', [
            'conductores' => $conductores,
            'title' => 'Reporte de Conductores',
            'subtitle' => 'Listado de conductores activos',
        ]);

        return $this->generarPdf($html, 'reporte_conductores_'.date('Y-m-d').'.pdf');
    }

    private function generarPdfUnidades($unidades)
    {
        $html = $this->getHtmlReportes('unidades', [
            'unidades' => $unidades,
            'title' => 'Reporte de Unidades',
            'subtitle' => 'Estado de la flota de unidades',
        ]);

        return $this->generarPdf($html, 'reporte_unidades_'.date('Y-m-d').'.pdf');
    }

    private function generarPdfMultas($multas, $fechaInicio, $fechaFin, $totalGeneral, $totalCobrado, $totalPendiente)
    {
        $html = $this->getHtmlReportes('sanciones', [
            'sanciones' => $multas,
            'fechaInicio' => $fechaInicio,
            'fechaFin' => $fechaFin,
            'totalGeneral' => $totalGeneral,
            'totalCobrado' => $totalCobrado,
            'totalPendiente' => $totalPendiente,
            'title' => 'Reporte de Multas',
            'subtitle' => 'Desde: '.$fechaInicio->format('d/m/Y').' Hasta: '.$fechaFin->format('d/m/Y'),
        ]);

        return $this->generarPdf($html, 'reporte_multas_'.date('Y-m-d').'.pdf');
    }

    private function generarPdf($html, $filename)
    {
        $options = new Options;
        $options->set('isRemoteEnabled', true);
        $options->set('isHtml5ParserEnabled', true);
        $options->set('defaultFont', 'Helvetica');

        $dompdf = new Dompdf($options);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();
        $dompdf->addInfo('Title', $filename);

        return response()->streamDownload(
            fn () => print ($dompdf->output()),
            $filename,
            ['Content-Type' => 'application/pdf']
        );
    }
}
