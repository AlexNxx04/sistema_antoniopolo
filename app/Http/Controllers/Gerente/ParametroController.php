<?php

namespace App\Http\Controllers\Gerente;

use App\Http\Controllers\Controller;
use App\Models\HistorialCambio;
use App\Models\Punto;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ParametroController extends Controller
{
    private $parametros = [
        'multa_por_minuto_retraso',
        'tiempo_estimado_ruta',
        'hora_inicio_operacion',
        'hora_fin_operacion',
        'telefono_empresa',
        'whatsapp_empresa',
    ];

    public static function getTiempoEspera($puntoId, $hora = null)
    {
        $hora = $hora ?? Carbon::now()->format('H:i');

        if ($puntoId == 1) {
            if ($hora >= '05:30' && $hora < '09:00') {
                return 10;
            }
            if ($hora >= '09:00' && $hora < '12:00') {
                return 15;
            }
            if ($hora >= '12:00' && $hora < '15:00') {
                return 10;
            }
            if ($hora >= '15:00' && $hora <= '19:30') {
                return 15;
            }
        } else {
            return 15;
        }

        return 15;
    }

    public static function getValorMultaPorMinuto()
    {
        $valor = self::obtenerParametroStatic('multa_por_minuto_retraso');

        return $valor !== null ? (float) $valor : 1.00;
    }

    public static function getTiempoEstimadoRuta()
    {
        $tiempo = self::obtenerParametroStatic('tiempo_estimado_ruta');

        return $tiempo !== null ? (int) $tiempo : 120;
    }

    private static function obtenerParametroStatic($clave)
    {
        $row = DB::table('parametros_operativos')->where('clave', $clave)->first();

        return $row ? $row->valor : null;
    }

    private function obtenerParametros()
    {
        $parametros = [];
        foreach ($this->parametros as $param) {
            $parametros[$param] = $this->obtenerParametroLocal($param);
        }

        return $parametros;
    }

    private function obtenerParametroLocal($clave)
    {
        $row = DB::table('parametros_operativos')->where('clave', $clave)->first();

        return $row ? $row->valor : null;
    }

    public function index(Request $request)
    {
        $puntos = Punto::where('activo', 1)->get();
        $parametros = $this->obtenerParametros();
        $puntoId = $request->punto_id ?? ($puntos->first()->punto_id ?? null);
        $puntoSelect = Punto::find($puntoId);

        return view('gerente.parametros.index', compact('parametros', 'puntos', 'puntoId', 'puntoSelect'));
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'multa_por_minuto_retraso' => 'required|numeric|min:0|max:10',
            'tiempo_estimado_ruta' => 'required|integer|min:1|max:300',
            'telefono_empresa' => 'required|string|max:20',
            'whatsapp_empresa' => 'required|string|max:20',
            'hora_inicio_operacion' => 'required|date_format:H:i',
            'hora_fin_operacion' => 'required|date_format:H:i',
        ]);

        $gerente = Auth::user();
        $oldData = $this->obtenerParametros();

        foreach ($this->parametros as $param) {
            $this->guardarParametro($param, $validated[$param], $gerente->usuario_id);
        }

        HistorialCambio::registrar(
            'parametros_operativos',
            0,
            'modificar',
            $gerente->usuario_id,
            'parametros',
            json_encode($oldData),
            json_encode($validated),
            $request->ip()
        );

        return redirect()->route('gerente.parametros.index')
            ->with('success', 'Parámetros globalizados correctamente.');
    }

    public function updatePunto(Request $request)
    {
        $validated = $request->validate([
            'punto_id' => 'required|exists:puntos,punto_id',
            'radio_geofence_m' => 'required|integer|min:10|max:1000',
            'latitud' => 'nullable|numeric|between:-90,90',
            'longitud' => 'nullable|numeric|between:-180,180',
        ]);

        $punto = Punto::find($validated['punto_id']);
        $punto->radio_geofence_m = $validated['radio_geofence_m'];
        if (isset($validated['latitud'])) {
            $punto->latitud = $validated['latitud'];
        }
        if (isset($validated['longitud'])) {
            $punto->longitud = $validated['longitud'];
        }
        $punto->save();

        HistorialCambio::registrar(
            'puntos',
            $punto->punto_id,
            'modificar',
            Auth::user()->usuario_id,
            'radio_geofence_m',
            $request->old_radio ?? '',
            $validated['radio_geofence_m'],
            $request->ip()
        );

        return redirect()->route('gerente.parametros.index', ['punto_id' => $punto->punto_id])
            ->with('success', 'Parámetros del punto actualizados correctamente.');
    }

    private function guardarParametro($clave, $valor, $updatedBy)
    {
        $exists = DB::table('parametros_operativos')->where('clave', $clave)->exists();

        if ($exists) {
            DB::table('parametros_operativos')
                ->where('clave', $clave)
                ->update(['valor' => $valor, 'updated_at' => now(), 'updated_by' => $updatedBy]);
        } else {
            $tipoDato = is_numeric($valor) ? (strpos($valor, '.') !== false ? 'decimal' : 'int') : 'string';
            DB::table('parametros_operativos')->insert([
                'clave' => $clave,
                'valor' => $valor,
                'tipo_dato' => $tipoDato,
                'updated_by' => $updatedBy,
            ]);
        }
    }
}
