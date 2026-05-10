<?php

namespace App\Http\Controllers;

use App\Models\Cita;
use App\Models\Especialidad;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\MorbilidadExport;

class MorbilidadController extends Controller
{
    public function index(Request $request)
    {
        $especialidades = Especialidad::where('estado', true)->get();

        $query = Cita::query()
            ->join('pacientes', 'citas.paciente_id', '=', 'pacientes.id')
            ->join('calendarios', 'citas.calendario_id', '=', 'calendarios.id')
            ->join('medicos', 'calendarios.medico_id', '=', 'medicos.id')
            ->join('especialidades', 'medicos.especialidad_id', '=', 'especialidades.id')
            ->leftJoin('morbilidades', 'citas.id', '=', 'morbilidades.cita_id')
            ->select(
                'citas.id',
                'pacientes.nombre as paciente_nombre',
                'pacientes.apellido as paciente_apellido',
                'pacientes.cedula as paciente_cedula',
                'citas.fecha_cita',
                'citas.observacion as cita_observacion',
                'medicos.nombre as medico_nombre',
                'medicos.apellido as medico_apellido',
                'especialidades.nombre as especialidad_nombre',
                'morbilidades.diagnostico',
                'morbilidades.observaciones as morbilidad_observaciones',
                'morbilidades.asistio'
            )
            ->where('citas.estado', 'Atendida')
            ->whereNotNull('morbilidades.id');

        if ($request->filled('especialidad_id')) {
            $query->where('especialidades.id', $request->especialidad_id);
        }
        if ($request->filled('fecha_desde')) {
            $query->whereDate('citas.fecha_cita', '>=', $request->fecha_desde);
        }
        if ($request->filled('fecha_hasta')) {
            $query->whereDate('citas.fecha_cita', '<=', $request->fecha_hasta);
        }

        $morbilidades = $query->orderBy('citas.fecha_cita', 'desc')->get();

        if ($request->has('export_excel')) {
            return Excel::download(new MorbilidadExport($morbilidades), 'morbilidades.xlsx');
        }
        if ($request->has('export_pdf')) {
            $pdf = Pdf::loadView('reportes.morbilidad_pdf', ['morbilidades' => $morbilidades]);
            return $pdf->download('morbilidades.pdf');
        }

        return view('morbilidad.index', compact('morbilidades', 'especialidades'));
    }
}
