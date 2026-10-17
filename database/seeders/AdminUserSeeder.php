<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        $exists = DB::table('usuarios')
            ->where('cedula', '0999999999')
            ->exists();

        if ($exists) {
            // Si ya existe, solo actualiza la contraseña
            DB::table('usuarios')
                ->where('cedula', '0999999999')
                ->update([
                    'password'   => '$2y$12$Rog9gLaIltqXgrcfbouZMekqqxM.cqLdcX1PiWX9kFwkWVBvToczC',
                    'updated_at' => now(),
                ]);

            $this->command->info('Contraseña del administrador actualizada correctamente.');
        } else {
            // Si no existe, lo inserta desde cero
            DB::table('usuarios')->insert([
                'cedula'     => '0999999999',
                'nombres'    => 'Administrador',
                'apellidos'  => 'Sistema',
                'telefono'   => '0999999999',
                'correo'     => 'admin@antoniopolo.com',
                'username'   => 'admin',
                'password'   => '$2y$12$Rog9gLaIltqXgrcfbouZMekqqxM.cqLdcX1PiWX9kFwkWVBvToczC',
                'rol_id'     => 1,
                'punto_id'   => 1,
                'unidad_id'  => null,
                'activo'     => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $this->command->info('Usuario administrador creado correctamente.');
        }
    }
}
