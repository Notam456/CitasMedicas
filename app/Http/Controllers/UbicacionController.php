<?php

namespace App\Http\Controllers;

use App\Models\Estado;
use App\Models\Municipio;
use App\Models\Parroquia; // <--- AGREGA ESTA LÍNEA
use Illuminate\Http\Request;

class UbicacionController extends Controller
{
    public function getEstados()
    {
        return response()->json(Estado::all());
    }

    public function getMunicipios($estado_id)
    {
        $municipios = Municipio::where('estado_id', $estado_id)->get();
        return response()->json($municipios);
    }

    // --- AGREGA ESTA FUNCIÓN AQUÍ ABAJO ---
    public function getParroquias($municipio_id)
    {
        $parroquias = Parroquia::where('municipio_id', $municipio_id)->get();
        return response()->json($parroquias);
    }
}
