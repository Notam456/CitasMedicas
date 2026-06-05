<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\WithEvents;

class MorbilidadExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithEvents
{
    protected $data;

    public function __construct($data)
    {
        if ($data instanceof \Illuminate\Support\LazyCollection) {
            $this->data = collect($data);
        } else {
            $this->data = $data;
        }
    }

    public function collection()
    {
        return $this->data;
    }

    public function headings(): array
    {
        return ['Paciente', 'Cédula', 'Fecha Cita', 'Especialidad', 'Médico', 'Diagnóstico', 'Observaciones'];
    }

    public function map($row): array
    {
        $fecha = $row->fecha_cita;
        if ($fecha && !is_string($fecha)) {
            $fecha = \Carbon\Carbon::parse($fecha)->format('d/m/Y');
        }

        // Diagnóstico combinado (patologías + libre)
        $diagnostico = '';
        if (!empty($row->patologias_nombres)) {
            $diagnostico = $row->patologias_nombres;
            if (!empty($row->diagnostico_libre)) {
                $diagnostico .= ' - ' . $row->diagnostico_libre;
            }
        } elseif (!empty($row->diagnostico_libre)) {
            $diagnostico = $row->diagnostico_libre;
        } else {
            $diagnostico = 'Sin diagnóstico';
        }

        // Observación
        $observacion = $row->cita_observacion ?: 'Asistió';

        return [
            $row->paciente_nombre . ' ' . $row->paciente_apellido,
            $row->paciente_cedula,
            $fecha,
            $row->especialidad_nombre,
            'Dr. ' . $row->medico_nombre . ' ' . $row->medico_apellido,
            $diagnostico,
            $observacion,
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $event->sheet->getStyle('A1:G1')->applyFromArray([
                    'font' => ['bold' => true],
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THICK,
                            'color' => ['argb' => 'FFFF0000'],
                        ],
                    ]
                ]);

                $highestRow = $event->sheet->getHighestRow();
                if ($highestRow >= 2) {
                    $dataRange = 'A2:G' . $highestRow;
                    $event->sheet->getStyle($dataRange)->applyFromArray([
                        'borders' => [
                            'allBorders' => [
                                'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                                'color' => ['argb' => 'FF000000'],
                            ],
                        ],
                    ]);
                }
            },
        ];
    }
}