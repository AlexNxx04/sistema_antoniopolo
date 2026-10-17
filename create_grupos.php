<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

if (!Schema::hasTable('grupos_descanso')) {
    Schema::create('grupos_descanso', function ($table) {
        $table->id('grupo_id');
        $table->unsignedBigInteger('ciclo_id');
        $table->foreign('ciclo_id')->references('ciclo_id')->on('ciclos_anuales')->cascadeOnDelete();
        $table->string('nombre', 50);
        $table->enum('dia_descanso', ['lunes','martes','miercoles','jueves','viernes','sabado','domingo']);
        $table->boolean('activo')->default(true);
        $table->timestamps();
    });
    
    echo "Table grupos_descanso created!\n";
} else {
    echo "Table already exists\n";
}

if (!Schema::hasTable('grupo_usuarios')) {
    Schema::create('grupo_usuarios', function ($table) {
        $table->id();
        $table->unsignedBigInteger('grupo_id');
        $table->foreign('grupo_id')->references('grupo_id')->on('grupos_descanso')->cascadeOnDelete();
        $table->unsignedBigInteger('usuario_id');
        $table->foreign('usuario_id')->references('usuario_id')->on('usuarios')->cascadeOnDelete();
    });
    
    echo "Table grupo_usuarios created!\n";
} else {
    echo "Table already exists\n";
}