<?php

use App\Http\Controllers\Gerente\AuditoriaController;
use App\Http\Controllers\Gerente\CicloAnualController;
use App\Http\Controllers\Gerente\DashboardController;
use App\Http\Controllers\Gerente\DocumentoController;
use App\Http\Controllers\Gerente\GpsController;
use App\Http\Controllers\Gerente\MultaController;
use App\Http\Controllers\Gerente\OperacionController;
use App\Http\Controllers\Gerente\ParametroController;
use App\Http\Controllers\Gerente\PerfilController;
use App\Http\Controllers\Gerente\ReporteController;
use App\Http\Controllers\Gerente\SorteosController;
use App\Http\Controllers\Gerente\TurnoDiarioController;
use App\Http\Controllers\Gerente\UnidadController;
use App\Http\Controllers\Gerente\UsuarioController;
use App\Http\Controllers\Gerente\VerificacionController;
use App\Http\Controllers\Gerente\NotificacionController;
use Illuminate\Support\Facades\Route;

Route::prefix('gerente')
    ->name('gerente.')
    ->middleware(['auth', 'gerente'])
    ->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
        Route::get('/verificar', [VerificacionController::class, 'index'])->name('verificar');

        Route::get('/operacion', [OperacionController::class, 'index'])->name('operacion.index');
        Route::get('/operacion/{id}', [OperacionController::class, 'show'])->name('operacion.show');

        Route::get('/sorteos', [SorteosController::class, 'index'])->name('sorteos.index');
        Route::get('/sorteos/{id}', [SorteosController::class, 'show'])->name('sorteos.show');

        Route::get('/ciclos', [CicloAnualController::class, 'index'])->name('ciclos.index');
        Route::get('/ciclos/{id}', [CicloAnualController::class, 'show'])->name('ciclos.show');
        Route::post('/ciclos/{id}/activar', [CicloAnualController::class, 'activar'])->name('ciclos.activar');

        Route::get('/turnos', [TurnoDiarioController::class, 'index'])->name('turnos.index');

        Route::get('/usuarios', [UsuarioController::class, 'index'])->name('usuarios.index');
        Route::get('/usuarios/create', [UsuarioController::class, 'create'])->name('usuarios.create');
        Route::post('/usuarios', [UsuarioController::class, 'store'])->name('usuarios.store');
        Route::get('/usuarios/{id}', [UsuarioController::class, 'show'])->name('usuarios.show');
        Route::get('/usuarios/{id}/edit', [UsuarioController::class, 'edit'])->name('usuarios.edit');
        Route::put('/usuarios/{id}', [UsuarioController::class, 'update'])->name('usuarios.update');
        Route::delete('/usuarios/{id}', [UsuarioController::class, 'destroy'])->name('usuarios.destroy');
        Route::post('/usuarios/{id}/restore', [UsuarioController::class, 'restore'])->name('usuarios.restore');
        Route::post('/usuarios/{id}/reset-password', [UsuarioController::class, 'resetPassword'])->name('usuarios.resetPassword');

        Route::get('/documentos', [DocumentoController::class, 'index'])->name('documentos.index');
        Route::get('/documentos/{id}', [DocumentoController::class, 'show'])->name('documentos.show');

        Route::get('/multas', [MultaController::class, 'index'])->name('multas.index');
        Route::get('/multas/{id}', [MultaController::class, 'show'])->name('multas.show');

        Route::get('/unidades', [UnidadController::class, 'index'])->name('unidades.index');
        Route::get('/unidades/create', [UnidadController::class, 'create'])->name('unidades.create');
        Route::post('/unidades', [UnidadController::class, 'store'])->name('unidades.store');
        Route::get('/unidades/{id}', [UnidadController::class, 'show'])->name('unidades.show');
        Route::get('/unidades/{id}/edit', [UnidadController::class, 'edit'])->name('unidades.edit');
        Route::put('/unidades/{id}', [UnidadController::class, 'update'])->name('unidades.update');
        Route::delete('/unidades/{id}', [UnidadController::class, 'destroy'])->name('unidades.destroy');
        Route::post('/unidades/{id}/reactivar', [UnidadController::class, 'reactivar'])->name('unidades.reactivar');

        Route::get('/reportes', [ReporteController::class, 'index'])->name('reportes.index');
        Route::get('/reportes/conductores', [ReporteController::class, 'conductores'])->name('reportes.conductores');
        Route::get('/reportes/unidades', [ReporteController::class, 'unidades'])->name('reportes.unidades');
        Route::get('/reportes/multas', [ReporteController::class, 'multas'])->name('reportes.multas');

        Route::get('/auditoria', [AuditoriaController::class, 'index'])->name('auditoria.index');
        Route::get('/auditoria/exportar', [AuditoriaController::class, 'exportar'])->name('auditoria.exportar');

        Route::get('/gps/mapa', [GpsController::class, 'mapa'])->name('gps.mapa');
        Route::get('/gps', [GpsController::class, 'index'])->name('gps.index');
        Route::get('/gps/posiciones-en-vivo', [GpsController::class, 'posicionesEnVivo'])->name('gps.posicionesEnVivo');
        Route::get('/gps/alertas', [GpsController::class, 'alertas'])->name('gps.alertas');
        Route::post('/gps/alertas/leidas', [GpsController::class, 'marcarAlertasLeidas'])->name('gps.alertas.leidas');

        Route::get('/parametros', [ParametroController::class, 'index'])->name('parametros.index');
        Route::post('/parametros', [ParametroController::class, 'update'])->name('parametros.update');
        Route::post('/parametros/punto', [ParametroController::class, 'updatePunto'])->name('parametros.updatePunto');

        Route::get('/perfil', [PerfilController::class, 'index'])->name('perfil.index');
        Route::put('/perfil', [PerfilController::class, 'update'])->name('perfil.update');
        Route::put('/perfil/password', [PerfilController::class, 'updatePassword'])->name('perfil.password');

        Route::get('/notificaciones', [NotificacionController::class, 'index'])->name('notificaciones.index');
        Route::get('/notificaciones/create', [NotificacionController::class, 'create'])->name('notificaciones.create');
        Route::post('/notificaciones', [NotificacionController::class, 'store'])->name('notificaciones.store');
        Route::post('/notificaciones/{id}/leer', [NotificacionController::class, 'marcarLeida'])->name('notificaciones.leer');
        Route::post('/notificaciones/todas-leer', [NotificacionController::class, 'marcarTodasLeidas'])->name('notificaciones.leerTodas');
        Route::delete('/notificaciones/{id}', [NotificacionController::class, 'destroy'])->name('notificaciones.destroy');
    });