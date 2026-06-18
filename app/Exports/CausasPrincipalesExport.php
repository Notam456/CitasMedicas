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

                $sheet->getRowDimension(1)->setRowHeight(40);
                $sheet->getRowDimension(2)->setRowHeight(28);
                $sheet->getRowDimension(3)->setRowHeight(20);
                $sheet->getRowDimension(4)->setRowHeight(30);
                $sheet->getRowDimension(5)->setRowHeight(25);

                if ($highestRow >= 4) {
                    $sheet->setAutoFilter('A4:H' . $highestRow);
                }
            },
        ];
    }
}
