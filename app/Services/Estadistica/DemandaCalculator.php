<?php

namespace App\Services\Estadistica;

use App\Http\Resources\StatResult;
use App\Services\ReporteService;

class DemandaCalculator implements StatCalculator
{
    public function calculate(array $params): StatResult
    {
        $data = ReporteService::movimientoConsultas([
            'tipo_rango' => 'rango',
            'fecha_desde' => $params['fecha_desde'],
            'fecha_hasta' => $params['fecha_hasta'],
            'tipo_paciente' => 'todas',
            'especialidad_id' => null,
        ]);

        $queryData = $data['queryData'];

        usort($queryData, fn ($a, $b) => ($b['agendadas'] + $b['atendidas']) <=> ($a['agendadas'] + $a['atendidas']));
        $top = array_slice($queryData, 0, 8);

        $labels = array_column($top, 'especialidad');
        $agendadas = array_column($top, 'agendadas');
        $atendidas = array_column($top, 'atendidas');

        return new StatResult(
            title: 'Demanda por Especialidad',
            chartType: 'bar',
            description: 'Top 8 especialidades con más demanda — agendadas vs atendidas.',
            labels: $labels,
            datasets: [
                [
                    'label' => 'Agendadas',
                    'data' => $agendadas,
                    'backgroundColor' => 'rgba(255, 159, 64, 0.75)',
                    'borderColor' => 'rgba(255, 159, 64, 1)',
                    'borderWidth' => 1,
                ],
                [
                    'label' => 'Atendidas',
                    'data' => $atendidas,
                    'backgroundColor' => 'rgba(54, 162, 235, 0.75)',
                    'borderColor' => 'rgba(54, 162, 235, 1)',
                    'borderWidth' => 1,
                ],
            ],
            meta: [
                'totales' => $data['totales'],
            ]
        );
    }

    public function chartType(): string
    {
        return 'bar';
    }

    public function title(): string
    {
        return 'Demanda por Especialidad';
    }
}
