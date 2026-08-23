<?php

namespace App\Http\Controllers;

use App\Services\EstadisticaService;
use Carbon\Carbon;

class EstadisticaController extends Controller
{
    public function datos()
    {
        $service = EstadisticaService::default();

        $params = [
            'fecha_desde' => Carbon::now()->startOfMonth()->toDateString(),
            'fecha_hasta' => Carbon::now()->endOfMonth()->toDateString(),
        ];

        $datos = $service->ejecutar($params);

        return response()->json($datos);
    }
}
