<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('roles')) {
            Schema::create('roles', function (Blueprint $table) {
                $table->id('rol_id');
                $table->string('nombre', 50)->unique();
                $table->string('descripcion', 200)->nullable();
                $table->boolean('activo')->default(true);
                $table->timestamp('created_at')->useCurrent();
            });
            DB::statement("INSERT INTO roles (nombre, descripcion) VALUES 
                ('Gerente', 'Administrador del sistema'),
                ('Tarjetera', 'Supervisora operativa'),
                ('Conductor', 'Operador de unidades')");
        }

        if (! Schema::hasTable('puntos')) {
            Schema::create('puntos', function (Blueprint $table) {
                $table->id('punto_id');
                $table->string('nombre', 100);
                $table->decimal('latitud', 10, 8)->nullable();
                $table->decimal('longitud', 11, 8)->nullable();
                $table->integer('radio_geofence_m')->default(50);
                $table->boolean('activo')->default(true);
                $table->timestamps();
            });
            DB::statement("INSERT INTO puntos (nombre, latitud, longitud) VALUES 
                ('Guaranda', -1.60330000, -79.00180000),
                ('Salinas', -1.40000000, -79.01670000)");
        }

        if (! Schema::hasTable('unidades')) {
            Schema::create('unidades', function (Blueprint $table) {
                $table->id('unidad_id');
                $table->string('placa', 10)->unique();
                $table->integer('numero_interno')->unique();
                $table->string('celular_gps', 15)->nullable();
                $table->unsignedBigInteger('punto_id');
                $table->foreign('punto_id')->references('punto_id')->on('puntos')->cascadeOnDelete();
                $table->enum('estado', ['activo', 'mantenimiento', 'dado_baja'])->default('activo');
                $table->timestamps();
            });
        }

        if (Schema::hasTable('usuarios') && ! Schema::hasColumn('usuarios', 'rol_id')) {
            Schema::table('usuarios', function (Blueprint $table) {
                $table->unsignedBigInteger('rol_id')->default(3);
                $table->foreign('rol_id')->references('rol_id')->on('roles')->restrictOnDelete();
                $table->unsignedBigInteger('punto_id')->nullable();
                $table->foreign('punto_id')->references('punto_id')->on('puntos')->nullOnDelete();
                $table->unsignedBigInteger('unidad_id')->nullable();
                $table->foreign('unidad_id')->references('unidad_id')->on('unidades')->nullOnDelete();
                $table->boolean('activo')->default(true);
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('ciclos_anuales')) {
            Schema::create('ciclos_anuales', function (Blueprint $table) {
                $table->id('ciclo_id');
                $table->year('anio')->unique();
                $table->date('fecha_inicio');
                $table->date('fecha_fin');
                $table->boolean('activo')->default(false);
                $table->unsignedBigInteger('creado_por');
                $table->foreign('creado_por')->references('usuario_id')->on('usuarios')->restrictOnDelete();
                $table->timestamps();
            });
            if (Schema::hasTable('usuarios')) {
                DB::statement("INSERT INTO ciclos_anuales (anio, fecha_inicio, fecha_fin, activo, creado_por) VALUES (2026, '2026-01-01', '2026-12-31', true, (SELECT MIN(usuario_id) FROM usuarios LIMIT 1))");
            }
        }

        if (! Schema::hasTable('parametros_operativos')) {
            Schema::create('parametros_operativos', function (Blueprint $table) {
                $table->id('parametro_id');
                $table->string('clave', 100)->unique();
                $table->string('valor', 200);
                $table->enum('tipo_dato', ['int', 'decimal', 'string'])->default('string');
                $table->string('descripcion', 300)->nullable();
                $table->unsignedBigInteger('updated_by');
                $table->foreign('updated_by')->references('usuario_id')->on('usuarios')->restrictOnDelete();
                $table->timestamps();
            });
            if (Schema::hasTable('usuarios')) {
                DB::statement("INSERT INTO parametros_operativos (clave, valor, tipo_dato, descripcion, updated_by) VALUES 
                    ('multa_por_minuto_retraso', '1.00', 'decimal', 'Valor por minuto de retraso', (SELECT MIN(usuario_id) FROM usuarios LIMIT 1)),
                    ('tiempo_estimado_ruta', '120', 'int', 'Tiempo estimado de ruta en minutos', (SELECT MIN(usuario_id) FROM usuarios LIMIT 1)),
                    ('hora_inicio_operacion', '05:30', 'string', 'Hora inicio operacion', (SELECT MIN(usuario_id) FROM usuarios LIMIT 1)),
                    ('hora_fin_operacion', '19:30', 'string', 'Hora fin operacion', (SELECT MIN(usuario_id) FROM usuarios LIMIT 1)),
                    ('telefono_empresa', '0991234567', 'string', 'Telefono empresa', (SELECT MIN(usuario_id) FROM usuarios LIMIT 1)),
                    ('whatsapp_empresa', '0991234567', 'string', 'WhatsApp empresa', (SELECT MIN(usuario_id) FROM usuarios LIMIT 1))");
            }
        }

        if (! Schema::hasTable('turnos_config')) {
            Schema::create('turnos_config', function (Blueprint $table) {
                $table->id('config_id');
                $table->unsignedBigInteger('ciclo_id');
                $table->foreign('ciclo_id')->references('ciclo_id')->on('ciclos_anuales')->cascadeOnDelete();
                $table->unsignedBigInteger('punto_id');
                $table->foreign('punto_id')->references('punto_id')->on('puntos')->cascadeOnDelete();
                $table->json('config_json');
                $table->unsignedBigInteger('creado_por')->nullable();
                $table->foreign('creado_por')->references('usuario_id')->on('usuarios')->nullOnDelete();
                $table->timestamp('creado_en')->useCurrent();
                $table->unique(['ciclo_id', 'punto_id']);
            });
        }

        if (! Schema::hasTable('grupos_descanso')) {
            Schema::create('grupos_descanso', function (Blueprint $table) {
                $table->id('grupo_id');
                $table->unsignedBigInteger('ciclo_id');
                $table->foreign('ciclo_id')->references('ciclo_id')->on('ciclos_anuales')->cascadeOnDelete();
                $table->string('nombre', 50);
                $table->integer('dia_descanso')->comment('0=Domingo, 1=Lunes...');
                $table->boolean('activo')->default(true);
                $table->unsignedBigInteger('creado_por')->nullable();
                $table->foreign('creado_por')->references('usuario_id')->on('usuarios')->nullOnDelete();
                $table->timestamp('creado_en')->useCurrent();
            });
        }

        if (! Schema::hasTable('grupo_miembros')) {
            Schema::create('grupo_miembros', function (Blueprint $table) {
                $table->id('miembro_id');
                $table->unsignedBigInteger('grupo_id');
                $table->foreign('grupo_id')->references('grupo_id')->on('grupos_descanso')->cascadeOnDelete();
                $table->unsignedBigInteger('usuario_id');
                $table->foreign('usuario_id')->references('usuario_id')->on('usuarios')->cascadeOnDelete();
            });
        }

        if (! Schema::hasTable('turnos_diarios')) {
            Schema::create('turnos_diarios', function (Blueprint $table) {
                $table->id('turno_id');
                $table->unsignedBigInteger('ciclo_id');
                $table->foreign('ciclo_id')->references('ciclo_id')->on('ciclos_anuales')->cascadeOnDelete();
                $table->date('fecha');
                $table->unsignedBigInteger('punto_id');
                $table->foreign('punto_id')->references('punto_id')->on('puntos')->cascadeOnDelete();
                $table->unsignedBigInteger('usuario_id');
                $table->foreign('usuario_id')->references('usuario_id')->on('usuarios')->cascadeOnDelete();
                $table->unsignedBigInteger('unidad_id');
                $table->foreign('unidad_id')->references('unidad_id')->on('unidades')->cascadeOnDelete();
                $table->integer('posicion');
                $table->boolean('es_puntero')->default(false);
                $table->boolean('es_descanso')->default(false);
                $table->unsignedBigInteger('generado_por');
                $table->foreign('generado_por')->references('usuario_id')->on('usuarios')->restrictOnDelete();
                $table->timestamp('generado_at')->useCurrent();
                $table->boolean('bloqueado')->default(false);
                $table->string('justificacion_retraso', 500)->nullable();
                $table->time('hora_limite')->nullable();
                $table->string('motivo_bloqueo', 255)->nullable();
                $table->unsignedBigInteger('bloqueado_por')->nullable();
                $table->foreign('bloqueado_por')->references('usuario_id')->on('usuarios')->nullOnDelete();
                $table->timestamp('bloqueado_en')->nullable();
                $table->unique(['fecha', 'punto_id', 'posicion'], 'uq_turno_posicion');
                $table->unique(['fecha', 'punto_id', 'usuario_id'], 'uq_turno_conductor');
                $table->index(['fecha', 'punto_id']);
            });
        }

        if (! Schema::hasTable('documentos')) {
            Schema::create('documentos', function (Blueprint $table) {
                $table->id('documento_id');
                $table->unsignedBigInteger('usuario_id');
                $table->foreign('usuario_id')->references('usuario_id')->on('usuarios')->cascadeOnDelete();
                $table->enum('tipo', ['licencia', 'matricula']);
                $table->string('numero', 50)->nullable();
                $table->date('fecha_emision')->nullable();
                $table->date('fecha_vencimiento');
                $table->enum('estado', ['vigente', 'vencido'])->default('vigente');
                $table->unsignedBigInteger('updated_by');
                $table->foreign('updated_by')->references('usuario_id')->on('usuarios')->restrictOnDelete();
                $table->timestamps();
                $table->unique(['usuario_id', 'tipo'], 'uq_documento_tipo');
            });
        }

        if (! Schema::hasTable('salidas')) {
            Schema::create('salidas', function (Blueprint $table) {
                $table->id('salida_id');
                $table->unsignedBigInteger('turno_id');
                $table->foreign('turno_id')->references('turno_id')->on('turnos_diarios')->cascadeOnDelete();
                $table->unsignedBigInteger('punto_origen_id');
                $table->foreign('punto_origen_id')->references('punto_id')->on('puntos')->restrictOnDelete();
                $table->timestamp('hora_salida');
                $table->unsignedBigInteger('registrado_por');
                $table->foreign('registrado_por')->references('usuario_id')->on('usuarios')->restrictOnDelete();
                $table->boolean('registro_auto')->default(false);
                $table->boolean('es_excepcion')->default(false);
                $table->string('motivo_excepcion', 200)->nullable();
                $table->boolean('infraccion_intervalo')->default(false);
            });
        }

        if (! Schema::hasTable('llegadas_gps')) {
            Schema::create('llegadas_gps', function (Blueprint $table) {
                $table->id('llegada_id');
                $table->unsignedBigInteger('salida_id');
                $table->foreign('salida_id')->references('salida_id')->on('salidas')->cascadeOnDelete();
                $table->unsignedBigInteger('punto_destino_id');
                $table->foreign('punto_destino_id')->references('punto_id')->on('puntos')->restrictOnDelete();
                $table->timestamp('hora_llegada');
                $table->integer('tiempo_trayecto_min');
                $table->boolean('es_offline')->default(false);
                $table->timestamp('sincronizado_at')->nullable();
            });
        }

        if (! Schema::hasTable('posiciones_gps')) {
            Schema::create('posiciones_gps', function (Blueprint $table) {
                $table->id('posicion_id');
                $table->unsignedBigInteger('salida_id');
                $table->foreign('salida_id')->references('salida_id')->on('salidas')->cascadeOnDelete();
                $table->decimal('latitud', 10, 8);
                $table->decimal('longitud', 11, 8);
                $table->decimal('velocidad', 6, 2)->nullable();
                $table->timestamp('timestamp');
                $table->boolean('es_offline')->default(false);
                $table->index(['salida_id', 'timestamp']);
            });
        }

        if (! Schema::hasTable('multas')) {
            Schema::create('multas', function (Blueprint $table) {
                $table->id('multa_id');
                $table->unsignedBigInteger('usuario_id');
                $table->foreign('usuario_id')->references('usuario_id')->on('usuarios')->cascadeOnDelete();
                $table->unsignedBigInteger('turno_id')->nullable();
                $table->foreign('turno_id')->references('turno_id')->on('turnos_diarios')->nullOnDelete();
                $table->unsignedBigInteger('salida_id')->nullable();
                $table->foreign('salida_id')->references('salida_id')->on('salidas')->nullOnDelete();
                $table->unsignedBigInteger('llegada_id')->nullable();
                $table->foreign('llegada_id')->references('llegada_id')->on('llegadas_gps')->nullOnDelete();
                $table->enum('tipo', ['retraso_llegada', 'ausencia', 'documento_vencido', 'indisciplina']);
                $table->decimal('monto', 10, 2);
                $table->integer('minutos_retraso')->nullable();
                $table->text('descripcion')->nullable();
                $table->enum('estado', ['pendiente', 'cobrada', 'anulada'])->default('pendiente');
                $table->unsignedBigInteger('registrado_por')->nullable();
                $table->foreign('registrado_por')->references('usuario_id')->on('usuarios')->nullOnDelete();
                $table->timestamp('registrado_en')->useCurrent();
                $table->unsignedBigInteger('cobrado_por')->nullable();
                $table->foreign('cobrado_por')->references('usuario_id')->on('usuarios')->nullOnDelete();
                $table->timestamp('cobrado_en')->nullable();
                $table->index(['usuario_id', 'estado']);
            });
        }

        if (! Schema::hasTable('historial_cambios')) {
            Schema::create('historial_cambios', function (Blueprint $table) {
                $table->id('historial_id');
                $table->string('tabla_afectada', 100);
                $table->integer('registro_id');
                $table->string('accion', 20);
                $table->string('campo_modificado', 100)->nullable();
                $table->text('valor_anterior')->nullable();
                $table->text('valor_nuevo')->nullable();
                $table->unsignedBigInteger('usuario_id');
                $table->foreign('usuario_id')->references('usuario_id')->on('usuarios')->restrictOnDelete();
                $table->timestamp('timestamp')->useCurrent();
                $table->string('ip_origen', 45)->nullable();
                $table->index(['tabla_afectada', 'timestamp']);
            });
        }

        if (! Schema::hasTable('notificaciones')) {
            Schema::create('notificaciones', function (Blueprint $table) {
                $table->id('notif_id');
                $table->unsignedBigInteger('usuario_id');
                $table->foreign('usuario_id')->references('usuario_id')->on('usuarios')->cascadeOnDelete();
                $table->enum('tipo', ['llamado_turno', 'sancion', 'documento_vencido', 'alerta_general']);
                $table->text('mensaje');
                $table->timestamp('enviado_at')->useCurrent();
                $table->boolean('leido')->default(false);
                $table->timestamp('leido_at')->nullable();
                $table->integer('referencia_id')->nullable();
                $table->string('referencia_tabla', 50)->nullable();
            });
        }

        if (! Schema::hasTable('justificativos')) {
            Schema::create('justificativos', function (Blueprint $table) {
                $table->id('justificativo_id');
                $table->unsignedBigInteger('turno_id');
                $table->foreign('turno_id')->references('turno_id')->on('turnos_diarios')->cascadeOnDelete();
                $table->enum('tipo', ['inasistencia', 'retraso', 'sustitucion']);
                $table->string('motivo', 300);
                $table->string('sustituto_nombre', 100)->nullable();
                $table->string('sustituto_cedula', 10)->nullable();
                $table->string('sustituto_relacion', 100)->nullable();
                $table->string('documento_url', 500)->nullable();
                $table->unsignedBigInteger('registrado_by');
                $table->foreign('registrado_by')->references('usuario_id')->on('usuarios')->restrictOnDelete();
                $table->timestamp('registrado_at')->useCurrent();
                $table->unsignedBigInteger('revisado_by')->nullable();
                $table->foreign('revisado_by')->references('usuario_id')->on('usuarios')->nullOnDelete();
                $table->timestamp('revisado_at')->nullable();
            });
        }

        if (! Schema::hasTable('presencia_punteros')) {
            Schema::create('presencia_punteros', function (Blueprint $table) {
                $table->id('presencia_id');
                $table->unsignedBigInteger('turno_id');
                $table->foreign('turno_id')->references('turno_id')->on('turnos_diarios')->cascadeOnDelete();
                $table->boolean('presente');
                $table->timestamp('hora_confirmacion');
                $table->unsignedBigInteger('confirmado_by');
                $table->foreign('confirmado_by')->references('usuario_id')->on('usuarios')->restrictOnDelete();
            });
        }

        if (! Schema::hasTable('intervalos_salida')) {
            Schema::create('intervalos_salida', function (Blueprint $table) {
                $table->id('intervalo_id');
                $table->unsignedBigInteger('punto_id');
                $table->foreign('punto_id')->references('punto_id')->on('puntos')->cascadeOnDelete();
                $table->string('dia_semana', 20)->default('1,2,3,4,5,6');
                $table->time('hora_inicio');
                $table->time('hora_fin');
                $table->integer('intervalo_min')->nullable();
                $table->unsignedBigInteger('updated_by');
                $table->foreign('updated_by')->references('usuario_id')->on('usuarios')->restrictOnDelete();
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('carreras_especiales')) {
            Schema::create('carreras_especiales', function (Blueprint $table) {
                $table->id('carrera_id');
                $table->unsignedBigInteger('turno_id');
                $table->foreign('turno_id')->references('turno_id')->on('turnos_diarios')->cascadeOnDelete();
                $table->timestamp('hora_salida_especial');
                $table->timestamp('hora_regreso')->nullable();
                $table->integer('posicion_antes');
                $table->integer('posicion_despues')->nullable();
                $table->unsignedBigInteger('registrado_by');
                $table->foreign('registrado_by')->references('usuario_id')->on('usuarios')->restrictOnDelete();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('carreras_especiales');
        Schema::dropIfExists('intervalos_salida');
        Schema::dropIfExists('presencia_punteros');
        Schema::dropIfExists('justificativos');
        Schema::dropIfExists('notificaciones');
        Schema::dropIfExists('historial_cambios');
        Schema::dropIfExists('multas');
        Schema::dropIfExists('posiciones_gps');
        Schema::dropIfExists('llegadas_gps');
        Schema::dropIfExists('salidas');
        Schema::dropIfExists('documentos');
        Schema::dropIfExists('turnos_diarios');
        Schema::dropIfExists('grupo_miembros');
        Schema::dropIfExists('grupos_descanso');
        Schema::dropIfExists('turnos_config');
        Schema::dropIfExists('parametros_operativos');
        Schema::dropIfExists('ciclos_anuales');
        if (Schema::hasColumn('usuarios', 'rol_id')) {
            Schema::table('usuarios', function (Blueprint $table) {
                $table->dropForeign(['rol_id']);
                $table->dropColumn(['rol_id', 'punto_id', 'unidad_id', 'activo']);
            });
        }
        Schema::dropIfExists('unidades');
        Schema::dropIfExists('puntos');
        Schema::dropIfExists('roles');
    }
};
