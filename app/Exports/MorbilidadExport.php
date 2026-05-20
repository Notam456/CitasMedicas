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
        // Acepta Collection o LazyCollection
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
        // Asegurar que fecha_cita esté formateada (si no viene formateada)
        $fecha = $row->fecha_cita;
        if ($fecha && !is_string($fecha)) {
            $fecha = \Carbon\Carbon::parse($fecha)->format('d/m/Y');
        }

        return [
            $row->paciente_nombre . ' ' . $row->paciente_apellido,
            $row->paciente_cedula,
            $fecha,
            $row->especialidad_nombre,
            'Dr. ' . $row->medico_nombre . ' ' . $row->medico_apellido,
            $row->diagnostico,
            $row->morbilidad_observaciones,
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                // Estilo para encabezados (igual que en MedicosExport)
                $event->sheet->getStyle('A1:G1')->applyFromArray([
                    'font' => ['bold' => true],
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THICK,
                            'color' => ['argb' => 'FFFF0000'],
                        ],
                    ]
                ]);

                // Bordes finos a todas las celdas con datos
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