<?php

namespace App\Http\Controllers;

use App\Models\Calendario;
use App\Models\Cita;
use App\Models\Especialidad;
use App\Models\Estado;
use App\Models\Expediente;
use App\Models\Municipio;
use App\Models\Medico;
use App\Models\Paciente;
use App\Models\User;
use App\Notifications\CitaCancelada;
use Carbon\Carbon;
use App\Http\Requests\StoreCitaRequest;
use App\Services\CitaService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;
use RealRashid\SweetAlert\Facades\Alert;

class CitaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if ($request->ajax() && $request->has('draw')) {
            return $this->dataTableResponse($request);
        }

        return redirect()->route('morbilidad.index');
    }

    private function buildBaseQuery(Request $request)
    {
        return Cita::query()
            ->join('pacientes', 'citas.paciente_id', '=', 'pacientes.id')
            ->join('calendarios', 'citas.calendario_id', '=', 'calendarios.id')
            ->leftJoin('medicos', 'calendarios.medico_id', '=', 'medicos.id')
            ->join('especialidades', 'calendarios.especialidad_id', '=', 'especialidades.id')
            ->select(
                'citas.id',
                'citas.fecha_cita',
                'citas.tipo_paciente',
                'citas.estado',
                'pacientes.nombre as paciente_nombre',
                'pacientes.apellido as paciente_apellido',
                'pacientes.cedula as paciente_cedula',
                'medicos.nombre as medico_nombre',
                'medicos.apellido as medico_apellido',
                'especialidades.nombre as especialidad_nombre'
            );
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
                    ->orWhere('citas.estado', 'ILIKE', "%{$search}%")
                    ->orWhere('citas.tipo_paciente', 'ILIKE', "%{$search}%");
            });
        }

        $filteredRecords = $query->count();

        if ($fechaFiltro = $request->fecha_filtro) {
            $query->whereDate('citas.fecha_cita', $fechaFiltro);
        }

        $orderColumn = $request->get('order')[0]['column'] ?? 4;
        $orderDir = $request->get('order')[0]['dir'] ?? 'desc';

        $columns = [
            0 => 'pacientes.nombre',
            1 => 'pacientes.cedula',
            2 => 'medicos.nombre',
            3 => 'especialidades.nombre',
            4 => 'citas.fecha_cita',
            5 => 'citas.tipo_paciente',
            6 => 'citas.estado',
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

            $tipoPacienteBadge = match ($row->tipo_paciente) {
                'primera_vez' => '<span class="badge bg-info">Primera vez</span>',
                'control'     => '<span class="badge bg-warning">Control</span>',
                'orden_medica' => '<span class="badge bg-secondary">Orden Médica</span>',
                default       => '<span class="badge bg-light text-dark">'.e($row->tipo_paciente).'</span>',
            };

            $medicoNombreFull = $row->medico_nombre 
                ? 'Dr. ' . $row->medico_nombre . ' ' . $row->medico_apellido 
                : 'Cualquier médico';

            if ($row->estado == 'Agendada') {
                $estadoBadge = '<span class="badge bg-success">Agendada</span>';
            } elseif ($row->estado == 'Atendida') {
                $estadoBadge = '<span class="badge bg-primary">Atendida</span>';
            } elseif ($row->estado == 'Cancelada') {
                $estadoBadge = '<span class="badge bg-danger">Cancelada</span>';
            } else {
                $estadoBadge = '<span class="badge bg-secondary">'.e($row->estado).'</span>';
            }

            if ($row->estado == 'Cancelada') {
                $accionesHtml = '<div class="hstack gap-2 justify-content-end"><span class="text-muted small">—</span></div>';
            } else {
                $btnShow = '<button type="button" data-id="'.$row->id.'" class="btn-show btn btn-xs btn-square btn-neutral"><i class="bi bi-eye"></i></button>';
                $accionesHtml = '<div class="hstack gap-2 justify-content-end">'.$btnShow.'</div>';
            }

            $dataFormatted[] = [
                $row->paciente_nombre.' '.$row->paciente_apellido,
                $row->paciente_cedula,
                $medicoNombreFull,
                $row->especialidad_nombre,
                Carbon::parse($row->fecha_cita)->format('d/m/Y'),
                $tipoPacienteBadge,
                $estadoBadge,
                $accionesHtml,
            ];
        }

        return response()->json([
            'draw' => intval($request->draw),
            'recordsTotal' => $totalRecords,
            'recordsFiltered' => $filteredRecords,
            'data' => $dataFormatted,
        ]);
    }

    public function create($id = null)
    {
        $especialidades = Especialidad::all();
        $estados = Estado::all();

        $defaultEstadoId = Estado::where('nombre', 'Yaracuy')->value('id');
        $defaultMunicipioId = $defaultEstadoId
            ? Municipio::where('nombre', 'San Felipe')
                ->where('estado_id', $defaultEstadoId)
                ->value('id')
            : null;

        return view('Cita.Formcita', compact('especialidades', 'estados', 'id', 'defaultEstadoId', 'defaultMunicipioId'));
    }

    public function getMedicosPorEspecialidad($id)
    {
      
        $medicos = Medico::where('especialidad_id', $id)
            ->whereHas('calendarios', function($q) {
                $q->whereDate('fecha', '>=', now()->toDateString());
            })
            ->get()
            ->toArray();

        $hasAnyDoctorPlanning = Calendario::where('especialidad_id', $id)
            ->whereNull('medico_id')
            ->whereDate('fecha', '>=', now()->toDateString())
            ->exists();

        $medicos_count = Medico::where('especialidad_id', $id)->count();

        // Add "Any Doctor" option if it has planning AND specialty has more than one doctor
        if ($hasAnyDoctorPlanning && $medicos_count > 1) {
            array_unshift($medicos, [
                'id' => 'any',
                'nombre' => 'Cualquier',
                'apellido' => 'Médico',
                'especialidad_id' => $id,
                'horarios' => []
            ]);
        }

        return response()->json($medicos);
    }

    public function disponibilidadMes(Request $request, $medico_id)
    {
        try {
            $mes = $request->mes;
            $anio = $request->anio;
            $tipo_paciente = $request->tipo_paciente;
            $especialidad_id = $request->especialidad_id;

            $medico_id_value = $medico_id === 'any' ? null : $medico_id;

            // 1. Obtener las planificaciones del médico para ese mes
            $query = Calendario::where('medico_id', $medico_id_value)
                ->whereYear('fecha', $anio)
                ->whereMonth('fecha', $mes)
                ->whereDate('fecha', '>=', now()->toDateString());

            if ($especialidad_id) {
                $query->where('especialidad_id', $especialidad_id);
            }

            $calendarios = $query->get();

            // 2. Mapear y calcular cupos libres
            $eventos = $calendarios->map(function ($cal) use ($tipo_paciente) {
                $isSuspended = false;
                if ($cal->medico_id) {
                    $isSuspended = \App\Models\SuspensionMedico::where('medico_id', $cal->medico_id)
                        ->where('fecha_inicio', '<=', $cal->fecha)
                        ->where('fecha_fin', '>=', $cal->fecha)
                        ->exists();
                }

                if ($isSuspended) {
                    return [
                        'id' => $cal->id,
                        'fecha' => $cal->fecha,
                        'hora_inicio' => $cal->hora_inicio,
                        'hora_fin' => $cal->hora_fin,
                        'disponibles' => 0,
                        'total' => 0,
                    ];
                }

                // Orden Médica: mostrar todos los slots sin verificar cupos
                if ($tipo_paciente === 'orden_medica') {
                    return [
                        'id' => $cal->id,
                        'fecha' => $cal->fecha,
                        'hora_inicio' => $cal->hora_inicio,
                        'hora_fin' => $cal->hora_fin,
                        'disponibles' => 999,
                        'total' => $cal->cupos_primera_vez + $cal->cupos_sucesivos,
                        'tipo' => 'orden_medica',
                    ];
                }

                // Contamos las citas existentes filtrando por el valor exacto del HTML
                $ocupados = Cita::where('calendario_id', $cal->id)
                    ->where('tipo_paciente', $tipo_paciente)
                    ->whereIn('estado', ['Agendada', 'Atendida'])
                    ->count();

                // Sincronizamos las columnas de tu tabla calendarios con el valor del HTML
                $capacidad_maxima = ($tipo_paciente === 'primera_vez')
                                    ? $cal->cupos_primera_vez
                                    : $cal->cupos_sucesivos;

                $disponibles = $capacidad_maxima - $ocupados;

                return [
                    'id' => $cal->id,
                    'fecha' => $cal->fecha,
                    'hora_inicio' => $cal->hora_inicio,
                    'hora_fin' => $cal->hora_fin,
                    'disponibles' => max(0, $disponibles),
                    'total' => $capacidad_maxima,
                ];
            });

            return response()->json($eventos);

        } catch (\Exception $e) {
            // Si algo falla adentro, esto enviará el texto del error en JSON limpio a la consola
            return response()->json([
                'error' => true,
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ], 500);
        }
    }

    public function tieneCitasEnEspecialidad($paciente_id, $especialidad_id)
    {
        $tiene = Cita::where('paciente_id', $paciente_id)
            ->whereHas('calendario.medico', function ($q) use ($especialidad_id) {
                $q->where('especialidad_id', $especialidad_id);
            })
            ->whereIn('estado', ['Agendada', 'Atendida'])
            ->exists();

        return response()->json(['tieneCitas' => $tiene]);
    }

    public function store(StoreCitaRequest $request)
    {
        $citaService = new CitaService();

        if ($request->sexo === 'Masculino' && Especialidad::find($request->especialidad_id)?->esSoloFemenino()) {
            return redirect()->back()->withInput()->withErrors([
                'especialidad_id' => 'Esta especialidad es exclusiva para pacientes de sexo femenino.'
            ]);
        }

        if ($request->tipo_paciente === 'primera_vez') {
            $cedulaCompleta = $request->cedula_tipo.'-'.$request->cedula;
            $paciente = Paciente::where('cedula', $cedulaCompleta)->first();
            if ($paciente && $citaService->verificarPrimeraVez($paciente->id, $request->especialidad_id)) {
                return redirect()->back()->withInput()->withErrors([
                    'tipo_paciente' => 'Este paciente ya tiene citas en esta especialidad. Seleccione "Control / Sucesivo".'
                ]);
            }
        }

        try {
            DB::beginTransaction();

            $calendario = Calendario::lockForUpdate()->findOrFail($request->calendario_id);
            if ($calendario->fecha !== $request->fecha_cita) {
                DB::rollBack();
                Alert::error('Error de Coherencia', 'La fecha seleccionada no coincide con la planificación del médico.');
                return redirect()->back()->withInput();
            }

            if (!$citaService->verificarCupos($request->calendario_id, $request->tipo_paciente)) {
                DB::rollBack();
                Alert::error('Sin Cupos', 'Lo sentimos, los cupos para este día se acaban de agotar.');
                return redirect()->back()->withInput();
            }

            $citaService->crearCita($request->validated());

            DB::commit();

            Alert::success('¡Éxito!', 'Cita registrada correctamente.');
            return redirect()->route('Citas.create');

        } catch (\Exception $e) {
            DB::rollBack();
            if ($e->getCode() == '23505') {
                Alert::error('Error', 'Este paciente ya tiene una cita en ese horario.');
                return redirect()->route('Citas.create');
            }
            Alert::error('Error', 'No se pudo registrar la cita. Intente de nuevo.');
            return redirect()->route('Citas.create');
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(int $id)
    {
        $cita = Cita::with('paciente', 'calendario.medico.especialidad')->findOrFail($id);

        return response()->json($cita);
    }

    public function cancelar(Request $request, Cita $cita)
    {
        $request->validate([
            'motivo' => 'required|in:ausencia_paciente,ausencia_medico',
            'observacion' => 'nullable|string|max:500',
        ]);

        if ($cita->estado !== 'Agendada') {
            return response()->json([
                'message' => 'Solo se pueden cancelar citas en estado "Agendada".',
            ], 409);
        }

        try {
            DB::beginTransaction();

            $cita->update(['estado' => 'Cancelada']);

            $cita->cancelacion()->create([
                'motivo' => $request->motivo,
                'cancelada_por' => Auth::id(),
                'observacion' => $request->observacion ?: null,
                'fecha_cancelacion' => now(),
            ]);

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'message' => 'No se pudo cancelar la cita. Intente de nuevo.',
            ], 500);
        }

        if (! auth()->user()->hasRole('administrador')) {
            $admins = User::role('administrador')->get();
            Notification::send($admins, new CitaCancelada($cita, auth()->user()));
        }

        return response()->json(['success' => true]);
    }
}
