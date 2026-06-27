<?php

namespace App\Http\Controllers;

use App\Models\Calendario;
use App\Models\Especialidad;
use App\Models\Medico;
use App\Models\User;
use App\Notifications\PlanificacionCreada;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;
use RealRashid\SweetAlert\Facades\Alert;

class CalendarioController extends Controller
{
    public function getDatosMes(Request $request)
    {
        $mes = $request->mes;
        $anio = $request->anio;
        $especialidadId = $request->especialidad_id;
        $medicoId = $request->medico_id;

        $query = Calendario::whereYear('fecha', $anio)
            ->whereMonth('fecha', $mes)
            ->with('medico')

            ->withCount(['citas as citas_primera_vez_count' => function ($q) {
                $q->whereIn('estado', ['Agendada', 'Atendida'])
                    ->where('tipo_paciente', 'primera_vez');
            }])

            ->withCount(['citas as citas_sucesivas_count' => function ($q) {
                $q->whereIn('estado', ['Agendada', 'Atendida'])
                    ->where('tipo_paciente', 'control');
            }]);

        if ($medicoId) {
            $medico_id_value = $medicoId === 'any' ? null : $medicoId;
            $query->where('medico_id', $medico_id_value);
            if ($especialidadId) {
                $query->where('especialidad_id', $especialidadId);
            }
        } elseif ($especialidadId) {
            $query->where('especialidad_id', $especialidadId);
        }

        $disponibilidad = $query->get();

        return response()->json($disponibilidad);
    }

    public function getMedicos($especialidad_id)
    {
        $medicos_query = Medico::where('especialidad_id', $especialidad_id)->get();
        $medicos_count = $medicos_query->count();
        $medicos = $medicos_query->toArray();
        
        if ($medicos_count > 1) {
            array_unshift($medicos, [
                'id' => 'any',
                'nombre' => 'Cualquier',
                'apellido' => 'Médico',
                'especialidad_id' => $especialidad_id,
                'horario' => null
            ]);
        }

        return response()->json($medicos);
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $especialidades = Especialidad::where('estado', true)->get();

        return view('calendario.index', compact('especialidades'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $especialidades = Especialidad::where('estado', true)->get();
        $title = '¿Estas seguro de que deseas eliminar este registro?';
        $texrt = 'Esta acción no se puede deshacer.';
        confirmDelete($title, $texrt);

        return view('calendario.create', compact('especialidades'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        if ($request->has('tipo_configuracion') && $request->tipo_configuracion == 'masivo') {
            return $this->storeMasivo($request);
        }

        $request->validate([
            'medico_id' => [
                'required',
                function ($attribute, $value, $fail) use ($request) {
                    if ($value === 'any') return;
                    $medico = Medico::find($value);
                    if (!$medico) {
                        $fail('El médico seleccionado no es válido.');
                        return;
                    }
                    if ($medico->horario && count($medico->horario) > 0) {
                        $diaSemana = Carbon::parse($request->fecha)->dayOfWeekIso;
                        if (!in_array($diaSemana, array_map('intval', $medico->horario))) {
                            $fail('El médico no tiene permitido atender en este día de la semana según su horario.');
                        }
                    }
                }
            ],
            'especialidad_id' => 'required|exists:especialidades,id',
            'fecha' => 'required|date|after_or_equal:today',
            'hora_inicio' => 'required|date_format:H:i',
            'hora_fin' => 'required|date_format:H:i|after:hora_inicio',

            'cupos_primera_vez' => [
                'required',
                'integer',
                'min:0',
                function ($attribute, $value, $fail) use ($request) {
                    $total = $value + ($request->cupos_sucesivos ?? 0);
                    if ($total <= 0) {
                        $fail('La suma de cupos (primera vez + sucesivos) debe ser mayor a cero.');
                    }
                },
            ],
            'cupos_sucesivos' => 'required|integer|min:0',
        ], [
            'fecha.after_or_equal' => 'No se pueden registrar cupos en fechas pasadas.',
        ]);
        DB::beginTransaction();
        try {
        $medico_id = $request->medico_id === 'any' ? null : $request->medico_id;
        Calendario::updateOrCreate(
            ['medico_id' => $medico_id, 'fecha' => $request->fecha, 'especialidad_id' => $request->especialidad_id],
            [
                'hora_inicio' => $request->hora_inicio,
                'hora_fin' => $request->hora_fin,
                'cupos_primera_vez' => $request->cupos_primera_vez,
                'cupos_sucesivos' => $request->cupos_sucesivos,
            ]
        );

        if ($medico_id) {
            $medico = Medico::with('especialidad')->find($medico_id);
            Notification::send(User::all(), new PlanificacionCreada(
                $medico,
                "disponibilidad para el {$request->fecha}",
            ));
        } else {
            $especialidad = Especialidad::find($request->especialidad_id);
        
        }
        DB::commit();
        return response()->json(['success' => true, 'message' => 'Cupos actualizados correctamente.']);
        } catch(\Exception $e) {
            DB::rollBack();

            Alert::error('Error', 'No se pudo completar la configuración. Por favor, intenta de nuevo.');

            return redirect()->back()->withInput();
        }
    }

    private function storeMasivo(Request $request)
    {
        $request->validate([
            'medico_id' => [
                'required',
                function ($attribute, $value, $fail) use ($request) {
                    if ($value === 'any') return;
                    $medico = Medico::find($value);
                    if (!$medico) {
                        $fail('El médico seleccionado no es válido.');
                        return;
                    }
                    if ($medico->horario && count($medico->horario) > 0) {
                        $diasPermitidos = array_map('intval', $medico->horario);
                        foreach ($request->dias_semana as $diaSeleccionado) {
                            if (!in_array((int)$diaSeleccionado, $diasPermitidos)) {
                                $fail('Uno o más días seleccionados no están permitidos en el horario del médico.');
                                break;
                            }
                        }
                    }
                }
            ],
            'especialidad_id' => 'required|exists:especialidades,id',
            'fecha_inicio' => 'required|date|after_or_equal:today',
            'duracion_rango' => 'required|in:1_week,1_month,3_months,6_months',
            'dias_semana' => 'required|array',
            'dias_semana.*' => 'required|integer|between:1,7',
            'hora_inicio' => 'required|date_format:H:i',
            'hora_fin' => 'required|date_format:H:i|after:hora_inicio',
            'cupos_primera_vez' => [
                'required',
                'integer',
                'min:0',
                function ($attribute, $value, $fail) use ($request) {
                    $total = $value + ($request->cupos_sucesivos ?? 0);
                    if ($total <= 0) {
                        $fail('La suma de cupos (primera vez + sucesivos) debe ser mayor a cero.');
                    }
                },
            ],
            'cupos_sucesivos' => 'required|integer|min:0',
        ], [
            'fecha_inicio.after_or_equal' => 'La fecha de inicio no puede ser una fecha pasada.',
        ]);

        $fechaInicio = Carbon::parse($request->fecha_inicio);
        $fechaFin = $fechaInicio->copy();

        switch ($request->duracion_rango) {
            case '1_week':
                $fechaFin->addWeek();
                break;
            case '1_month':
                $fechaFin->addMonth();
                break;
            case '3_months':
                $fechaFin->addMonths(3);
                break;
            case '6_months':
                $fechaFin->addMonths(6);
                break;
        }

        $dateTimeInicio = new \DateTime($fechaInicio->toDateString());
        $dateTimeFin = new \DateTime($fechaFin->toDateString());
        $interval = new \DateInterval('P1D');
        $period = new \DatePeriod($dateTimeInicio, $interval, $dateTimeFin->modify('+1 day'));

        DB::beginTransaction();
        try {
            $count = 0;
            $overwritten = 0;
            $medico_id = $request->medico_id === 'any' ? null : $request->medico_id;

            foreach ($period as $date) {
                $dayOfWeek = $date->format('N');

                if (in_array($dayOfWeek, $request->dias_semana)) {
                    $fechaStr = $date->format('Y-m-d');

                    $exists = Calendario::where('medico_id', $medico_id)
                        ->where('especialidad_id', $request->especialidad_id)
                        ->where('fecha', $fechaStr)
                        ->exists();

                    if ($exists) {
                        $overwritten++;
                    }

                    Calendario::updateOrCreate(
                        ['medico_id' => $medico_id, 'fecha' => $fechaStr, 'especialidad_id' => $request->especialidad_id],
                        [
                            'hora_inicio' => $request->hora_inicio,
                            'hora_fin' => $request->hora_fin,
                            'cupos_primera_vez' => $request->cupos_primera_vez,
                            'cupos_sucesivos' => $request->cupos_sucesivos,
                        ]
                    );
                    $count++;
                }
            }
            DB::commit();
            $message = "Se han configurado $count días.";
            if ($overwritten > 0) {
                $message .= " Se sobrescribieron $overwritten configuraciones existentes.";
            }

            if ($medico_id) {
                $medico = Medico::with('especialidad')->find($medico_id);
                Notification::send(User::all(), new PlanificacionCreada(
                    $medico,
                    "disponibilidad para {$count} días.",
                ));
            }

            Alert::success($message);

            return redirect()->route('calendario.index');
        } catch (\Exception $e) {

            DB::rollBack();

            Alert::error('Error', 'No se pudo completar la configuración masiva. Por favor, intenta de nuevo.');

            return redirect()->back()->withInput();
        }
    }
}
