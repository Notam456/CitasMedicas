<?php

namespace App\Http\Controllers;

use App\Models\Especialidad;
use App\Models\Medico;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

use App\Exports\MedicosExport;
use Maatwebsite\Excel\Facades\Excel;

class ReporteController extends Controller
{
    public function index()
    {
        $especialidades = Especialidad::where('estado', true)->get();
        return view('reportes.index', compact('especialidades'));
    }

    public function medicosPorEspecialidad(Request $request)
    {
        $request->validate([
            'especialidad_id' => 'nullable|exists:especialidad,id_especialidad'
        ]);

        $especialidadId = $request->especialidad_id;

        $medicos = Medico::with('especialidad')
            ->when($especialidadId, function ($query) use ($especialidadId) {
                return $query->where('id_especialidad', $especialidadId);
            })
           // ->where('estado', true)
            ->get();

        $especialidad = $especialidadId ? Especialidad::find($especialidadId) : null;

        $logoRuta = public_path('assets/img/membreteMPPS2.png');
        $logoData = base64_encode(file_get_contents($logoRuta));
        $membrete = 'data:image/png;base64,' . $logoData;

        $pdf = Pdf::loadView('reportes.medicos_por_especialidad_pdf', compact('especialidad', 'medicos', 'membrete'));

        $nombreArchivo = $especialidad ? 'medicos_' . $especialidad->nombre : 'todos_los_medicos';
        return $pdf->stream($nombreArchivo . '.pdf');
    }

    public function exportarMedicosExcel()
{
    return Excel::download(new MedicosExport, 'medicos_hospital.xlsx');
}
}
