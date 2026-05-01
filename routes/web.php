<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\EspecialidadController;
use App\Http\Controllers\MedicoController;
use App\Http\Controllers\EstadoController;
use App\Http\Controllers\MunicipioController;
use App\Http\Controllers\ParroquiaController;
use App\Http\Controllers\UbicacionController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\PacienteController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ReporteController;

use function PHPUnit\Framework\returnValue;

//Ruta de inicio
Route::get('/', function () {return view('login');}); 

//Rutas para las vistas de autenticación
Route::view('/login', 'login')->name('login');
Route::view('/signup', 'signup')->name('signup');
Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard')->middleware('auth');
Route::post('/iniciar-sesion', [LoginController::class, 'login'])->name('iniciar-sesion');
Route::post('/validar-registro', [LoginController::class, 'register'])->name('register');
Route::get('/cerrar-sesion', [LoginController::class, 'logout'])->name('logout');

//Ruta user
Route::resource('users', UserController::class)->middleware('auth');
Route::get('/users/{id}/edit', [UserController::class, 'edit']);

//Ruta maestros
Route::resource('paciente', PacienteController::class)->middleware('auth');
Route::get('/paciente/{id}/edit', [PacienteController::class, 'edit']);
Route::get('/paciente/{id}/show', [PacienteController::class, 'show']);

Route::resource('especialidades', EspecialidadController::class)->middleware('auth');
Route::get('/especialidades/{id}/edit', [EspecialidadController::class, 'edit']);
Route::get('/especialidades/{id}/show', [EspecialidadController::class, 'show']);

Route::resource('medicos', MedicoController::class)->middleware('auth');
Route::get('/medicos/{id}/edit', [MedicoController::class, 'edit']);
Route::get('/medicos/{id}/show', [MedicoController::class, 'show']);


Route::resource('estados', EstadoController::class)->middleware('auth');
Route::get('/estados/{id}/edit', [EstadoController::class, 'edit']);
Route::get('/estados/{id}/show', [EstadoController::class, 'show']);

Route::resource('municipios', MunicipioController::class)->middleware('auth');
Route::get('/municipios/{id}/edit', [MunicipioController::class, 'edit']);
Route::get('/municipios/{id}/show', [MunicipioController::class, 'show']);

Route::resource('parroquias', ParroquiaController::class)->middleware('auth');


Route::get('/municipios-por-estado/{estado_id}', [ParroquiaController::class, 'getMunicipiosPorEstado']);

// Dashboard y Reportes Yajure

// Route::get('/', [DashboardController::class, 'index'])->name('inicio'); // yajure: no me borren este bypass por favor!

Route::middleware('auth')->group(function () {
    Route::get('/reportes', [ReporteController::class, 'index'])->name('reportes.index');
    Route::get('/reportes/medicos-por-especialidad', [ReporteController::class, 'medicosPorEspecialidad'])->name('reportes.medicos_especialidad');
});

Route::get('/reportes/medicos/excel', [ReporteController::class, 'exportarMedicosExcel'])->name('reportes.medicos_excel');

