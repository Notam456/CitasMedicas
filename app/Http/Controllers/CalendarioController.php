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

    return back()->with('success', 'Cupos actualizados correctamente.');
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
