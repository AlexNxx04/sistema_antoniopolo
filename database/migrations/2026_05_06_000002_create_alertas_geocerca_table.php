<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('alertas_geocerca', function (Blueprint $table) {
            $table->id('alerta_id');
            $table->foreignId('salida_id')->constrained('salidas', 'salida_id');
            $table->foreignId('turno_id')->constrained('turnos_diarios', 'turno_id');
            $table->foreignId('unidad_id')->constrained('unidades', 'unidad_id');
            $table->string('tipo', 30); // 'desvio_ruta', 'fuera_geocerca', 'ingreso_geocerca'
            $table->decimal('latitud', 10, 8);
            $table->decimal('longitud', 11, 8);
            $table->decimal('distancia_m', 10, 2);
            $table->foreignId('punto_id')->nullable()->constrained('puntos', 'punto_id');
            $table->text('mensaje')->nullable();
            $table->boolean('leido')->default(false);
            $table->timestamp('registrado_at')->useCurrent();
        });
    }

    public function down()
    {
        Schema::dropIfExists('alertas_geocerca');
    }
};
