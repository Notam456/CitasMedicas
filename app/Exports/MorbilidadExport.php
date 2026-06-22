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
    protected $tipo_paciente;
    protected $estado;
    protected $fecha_registro_desde;
    protected $fecha_registro_hasta;

    public function __construct($data, $especialidad = null, $fecha_desde = null, $fecha_hasta = null, $tipo_paciente = null, $estado = null, $fecha_registro_desde = null, $fecha_registro_hasta = null)
    {
        $this->data = $data;
        $this->especialidad = $especialidad;
        $this->fecha_desde = $fecha_desde;
        $this->fecha_hasta = $fecha_hasta;
        $this->tipo_paciente = $tipo_paciente;
        $this->estado = $estado;
        $this->fecha_registro_desde = $fecha_registro_desde;
        $this->fecha_registro_hasta = $fecha_registro_hasta;
    }

    public function view(): View
    {
        return view('reportes.excel.morbilidad_excel', [
            'morbilidades' => $this->data,
            'especialidad' => $this->especialidad,
            'fecha_desde' => $this->fecha_desde,
            'fecha_hasta' => $this->fecha_hasta,
            'tipo_paciente' => $this->tipo_paciente,
            'estado' => $this->estado,
            'fecha_registro_desde' => $this->fecha_registro_desde,
            'fecha_registro_hasta' => $this->fecha_registro_hasta,
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
                    $sheet->setAutoFilter('A4:H' . $highestRow);
                }
            },
        ];
    }
}
