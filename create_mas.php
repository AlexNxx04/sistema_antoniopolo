<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

$unidades = DB::select("SELECT unidad_id, numero_interno FROM unidades WHERE punto_id = 2 AND estado = 'activo' ORDER BY numero_interno");

$count = 0;
foreach ($unidades as $u) {
    for ($i = 1; $i <= 7; $i++) {
        $conductorId = DB::table('usuarios')->insertGetId([
            'cedula' => '00000000' . ($u->numero_interno * 10 + $i),
            'nombres' => 'Conductor S' . $u->numero_interno . '_' . $i,
            'apellidos' => 'Apellido',
            'telefono' => '0999999' . str_pad($count, 5, '0', STR_PAD_LEFT),
            'correo' => 's' . $u->numero_interno . $i . '@test.com',
            'username' => 's' . $u->numero_interno . $i,
            'password_hash' => bcrypt('password'),
            'rol_id' => 3,
            'punto_id' => 2,
            'unidad_id' => $u->unidad_id,
            'activo' => 1,
        ]);
        $count++;
        echo "Creado conductor $count\n";
    }
}

echo "\nTotal: $count conductores para Salinas";