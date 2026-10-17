<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('turnos_diarios', function (Blueprint $table) {
            if (!Schema::hasColumn('turnos_diarios', 'generado_por')) {
                $table->unsignedBigInteger('generado_por')->nullable()->after('es_descanso');
                $table->foreign('generado_por')->references('usuario_id')->on('usuarios')->nullOnDelete();
            }
        });
    }

    public function down(): void
    {
        Schema::table('turnos_diarios', function (Blueprint $table) {
            $table->dropForeign(['generado_por']);
            $table->dropColumn('generado_por');
        });
    }
};