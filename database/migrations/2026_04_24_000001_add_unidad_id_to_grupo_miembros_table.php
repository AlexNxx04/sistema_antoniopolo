<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('grupo_miembros', function (Blueprint $table) {
            $table->unsignedBigInteger('unidad_id')->nullable()->after('usuario_id');
            $table->foreign('unidad_id')->references('unidad_id')->on('unidades')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('grupo_miembros', function (Blueprint $table) {
            $table->dropForeign(['unidad_id']);
            $table->dropColumn('unidad_id');
        });
    }
};