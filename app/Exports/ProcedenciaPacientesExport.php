<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use Illuminate\Contracts\View\View;

class ProcedenciaPacientesExport implements FromView, ShouldAutoSize, WithEvents
{
    protected $reporteFinal;
    protected $totalesGlobales;
    protected $titulo;
    protected $fecha_desde;
    protected $fecha_hasta;

    public function __construct($reporteFinal, $totalesGlobales, $titulo, $fecha_desde, $fecha_hasta)
    {
        $this->reporteFinal = $reporteFinal;
        $this->totalesGlobales = $totalesGlobales;
        $this->titulo = $titulo;
        $this->fecha_desde = $fecha_desde;
        $this->fecha_hasta = $fecha_hasta;
    }

    public function view(): View
    {
        return view('reportes.excel.procedencia_pacientes_excel', [
            'reporteFinal' => $this->reporteFinal,
            'totalesGlobales' => $this->totalesGlobales,
            'titulo' => $this->titulo,
            'fecha_desde' => $this->fecha_desde,
            'fecha_hasta' => $this->fecha_hasta,
        ]);
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet;
                $highestRow = $sheet->getHighestRow();
                
                // Estilo para encabezados (primera fila)
                $sheet->getStyle('A1:E1')->applyFromArray([
                    'font' => ['bold' => true],
                    'fill' => [
                        'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                        'startColor' => ['argb' => 'FFD3D3D3'],
                    ],
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                            'color' => ['argb' => 'FF000000'],
                        ],
                    ],
                ]);
                
                // Aplicar bordes a todas las celdas con datos
                if ($highestRow >= 2) {
                    $dataRange = 'A2:E' . $highestRow;
                    $sheet->getStyle($dataRange)->applyFromArray([
                        'borders' => [
                            'allBorders' => [
                                'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                                'color' => ['argb' => 'FF000000'],
                            ],
                        ],
                    ]);
                }
                
                // Aplicar fondo a filas de subtotal y total
                $rows = range(2, $highestRow);
                foreach ($rows as $row) {
                    $cell = $sheet->getCell("A$row");
                    if (strpos($cell->getValue(), 'Subtotal') !== false || strpos($cell->getValue(), 'TOTAL GENERAL') !== false) {
                        $sheet->getStyle("A$row:E$row")->applyFromArray([
                            'font' => ['bold' => true],
                            'fill' => [
                                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                                'startColor' => ['argb' => 'FFD1ECF1'],
                            ],
                        ]);
                    }
                    if (strpos($cell->getValue(), 'TOTAL GENERAL') !== false) {
                        $sheet->getStyle("A$row:E$row")->applyFromArray([
                            'fill' => [
                                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                                'startColor' => ['argb' => 'FFC3E6CB'],
                            ],
                        ]);
                    }
                }
            },
        ];
    }
}
