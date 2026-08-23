<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use Illuminate\Contracts\View\View;

class InasistenciasExport implements FromView, ShouldAutoSize, WithEvents
{
    protected $data;
    protected $totales;
    protected $titulo;
    protected $fechaTexto;

    public function __construct($data, $totales, $titulo, $fechaTexto)
    {
        $this->data = $data;
        $this->totales = $totales;
        $this->titulo = $titulo;
        $this->fechaTexto = $fechaTexto;
    }

    public function view(): View
    {
        return view('reportes.excel.inasistencias_excel', [
            'data' => $this->data,
            'totales' => $this->totales,
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

                $sheet->getRowDimension(1)->setRowHeight(40);
                $sheet->getRowDimension(2)->setRowHeight(28);
                $sheet->getRowDimension(3)->setRowHeight(20);
                $sheet->getRowDimension(4)->setRowHeight(35);

                if ($highestRow >= 4) {
                    $sheet->setAutoFilter('A4:I' . $highestRow);
                }

                // Apply percentage format to columns D, F, I (data rows start at 5)
                if ($highestRow >= 5) {
                    $percentColumns = ['D', 'F', 'I'];
                    foreach ($percentColumns as $col) {
                        $sheet->getStyle($col . '5:' . $col . $highestRow)
                            ->getNumberFormat()
                            ->setFormatCode('0.0%');
                    }
                }
            },
        ];
    }
}
