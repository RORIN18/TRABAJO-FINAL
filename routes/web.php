<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\{AuthController, EstudianteController, CarreraController, InscripcionController, ParaleloController, DocenteController, MateriaController, GestionController};

Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register']);

Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('/', function () {
        return view('dashboard', [
            'totalEstudiantes' => \App\Models\Estudiante::count(),
            'totalMaterias' => \App\Models\Materia::count(),
            'totalDocentes' => \App\Models\Docente::count(),
            'totalParalelos' => \App\Models\Paralelo::count(),
            'totalInscripciones' => \App\Models\Inscripcion::count(),
            'totalCarreras' => \App\Models\Carrera::count(),
            'gestionVigente' => \App\Models\Gestion::where('vigente', true)->first(),
        ]);
    })->name('dashboard');

    Route::get('/estudiantes/create', [EstudianteController::class,'create'])->middleware('is_admin')->name('estudiantes.create');
    Route::get('/estudiantes/{estudiante}/edit', [EstudianteController::class,'edit'])->middleware('is_admin')->name('estudiantes.edit');
    Route::get('/estudiantes', [EstudianteController::class,'index'])->name('estudiantes.index');
    Route::get('/estudiantes/{estudiante}', [EstudianteController::class,'show'])->name('estudiantes.show');

    Route::get('/carreras/create', [CarreraController::class,'create'])->middleware('is_admin')->name('carreras.create');
    Route::get('/carreras/{carrera}/edit', [CarreraController::class,'edit'])->middleware('is_admin')->name('carreras.edit');
    Route::get('/carreras', [CarreraController::class,'index'])->name('carreras.index');
    Route::get('/carreras/{carrera}', [CarreraController::class,'show'])->name('carreras.show');

    Route::get('/paralelos/create', [ParaleloController::class,'create'])->middleware('is_admin')->name('paralelos.create');
    Route::get('/paralelos/{paralelo}/edit', [ParaleloController::class,'edit'])->middleware('is_admin')->name('paralelos.edit');
    Route::get('/paralelos', [ParaleloController::class,'index'])->name('paralelos.index');
    Route::get('/paralelos/docentes-disponibles', [ParaleloController::class, 'docentesDisponibles'])->middleware('is_admin')->name('paralelos.docentes-disponibles');
    Route::get('/paralelos/codigos-disponibles', [ParaleloController::class, 'codigosDisponibles'])->middleware('is_admin')->name('paralelos.codigos-disponibles');
    Route::get('/paralelos/{paralelo}', [ParaleloController::class,'show'])->name('paralelos.show');

    Route::get('/docentes/create', [DocenteController::class,'create'])->middleware('is_admin')->name('docentes.create');
    Route::get('/docentes/{docente}/edit', [DocenteController::class,'edit'])->middleware('is_admin')->name('docentes.edit');
    Route::get('/docentes', [DocenteController::class,'index'])->name('docentes.index');
    Route::get('/docentes/{docente}', [DocenteController::class,'show'])->name('docentes.show');

    Route::get('/materias/create', [MateriaController::class,'create'])->middleware('is_admin')->name('materias.create');
    Route::get('/materias/{materia}/edit', [MateriaController::class,'edit'])->middleware('is_admin')->name('materias.edit');
    Route::get('/materias', [MateriaController::class,'index'])->name('materias.index');
    Route::get('/materias/{materia}', [MateriaController::class,'show'])->name('materias.show');

    Route::get('/inscripciones/create', [InscripcionController::class, 'create'])->middleware('is_admin')->name('inscripciones.create');
    Route::get('/inscripciones/estudiantes-disponibles', [InscripcionController::class, 'estudiantesDisponibles'])->middleware('is_admin')->name('inscripciones.estudiantes-disponibles');
    Route::get('/inscripciones/paralelos-disponibles', [InscripcionController::class, 'paralelosDisponibles'])->middleware('is_admin')->name('inscripciones.paralelos-disponibles');
    Route::get('/inscripciones', [InscripcionController::class, 'index'])->name('inscripciones.index');
    Route::get('/inscripciones/{inscripcion}', [InscripcionController::class, 'show'])->name('inscripciones.show');

    Route::get('/gestiones', [GestionController::class, 'index'])->name('gestiones.index');
    Route::get('/gestiones/create', [GestionController::class, 'create'])->middleware('is_admin')->name('gestiones.create');
    Route::get('/gestiones/{gestion}/edit', [GestionController::class, 'edit'])->middleware('is_admin')->name('gestiones.edit');
    Route::get('/gestiones/{gestion}', [GestionController::class, 'show'])->name('gestiones.show');

    Route::middleware('is_admin')->group(function () {
        Route::post('/estudiantes', [EstudianteController::class, 'store'])->name('estudiantes.store');
        Route::put('/estudiantes/{estudiante}', [EstudianteController::class, 'update'])->name('estudiantes.update');
        Route::delete('/estudiantes/{estudiante}', [EstudianteController::class, 'destroy'])->name('estudiantes.destroy');

        Route::post('/carreras', [CarreraController::class, 'store'])->name('carreras.store');
        Route::put('/carreras/{carrera}', [CarreraController::class, 'update'])->name('carreras.update');
        Route::delete('/carreras/{carrera}', [CarreraController::class, 'destroy'])->name('carreras.destroy');

        Route::post('/paralelos', [ParaleloController::class, 'store'])->name('paralelos.store');
        Route::put('/paralelos/{paralelo}', [ParaleloController::class, 'update'])->name('paralelos.update');
        Route::delete('/paralelos/{paralelo}', [ParaleloController::class, 'destroy'])->name('paralelos.destroy');

        Route::post('/docentes', [DocenteController::class, 'store'])->name('docentes.store');
        Route::put('/docentes/{docente}', [DocenteController::class, 'update'])->name('docentes.update');
        Route::delete('/docentes/{docente}', [DocenteController::class, 'destroy'])->name('docentes.destroy');

        Route::post('/materias', [MateriaController::class, 'store'])->name('materias.store');
        Route::put('/materias/{materia}', [MateriaController::class, 'update'])->name('materias.update');
        Route::delete('/materias/{materia}', [MateriaController::class, 'destroy'])->name('materias.destroy');

        Route::post('/gestiones', [GestionController::class, 'store'])->name('gestiones.store');
        Route::put('/gestiones/{gestion}', [GestionController::class, 'update'])->name('gestiones.update');
        Route::delete('/gestiones/{gestion}', [GestionController::class, 'destroy'])->name('gestiones.destroy');
        Route::post('/gestiones/{gestion}/activar', [GestionController::class, 'activar'])->name('gestiones.activar');

        Route::post('/inscripciones', [InscripcionController::class, 'store'])->name('inscripciones.store');
        Route::put('/inscripciones/{inscripcion}', [InscripcionController::class, 'update'])->name('inscripciones.update');
        Route::delete('/inscripciones/{inscripcion}', [InscripcionController::class, 'destroy'])->name('inscripciones.destroy');

        Route::post('/inscripciones/{inscripcion}/nota', [InscripcionController::class, 'registrarNota'])->name('inscripciones.nota');
        Route::post('/inscripciones/{inscripcion}/asistencia', [InscripcionController::class, 'registrarAsistencia'])->name('inscripciones.asistencia');
    });
});
