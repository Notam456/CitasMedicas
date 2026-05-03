<?php

use App\Http\Controllers\CalendarioController;
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
use App\Http\Controllers\CitaController;

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
Route::get('/parroquias/{id}/edit', [ParroquiaController::class, 'edit']);
Route::get('/parroquias/{id}/show', [ParroquiaController::class, 'show']);

//Rutas para Agendar Cita
Route::get('Citas/agendar/{id}', [CitaController::class, 'create'])->name('Citas.agendar.especialidad')->middleware('auth');
Route::get('Citas/especialidad/{id}/agendar', [CitaController::class, 'createParaEspecialidad'])->name('Citas.createEspecialidad')->middleware('auth');
// Ruta API para que el formulario busque al paciente sin recargar la página
Route::get('api/paciente/buscar/{cedula}', [PacienteController::class, 'buscarPorCedula'])->name('paciente.buscar')->middleware('auth');
//Rutas resource
Route::resource('Citas', CitaController::class)->middleware('auth');
//Rutas para el select de ubicacion
Route::get('/api/municipios/{estado_id}', function($estado_id) 
{return App\Models\Municipio::where('estado_id', $estado_id)->get();});
Route::get('/api/parroquias/{municipio_id}', function($municipio_id) 
{return App\Models\Parroquia::where('municipio_id', $municipio_id)->get();});


Route::get('/calendario/medicos/{especialidad}', [CalendarioController::class, 'getMedicos']);
Route::get('/calendario/eventos', [CalendarioController::class, 'getDatosMes']);
Route::resource('calendario', CalendarioController::class)->middleware('auth');



Route::get('/municipios-por-estado/{estado_id}', [ParroquiaController::class, 'getMunicipiosPorEstado']);

// Dashboard y Reportes Yajure

// Route::get('/', [DashboardController::class, 'index'])->name('inicio'); // yajure: no me borren este bypass por favor!

Route::middleware('auth')->group(function () {
    Route::get('/reportes', [ReporteController::class, 'index'])->name('reportes.index');
    Route::get('/reportes/medicos-por-especialidad', [ReporteController::class, 'medicosPorEspecialidad'])->name('reportes.medicos_especialidad');
});

Route::get('/reportes/medicos/excel', [ReporteController::class, 'exportarMedicosExcel'])->name('reportes.medicos_excel');

