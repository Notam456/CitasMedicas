<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use Illuminate\Contracts\View\View;

class MorbilidadExport implements FromView, ShouldAutoSize, WithEvents
{
    protected $data;
    protected $especialidad;
    protected $fecha_desde;
    protected $fecha_hasta;

    public function __construct($data, $especialidad = null, $fecha_desde = null, $fecha_hasta = null)
    {
        $this->data = $data;
        $this->especialidad = $especialidad;
        $this->fecha_desde = $fecha_desde;
        $this->fecha_hasta = $fecha_hasta;
    }

    public function view(): View
    {
        return view('reportes.excel.morbilidad_excel', [
            'morbilidades' => $this->data,
            'especialidad' => $this->especialidad,
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
                    $sheet->setAutoFilter('A4:G' . $highestRow);
                }
            },
        ];
    }
}
