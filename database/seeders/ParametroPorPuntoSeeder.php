<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ParametroPorPuntoSeeder extends Seeder
{
    public function run(): void
    {
        $puntos = DB::table('puntos')->get();
        $parametrosPorPunto = [
            'intervalo_salidas',
            'tiempo_maximo_ruta',
            'multa_retraso',
            'multa_no_presentacion',
            'multa_ausencia_puntero',
            'radio_geofence',
            'tolerancia_retraso',
        ];

        $defaults = [
            1 => [
                'intervalo_salidas' => 15,
                'tiempo_maximo_ruta' => 60,
                'multa_retraso' => 10,
                'multa_no_presentacion' => 15,
                'multa_ausencia_puntero' => 5,
                'radio_geofence' => 200,
                'tolerancia_retraso' => 5,
            ],
            2 => [
                'intervalo_salidas' => 20,
                'tiempo_maximo_ruta' => 75,
                'multa_retraso' => 10,
                'multa_no_presentacion' => 15,
                'multa_ausencia_puntero' => 5,
                'radio_geofence' => 200,
                'tolerancia_retraso' => 5,
            ],
        ];

        foreach ($puntos as $punto) {
            foreach ($parametrosPorPunto as $param) {
                $exists = DB::table('parametros_sistema')
                    ->where('parametro', $param)
                    ->where('punto_id', $punto->punto_id)
                    ->exists();

                if (! $exists) {
                    DB::table('parametros_sistema')->insert([
                        'parametro' => $param,
                        'punto_id' => $punto->punto_id,
                        'valor' => $defaults[$punto->punto_id][$param],
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }
        }
    }
}
