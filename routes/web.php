<?php

use App\Http\Controllers\UbicacionController;

// Añade estas si no están
Route::get('/estados', [UbicacionController::class, 'getEstados']);
Route::get('/municipios/{estado_id}', [UbicacionController::class, 'getMunicipios']);
Route::get('/parroquias/{municipio_id}', [UbicacionController::class, 'getParroquias']);