<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

try {
    DB::statement('ALTER TABLE turnos_diarios ADD CONSTRAINT turnos_diarios_generado_por_foreign FOREIGN KEY (generado_por) REFERENCES usuarios(usuario_id) ON DELETE SET NULL');
    echo "Foreign key added successfully!\n";
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}