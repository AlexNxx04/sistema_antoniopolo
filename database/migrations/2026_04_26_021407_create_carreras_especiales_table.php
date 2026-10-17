<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('carreras_especiales', function (Blueprint $table) {
            $table->id();
            $table->foreignId('turno_id')->constrained('turnos_diarios')->onDelete('cascade');
            $table->foreignId('punto_id');
            $table->date('fecha');
            $table->dateTime('fecha_inicio');
            $table->dateTime('fecha_fin')->nullable();
            $table->integer('posicion_original');
            $table->string('tipo')->default('carrera_especial');
            $table->text('observaciones')->nullable();
            $table->foreignId('registrado_por');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('carreras_especiales');
    }
};