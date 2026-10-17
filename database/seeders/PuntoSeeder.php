<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PuntoSeeder extends Seeder
{
    public function run(): void
    {
        $puntos = [
            [
                'nombre' => 'Guaranda',
                'latitud' => -1.6033,
                'longitud' => -79.0018,
                'radio_geofence_m' => 100,
                'activo' => 1,
            ],
            [
                'nombre' => 'Salinas',
                'latitud' => -1.4000,
                'longitud' => -79.0167,
                'radio_geofence_m' => 100,
                'activo' => 1,
            ],
        ];

        foreach ($puntos as $punto) {
            DB::table('puntos')->updateOrInsert(
                ['nombre' => $punto['nombre']],
                $punto
            );
        }
    }
}
