<?php

namespace App\Http\Controllers;

use App\Models\Paciente;
use App\Models\Parroquia;
use Illuminate\Http\Request;
use RealRashid\SweetAlert\Facades\Alert;

class PacienteController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $pacientes = Paciente::with('parroquia.municipio.estado')->get();

        $title = '¿Estas seguro de que deseas eliminar este paciente?';
        $texrt = 'Esta acción no se puede deshacer.';
        confirmDelete($title, $texrt);

        return view('paciente.listapacientes', compact('pacientes'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create() {}

        //Funcion para buscar si el paciente existe y mandar los datos si lo encuentra
    public function buscarPorCedula($cedula)
    {
        $paciente = Paciente::with(['parroquia.municipio.estado'])->where('cedula', $cedula)->first();

        if ($paciente) {
            return response()->json(['encontrado' => true, 'datos' => $paciente]);
        } else {
            return response()->json(['encontrado' => false]);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'apellido' => 'required|string|max:255',
            'cedula' => 'required|string|min:8|max:20|unique:pacientes,cedula',
            'fecha_nacimiento' => 'required|date',
            'telefono' => 'required|string|min:7|max:15',
            'parroquia_id' => 'required|exists:parroquias,id',
            'direccion' => 'nullable|string|max:255',
        ]);

        Paciente::create([
            'nombre' => $request->nombre,
            'apellido' => $request->apellido,
            'cedula' => $request->cedula,
            'fecha_nacimiento' => $request->fecha_nacimiento,
            'telefono' => $request->telefono,
            'parroquia_id' => $request->parroquia_id,
            'direccion' => $request->direccion,
        ]);

        Alert::success('Paciente creado exitosamente.');

        return redirect()->route('paciente.index');
    }

    /**
     * Display the specified resource.
     */
    public function show(int $id)
    {
        $pacienteToshow = Paciente::with('parroquia.municipio.estado')->findOrFail($id);
        $pacientes = Paciente::with('parroquia.municipio.estado')->get();

        return view('paciente.listapacientes', compact('pacientes', 'pacienteToshow'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Paciente $paciente)
    {
        $pacienteToEdit = Paciente::findOrFail($paciente->id);
        $pacientes = Paciente::with('parroquia.municipio.estado')->get();

        return view('paciente.listapacientes', compact('pacientes', 'pacienteToEdit'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, int $id)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'apellido' => 'required|string|max:255',
            'cedula' => 'required|string|min:8|max:20|unique:pacientes,cedula,'.$id,
            'fecha_nacimiento' => 'required|date',
            'telefono' => 'required|string|min:7|max:15',
            'parroquia_id' => 'required|exists:parroquias,id',
            'direccion' => 'nullable|string|max:255',
        ]);

        $paciente = Paciente::findOrFail($id);
        $paciente->update([
            'nombre' => $request->nombre,
            'apellido' => $request->apellido,
            'cedula' => $request->cedula,
            'fecha_nacimiento' => $request->fecha_nacimiento,
            'telefono' => $request->telefono,
            'parroquia_id' => $request->parroquia_id,
            'direccion' => $request->direccion,
        ]);

        Alert::success('Paciente actualizado exitosamente.');

        return redirect()->route('paciente.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(int $id)
    {
        $paciente = Paciente::findOrFail($id);
        $paciente->delete();

        Alert::success('Paciente eliminado exitosamente.');

        return redirect()->route('paciente.index');
    }
}
