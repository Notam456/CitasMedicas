<?php

namespace App\Http\Controllers;

use App\Models\Cita;
use App\Models\Especialidad;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Mccarlosen\LaravelMpdf\Facades\LaravelMpdf as Pdf;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\MorbilidadExport;
use Carbon\Carbon;

class MorbilidadController extends Controller
{
    public function toggleHistoriaTraida(Cita $cita)
    {
        if ($cita->estado !== 'Agendada') {
            return response()->json([
                'message' => 'Solo se puede marcar la historia como traída en citas agendadas.',
            ], 403);
        }

        $cita->historia_traida = !$cita->historia_traida;
        $cita->save();

        return response()->json(['historia_traida' => $cita->historia_traida]);
    }

    public function index(Request $request)
    {
        $especialidades = Especialidad::where('estado', true)->get();

        if ($request->ajax() && $request->has('draw')) {
            return $this->dataTableResponse($request);
        }

        if ($request->has('export_excel') || $request->has('export_pdf')) {
            $query = $this->buildBaseQuery($request);

            $esAgendadas = $request->estado === 'Agendada';

            if ($esAgendadas) {
                $query->leftJoin('parroquias', 'pacientes.parroquia_id', '=', 'parroquias.id')
                    ->leftJoin('municipios', 'parroquias.municipio_id', '=', 'municipios.id')
                    ->leftJoin('distritos', 'municipios.distrito_id', '=', 'distritos.id')
                    ->addSelect(
                        DB::raw("EXTRACT(YEAR FROM AGE(pacientes.fecha_nacimiento)) as edad"),
                        'pacientes.sexo',
                        DB::raw("COALESCE(distritos.nombre, 'Ignorado') as distrito_nombre"),
                        DB::raw("COALESCE(municipios.nombre, 'Ignorado') as municipio_nombre")
                    );
            }

            $query->orderBy(DB::raw("CASE WHEN citas.tipo_paciente = 'primera_vez' THEN 0 ELSE 1 END"), 'asc')
                  ->orderBy('citas.fecha_cita', 'desc');

            $especialidad = $request->filled('especialidad_id')
                ? Especialidad::find($request->especialidad_id)?->nombre
                : null;
            $fecha_desde = $request->fecha_desde;
            $fecha_hasta = $request->fecha_hasta;
            $tipo_paciente = $request->tipo_paciente;
            $estado = $request->estado;
            $fecha_registro_desde = $request->fecha_registro_desde;
            $fecha_registro_hasta = $request->fecha_registro_hasta;

            $medicoModel = $request->filled('medico_id')
                ? \App\Models\Medico::find($request->medico_id)
                : null;
            $medicoNombreStr = $medicoModel ? 'Dr. ' . $medicoModel->nombre . ' ' . $medicoModel->apellido : null;
            $especialidadHeader = $especialidad ?: ($medicoModel ? $medicoModel->especialidad->nombre : null);

            $slugEsp = $especialidadHeader ? str_replace(' ', '-', mb_strtolower($especialidadHeader)) : null;
            $slugEstado = $estado ? str_replace(' ', '-', mb_strtolower($estado)) : null;
            $slugMedico = $medicoNombreStr ? str_replace(' ', '-', mb_strtolower($medicoNombreStr)) : null;

            $partesNombre = ['reporte-de-citas'];
            if ($fecha_desde && $fecha_hasta) {
                if ($fecha_desde === $fecha_hasta) {
                    $partesNombre[] = Carbon::parse($fecha_desde)->format('d-m-y');
                } else {
                    $partesNombre[] = Carbon::parse($fecha_desde)->format('d-m-y') . '-al-' . Carbon::parse($fecha_hasta)->format('d-m-y');
                }
            }
            if ($slugEsp) $partesNombre[] = $slugEsp;
            if ($slugMedico) $partesNombre[] = $slugMedico;
            if ($slugEstado) $partesNombre[] = $slugEstado;
            $nombreArchivo = implode('-', $partesNombre);

            $mostrarFechaCita = empty($fecha_desde) || empty($fecha_hasta) || $fecha_desde !== $fecha_hasta;
            $mostrarColumnaMedico = !$request->filled('medico_id');
            $mostrarColumnaTipo = !$esAgendadas || empty($tipo_paciente);

            if ($request->has('export_excel')) {
                ini_set('memory_limit', '512M');
                ini_set('max_execution_time', 300);
                $morbilidades = $query->lazy();

                if ($esAgendadas) {
                    return Excel::download(
                        new \App\Exports\AgendadasExport($morbilidades, $especialidad, $fecha_desde, $fecha_hasta, $tipo_paciente, $estado, $fecha_registro_desde, $fecha_registro_hasta, $medicoNombreStr, $especialidadHeader, $mostrarColumnaTipo),
                        $nombreArchivo . '.xlsx'
                    );
                }

                return Excel::download(
                    new MorbilidadExport($morbilidades, $especialidad, $fecha_desde, $fecha_hasta, $tipo_paciente, $estado, $fecha_registro_desde, $fecha_registro_hasta, $medicoNombreStr, $especialidadHeader),
                    $nombreArchivo . '.xlsx'
                );
            }

            if ($request->has('export_pdf')) {
                $total = $query->count();
                $limiteAdvertencia = 5000;
                if ($total > $limiteAdvertencia) {
                    session()->flash('warning', "El PDF contiene {$total} registros. Puede consumir mucha memoria y tiempo. Considere aplicar filtros más específicos.");
                }
                ini_set('memory_limit', '1024M');
                ini_set('max_execution_time', 300);
                ini_set('pcre.backtrack_limit', '10000000');
                $membrete = $this->getMembreteBase64();
                $morbilidades = $query->get();

                if ($esAgendadas) {
                    $pdf = Pdf::loadView('reportes.pdf.agendadas_pdf', compact('morbilidades', 'membrete', 'especialidad', 'fecha_desde', 'fecha_hasta', 'tipo_paciente', 'estado', 'fecha_registro_desde', 'fecha_registro_hasta', 'mostrarFechaCita', 'medicoNombreStr', 'mostrarColumnaMedico', 'especialidadHeader', 'mostrarColumnaTipo'));
                } else {
                    $pdf = Pdf::loadView('reportes.pdf.morbilidad_pdf', compact('morbilidades', 'membrete', 'especialidad', 'fecha_desde', 'fecha_hasta', 'tipo_paciente', 'estado', 'fecha_registro_desde', 'fecha_registro_hasta', 'mostrarFechaCita', 'medicoNombreStr', 'mostrarColumnaMedico', 'especialidadHeader'));
                }
                return $pdf->stream($nombreArchivo . '.pdf');
            }
        }

        $medicos = \App\Models\Medico::with('especialidad')
            ->orderBy('nombre')
            ->get();
        return view('morbilidad.index', compact('especialidades', 'medicos'));
    }

    public function pendientes(Request $request)
    {
        if ($request->ajax() && $request->has('draw')) {
            return $this->dataTablePendientes($request);
        }
        $especialidades = Especialidad::where('estado', true)->get();
        return view('morbilidad.pendientes', compact('especialidades'));
    }

    public function getCita($id)
    {
        $cita = Cita::with([
            'paciente',
            'medico.especialidad',
            'patologias',
            'atendidoPor',
            'user',
        ])->findOrFail($id);

        return response()->json($cita);
    }

    public function pdfCita($id)
    {
        $cita = Cita::with([
            'paciente',
            'medico.especialidad',
            'patologias',
            'atendidoPor',
        ])->findOrFail($id);

        $membrete = $this->getMembreteBase64();
        $pdf = Pdf::loadView('reportes.pdf.cita_pdf', compact('cita', 'membrete'));
        return $pdf->stream('cita_' . $id . '.pdf');
    }

    private function buildBaseQuery(Request $request)
    {
        $query = Cita::query()
            ->join('pacientes', 'citas.paciente_id', '=', 'pacientes.id')
            ->join('calendarios', 'citas.calendario_id', '=', 'calendarios.id')
            ->join('medicos', 'calendarios.medico_id', '=', 'medicos.id')
            ->join('especialidades', 'medicos.especialidad_id', '=', 'especialidades.id')
            ->leftJoin('expedientes', 'pacientes.id', '=', 'expedientes.paciente_id')
            ->leftJoin(DB::raw("(SELECT cp.cita_id, STRING_AGG(p.nombre, ', ' ORDER BY p.nombre) as patologias_nombres FROM cita_patologias cp JOIN patologias p ON p.id = cp.patologia_id GROUP BY cp.cita_id) as pats"), 'pats.cita_id', '=', 'citas.id')
            ->select(
                'citas.id',
                'citas.paciente_id',
                'citas.historia_traida',
                'expedientes.numero_expediente',
                'pacientes.nombre as paciente_nombre',
                'pacientes.apellido as paciente_apellido',
                'pacientes.cedula as paciente_cedula',
                'citas.fecha_cita',
                'citas.observacion as cita_observacion',
                'medicos.nombre as medico_nombre',
                'medicos.apellido as medico_apellido',
                'especialidades.nombre as especialidad_nombre',
                'especialidades.id as especialidad_id',
                'citas.diagnostico_libre',
                'citas.estado',
                'citas.tipo_paciente',
                'citas.created_at',
                'pats.patologias_nombres'
            );

        if ($request->filled('especialidad_id')) {
            $query->where('especialidades.id', $request->especialidad_id);
        }
        if ($request->filled('fecha_desde')) {
            $query->whereDate('citas.fecha_cita', '>=', $request->fecha_desde);
        }
        if ($request->filled('fecha_hasta')) {
            $query->whereDate('citas.fecha_cita', '<=', $request->fecha_hasta);
        }
        if ($request->filled('tipo_paciente')) {
            $query->where('citas.tipo_paciente', $request->tipo_paciente);
        }
        if ($request->filled('estado')) {
            $query->where('citas.estado', $request->estado);
        }
        if ($request->filled('fecha_registro_desde')) {
            $query->whereDate('citas.created_at', '>=', $request->fecha_registro_desde);
        }
        if ($request->filled('fecha_registro_hasta')) {
            $query->whereDate('citas.created_at', '<=', $request->fecha_registro_hasta);
        }
        if ($request->filled('medico_id')) {
            $query->where('medicos.id', $request->medico_id);
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
                  ->orWhere('especialidades.nombre', 'ILIKE', "%{$search}%");
            });
        }
        $filteredRecords = $query->count();

        $orderColumn = $request->get('order')[0]['column'] ?? 3;
        $orderDir = $request->get('order')[0]['dir'] ?? 'asc';
        $columns = [
            0 => 'expedientes.numero_expediente',
            1 => 'pacientes.nombre',
            2 => 'pacientes.cedula',
            3 => 'citas.fecha_cita',
            4 => 'especialidades.nombre',
            5 => 'medicos.nombre',
        ];

        $query->orderBy(DB::raw("CASE WHEN citas.tipo_paciente = 'primera_vez' THEN 0 ELSE 1 END"), 'asc');
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
            $tipoBadge = match ($row->tipo_paciente) {
                'primera_vez' => '<span class="badge bg-info">Primera Vez</span>',
                'control'     => '<span class="badge bg-warning text-dark">Sucesiva</span>',
                'orden_medica' => '<span class="badge bg-secondary">Orden Médica</span>',
                default       => '<span class="badge bg-light text-dark">'.e($row->tipo_paciente).'</span>',
            };

            $estadoBadge = match ($row->estado) {
                'Atendida' => '<span class="badge bg-primary">Atendida</span>',
                'Cancelada' => '<span class="badge bg-danger">Cancelada</span>',
                default => '<span class="badge bg-success">Agendada</span>',
            };

            $btnShow = '<button type="button" data-id="' . $row->id . '" class="btn-show-cita btn btn-xs btn-square btn-neutral" title="Ver detalles"><i class="bi bi-eye"></i></button>';
            $btnEdit = $row->estado === 'Atendida'
                ? '<button type="button" data-id="' . $row->id . '" class="btn-edit-cita btn btn-xs btn-square btn-neutral text-info-hover border-info-hover" title="Editar Diagnóstico"><i class="bi bi-pencil"></i></button>'
                : '';

            $acciones = '<div class="hstack gap-2 justify-content-end">' . $btnShow . $btnEdit . '</div>';

            $numero = $row->numero_expediente ?? '';
            $historiaInput = '<input type="text" class="form-control form-control-sm input-historia"
                data-paciente-id="' . $row->paciente_id . '"
                data-anterior="' . e($numero) . '"
                value="' . e($numero) . '"
                placeholder="00-00-00" maxlength="8" autocomplete="off">';

            $editableTraida = $row->estado === 'Agendada';
            $traida = (bool) $row->historia_traida;
            $switchTraida = '<div class="th-fh-switch' . ($editableTraida ? ' editable' : '') . ($traida ? ' th' : ' fh') . '"
                data-cita-id="' . $row->id . '"
                data-traida="' . ($traida ? '1' : '0') . '"
                data-editable="' . ($editableTraida ? '1' : '0') . '"
                title="Historia traída del archivo"><span class="seg th">TH</span><span class="seg fh">FH</span></div>';

            $historiaCol = '<div class="historia-inline">' . $historiaInput . $switchTraida . '</div>';

            $dataFormatted[] = [
                $historiaCol,
                $row->paciente_nombre . ' ' . $row->paciente_apellido,
                $row->paciente_cedula,
                Carbon::parse($row->fecha_cita)->format('d/m/Y'),
                $row->especialidad_nombre,
                'Dr. ' . $row->medico_nombre . ' ' . $row->medico_apellido,
                $tipoBadge,
                $estadoBadge,
                Carbon::parse($row->created_at)->format('d/m/Y'),
                $acciones,
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
            ->leftJoin('expedientes', 'pacientes.id', '=', 'expedientes.paciente_id')
            ->select(
                'citas.id',
                'citas.paciente_id',
                'pacientes.nombre as paciente_nombre',
                'pacientes.apellido as paciente_apellido',
                'pacientes.cedula as paciente_cedula',
                'citas.fecha_cita',
                'medicos.nombre as medico_nombre',
                'medicos.apellido as medico_apellido',
                'especialidades.nombre as especialidad_nombre',
                'expedientes.numero_expediente'
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
            2 => 'expedientes.numero_expediente',
            3 => 'citas.fecha_cita',
            4 => 'especialidades.nombre',
            5 => 'medicos.nombre',
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
            $btnAtender = '<button type="button" data-id="'.$row->id.'" class="btn-atender btn btn-xs btn-square btn-primary" data-bs-toggle="modal" data-bs-target="#modalAtender" title="Atender cita"><i class="bi bi-clipboard-plus"></i></button>';
            $btnCancelar = '<button type="button" data-id="'.$row->id.'" class="btn-cancelar-cita btn btn-xs btn-square btn-neutral text-danger-hover border-danger-hover" title="Cancelar cita"><i class="bi bi-trash"></i></button>';

            if ($row->numero_expediente) {
                $expedienteBadge = '<span class="badge bg-success">' . e($row->numero_expediente) . '</span>';
            } else {
                $expedienteBadge = '<span class="badge bg-secondary">Sin asignar</span>';
            }

            $acciones = '<div class="hstack gap-2 justify-content-end">' . $btnAtender . $btnCancelar . '</div>';

            $dataFormatted[] = [
                $row->paciente_nombre . ' ' . $row->paciente_apellido,
                $row->paciente_cedula,
                $expedienteBadge,
                Carbon::parse($row->fecha_cita)->format('d/m/Y'),
                $row->especialidad_nombre,
                'Dr. ' . $row->medico_nombre . ' ' . $row->medico_apellido,
                $acciones,
            ];
        }
        return response()->json([
            'draw' => intval($request->draw),
            'recordsTotal' => $totalRecords,
            'recordsFiltered' => $filteredRecords,
            'data' => $dataFormatted,
        ]);
    }

    private function getMembreteBase64(): string
    {
        $ruta = public_path('assets/img/membreteMPPS2.png');
        if (file_exists($ruta)) {
            return 'data:image/png;base64,' . base64_encode(file_get_contents($ruta));
        }
        return '';
    }
}
