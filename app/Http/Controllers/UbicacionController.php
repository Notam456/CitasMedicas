<?php

namespace App\Http\Controllers;

use App\Models\Estado;
use App\Models\Municipio;
use App\Models\Parroquia;
use Illuminate\Http\Request;

class UbicacionController extends Controller
{
    public function getEstados()
    {
        return response()->json(Estado::all());
    }

    public function getMunicipios($estado_id)
    {
        // Usamos where para filtrar por el ID del estado seleccionado
        $municipios = Municipio::where('estado_id', $estado_id)->get();
        return response()->json($municipios);
    }

    public function getParroquias($municipio_id)
    {
        // Filtramos las parroquias por el ID del municipio
        $parroquias = Parroquia::where('municipio_id', $municipio_id)->get();
        return response()->json($parroquias);
    }
}
