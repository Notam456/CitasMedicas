<?php

namespace App\Services\Estadistica;

use App\Http\Resources\StatResult;
use App\Services\ReporteService;
use Carbon\Carbon;

class EficienciaCalculator implements StatCalculator
{
    public function calculate(array $params): StatResult
    {
        $fechaDesde = Carbon::now()->startOfYear()->toDateString();

        $data = ReporteService::eficienciaAtencion([
            'tipo_rango' => 'rango',
            'fecha_desde' => $fechaDesde,
            'fecha_hasta' => $params['fecha_hasta'],
        ]);

        $queryData = $data['queryData'];

        Carbon::setLocale('es');
        $labels = array_map(fn ($item) => Carbon::parse($item['mes'] . '-01')->translatedFormat('M Y'), $queryData);
        $tasaAtencion = array_column($queryData, 'tasa_atencion');
        $tasaCancelacion = array_column($queryData, 'tasa_cancelacion');
        $promedioEspera = array_column($queryData, 'promedio_dias_espera');

        return new StatResult(
            title: 'Tendencia de Eficiencia',
            chartType: 'line',
            description: 'Tasas de atención y cancelación — ' . Carbon::now()->year,
            labels: $labels,
            datasets: [
                [
                    'label' => 'Tasa Atención %',
                    'data' => $tasaAtencion,
                    'borderColor' => 'rgba(75, 192, 192, 1)',
                    'backgroundColor' => 'rgba(75, 192, 192, 0.15)',
                    'fill' => true,
                    'tension' => 0.3,
                ],
                [
                    'label' => 'Tasa Cancelación %',
                    'data' => $tasaCancelacion,
                    'borderColor' => 'rgba(255, 99, 132, 1)',
                    'backgroundColor' => 'rgba(255, 99, 132, 0.15)',
                    'fill' => true,
                    'tension' => 0.3,
                ],
            ],
            meta: [
                'totales' => $data['totales'],
                'promedio_dias_espera' => $data['totales']['promedio_dias_espera'] ?? 0,
            ]
        );
    }

    public function chartType(): string
    {
        return 'line';
    }

    public function title(): string
    {
        return 'Tendencia de Eficiencia';
    }
}
