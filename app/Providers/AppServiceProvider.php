<?php

namespace App\Providers;

use App\Models\Notificacion;
use App\Services\CedulaUserProvider;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        Auth::provider('cedula', function ($app, array $config) {
            return new CedulaUserProvider($app['hash'], $config['model']);
        });

        View::composer('layouts.gerente', function ($view) {
            $user = Auth::user();
            if ($user) {
                $notificaciones = Notificacion::where('usuario_id', $user->usuario_id)
                    ->orderBy('enviado_at', 'desc')
                    ->limit(10)
                    ->get();
                $notificacionesNoLeidas = Notificacion::where('usuario_id', $user->usuario_id)
                    ->where('leido', 0)
                    ->count();
                $view->with(compact('notificaciones', 'notificacionesNoLeidas'));
            }
        });

        View::composer('layouts.tarjetera', function ($view) {
            $user = Auth::user();
            if ($user) {
                $notificaciones = Notificacion::where('usuario_id', $user->usuario_id)
                    ->orderBy('enviado_at', 'desc')
                    ->limit(10)
                    ->get();
                $notificacionesNoLeidas = Notificacion::where('usuario_id', $user->usuario_id)
                    ->where('leido', 0)
                    ->count();
                $view->with(compact('notificaciones', 'notificacionesNoLeidas'));
            }
        });
    }
}
