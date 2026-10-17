<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('historial_cambios', function (Blueprint $table) {
            $table->integer('punto_id')->nullable()->after('ip_origen');
            $table->string('descripcion')->nullable()->after('punto_id');
        });
    }

    public function down(): void
    {
        Schema::table('historial_cambios', function (Blueprint $table) {
            $table->dropColumn(['punto_id', 'descripcion']);
        });
    }
};