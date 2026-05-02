<?php

namespace App\Http\Controllers;

use App\Models\Medico;
use App\Models\Especialidad;
use Illuminate\Http\Request;

class MedicoController extends Controller
{
    public function index()
    {
        $medicos = Medico::with('especialidad')->get();
        $especialidades = Especialidad::all();

        $title = '¿Estas seguro de que deseas eliminar este médico?';
        $texrt = 'Esta acción no se puede deshacer.';
        confirmDelete($title, $texrt);

        return view('medicos.listaMedicos', compact('medicos', 'especialidades'));
    }

    public function create()
    {
        // El registro se hace desde el modal en la misma vista.
    }

    public function show(int $id)
    {
        $medicoToShow = Medico::with('especialidad')->findOrFail($id);

        return response()->json($medicoToShow);
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'apellido' => 'required|string|max:255',
            'cedula' => 'required|string|unique:medicos,cedula',
            'telefono' => 'required|string|max:20',
            'especialidad_id' => 'required|exists:especialidades,id',
        ]);

        Medico::create($request->only([
            'nombre',
            'apellido',
            'cedula',
            'telefono',
            'especialidad_id',
        ]));

        alert()->success('Médico creado exitosamente.');
        return redirect()->route('medicos.index');
    }

    public function edit(int $id)
    {
        $medicoToEdit = Medico::with('especialidad')->findOrFail($id);
        return response()->json($medicoToEdit);
    }

    public function update(Request $request, int $id)
    {
        $medico = Medico::findOrFail($id);

        $request->validate([
            'nombre' => 'required|string|max:255',
            'apellido' => 'required|string|max:255',
            'cedula' => 'required|string|unique:medicos,cedula,' . $id,
            'telefono' => 'required|string|max:20',
            'especialidad_id' => 'required|exists:especialidades,id',
        ]);

        $medico->update($request->only([
            'nombre',
            'apellido',
            'cedula',
            'telefono',
            'especialidad_id',
        ]));

        alert()->success('Médico actualizado exitosamente.');
        return redirect()->route('medicos.index');
    }

    public function destroy(int $id)
    {
        $medico = Medico::findOrFail($id);
        $medico->delete();

        alert()->success('Médico eliminado exitosamente.');
        return redirect()->route('medicos.index');
    }
}