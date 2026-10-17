<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('ciclos_anuales', function (Blueprint $table) {
            $table->integer('vuelta_actual')->default(0)->after('activo');
        });
    }

    public function down(): void
    {
        Schema::table('ciclos_anuales', function (Blueprint $table) {
            $table->dropColumn('vuelta_actual');
        });
    }
};