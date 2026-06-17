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

    public function __construct($data, $titulo, $tipoPaciente, $fechaTexto)
    {
        $this->data = $data;
        $this->titulo = $titulo;
        $this->tipoPaciente = $tipoPaciente;
        $this->fechaTexto = $fechaTexto;
    }

    public function view(): View
    {
        return view('reportes.excel.movimiento_consultas_excel', [
            'data' => $this->data,
            'titulo' => $this->titulo,
            'tipoPaciente' => $this->tipoPaciente,
            'fechaTexto' => $this->fechaTexto,
        ]);
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet;
                $highestRow = $sheet->getHighestRow();
                
                $sheet->getStyle('A1:D1')->applyFromArray([
                    'font' => ['bold' => true],
                    'fill' => [
                        'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                        'startColor' => ['argb' => 'FFD3D3D3'],
                    ],
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                        ],
                    ],
                ]);
                
                $sheet->setAutoFilter('A1:D' . $highestRow);
                
                if ($highestRow >= 2) {
                    $sheet->getStyle('A2:D' . $highestRow)->applyFromArray([
                        'borders' => [
                            'allBorders' => [
                                'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                            ],
                        ],
                    ]);
                }
            },
        ];
    }
}