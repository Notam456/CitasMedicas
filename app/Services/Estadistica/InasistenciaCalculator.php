<?php

namespace App\Services\Estadistica;

use App\Http\Resources\StatResult;
use App\Services\ReporteService;

class InasistenciaCalculator implements StatCalculator
{
    public function calculate(array $params): StatResult
    {
        $data = ReporteService::inasistencias([
            'tipo_rango' => 'rango',
            'fecha_desde' => $params['fecha_desde'],
            'fecha_hasta' => $params['fecha_hasta'],
        ]);

        $queryData = $data['queryData'];

        usort($queryData, fn ($a, $b) => $b['total_inasistencias'] <=> $a['total_inasistencias']);
        $top = array_slice($queryData, 0, 8);

        $labels = array_column($top, 'especialidad');
        $ausenciaPaciente = array_column($top, 'ausencia_paciente');
        $ausenciaMedico = array_column($top, 'ausencia_medico');

        return new StatResult(
            title: 'Inasistencias por Especialidad',
            chartType: 'bar',
            description: 'Top 8 especialidades con más inasistencias — ausencia paciente vs médico.',
            labels: $labels,
            datasets: [
                [
                    'label' => 'Ausencia Paciente',
                    'data' => $ausenciaPaciente,
                    'backgroundColor' => 'rgba(255, 99, 132, 0.75)',
                    'borderColor' => 'rgba(255, 99, 132, 1)',
                    'borderWidth' => 1,
                ],
                [
                    'label' => 'Ausencia Médico',
                    'data' => $ausenciaMedico,
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
        return 'Inasistencias por Especialidad';
    }
}
