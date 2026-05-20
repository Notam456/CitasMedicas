<?php

namespace App\Http\Controllers;

use App\Models\Calendario;
use App\Models\Medico;
use App\Models\Especialidad;
use Illuminate\Http\Request;
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
            ->with('medico');

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
            'medico_id' => 'required',
            'fecha' => 'required|date',
            'hora_inicio' => 'required',
            'hora_fin' => 'required',
            'cupos_primera_vez' => [
                'required',
                'integer',
                'min:0',
                function ($attribute, $value, $fail) use ($request) {
                    $total = $value + $request->cupos_sucesivos;
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

        return response()->json(['success' => true, 'message' => 'Cupos actualizados correctamente.']);
    }

    private function storeMasivo(Request $request)
    {
        $request->validate([
            'medico_id' => 'required',
            'fecha_inicio' => 'required|date',
            'fecha_fin' => 'required|date|after_or_equal:fecha_inicio',
            'dias_semana' => 'required|array',
            'hora_inicio' => 'required',
            'hora_fin' => 'required',
            'cupos_primera_vez' => [
                'required',
                'integer',
                'min:0',
                function ($attribute, $value, $fail) use ($request) {
                    $total = $value + $request->cupos_sucesivos;
                    if ($total <= 0) {
                        $fail('La suma de cupos (primera vez + sucesivos) debe ser mayor a cero.');
                    }
                },
            ],
            'cupos_sucesivos' => 'required|integer|min:0',
        ]);

        $fechaInicio = new \DateTime($request->fecha_inicio);
        $fechaFin = new \DateTime($request->fecha_fin);
        $interval = new \DateInterval('P1D');
        $period = new \DatePeriod($fechaInicio, $interval, $fechaFin->modify('+1 day'));

        $count = 0;
        $overwritten = 0;

        foreach ($period as $date) {
            $dayOfWeek = $date->format('N'); // 1 (Mon) to 7 (Sun)
            // Adjust to match whatever format we use in frontend if needed, but 1-7 is standard.
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

        return redirect()->route('calendario.index')->with('success', $message);
    }

    /**
     * Display the specified resource.
     */
    public function show(Calendario $calendario)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Calendario $calendario)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Calendario $calendario)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Calendario $calendario)
    {
        //
    }
}
