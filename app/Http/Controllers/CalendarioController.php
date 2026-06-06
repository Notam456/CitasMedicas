<?php

namespace App\Http\Controllers;

use App\Models\Calendario;
use App\Models\Especialidad;
use App\Models\Medico;
use App\Models\User;
use App\Notifications\PlanificacionCreada;
use Carbon\Carbon;
use Illuminate\Http\Request;
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
            $query->where('medico_id', $medicoId);
        } elseif ($especialidadId) {
            $query->whereHas('medico', function ($q) use ($especialidadId) {
                $q->where('especialidad_id', $especialidadId);
            });
        }

        $disponibilidad = $query->get();

        return response()->json($disponibilidad);
    }

    public function getMedicos($especialidad_id)
    {
        $medicos = Medico::where('especialidad_id', $especialidad_id)->get();

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
            'medico_id' => 'required|exists:medicos,id',
            'fecha' => 'required|date',
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
        ]);

        Calendario::updateOrCreate(
            ['medico_id' => $request->medico_id, 'fecha' => $request->fecha],
            [
                'hora_inicio' => $request->hora_inicio,
                'hora_fin' => $request->hora_fin,
                'cupos_primera_vez' => $request->cupos_primera_vez,
                'cupos_sucesivos' => $request->cupos_sucesivos,
            ]
        );

        $medico = Medico::with('especialidad')->find($request->medico_id);
        Notification::send(User::all(), new PlanificacionCreada(
            $medico,
            "disponibilidad para el {$request->fecha}",
        ));

        return response()->json(['success' => true, 'message' => 'Cupos actualizados correctamente.']);
    }

    private function storeMasivo(Request $request)
    {
        $request->validate([
            'medico_id' => 'required|exists:medicos,id', 
            'fecha_inicio' => 'required|date',
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

        $count = 0;
        $overwritten = 0;

        foreach ($period as $date) {
            $dayOfWeek = $date->format('N');

            if (in_array($dayOfWeek, $request->dias_semana)) {
                $fechaStr = $date->format('Y-m-d');

                $exists = Calendario::where('medico_id', $request->medico_id)
                    ->where('fecha', $fechaStr)
                    ->exists();

                if ($exists) {
                    $overwritten++;
                }

                Calendario::updateOrCreate(
                    ['medico_id' => $request->medico_id, 'fecha' => $fechaStr],
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

        $message = "Se han configurado $count días.";
        if ($overwritten > 0) {
            $message .= " Se sobrescribieron $overwritten configuraciones existentes.";
        }

        $medico = Medico::with('especialidad')->find($request->medico_id);
        Notification::send(User::all(), new PlanificacionCreada(
            $medico,
            "disponibilidad para {$count} días.",
        ));

        Alert::success($message);
        return redirect()->route('calendario.index');
    }

   
}
