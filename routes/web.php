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
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ReporteController;
use App\Http\Controllers\PacienteController;

// --- RUTAS DE INICIO Y AUTENTICACIÓN ---
Route::get('/', function () {
    return view('login');
}); 

Route::view('/login', 'login')->name('login');
Route::view('/signup', 'signup')->name('signup');
Route::post('/iniciar-sesion', [LoginController::class, 'login'])->name('iniciar-sesion');
Route::post('/validar-registro', [LoginController::class, 'register'])->name('register');
Route::get('/cerrar-sesion', [LoginController::class, 'logout'])->name('logout');

// --- RUTAS PROTEGIDAS POR AUTH ---
Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::resource('users', UserController::class);
    
    // Reportes
    Route::get('/reportes', [ReporteController::class, 'index'])->name('reportes.index');
    Route::get('/reportes/medicos-por-especialidad', [ReporteController::class, 'medicosPorEspecialidad'])->name('reportes.medicos_especialidad');
    Route::get('/reportes/medicos/excel', [ReporteController::class, 'exportarMedicosExcel'])->name('reportes.medicos_excel');
});

// --- RECURSOS DEL SISTEMA ---
Route::resource('especialidades', EspecialidadController::class);
Route::resource('medicos', MedicoController::class);
Route::resource('estados', EstadoController::class);
Route::resource('municipios', MunicipioController::class);
Route::resource('parroquias', ParroquiaController::class);

// --- RUTAS DE PACIENTES ---
// Al definirlo como resource, Laravel crea automáticamente index, create, store, etc.
Route::resource('pacientes', PacienteController::class);

// --- UBICACIONES (SELECTS EN CASCADA PARA AXIOS) ---
Route::get('/municipios-por-estado/{estado_id}', [UbicacionController::class, 'getMunicipios']);
Route::get('/parroquias-por-municipio/{municipio_id}', [UbicacionController::class, 'getParroquias']);