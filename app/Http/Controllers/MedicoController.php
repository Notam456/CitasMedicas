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
        return view('medico', compact('medicos'));
    }

    public function create()
    {
        $especialidades = Especialidad::all();
        return view('medico_create', compact('especialidades'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombres' => 'required|string|max:255',
            'apellidos' => 'required|string|max:255',
            'cedula' => 'required|string|unique:medico',
            'telefono' => 'required|string|max:20',
            'id_especialidad' => 'required|exists:especialidad,id_especialidad',
            'estado' => 'boolean'
        ]);

        Medico::create($request->all());
        return redirect()->route('medicos.index')->with('success', 'Médico creado');
    }

    public function edit($id)
    {
        $medico = Medico::findOrFail($id);
        $especialidades = Especialidad::all();
        return view('medico_edit', compact('medico', 'especialidades'));
    }

    public function update(Request $request, $id)
    {
        $medico = Medico::findOrFail($id);
        $request->validate([
            'nombres' => 'required|string|max:255',
            'apellidos' => 'required|string|max:255',
            'cedula' => 'required|string|unique:medico,cedula,' . $id . ',id_medico',
            'telefono' => 'required|string|max:20',
            'id_especialidad' => 'required|exists:especialidad,id_especialidad',
            'estado' => 'boolean'
        ]);

        $medico->update($request->all());
        return redirect()->route('medicos.index')->with('success', 'Médico actualizado');
    }

    public function destroy($id)
    {
        $medico = Medico::findOrFail($id);
        $medico->update(['estado' => false]);
        return redirect()->route('medicos.index')->with('success', 'Médico desactivado');
    }
}