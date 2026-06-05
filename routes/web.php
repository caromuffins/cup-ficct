<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\InscripcionController;
use App\Http\Controllers\RequisitoController;
use App\Http\Controllers\PagoController;
use App\Http\Controllers\Admin\PostulanteController as AdminPostulanteController;
use App\Http\Controllers\Admin\GrupoController as AdminGrupoController;
use App\Http\Controllers\Admin\DocenteController as AdminDocenteController;
use App\Http\Controllers\Postulante\GrupoController as PostulanteGrupoController;

Route::get('/', function () {
    return redirect('/login');
});

Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::prefix('postulante')->name('postulante.')->group(function () {
        Route::get('/inscripcion', [InscripcionController::class, 'index'])->name('inscripcion.index');
        Route::post('/inscripcion', [InscripcionController::class, 'store'])->name('inscripcion.store');
        Route::get('/requisitos', [RequisitoController::class, 'index'])->name('requisitos.index');
        Route::post('/requisitos', [RequisitoController::class, 'store'])->name('requisitos.store');
        Route::get('/pago/crear',    [PagoController::class, 'crear'])->name('pago.crear');
        Route::get('/pago/exitoso',  [PagoController::class, 'exitoso'])->name('pago.exitoso');
        Route::get('/pago/cancelado',[PagoController::class, 'cancelado'])->name('pago.cancelado');
        Route::get('/grupo', [PostulanteGrupoController::class, 'index'])->name('grupo.index');
    });

    Route::prefix('admin')->name('admin.')->group(function () {
        Route::resource('postulantes', AdminPostulanteController::class);
        Route::post('requisitos/{id}/validar', [AdminPostulanteController::class, 'validarRequisito'])->name('requisitos.validar');
        Route::get('grupos', [AdminGrupoController::class, 'index'])->name('grupos.index');
        Route::post('grupos/generar', [AdminGrupoController::class, 'generar'])->name('grupos.generar');
        Route::get('grupos/{id}', [AdminGrupoController::class, 'show'])->name('grupos.show');
        Route::resource('docentes', AdminDocenteController::class);
    });

    Route::prefix('docente')->name('docente.')->group(function () {
        // rutas del docente aqui
    });
});

require __DIR__.'/auth.php';