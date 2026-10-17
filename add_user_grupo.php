<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

try {
    DB::statement('ALTER TABLE grupo_miembros ADD COLUMN usuario_id INT NULL AFTER unidad_id');
    echo "usuario_id added to grupo_miembros!\n";
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}