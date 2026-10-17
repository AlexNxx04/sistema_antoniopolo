<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

try {
    DB::statement('ALTER TABLE unidades ADD COLUMN telefono VARCHAR(20) NULL');
    echo "telefono added!\n";
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}