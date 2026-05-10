<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class MorbilidadExport implements FromCollection, WithHeadings, WithMapping
{
    protected $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function collection()
    {
        return $this->data;
    }

    public function headings(): array
    {
        return [
            'Paciente', 'Cédula', 'Fecha Cita', 'Especialidad', 'Médico', 'Diagnóstico', 'Observaciones'
        ];
    }

    public function map($row): array
    {
        return [
            $row->paciente_nombre . ' ' . $row->paciente_apellido,
            $row->paciente_cedula,
            $row->fecha_cita,
            $row->especialidad_nombre,
            'Dr. ' . $row->medico_nombre . ' ' . $row->medico_apellido,
            $row->diagnostico,
            $row->morbilidad_observaciones,
        ];
    }
}