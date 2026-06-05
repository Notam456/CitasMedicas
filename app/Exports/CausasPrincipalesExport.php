<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use Illuminate\Contracts\View\View;

class CausasPrincipalesExport implements FromView, ShouldAutoSize, WithEvents
{
    protected $data;
    protected $titulo;
    protected $fechaTexto;

    public function __construct($data, $titulo, $fechaTexto)
    {
        $this->data = $data;
        $this->titulo = $titulo;
        $this->fechaTexto = $fechaTexto;
    }

    public function view(): View
    {
        return view('reportes.excel.causas_principales_excel', [
            'data' => $this->data,
            'titulo' => $this->titulo,
            'fechaTexto' => $this->fechaTexto,
        ]);
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet;
                $highestRow = $sheet->getHighestRow();

                $sheet->getStyle('A1:H2')->applyFromArray([
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

                if ($highestRow >= 3) {
                    $dataRange = 'A3:H' . $highestRow;
                    $sheet->getStyle($dataRange)->applyFromArray([
                        'borders' => [
                            'allBorders' => [
                                'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                                'color' => ['argb' => 'FF000000'],
                            ],
                        ],
                    ]);
                }

                $rows = range(2, $highestRow);
                foreach ($rows as $row) {
                    $cell = $sheet->getCell("A$row");
                    if (strpos($cell->getValue(), 'TOTAL GENERAL') !== false) {
                        $sheet->getStyle("A$row:H$row")->applyFromArray([
                            'font' => ['bold' => true],
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