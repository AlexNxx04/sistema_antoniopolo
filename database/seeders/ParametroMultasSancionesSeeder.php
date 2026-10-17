<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ParametroMultasSancionesSeeder extends Seeder
{
    public function run(): void
    {
        $puntos = DB::table('puntos')->get();

        $parametrosPorPunto = [
            'multa_por_minuto_retraso' => 1,
            'sancion_no_presentacion' => 'no_operar_dia_completo',
            'sancion_ausencia_puntero' => 'operar_hasta_9am',
            'radio_geofence' => 200,
        ];

        foreach ($puntos as $punto) {
            foreach ($parametrosPorPunto as $param => $valor) {
                $exists = DB::table('parametros_sistema')
                    ->where('parametro', $param)
                    ->where('punto_id', $punto->punto_id)
                    ->exists();

                if (! $exists) {
                    DB::table('parametros_sistema')->insert([
                        'parametro' => $param,
                        'punto_id' => $punto->punto_id,
                        'valor' => $valor,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }
        }

        $parametrosGlobales = [
            'telefono_empresa' => '032030000',
            'whatsapp_empresa' => '593999999999',
        ];

        foreach ($parametrosGlobales as $param => $valor) {
            $exists = DB::table('parametros_sistema')
                ->where('parametro', $param)
                ->whereNull('punto_id')
                ->exists();

            if (! $exists) {
                DB::table('parametros_sistema')->insert([
                    'parametro' => $param,
                    'punto_id' => null,
                    'valor' => $valor,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }
}
