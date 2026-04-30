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

//Ruta maestros
Route::resource('paciente', PacienteController::class)->middleware('auth');
Route::resource('especialidades', EspecialidadController::class)->middleware('auth');
Route::resource('medicos', MedicoController::class)->middleware('auth');
Route::resource('estados', EstadoController::class)->middleware('auth');
Route::resource('municipios', MunicipioController::class)->middleware('auth');
Route::resource('parroquias', ParroquiaController::class)->middleware('auth');


// Dashboard y Reportes Yajure

// Route::get('/', [DashboardController::class, 'index'])->name('inicio'); // yajure: no me borren este bypass por favor!

Route::middleware('auth')->group(function () {
    Route::get('/reportes', [ReporteController::class, 'index'])->name('reportes.index');
    Route::get('/reportes/medicos-por-especialidad', [ReporteController::class, 'medicosPorEspecialidad'])->name('reportes.medicos_especialidad');
});

Route::get('/reportes/medicos/excel', [ReporteController::class, 'exportarMedicosExcel'])->name('reportes.medicos_excel');

