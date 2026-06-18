<?php

namespace App\Http\Controllers;

use App\Models\Calendario;
use App\Models\Cita;
use App\Models\Especialidad;
use App\Models\Estado;
use App\Models\Medico;
use App\Models\Paciente;
use App\Models\User;
use App\Notifications\CitaCancelada;
use App\Notifications\CitaReagendada;
use Carbon\Carbon;
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

        $title = '¿Estas seguro de que deseas cancelar esta cita?';
        $texrt = 'La cita será marcada como cancelada.';
        confirmDelete($title, $texrt);

        return view('Cita.index');
    }

    private function buildBaseQuery(Request $request)
    {
        return Cita::query()
            ->join('pacientes', 'citas.paciente_id', '=', 'pacientes.id')
            ->join('calendarios', 'citas.calendario_id', '=', 'calendarios.id')
            ->join('medicos', 'calendarios.medico_id', '=', 'medicos.id')
            ->join('especialidades', 'medicos.especialidad_id', '=', 'especialidades.id')
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

            $tipoPacienteBadge = $row->tipo_paciente == 'primera_vez'
                ? '<span class="badge bg-info">Primera vez</span>'
                : '<span class="badge bg-warning">Control</span>';

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
                $btnReagendar = $row->estado == 'Agendada'
                    ? '<a href="'.route('Citas.edit', $row->id).'" class="btn btn-xs btn-square btn-neutral text-info-hover border-info-hover" title="Reagendar"><i class="bi bi-calendar2-week"></i></a>'
                    : '';
                $btnDelete = '<a href="'.route('Citas.destroy', $row->id).'" class="btn btn-xs btn-square btn-neutral text-danger-hover border-danger-hover" data-confirm-delete="true"><i class="bi bi-trash"></i></a>';
                $accionesHtml = '<div class="hstack gap-2 justify-content-end">'.$btnShow.$btnReagendar.$btnDelete.'</div>';
            }

            $dataFormatted[] = [
                $row->paciente_nombre.' '.$row->paciente_apellido,
                $row->paciente_cedula,
                'Dr. '.$row->medico_nombre.' '.$row->medico_apellido,
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

        return view('Cita.Formcita', compact('especialidades', 'estados', 'id'));
    }

    public function getMedicosPorEspecialidad($id)
    {
        $medicos = Medico::where('especialidad_id', $id)->get();

        return response()->json($medicos);
    }

    public function disponibilidadMes(Request $request, $medico_id)
    {
        try {
            $mes = $request->mes;
            $anio = $request->anio;
            $tipo_paciente = $request->tipo_paciente; // Recibe 'primera_vez' o 'control'

            // 1. Obtener las planificaciones del médico para ese mes
            $calendarios = Calendario::where('medico_id', $medico_id)
                ->whereYear('fecha', $anio)
                ->whereMonth('fecha', $mes)
                ->whereDate('fecha', '>=', now()->toDateString())
                ->get();

            // 2. Mapear y calcular cupos libres
            $eventos = $calendarios->map(function ($cal) use ($tipo_paciente) {

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

    public function store(Request $request)
    {
        $request->validate([
            // Datos del paciente
            'cedula_tipo' => 'required|in:V,E',
            'cedula' => 'required|string|min:7|max:20',
            'rif' => 'required|string|max:20',
            'nombre' => 'required|string|max:255',
            'apellido' => 'required|string|max:255',
            'fecha_nacimiento' => 'required|date',
            'telefono' => 'required|string|min:7|max:15',
            'parroquia_id' => 'required|numeric|exists:parroquias,id',
            'direccion' => 'nullable|string|max:255',
            'sexo' => 'required|in:Masculino,Femenino',
            // Datos de la cita
            'calendario_id' => 'required|numeric|exists:calendarios,id',
            'fecha_cita' => 'required|date|after_or_equal:today',
            'observacion' => 'nullable|string',
            'especialidad_id' => 'required|exists:especialidades,id',
            'tipo_paciente' => 'required|string|in:primera_vez,control',
        ]);

        try {
            DB::beginTransaction();

            $calendario = Calendario::lockForUpdate()->findOrFail($request->calendario_id);
            if ($calendario->fecha !== $request->fecha_cita) {
                DB::rollBack();
                Alert::error('Error de Coherencia', 'La fecha seleccionada no coincide con la planificación del médico.');

                return redirect()->back()->withInput();
            }

            $ocupados = Cita::where('calendario_id', $calendario->id)
                ->where('tipo_paciente', $request->tipo_paciente)
                ->whereIn('estado', ['Agendada', 'Atendida'])
                ->count();

            $capacidad_maxima = ($request->tipo_paciente === 'primera_vez')
            ? $calendario->cupos_primera_vez
            : $calendario->cupos_sucesivos;

            if ($ocupados >= $capacidad_maxima) {
                DB::rollBack();
                Alert::error('Sin Cupos', 'Lo sentimos, los cupos para este día se acaban de agotar.');

                return redirect()->back()->withInput();
            }

            $cedulaCompleta = $request->cedula_tipo.'-'.$request->cedula;
            $rifCompleto = 'J-'.$request->rif;

            $paciente = Paciente::firstOrCreate(
                ['cedula' => $cedulaCompleta],
                [
                    'rif' => $rifCompleto,
                    'nombre' => $request->nombre,
                    'apellido' => $request->apellido,
                    'fecha_nacimiento' => $request->fecha_nacimiento,
                    'telefono' => $request->telefono,
                    'parroquia_id' => $request->parroquia_id,
                    'direccion' => $request->direccion,
                    'sexo' => $request->sexo,
                ]
            );

            Cita::create([
                'paciente_id' => $paciente->id,
                'calendario_id' => $request->calendario_id,
                'user_id' => Auth::id() ?? 1,
                'fecha_registro' => now()->toDateString(),
                'fecha_cita' => $request->fecha_cita,
                'estado' => 'Agendada',
                'tipo_paciente' => $request->tipo_paciente,
                'observacion' => $request->observacion,
            ]);

            DB::commit();

            Alert::success('¡Éxito!', 'Cita registrada correctamente.');

            return redirect()->route('Citas.index');

        } catch (\Exception $e) {
            DB::rollBack();

            if ($e->getCode() == '23505') {
                Alert::error('Error', 'Este paciente ya tiene una cita en ese horario.');

                return redirect()->route('Citas.index');
            }
            Alert::error('Error', 'No se pudo registrar la cita. Intente de nuevo.');
            
            return redirect()->route('Citas.index');
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

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Cita $cita)
    {
        if (trim($cita->estado) !== 'Agendada') {
            Alert::error('Error', 'Solo se pueden reagendar citas con estado "Agendada".');

            return redirect()->route('Citas.index');
        }

        if ($cita->reagendada_contador >= 2) {
            Alert::error('Límite alcanzado', 'Esta cita ya ha sido reagendada el máximo de 2 veces.');

            return redirect()->route('Citas.index');
        }

        $cita->load('paciente', 'calendario.medico.especialidad');
        $especialidades = Especialidad::all();

        return view('Cita.Editcita', compact('cita', 'especialidades'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Cita $cita)
    {
        if (trim($cita->estado) !== 'Agendada') {
            Alert::error('Error', 'Solo se pueden reagendar citas agendadas.');

            return redirect()->route('Citas.index');
        }

        if ($cita->reagendada_contador >= 2) {
            Alert::error('Límite alcanzado', 'Esta cita ya ha sido reagendada el máximo de 2 veces.');

            return redirect()->route('Citas.index');
        }

        $fechaOriginal = $cita->fecha_cita;

        $request->validate([
            'calendario_id' => 'required|numeric|exists:calendarios,id',
            'fecha_cita' => 'required|date|after_or_equal:today',
            'observacion' => 'nullable|string',
        ]);
        try {
            DB::beginTransaction();
            $cita->update([
                'calendario_id' => $request->calendario_id,
                'fecha_cita' => $request->fecha_cita,
                'observacion' => $request->observacion,
                'reagendada_contador' => $cita->reagendada_contador + 1,
            ]);

            if (! auth()->user()->hasRole('administrador')) {
                $admins = User::role('administrador')->get();
                Notification::send($admins, new CitaReagendada($cita, auth()->user(), $fechaOriginal));
            }

            DB::commit();
            Alert::success('¡Éxito!', 'Cita reagendada correctamente.');

            return redirect()->route('Citas.index');
        } catch (\Exception $e) {
            DB::rollBack();
            Alert::error('Error', 'No se pudo reagendar la cita. Intente de nuevo.');

            return redirect()->route('Citas.index');

        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Cita $cita)
    {
        $cita->update(['estado' => 'Cancelada']);

        if (! auth()->user()->hasRole('administrador')) {
            $admins = User::role('administrador')->get();
            Notification::send($admins, new CitaCancelada($cita, auth()->user()));
        }

        Alert::success('¡Éxito!', 'Cita cancelada correctamente.');

        return redirect()->route('Citas.index');
    }
}
