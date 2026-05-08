<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Cita;
use App\Models\Especialidad;
use App\Models\Paciente;
use App\Models\Estado;
use App\Models\Calendario;
use Illuminate\Support\Facades\DB;
use RealRashid\SweetAlert\Facades\Alert;
use Illuminate\Support\Facades\Auth;

class CitaController extends Controller
{
    
    public function index()
    {
        $especialidades = Especialidad::with('medicos')->get();

        return view('Cita.SeleccionarEspecialidad', compact('especialidades'));
    }

    public function create(Request $request, int $id)
    {
        $especialidad = Especialidad::findOrfail($id);  

        $estados = Estado::orderBy('nombre', 'asc')->get();
        
        $citas = Cita::whereHas('calendario.medico', function($query) use ($id) {
        $query->where('especialidad_id', $id);
            })
            ->with('paciente')
            ->orderBy('fecha_cita', 'desc')
            ->take(10)
            ->get();

        return view('Cita.AgendarCita', compact('especialidad', 'estados', 'citas'));
    }

    public function createParaEspecialidad(int $id)
    {
        $especialidad = Especialidad::findOrfail($id);

        $estados = Estado::orderBy('nombre', 'asc')->get();
        
        return view('Cita.Formcita', compact('especialidad' , 'estados'));
    }

    public function contarcupos($calendario_id)
{
    // Buscamos el registro del calendario para saber el límite total de cupos
    $calendario = Calendario::findOrFail($calendario_id);

    // Obtenemos los números de cupo que ya están registrados en la tabla citas para este calendario
    $cuposOcupados = Cita::where('calendario_id', $calendario_id)
                        ->pluck('numero_cupo')
                        ->toArray();

    $siguienteCupo = null;

    // Recorremos desde el 1 hasta el máximo definido en el calendario
    for ($i = 1; $i <= $calendario->cupos_disponibles; $i++) {
        // El primer número que no esté en el array de ocupados es el nuestro
        if (!in_array($i, $cuposOcupados)) {
            $siguienteCupo = $i;
            break; 
        }
    }

    if (!$siguienteCupo) {
        return response()->json(['error' => 'No hay cupos disponibles para esta fecha'], 422);
    }

    return response()->json([
        'numero_cupo' => $siguienteCupo,
        'calendario_id' => $calendario->id
    ]);
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
