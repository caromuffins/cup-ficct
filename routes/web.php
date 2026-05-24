<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;

Route::get('/', function () {
    return redirect('/login');
});

Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    // rutas del admin aqui
});

Route::middleware(['auth', 'role:docente'])->prefix('docente')->name('docente.')->group(function () {
    // rutas del docente aqui
});

Route::middleware(['auth', 'role:postulante'])->prefix('postulante')->name('postulante.')->group(function () {
    // rutas del postulante aqui
});

require __DIR__.'/auth.php';