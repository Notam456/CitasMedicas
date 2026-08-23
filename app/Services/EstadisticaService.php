<?php

namespace App\Services;

use App\Services\Estadistica\StatCalculator;

class EstadisticaService
{
    private array $calculators;

    public function __construct(array $calculators)
    {
        $this->calculators = $calculators;
    }

    public function ejecutar(array $params): array
    {
        $results = [];
        foreach ($this->calculators as $calculator) {
            $results[] = $calculator->calculate($params)->toArray();
        }
        return $results;
    }

    public static function default(): self
    {
        return new self([
            new \App\Services\Estadistica\ProductividadCalculator(),
            new \App\Services\Estadistica\InasistenciaCalculator(),
            new \App\Services\Estadistica\EficienciaCalculator(),
            new \App\Services\Estadistica\CausasCalculator(),
            new \App\Services\Estadistica\DemandaCalculator(),
        ]);
    }
}
