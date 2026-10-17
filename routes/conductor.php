<?php

use App\Http\Controllers\Conductor\ConductorController;
use Illuminate\Support\Facades\Route;

Route::prefix('conductor')
    ->name('conductor.')
    ->middleware(['auth', 'conductor'])
    ->group(function () {
        Route::get('/', [ConductorController::class, 'index'])->name('index');
        Route::get('/offline', [ConductorController::class, 'offline'])->name('offline');
        Route::get('/historial', [ConductorController::class, 'historial'])->name('historial');
        Route::get('/multas', [ConductorController::class, 'multas'])->name('multas');
        Route::get('/justificativos', [ConductorController::class, 'justificativos'])->name('justificativos');
        Route::post('/justificativos', [ConductorController::class, 'crearJustificativo'])->name('justificativos.crear');
        Route::get('/notificaciones', [ConductorController::class, 'notificaciones'])->name('notificaciones');
        Route::post('/notificaciones/{id}/leer', [ConductorController::class, 'marcarLeida'])->name('notificaciones.leer');

        Route::post('/confirmar-presencia', [ConductorController::class, 'confirmarPresencia'])->name('confirmarPresencia');
        Route::post('/registrar-salida', [ConductorController::class, 'registrarSalida'])->name('registrarSalida');
        Route::post('/registrar-posicion', [ConductorController::class, 'registrarPosicion'])->name('registrarPosicion');
        Route::post('/registrar-llegada', [ConductorController::class, 'registrarLlegada'])->name('registrarLlegada');
        Route::post('/sincronizar-offline', [ConductorController::class, 'sincronizarOffline'])->name('sincronizarOffline');
    });
