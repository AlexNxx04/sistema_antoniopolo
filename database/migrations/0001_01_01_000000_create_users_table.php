<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('usuarios')) {
            Schema::create('usuarios', function (Blueprint $table) {
                $table->id('usuario_id');
                $table->string('cedula', 10)->unique();
                $table->string('nombres', 100);
                $table->string('apellidos', 100);
                $table->string('telefono', 15)->nullable();
                $table->string('correo', 100)->nullable();
                $table->string('username', 50)->unique()->nullable();
                $table->string('password');
                $table->unsignedBigInteger('rol_id');
                $table->unsignedBigInteger('punto_id')->nullable();
                $table->unsignedBigInteger('unidad_id')->nullable();
                $table->boolean('activo')->default(true);
                $table->timestamps();

                $table->foreign('rol_id')->references('rol_id')->on('roles')->onDelete('restrict');
                $table->foreign('punto_id')->references('punto_id')->on('puntos')->onDelete('set null');
                $table->foreign('unidad_id')->references('unidad_id')->on('unidades')->onDelete('set null');
            });
        }

        if (! Schema::hasTable('password_reset_tokens')) {
            Schema::create('password_reset_tokens', function (Blueprint $table) {
                $table->string('email')->primary();
                $table->string('token');
                $table->timestamp('created_at')->nullable();
            });
        }

        if (! Schema::hasTable('sessions')) {
            Schema::create('sessions', function (Blueprint $table) {
                $table->string('id')->primary();
                $table->foreignId('user_id')->nullable()->index();
                $table->string('ip_address', 45)->nullable();
                $table->text('user_agent')->nullable();
                $table->longText('payload');
                $table->integer('last_activity')->index();
            });
        }

        if (! Schema::hasTable('usuarios') || ! DB::table('usuarios')->where('cedula', '0999999999')->exists()) {
            DB::table('usuarios')->insert([
                'cedula' => '0999999999',
                'nombres' => 'Administrador',
                'apellidos' => 'Sistema',
                'telefono' => '0999999999',
                'correo' => 'admin@antoniopolo.com',
                'username' => 'admin',
                'password' => Hash::make('admin123'),
                'rol_id' => 1,
                'punto_id' => 1,
                'activo' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('sessions');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('usuarios');
    }
};
