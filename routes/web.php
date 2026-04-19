<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\EstadoController;
use App\Http\Controllers\MunicipioController;
use App\Http\Controllers\ParroquiaController;
use App\Http\Controllers\UbicacionController;

// Rutas de CRUD estándar
Route::resource('estados', EstadoController::class);
Route::resource('municipios', MunicipioController::class);
Route::resource('parroquias', ParroquiaController::class);

// Rutas para los menús desplegables en cascada
Route::get('/municipios-por-estado/{estado_id}', [UbicacionController::class, 'getMunicipios']);
Route::get('/parroquias-por-municipio/{municipio_id}', [UbicacionController::class, 'getParroquias']);