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
                
                // Estilo para encabezados
                $sheet->getStyle('A1:E1')->applyFromArray([
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
                
                $sheet->setAutoFilter('A1:E' . $highestRow);
                
                if ($highestRow >= 2) {
                    $sheet->getStyle('A2:E' . $highestRow)->applyFromArray([
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