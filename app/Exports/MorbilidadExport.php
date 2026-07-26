<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use Illuminate\Contracts\View\View;

class MorbilidadExport implements FromView, WithEvents
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
        $showEsp = empty($this->especialidad);
        $showEstado = empty($this->estado);
        $showTipo = empty($this->tipo_paciente);
        $showFechaCita = empty($this->fecha_desde) || empty($this->fecha_hasta) || $this->fecha_desde !== $this->fecha_hasta;
        $showFechaRegistro = empty($this->fecha_registro_desde) || empty($this->fecha_registro_hasta) || $this->fecha_registro_desde !== $this->fecha_registro_hasta;

        $lastColIndex = 5 + ($showEsp ? 1 : 0) + ($showFechaCita ? 1 : 0) + ($showTipo ? 1 : 0) + ($showEstado ? 1 : 0) + ($showFechaRegistro ? 1 : 0);
        $lastColLetter = chr(65 + $lastColIndex);

        return [
            AfterSheet::class => function (AfterSheet $event) use ($lastColLetter) {
                $sheet = $event->sheet;
                $highestRow = $sheet->getHighestRow();

                $sheet->getRowDimension(1)->setRowHeight(40);
                $sheet->getRowDimension(2)->setRowHeight(28);
                $sheet->getRowDimension(3)->setRowHeight(20);
                $sheet->getRowDimension(4)->setRowHeight(35);

                $sheet->getColumnDimension('A')->setWidth(15);
                $sheet->getColumnDimension('B')->setWidth(15);
                $sheet->getColumnDimension('C')->setWidth(32);
                $sheet->getColumnDimension('D')->setWidth(22);
                $sheet->getColumnDimension('E')->setWidth(28);
                $sheet->getColumnDimension('F')->setWidth(14);
                $sheet->getColumnDimension('G')->setWidth(14);
                $sheet->getColumnDimension('H')->setWidth(14);
                $sheet->getColumnDimension('I')->setWidth(14);
                $sheet->getColumnDimension('J')->setWidth(25);
                $sheet->getColumnDimension('K')->setWidth(40);

                if ($highestRow >= 4) {
                    $sheet->setAutoFilter('A4:' . $lastColLetter . $highestRow);
                }
            },
        ];
    }
}
