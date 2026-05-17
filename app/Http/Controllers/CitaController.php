<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Cita;
use App\Models\Especialidad;
use App\Models\Paciente;
use App\Models\Estado;
use App\Models\Calendario;
use App\Models\Medico;
use Illuminate\Support\Facades\DB;
use RealRashid\SweetAlert\Facades\Alert;
use Illuminate\Support\Facades\Auth;

class CitaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $especialidades = Especialidad::with('medicos')->get();

        return view('Cita.SeleccionarEspecialidad', compact('especialidades'));
    }

    /**
     * Show the form for creating a new resource.
     */

    public function create()
    {
        $especialidades = Especialidad::all();
        $estados = Estado::all();

        return view('Cita.Formcita', compact('especialidades', 'estados'));
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
                    'total' => $capacidad_maxima
                ];
            });

            return response()->json($eventos);

        } catch (\Exception $e) {
            // Si algo falla adentro, esto enviará el texto del error en JSON limpio a la consola
            return response()->json([
                'error' => true,
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ], 500);
        }
    }


    public function store(Request $request)
    {
        $request->validate([
            //Datos del paciente
            'cedula' => 'required|string|min:8|max:20',
            'nombre' => 'required|string|max:255',
            'apellido' => 'required|string|max:255',
            'fecha_nacimiento' => 'required|date',
            'telefono' => 'required|string|min:7|max:15',
            'parroquia_id' => 'required|numeric', 
            'direccion' => 'nullable|string|max:255',
            //Datos de la cita
            'calendario_id' => 'required|numeric',
            'fecha_cita' => 'required|date',
            'observacion' => 'nullable|string',
            'especialidad_id' => 'required|exists:especialidades,id'
        ]);

        try {
            DB::beginTransaction();

            $paciente = Paciente::firstOrCreate(
                ['cedula' => $request->cedula],
                [
                    'nombre' => $request->nombre,
                    'apellido' => $request->apellido,
                    'fecha_nacimiento' => $request->fecha_nacimiento,
                    'telefono' => $request->telefono,
                    'parroquia_id' => $request->parroquia_id,
                    'direccion' => $request->direccion,
                ]
            );

            Cita::create([
                'paciente_id' => $paciente->id,
                'calendario_id' => $request->calendario_id,
                'user_id' => Auth::id() ?? 1,
                'fecha_registro' => now()->toDateString(),
                'fecha_cita' => $request->fecha_cita,
                'estado' => 'Agendada',
                'observacion' => $request->observacion,
            ]);

            DB::commit();

            Alert::success('¡Éxito!', 'Cita y/o paciente registrados correctamente.');
            
            return redirect()->route('Citas.index');

    }catch (\Exception $e) {
    DB::rollBack();
    // Esto te mostrará el mensaje exacto del error en lugar de uno genérico
    Alert::error('Error Crítico', $e->getMessage()); 
    return back()->withInput();
    }
    }


    /**
     * Display the specified resource.
     */
    public function show(Cita $cita)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Cita $cita)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Cita $cita)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Cita $cita)
    {
        //
    }
}
