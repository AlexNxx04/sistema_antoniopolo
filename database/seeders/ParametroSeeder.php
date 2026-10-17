<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ParametroSeeder extends Seeder
{
    public function run(): void
    {
        $parametros = [
            ['parametro' => 'intervalo_salidas', 'valor' => 15],
            ['parametro' => 'tiempo_maximo_ruta', 'valor' => 60],
            ['parametro' => 'multa_retraso', 'valor' => 10.00],
            ['parametro' => 'multa_no_presentacion', 'valor' => 15.00],
            ['parametro' => 'multa_ausencia_puntero', 'valor' => 5.00],
            ['parametro' => 'radio_geofence', 'valor' => 200],
            ['parametro' => 'tolerancia_retraso', 'valor' => 5],
            ['parametro' => 'punto_partida_guaranda', 'valor' => 'Cooperativa Salinas 214'],
            ['parametro' => 'punto_partida_salinas', 'valor' => 'Confites El Salinerito'],
            ['parametro' => 'telefono_empresa', 'valor' => '032030000'],
            ['parametro' => 'whatsapp_empresa', 'valor' => '593999999999'],
        ];

        foreach ($parametros as $param) {
            $exists = DB::table('parametros_sistema')->where('parametro', $param['parametro'])->exists();
            if (! $exists) {
                DB::table('parametros_sistema')->insert([
                    'parametro' => $param['parametro'],
                    'valor' => $param['valor'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }
}
