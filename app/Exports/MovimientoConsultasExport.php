<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use Illuminate\Contracts\View\View;

class MovimientoConsultasExport implements FromView, ShouldAutoSize, WithEvents
{
    protected $data;
    protected $titulo;
    protected $tipoPaciente;
    protected $fechaTexto;
    protected $columnas;
    protected $especialidadNombre;
    protected $totales;
    protected $especialidadSeleccionada;

    public function __construct($data, $titulo, $tipoPaciente, $fechaTexto, $columnas, $especialidadNombre, $totales, $especialidadSeleccionada)
    {
        $this->data = $data;
        $this->titulo = $titulo;
        $this->tipoPaciente = $tipoPaciente;
        $this->fechaTexto = $fechaTexto;
        $this->columnas = $columnas;
        $this->especialidadNombre = $especialidadNombre;
        $this->totales = $totales;
        $this->especialidadSeleccionada = $especialidadSeleccionada;
    }

    public function view(): View
    {
        return view('reportes.excel.movimiento_consultas_excel', [
            'data' => $this->data,
            'titulo' => $this->titulo,
            'tipoPaciente' => $this->tipoPaciente,
            'fechaTexto' => $this->fechaTexto,
            'columnas' => $this->columnas,
            'especialidadNombre' => $this->especialidadNombre,
            'totales' => $this->totales,
            'especialidadSeleccionada' => $this->especialidadSeleccionada,
        ]);
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet;
                $highestRow = $sheet->getHighestRow();
                $totalColumnas = count($this->columnas) + ($this->especialidadSeleccionada ? 0 : 1);
                $ultimaColumna = $this->columnLetter($totalColumnas);

                $sheet->getRowDimension(1)->setRowHeight(40);
                $sheet->getRowDimension(2)->setRowHeight(28);
                $sheet->getRowDimension(3)->setRowHeight(20);
                $sheet->getRowDimension(4)->setRowHeight(35);

                if ($highestRow >= 4) {
                    $sheet->setAutoFilter('A4:' . $ultimaColumna . $highestRow);
                }
            },
        ];
    }

    private function columnLetter($num)
    {
        $letter = '';
        while ($num > 0) {
            $mod = ($num - 1) % 26;
            $letter = chr(65 + $mod) . $letter;
            $num = intdiv($num - 1, 26);
        }
        return $letter;
    }
}
