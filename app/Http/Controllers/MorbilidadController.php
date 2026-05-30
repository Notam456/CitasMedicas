<?php

namespace App\Http\Controllers;

use App\Models\Cita;
use App\Models\Especialidad;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\MorbilidadExport;
use Carbon\Carbon;

class MorbilidadController extends Controller
{
    public function index(Request $request)
    {
        $especialidades = Especialidad::where('estado', true)->get();
    
        if ($request->ajax() && $request->has('draw')) {
            return $this->dataTableResponse($request);
        }
    
        if ($request->has('export_excel') || $request->has('export_pdf')) {
            $query = $this->buildBaseQuery($request);
            $query->orderBy('citas.fecha_cita', 'desc');
            
            if ($request->has('export_excel')) {
                $morbilidades = $query->lazy();
                return Excel::download(new MorbilidadExport($morbilidades), 'morbilidades.xlsx');
            }
            
            if ($request->has('export_pdf')) {
                $total = $query->count();
                $limite = 1000;
                if ($total > $limite) {
                    return back()->with('error', "El PDF no puede generar {$total} registros. Límite {$limite}.");
                }
                ini_set('memory_limit', '1024M');
                ini_set('max_execution_time', 300);
                $morbilidades = $query->orderBy('citas.fecha_cita', 'desc')->get();
                $membrete = public_path('assets/img/membreteMPPS2.png');
                $pdf = Pdf::loadView('reportes.morbilidad_pdf', compact('morbilidades', 'membrete'));
                return $pdf->stream('morbilidades.pdf');
            }
        }

        return view('morbilidad.index', compact('especialidades'));
    }

    public function pendientes(Request $request)
    {
        if ($request->ajax() && $request->has('draw')) {
            return $this->dataTablePendientes($request);
        }
        $especialidades = Especialidad::where('estado', true)->get();
        return view('morbilidad.pendientes', compact('especialidades'));
    }

    private function buildBaseQuery(Request $request)
    {
        $query = Cita::query()
            ->join('pacientes', 'citas.paciente_id', '=', 'pacientes.id')
            ->join('calendarios', 'citas.calendario_id', '=', 'calendarios.id')
            ->join('medicos', 'calendarios.medico_id', '=', 'medicos.id')
            ->join('especialidades', 'medicos.especialidad_id', '=', 'especialidades.id')
            ->leftJoin('cita_patologias', 'citas.id', '=', 'cita_patologias.cita_id')
            ->leftJoin('patologias', 'cita_patologias.patologia_id', '=', 'patologias.id')
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
                'citas.diagnostico_libre',
                'citas.estado'
            )
            ->where('citas.estado', 'Atendida')
            ->whereNotNull('citas.diagnostico_libre'); // al menos un diagnóstico libre o patologías

        if ($request->filled('especialidad_id')) {
            $query->where('especialidades.id', $request->especialidad_id);
        }
        if ($request->filled('fecha_desde')) {
            $query->whereDate('citas.fecha_cita', '>=', $request->fecha_desde);
        }
        if ($request->filled('fecha_hasta')) {
            $query->whereDate('citas.fecha_cita', '<=', $request->fecha_hasta);
        }
        return $query;
    }

    private function dataTableResponse(Request $request)
    {
        $query = $this->buildBaseQuery($request);
        $totalRecords = $query->count();
        if ($search = $request->get('search')['value']) {
            $query->where(function ($q) use ($search) {
                $q->where('pacientes.nombre', 'ILIKE', "%{$search}%")
                  ->orWhere('pacientes.apellido', 'ILIKE', "%{$search}%")
                  ->orWhere('pacientes.cedula', 'ILIKE', "%{$search}%")
                  ->orWhere('medicos.nombre', 'ILIKE', "%{$search}%")
                  ->orWhere('medicos.apellido', 'ILIKE', "%{$search}%")
                  ->orWhere('especialidades.nombre', 'ILIKE', "%{$search}%")
                  ->orWhere('citas.diagnostico_libre', 'ILIKE', "%{$search}%");
            });
        }
        $filteredRecords = $query->count();
        $orderColumn = $request->get('order')[0]['column'] ?? 2;
        $orderDir = $request->get('order')[0]['dir'] ?? 'desc';
        $columns = [
            0 => 'pacientes.nombre',
            1 => 'pacientes.cedula',
            2 => 'citas.fecha_cita',
            3 => 'especialidades.nombre',
            4 => 'medicos.nombre',
            5 => 'citas.diagnostico_libre'
        ];
        if (isset($columns[$orderColumn])) {
            $query->orderBy($columns[$orderColumn], $orderDir);
        } else {
            $query->orderBy('citas.fecha_cita', 'desc');
        }
        $start = $request->get('start', 0);
        $length = $request->get('length', 10);
        $data = $query->skip($start)->take($length)->get();
        $dataFormatted = [];
        foreach ($data as $row) {
            // Obtener patologías asociadas
            $cita = Cita::find($row->id);
            $patologiasNombres = $cita->patologias->pluck('nombre')->toArray();
            $diagnostico = '';
            if (!empty($patologiasNombres)) {
                $diagnostico = implode(', ', $patologiasNombres);
                if ($row->diagnostico_libre) {
                    $diagnostico .= ' - ' . $row->diagnostico_libre;
                }
            } else {
                $diagnostico = $row->diagnostico_libre ?: 'Sin diagnóstico';
            }
            $observacion = $row->cita_observacion ?: 'Asistió';
            $dataFormatted[] = [
                $row->paciente_nombre . ' ' . $row->paciente_apellido,
                $row->paciente_cedula,
                Carbon::parse($row->fecha_cita)->format('d/m/Y'),
                $row->especialidad_nombre,
                'Dr. ' . $row->medico_nombre . ' ' . $row->medico_apellido,
                $diagnostico,
                $observacion,
            ];
        }
        return response()->json([
            'draw' => intval($request->draw),
            'recordsTotal' => $totalRecords,
            'recordsFiltered' => $filteredRecords,
            'data' => $dataFormatted,
        ]);
    }

    private function buildBasePendientes(Request $request)
    {
        $query = Cita::query()
            ->join('pacientes', 'citas.paciente_id', '=', 'pacientes.id')
            ->join('calendarios', 'citas.calendario_id', '=', 'calendarios.id')
            ->join('medicos', 'calendarios.medico_id', '=', 'medicos.id')
            ->join('especialidades', 'medicos.especialidad_id', '=', 'especialidades.id')
            ->select(
                'citas.id',
                'pacientes.nombre as paciente_nombre',
                'pacientes.apellido as paciente_apellido',
                'pacientes.cedula as paciente_cedula',
                'citas.fecha_cita',
                'medicos.nombre as medico_nombre',
                'medicos.apellido as medico_apellido',
                'especialidades.nombre as especialidad_nombre'
            )
            ->where('citas.estado', 'Agendada')
            ->whereDate('citas.fecha_cita', Carbon::today());

        if ($request->filled('especialidad_id')) {
            $query->where('especialidades.id', $request->especialidad_id);
        }
        return $query;
    }

    private function dataTablePendientes(Request $request)
    {
        $query = $this->buildBasePendientes($request);
        $totalRecords = $query->count();
        if ($search = $request->get('search')['value']) {
            $query->where(function ($q) use ($search) {
                $q->where('pacientes.nombre', 'ILIKE', "%{$search}%")
                  ->orWhere('pacientes.apellido', 'ILIKE', "%{$search}%")
                  ->orWhere('pacientes.cedula', 'ILIKE', "%{$search}%")
                  ->orWhere('medicos.nombre', 'ILIKE', "%{$search}%")
                  ->orWhere('medicos.apellido', 'ILIKE', "%{$search}%")
                  ->orWhere('especialidades.nombre', 'ILIKE', "%{$search}%");
            });
        }
        $filteredRecords = $query->count();
        $orderColumn = $request->get('order')[0]['column'] ?? 2;
        $orderDir = $request->get('order')[0]['dir'] ?? 'asc';
        $columns = [
            0 => 'pacientes.nombre',
            1 => 'pacientes.cedula',
            2 => 'citas.fecha_cita',
            3 => 'especialidades.nombre',
            4 => 'medicos.nombre',
        ];
        if (isset($columns[$orderColumn])) {
            $query->orderBy($columns[$orderColumn], $orderDir);
        } else {
            $query->orderBy('citas.fecha_cita', 'asc');
        }
        $start = $request->get('start', 0);
        $length = $request->get('length', 10);
        $data = $query->skip($start)->take($length)->get();
        $dataFormatted = [];
        foreach ($data as $row) {
            $btnAtender = '<button type="button" data-id="'.$row->id.'" class="btn-atender btn btn-xs btn-square btn-primary" data-bs-toggle="modal" data-bs-target="#modalAtender"><i class="bi bi-clipboard-plus"></i></button>';
            $dataFormatted[] = [
                $row->paciente_nombre . ' ' . $row->paciente_apellido,
                $row->paciente_cedula,
                Carbon::parse($row->fecha_cita)->format('d/m/Y'),
                $row->especialidad_nombre,
                'Dr. ' . $row->medico_nombre . ' ' . $row->medico_apellido,
                $btnAtender,
            ];
        }
        return response()->json([
            'draw' => intval($request->draw),
            'recordsTotal' => $totalRecords,
            'recordsFiltered' => $filteredRecords,
            'data' => $dataFormatted,
        ]);
    }
}
