<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::rename('migrations', 'migraciones');
        Schema::rename('password_reset_tokens', 'reseteo_claves');
        Schema::rename('sessions', 'sesiones');
    }

    public function down(): void
    {
        Schema::rename('migraciones', 'migrations');
        Schema::rename('reseteo_claves', 'password_reset_tokens');
        Schema::rename('sesiones', 'sessions');
    }
};
