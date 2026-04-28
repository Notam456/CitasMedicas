<?php

namespace App\Exports;

use App\Models\Medico;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\WithEvents;

class MedicosExport implements FromQuery, WithHeadings, WithMapping, ShouldAutoSize, WithEvents
{
    public function query()
    {
        return Medico::with('especialidad');
    }

    public function headings(): array
    {
        return [/*'ID' ,*/ 'Nombres', 'Apellidos', 'Cédula', 'Teléfono', 'Especialidad'];
    }

    public function map($medico): array
    {
        return [
           // $medico->id_medico,
            $medico->nombres,
            $medico->apellidos,
            $medico->cedula,
            $medico->telefono,
            $medico->especialidad->nombre ?? 'N/A',
        ];
    }

    public function registerEvents(): array {
        return [
            AfterSheet::class => function (AfterSheet $event){
                $event->sheet->getStyle('A1:E1')->applyFromArray([
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
                    $dataRange = 'A2:E' . $highestRow;
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