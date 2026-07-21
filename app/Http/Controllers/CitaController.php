<?php

namespace App\Http\Controllers;

use App\Models\Calendario;
use App\Models\Cita;
use App\Models\Especialidad;
use App\Models\Estado;
use App\Models\Municipio;
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
                $btnReagendar = $row->estado == 'Agendada'
                    ? '<a href="'.route('Citas.edit', $row->id).'" class="btn btn-xs btn-square btn-neutral text-info-hover border-info-hover" title="Reagendar"><i class="bi bi-calendar2-week"></i></a>'
                    : '';
                $btnDelete = '<a href="'.route('Citas.destroy', $row->id).'" class="btn btn-xs btn-square btn-neutral text-danger-hover border-danger-hover" data-confirm-delete="true"><i class="bi bi-trash"></i></a>';
                $accionesHtml = '<div class="hstack gap-2 justify-content-end">'.$btnShow.$btnReagendar.$btnDelete.'</div>';
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
                'horario' => null
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

    public function store(Request $request)
    {
        $request->merge([
            'nombre' => mb_convert_case(trim($request->nombre), MB_CASE_TITLE, 'UTF-8'),
            'apellido' => mb_convert_case(trim($request->apellido), MB_CASE_TITLE, 'UTF-8'),
        ]);

        $request->validate([
            // Datos del paciente
            'cedula_tipo' => 'required|in:V,E',
            'cedula' => 'required|string|min:7|max:20|regex:/^[0-9]+$/',
            'rif' => 'nullable|string|max:20',
            'nombre' => 'required|string|max:255|regex:/^[a-zA-ZáéíóúÁÉÍÓÚñÑüÜ\s]+$/u',
            'apellido' => 'required|string|max:255|regex:/^[a-zA-ZáéíóúÁÉÍÓÚñÑüÜ\s]+$/u',
            'fecha_nacimiento' => 'required|date',
            'telefono' => 'required|string|min:7|max:15|regex:/^[\d\-\(\)\s\+]+$/',
            'parroquia_id' => 'required|numeric|exists:parroquias,id',
            'direccion' => 'nullable|string|max:255',
            'sexo' => 'required|in:Masculino,Femenino',
            // Datos de la cita
            'calendario_id' => 'required|numeric|exists:calendarios,id',
            'fecha_cita' => 'required|date|after_or_equal:today',
            'observacion' => 'nullable|string',
            'especialidad_id' => 'required|exists:especialidades,id',
            'tipo_paciente' => 'required|string|in:primera_vez,control,orden_medica',
        ]);

        if ($request->tipo_paciente === 'primera_vez') {
            $cedulaCompleta = $request->cedula_tipo.'-'.$request->cedula;
            $paciente = Paciente::where('cedula', $cedulaCompleta)->first();

            if ($paciente) {
                $tieneCitas = Cita::where('paciente_id', $paciente->id)
                    ->whereHas('calendario.medico', function ($q) use ($request) {
                        $q->where('especialidad_id', $request->especialidad_id);
                    })
                    ->whereIn('estado', ['Agendada', 'Atendida'])
                    ->exists();

                if ($tieneCitas) {
                    return redirect()->back()->withInput()->withErrors([
                        'tipo_paciente' => 'Este paciente ya tiene citas en esta especialidad. Seleccione "Control / Sucesivo".'
                    ]);
                }
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

            if ($request->tipo_paciente !== 'orden_medica') {
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
            }

            $cedulaCompleta = $request->cedula_tipo.'-'.$request->cedula;
            $rifCompleto = $request->rif ? 'J-'.$request->rif : '';

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

            session()->flash('paciente_id', $paciente->id);

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

            return redirect()->route('morbilidad.index');

        } catch (\Exception $e) {
            DB::rollBack();

            if ($e->getCode() == '23505') {
                Alert::error('Error', 'Este paciente ya tiene una cita en ese horario.');

                return redirect()->route('morbilidad.index');
            }
            Alert::error('Error', 'No se pudo registrar la cita. Intente de nuevo.');
            
            return redirect()->route('morbilidad.index');
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

            return redirect()->route('morbilidad.index');
        }

        if ($cita->reagendada_contador >= 2) {
            Alert::error('Límite alcanzado', 'Esta cita ya ha sido reagendada el máximo de 2 veces.');

            return redirect()->route('morbilidad.index');
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

            return redirect()->route('morbilidad.index');
        }

        if ($cita->reagendada_contador >= 2) {
            Alert::error('Límite alcanzado', 'Esta cita ya ha sido reagendada el máximo de 2 veces.');

            return redirect()->route('morbilidad.index');
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

            return redirect()->route('morbilidad.index');
        } catch (\Exception $e) {
            DB::rollBack();
            Alert::error('Error', 'No se pudo reagendar la cita. Intente de nuevo.');

            return redirect()->route('morbilidad.index');

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

        return redirect()->route('morbilidad.index');
    }
}
