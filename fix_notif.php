<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

DB::statement("ALTER TABLE notificaciones MODIFY COLUMN tipo ENUM('llamado_turno','sancion','documento_vencido','alerta_general','confirmacion','llamado','bloqueo')");
echo "Tipo updated!\n";