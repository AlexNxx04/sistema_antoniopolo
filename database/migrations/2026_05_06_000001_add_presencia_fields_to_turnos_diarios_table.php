<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('turnos_diarios', function (Blueprint $table) {
            $table->boolean('presencia_confirmada')->default(false)->after('es_descanso');
            $table->timestamp('presencia_at')->nullable()->after('presencia_confirmada');
            $table->decimal('presencia_lat', 10, 6)->nullable()->after('presencia_at');
            $table->decimal('presencia_lng', 11, 6)->nullable()->after('presencia_lat');
            $table->boolean('es_puntero_confirmado')->nullable()->after('presencia_lng');
        });
    }

    public function down()
    {
        Schema::table('turnos_diarios', function (Blueprint $table) {
            $table->dropColumn([
                'presencia_confirmada',
                'presencia_at',
                'presencia_lat',
                'presencia_lng',
                'es_puntero_confirmado'
            ]);
        });
    }
};
