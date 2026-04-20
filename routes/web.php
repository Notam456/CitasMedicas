<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\EspecialidadController;
use App\Http\Controllers\MedicoController;
use App\Http\Controllers\EstadoController;
use App\Http\Controllers\MunicipioController;
use App\Http\Controllers\ParroquiaController;
use App\Http\Controllers\UbicacionController;

Route::get('/', function () {
    return view('dashboard');
});

Route::get('/login', function () {
    return view('login');
});

Route::get('/signup', function () {
    return view('signup');
});


Route::resource('especialidades', EspecialidadController::class);
Route::resource('medicos', MedicoController::class);
Route::resource('estados', EstadoController::class);
Route::resource('municipios', MunicipioController::class);
Route::resource('parroquias', ParroquiaController::class);

// Rutas para los menús desplegables en cascada
Route::get('/municipios-por-estado/{estado_id}', [UbicacionController::class, 'getMunicipios']);
Route::get('/parroquias-por-municipio/{municipio_id}', [UbicacionController::class, 'getParroquias']);
