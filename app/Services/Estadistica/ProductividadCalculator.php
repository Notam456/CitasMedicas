<?php

namespace App\Services\Estadistica;

use App\Http\Resources\StatResult;
use App\Services\ReporteService;
use Carbon\Carbon;

class ProductividadCalculator implements StatCalculator
{
    public function calculate(array $params): StatResult
    {
        $data = ReporteService::productividadMedico([
            'tipo_rango' => 'rango',
            'fecha_desde' => $params['fecha_desde'],
            'fecha_hasta' => $params['fecha_hasta'],
        ]);

        $all = $data['queryData'];

        $top5 = array_slice($all, 0, 5);
        $bottom5 = array_reverse(array_slice($all, -5));

        return new StatResult(
            title: 'Productividad por Médico',
            chartType: 'table',
            description: 'Médicos con mayor y menor tasa de atención en el período seleccionado.',
            labels: [],
            datasets: [],
            meta: [
                'top5' => $top5,
                'bottom5' => $bottom5,
                'total_medicos' => count($all),
                'totales' => $data['totales'],
            ]
        );
    }

    public function chartType(): string
    {
        return 'table';
    }

    public function title(): string
    {
        return 'Productividad por Médico';
    }
}
