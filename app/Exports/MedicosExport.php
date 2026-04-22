<?php

namespace App\Exports;

use App\Models\Medico;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class MedicosExport implements FromQuery, WithHeadings, WithMapping, ShouldAutoSize
{
    public function query()
    {
        return Medico::with('especialidad');
    }

    public function headings(): array
    {
        return ['ID', 'Nombres', 'Apellidos', 'Cédula', 'Teléfono', 'Especialidad'];
    }

    public function map($medico): array
    {
        return [
            $medico->id_medico,
            $medico->nombres,
            $medico->apellidos,
            $medico->cedula,
            $medico->telefono,
            $medico->especialidad->nombre ?? 'N/A',
        ];
    }
}