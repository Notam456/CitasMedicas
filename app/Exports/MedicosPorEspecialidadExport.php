<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use Illuminate\Contracts\View\View;

class MedicosPorEspecialidadExport implements FromView, ShouldAutoSize, WithEvents
{
    protected $medicos;
    protected $especialidad;
    protected $titulo;

    public function __construct($medicos, $especialidad, $titulo)
    {
        $this->medicos = $medicos;
        $this->especialidad = $especialidad;
        $this->titulo = $titulo;
    }

    public function view(): View
    {
        return view('reportes.excel.medicos_por_especialidad_excel', [
            'medicos' => $this->medicos,
            'especialidad' => $this->especialidad,
            'titulo' => $this->titulo,
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
