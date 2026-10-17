<?php

use App\Http\Controllers\ProfileController;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', function () {
        $user = User::find(Auth::id());

        if ($user->isGerente()) {
            return redirect()->route('gerente.dashboard');
        } elseif ($user->isTarjetera()) {
            return redirect()->route('tarjetera.dashboard');
        } else {
            return redirect()->route('conductor.index');
        }
    })->name('dashboard');

    Route::get('/profile', [ProfileController::class, 'show'])->name('profile.show');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
});

require __DIR__.'/auth.php';
require __DIR__.'/gerente.php';
require __DIR__.'/tarjetera.php';
require __DIR__.'/conductor.php';
