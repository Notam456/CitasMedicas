<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use Illuminate\Contracts\View\View;

class CitasSinDiagnosticoExport implements FromView, ShouldAutoSize, WithEvents
{
    protected $data;
    protected $totales;
    protected $titulo;
    protected $fechaTexto;
    protected $especialidadNombre;

    public function __construct($data, $totales, $titulo, $fechaTexto, $especialidadNombre)
    {
        $this->data = $data;
        $this->totales = $totales;
        $this->titulo = $titulo;
        $this->fechaTexto = $fechaTexto;
        $this->especialidadNombre = $especialidadNombre;
    }

    public function view(): View
    {
        return view('reportes.excel.citas_sin_diagnostico_excel', [
            'data' => $this->data,
            'totales' => $this->totales,
            'titulo' => $this->titulo,
            'fechaTexto' => $this->fechaTexto,
            'especialidadNombre' => $this->especialidadNombre,
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
