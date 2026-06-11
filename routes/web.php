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
use App\Http\Controllers\Admin\HorarioController;
use App\Http\Controllers\Admin\NotaController;
use App\Http\Controllers\Admin\AdmisionController;
use App\Http\Controllers\Admin\ReporteController;
use App\Http\Controllers\Admin\ConsultaController;
use App\Http\Controllers\Docente\DashboardController as DocenteDashboardController;
use App\Http\Controllers\Postulante\GrupoController as PostulanteGrupoController;
use App\Http\Controllers\Postulante\ResultadosController as PostulanteResultadosController;

Route::get('/', function () {
    return redirect('/login');
});

Route::get('/admitidos', [AdmisionController::class, 'listaPublica'])->name('admision.lista-publica');

Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::prefix('postulante')->name('postulante.')->middleware('role:postulante')->group(function () {
        Route::get('/inscripcion', [InscripcionController::class, 'index'])->name('inscripcion.index');
        Route::post('/inscripcion', [InscripcionController::class, 'store'])->name('inscripcion.store');
        Route::get('/requisitos', [RequisitoController::class, 'index'])->name('requisitos.index');
        Route::post('/requisitos', [RequisitoController::class, 'store'])->name('requisitos.store');
        Route::get('/pago/crear',    [PagoController::class, 'crear'])->name('pago.crear');
        Route::get('/pago/exitoso',  [PagoController::class, 'exitoso'])->name('pago.exitoso');
        Route::get('/pago/cancelado',[PagoController::class, 'cancelado'])->name('pago.cancelado');
        Route::get('/grupo', [PostulanteGrupoController::class, 'index'])->name('grupo.index');
        Route::get('/notas', [PostulanteResultadosController::class, 'notas'])->name('notas.index');
        Route::get('/admision', [PostulanteResultadosController::class, 'admision'])->name('admision.index');
    });

    Route::prefix('admin')->name('admin.')->middleware('role:admin')->group(function () {
        Route::get('postulantes/importar', [AdminPostulanteController::class, 'importar'])->name('postulantes.importar');
        Route::post('postulantes/importar', [AdminPostulanteController::class, 'importarProcesar'])->name('postulantes.importar.procesar');
        Route::get('postulantes/plantilla', [AdminPostulanteController::class, 'descargarPlantilla'])->name('postulantes.plantilla');
        Route::resource('postulantes', AdminPostulanteController::class);
        Route::post('requisitos/{id}/validar', [AdminPostulanteController::class, 'validarRequisito'])->name('requisitos.validar');
        Route::get('grupos', [AdminGrupoController::class, 'index'])->name('grupos.index');
        Route::post('grupos/generar', [AdminGrupoController::class, 'generar'])->name('grupos.generar');
        Route::get('grupos/{id}', [AdminGrupoController::class, 'show'])->name('grupos.show');
        Route::resource('docentes', AdminDocenteController::class);
        Route::get('horarios', [HorarioController::class, 'index'])->name('horarios.index');
        Route::post('horarios', [HorarioController::class, 'store'])->name('horarios.store');
        Route::delete('horarios/{id}', [HorarioController::class, 'destroy'])->name('horarios.destroy');
        Route::get('notas', [NotaController::class, 'index'])->name('notas.index');
        Route::get('notas/alumnos', [NotaController::class, 'getAlumnos'])->name('notas.alumnos');
        Route::post('notas', [NotaController::class, 'store'])->name('notas.store');
        Route::get('admision', [AdmisionController::class, 'index'])->name('admision.index');
        Route::post('admision/calcular', [AdmisionController::class, 'calcular'])->name('admision.calcular');
        Route::post('admision/asignar-carreras', [AdmisionController::class, 'asignarCarreras'])->name('admision.asignarCarreras');
        Route::post('admision/publicar', [AdmisionController::class, 'publicar'])->name('admision.publicar');
        Route::get('reportes', [ReporteController::class, 'index'])->name('reportes.index');
        Route::get('reportes/aprobados', [ReporteController::class, 'aprobados'])->name('reportes.aprobados');
        Route::get('reportes/docentes', [ReporteController::class, 'docentes'])->name('reportes.docentes');
        Route::get('reportes/exportar/aprobados', [ReporteController::class, 'exportarAprobados'])->name('reportes.exportar.aprobados');
        Route::get('reportes/exportar/docentes', [ReporteController::class, 'exportarDocentes'])->name('reportes.exportar.docentes');
        Route::get('consultas', [ConsultaController::class, 'index'])->name('consultas.index');
        Route::get('consultas/ejecutar', [ConsultaController::class, 'ejecutar'])->name('consultas.ejecutar');
    });

    Route::prefix('docente')->name('docente.')->middleware('role:docente')->group(function () {
        Route::get('dashboard', [DocenteDashboardController::class, 'index'])->name('dashboard');
        Route::get('grupos', [DocenteDashboardController::class, 'grupos'])->name('grupos');
        Route::get('horario', [DocenteDashboardController::class, 'horario'])->name('horario');
        Route::get('notas', [DocenteDashboardController::class, 'notas'])->name('notas');
        Route::get('notas/alumnos', [DocenteDashboardController::class, 'getAlumnos'])->name('notas.alumnos');
        Route::post('notas', [DocenteDashboardController::class, 'storeNotas'])->name('notas.store');
    });
});

require __DIR__.'/auth.php';