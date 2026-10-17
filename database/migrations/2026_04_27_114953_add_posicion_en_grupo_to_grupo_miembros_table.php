<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('grupo_miembros', function (Blueprint $table) {
            $table->integer('posicion_en_grupo')->default(0)->after('miembro_id');
        });
    }

    public function down(): void
    {
        Schema::table('grupo_miembros', function (Blueprint $table) {
            $table->dropColumn('posicion_en_grupo');
        });
    }
};