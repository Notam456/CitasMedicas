<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use Illuminate\Contracts\View\View;

class EficienciaAtencionExport implements FromView, ShouldAutoSize, WithEvents
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
        return view('reportes.excel.eficiencia_atencion_excel', [
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
                    $sheet->setAutoFilter('A4:L' . $highestRow);
                }

                if ($highestRow >= 5) {
                    $percentColumns = ['E', 'G', 'I', 'K', 'L'];
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
