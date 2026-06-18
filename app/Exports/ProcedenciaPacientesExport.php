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

                $sheet->getRowDimension(1)->setRowHeight(40);
                $sheet->getRowDimension(2)->setRowHeight(28);
                $sheet->getRowDimension(3)->setRowHeight(20);
                $sheet->getRowDimension(4)->setRowHeight(35);

                if ($highestRow >= 4) {
                    $sheet->setAutoFilter('A4:E' . $highestRow);
                }
            },
        ];
    }
}
