<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

$unidades = DB::select("SELECT unidad_id, numero_interno, placa FROM unidades WHERE punto_id = 2 AND estado = 'activo' ORDER BY numero_interno");

foreach ($unidades as $u) {
    $nombre = 'Conductor Salinas ' . $u->numero_interno;
    
    $conductorId = DB::table('usuarios')->insertGetId([
        'cedula' => '000000000' . $u->numero_interno,
        'nombres' => $nombre,
        'apellidos' => 'Test',
        'telefono' => '099999999' . $u->numero_interno,
        'correo' => 'salinas' . $u->numero_interno . '@test.com',
        'username' => 'salinas' . $u->numero_interno,
        'password_hash' => bcrypt('password'),
        'rol_id' => 3,
        'punto_id' => 2,
        'unidad_id' => $u->unidad_id,
        'activo' => 1,
    ]);
    
    echo "Creado: {$nombre} (ID: {$conductorId}) para Unidad {$u->numero_interno}\n";
}

echo "\nListo! Ahora hay " . count($unidades) . " conductores para Salinas.";