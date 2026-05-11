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
    
        // Si es AJAX de DataTables, devuelve la respuesta paginada (sin cambios)
        if ($request->ajax() && $request->has('draw')) {
            return $this->dataTableResponse($request);
        }
    
        // Para exportaciones NO usamos get() sobre toda la tabla, sino lazy
        if ($request->has('export_excel') || $request->has('export_pdf')) {
            $query = $this->buildBaseQuery($request);
            $query->orderBy('citas.fecha_cita', 'desc');
            
            // Opción 1: usar lazy() para evitar cargar todo en memoria
            $morbilidades = $query->lazy(); // o ->cursor()
            
            if ($request->has('export_excel')) {
                return Excel::download(new MorbilidadExport($morbilidades), 'morbilidades.xlsx');
            }
            
            if ($request->has('export_pdf')) {
                $query = $this->buildBaseQuery($request);
                $total = $query->count();
                
                // Límite seguro para PDF (ajústalo según tu servidor)
                $limite = 1000;
                
                if ($total > $limite) {
                    return back()->with('error', "El PDF no puede generar {$total} registros. El límite es {$limite}. Aplica filtros o descarga el Excel.");
                }
                
                ini_set('memory_limit', '1024M');
                ini_set('max_execution_time', 300);
                
                $morbilidades = $query->orderBy('citas.fecha_cita', 'desc')->get();
                $pdf = Pdf::loadView('reportes.morbilidad_pdf', ['morbilidades' => $morbilidades]);
                return $pdf->download('morbilidades.pdf');
            }
        }

        return view('morbilidad.index', compact('especialidades'));
    }

    private function buildBaseQuery(Request $request)
    {
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

        return $query;
    }

    private function dataTableResponse(Request $request)
    {
        $query = $this->buildBaseQuery($request);
        
        $totalRecords = $query->count(); // conteo total sin paginar
        
        // Búsqueda global
        if ($search = $request->get('search')['value']) {
            $query->where(function ($q) use ($search) {
                $q->where('pacientes.nombre', 'ILIKE', "%{$search}%")
                  ->orWhere('pacientes.apellido', 'ILIKE', "%{$search}%")
                  ->orWhere('pacientes.cedula', 'ILIKE', "%{$search}%")
                  ->orWhere('medicos.nombre', 'ILIKE', "%{$search}%")
                  ->orWhere('medicos.apellido', 'ILIKE', "%{$search}%")
                  ->orWhere('especialidades.nombre', 'ILIKE', "%{$search}%")
                  ->orWhere('morbilidades.diagnostico', 'ILIKE', "%{$search}%");
            });
        }
        
        $filteredRecords = $query->count();
        
        // Ordenamiento
        $orderColumn = $request->get('order')[0]['column'] ?? 2;
        $orderDir = $request->get('order')[0]['dir'] ?? 'desc';
        $columns = [
            0 => 'pacientes.nombre',
            1 => 'pacientes.cedula',
            2 => 'citas.fecha_cita',
            3 => 'especialidades.nombre',
            4 => 'medicos.nombre',
            5 => 'morbilidades.diagnostico',
            6 => 'morbilidades.observaciones'
        ];
        if (isset($columns[$orderColumn])) {
            $query->orderBy($columns[$orderColumn], $orderDir);
        } else {
            $query->orderBy('citas.fecha_cita', 'desc');
        }
        
        // Paginación
        $start = $request->get('start', 0);
        $length = $request->get('length', 10);
        $data = $query->skip($start)->take($length)->get();
        
        // Formatear datos para DataTables
        $dataFormatted = [];
        foreach ($data as $row) {
            $dataFormatted[] = [
                $row->paciente_nombre . ' ' . $row->paciente_apellido,
                $row->paciente_cedula,
                \Carbon\Carbon::parse($row->fecha_cita)->format('d/m/Y'),
                $row->especialidad_nombre,
                'Dr. ' . $row->medico_nombre . ' ' . $row->medico_apellido,
                $row->diagnostico,
                $row->morbilidad_observaciones,
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
