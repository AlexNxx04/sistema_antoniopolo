<?php

use App\Http\Controllers\Tarjetera\DashboardController;
use App\Http\Controllers\Tarjetera\PerfilController;
use App\Http\Controllers\Tarjetera\DocumentoController;
use App\Http\Controllers\Tarjetera\UnidadController;
use App\Http\Controllers\Tarjetera\TurnoController;
use App\Http\Controllers\Tarjetera\SalidaController;
use App\Http\Controllers\Tarjetera\MultaController;
use App\Http\Controllers\Tarjetera\CicloController;
use App\Http\Controllers\Tarjetera\SorteoController;
use App\Http\Controllers\Tarjetera\HistorialController;
use App\Http\Controllers\Tarjetera\AuditoriaController;
use App\Http\Controllers\Tarjetera\CarreraController;
use App\Http\Controllers\Tarjetera\ControlColaController;
use App\Http\Controllers\Tarjetera\GPSController;
use App\Http\Controllers\Tarjetera\JustificativoController;
use App\Http\Controllers\Tarjetera\ConductorController;
use App\Http\Controllers\Gerente\NotificacionController;

use Illuminate\Support\Facades\Route;

Route::prefix('tarjetera')
    ->name('tarjetera.')
    ->middleware(['auth', 'tarjetera'])
    ->group(function () {
        // Dashboard
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
        
        // Perfil
        Route::get('/perfil', [PerfilController::class, 'index'])->name('perfil.index');
        Route::put('/perfil', [PerfilController::class, 'update'])->name('perfil.update');
        Route::put('/perfil/password', [PerfilController::class, 'updatePassword'])->name('perfil.password');
        
        // Documentos
        Route::get('/documentos', [DocumentoController::class, 'index'])->name('documentos.index');
        Route::post('/documentos/{id}/validar', [DocumentoController::class, 'validar'])->name('documentos.validar');
        
        // Unidades
        Route::get('/unidades', [UnidadController::class, 'index'])->name('unidades.index');
        
        Route::get('/conductores', [ConductorController::class, 'index'])->name('conductores.index');
        
        // Turnos
        Route::get('/turnos', [TurnoController::class, 'index'])->name('turnos.index');
        Route::post('/turnos/{id}/confirmar-presencia', [TurnoController::class, 'confirmarPresencia'])->name('turnos.confirmar');
        Route::post('/turnos/{id}/llamar', [TurnoController::class, 'llamar'])->name('turnos.llamar');
        Route::post('/turnos/{id}/bloquear', [TurnoController::class, 'bloquear'])->name('turnos.bloquear');
        Route::post('/turnos/{id}/liberar', [TurnoController::class, 'liberar'])->name('turnos.liberar');
        Route::post('/turnos/{id}/sancionar', [TurnoController::class, 'sancionar'])->name('turnos.sancionar');
        
        // Salidas
        Route::get('/salidas', [SalidaController::class, 'index'])->name('salidas.index');
        Route::get('/salidas/registrar', [SalidaController::class, 'create'])->name('salidas.create');
        Route::post('/salidas/registrar', [SalidaController::class, 'store'])->name('salidas.store');
        
        // Multas
        Route::get('/multas', [MultaController::class, 'index'])->name('multas.index');
        Route::post('/multas/calcular', [MultaController::class, 'calcular'])->name('multas.calcular');
        Route::post('/multas/{id}/cobrar', [MultaController::class, 'cobrar'])->name('multas.cobrar');
        Route::post('/multas/{id}/marcar', [MultaController::class, 'marcar'])->name('multas.marcar');
        
        // Ciclos Anuales
        Route::get('/ciclos', [CicloController::class, 'index'])->name('ciclos.index');
        Route::post('/ciclos', [CicloController::class, 'store'])->name('ciclos.store');
        Route::put('/ciclos/{id}', [CicloController::class, 'update'])->name('ciclos.update');
        Route::post('/ciclos/{id}/activar', [CicloController::class, 'activar'])->name('ciclos.activar');
        Route::post('/ciclos/{id}/desactivar', [CicloController::class, 'desactivar'])->name('ciclos.desactivar');
        
        // Sorteo Diario
        Route::get('/sorteo', [SorteoController::class, 'index'])->name('sorteo.index');
        Route::post('/sorteo/ejecutar', [SorteoController::class, 'ejecutar'])->name('sorteo.ejecutar');
        Route::post('/sorteo/crear-grupos', [SorteoController::class, 'crearGrupos'])->name('sorteo.crearGrupos');
        Route::post('/sorteo/eliminar-grupos', [SorteoController::class, 'eliminarGrupos'])->name('sorteo.eliminarGrupos');
        Route::post('/sorteo/limpiar-sorteos', [SorteoController::class, 'limpiarSorteos'])->name('sorteo.limpiarSorteos');
        Route::post('/sorteo/randomizar-dias', [SorteoController::class, 'randomizarDias'])->name('sorteo.randomizarDias');
        
        // Historial
        Route::get('/historial', [HistorialController::class, 'index'])->name('historial.index');
        Route::get('/historial/pdf', [HistorialController::class, 'pdf'])->name('historial.pdf');

        // Auditoria
        Route::get('/auditoria', [AuditoriaController::class, 'index'])->name('auditoria.index');
        Route::get('/auditoria/exportar', [AuditoriaController::class, 'exportar'])->name('auditoria.exportar');
        
        // Registrar Carrera
        Route::get('/carrera', [CarreraController::class, 'index'])->name('carrera.index');
        Route::post('/carrera/registrar', [CarreraController::class, 'registrar'])->name('carrera.registrar');
        Route::post('/carrera/finalizar/{id}', [CarreraController::class, 'finalizar'])->name('carrera.finalizar');
        
// Control de Cola
        Route::get('/control-cola', [ControlColaController::class, 'index'])->name('control-cola.index');
        Route::post('/control-cola/bloquear/{id}', [ControlColaController::class, 'bloquear'])->name('control-cola.bloquear');
        Route::post('/control-cola/sancionar/{id}', [ControlColaController::class, 'sancionar'])->name('control-cola.sancionar');
        
        // GPS - Tiempo Real
        Route::get('/gps', [GPSController::class, 'index'])->name('gps.index');
        Route::get('/gps/mapa-en-vivo', [GPSController::class, 'mapaEnVivo'])->name('gps.mapaEnVivo');
        Route::get('/gps/posiciones-en-vivo', [GPSController::class, 'posicionesEnVivo'])->name('gps.posicionesEnVivo');
        Route::get('/gps/alertas', [GPSController::class, 'alertas'])->name('gps.alertas');
        Route::post('/gps/alertas/leidas', [GPSController::class, 'marcarAlertasLeidas'])->name('gps.alertas.leidas');
        Route::post('/gps/salida', [GPSController::class, 'registrarSalida'])->name('gps.salida');
        Route::post('/gps/finalizar/{id}', [GPSController::class, 'finalizarCarrera'])->name('gps.finalizar');
        
        Route::get('/justificativos', [JustificativoController::class, 'index'])->name('justificativos.index');
        Route::post('/justificativos/{id}/aprobar', [JustificativoController::class, 'aprobar'])->name('justificativos.aprobar');
        Route::post('/justificativos/{id}/rechazar', [JustificativoController::class, 'rechazar'])->name('justificativos.rechazar');
        
        // Notificaciones
        Route::get('/notificaciones', [NotificacionController::class, 'index'])->name('notificaciones.index');
        Route::post('/notificaciones/{id}/leer', [NotificacionController::class, 'marcarLeida'])->name('notificaciones.leer');
        Route::post('/notificaciones/todas-leer', [NotificacionController::class, 'marcarTodasLeidas'])->name('notificaciones.leerTodas');
    });