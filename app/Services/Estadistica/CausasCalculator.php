<?php

namespace App\Services\Estadistica;

use App\Http\Resources\StatResult;
use App\Services\ReporteService;

class CausasCalculator implements StatCalculator
{
    public function calculate(array $params): StatResult
    {
        $data = ReporteService::causasPrincipales([
            'tipo_rango' => 'rango',
            'fecha_desde' => $params['fecha_desde'],
            'fecha_hasta' => $params['fecha_hasta'],
        ]);

        $top10 = array_slice($data['queryData'], 0, 10);

        $labels = array_column($top10, 'patologia');
        $totales = array_column($top10, 'total');

        return new StatResult(
            title: 'Top 10 Patologías Más Frecuentes',
            chartType: 'bar',
            description: 'Patologías con mayor incidencia en el período seleccionado.',
            labels: $labels,
            datasets: [
                [
                    'label' => 'Total Consultas',
                    'data' => $totales,
                    'backgroundColor' => 'rgba(153, 102, 255, 0.7)',
                    'borderColor' => 'rgba(153, 102, 255, 1)',
                ],
            ],
            meta: [
                'detail' => $top10,
            ]
        );
    }

    public function chartType(): string
    {
        return 'bar';
    }

    public function title(): string
    {
        return 'Top 10 Patologías Más Frecuentes';
    }
}
