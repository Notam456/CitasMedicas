<?php

namespace App\Services\Estadistica;

use App\Http\Resources\StatResult;

interface StatCalculator
{
    /**
     * Calcula los datos estadísticos para el período dado.
     *
     * @param array $params ['fecha_desde' => string, 'fecha_hasta' => string]
     */
    public function calculate(array $params): StatResult;

    /** Tipo de visualización: 'bar', 'line', 'doughnut', 'table' */
    public function chartType(): string;

    /** Título del bloque estadístico */
    public function title(): string;
}
