<?php

namespace App\Http\Controllers;

use App\Models\Especialidad;
use Illuminate\Http\Request;

class EspecialidadController extends Controller
{
    public function index()
    {
        $especialidades = Especialidad::all();
        return view('especialidades', compact('especialidades'));
    }

    public function create()
    {
        return view('especialidades_create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'descripcion' => 'nullable|string',
            'estado' => 'boolean'
        ]);

        Especialidad::create($request->all());
        return redirect()->route('especialidades.index');
    }

    public function edit($id)
    {
        $especialidad = Especialidad::findOrFail($id);
        return view('especialidades_edit', compact('especialidad'));
    }

    public function update(Request $request, $id)
    {
        $especialidad = Especialidad::findOrFail($id);
        $especialidad->update($request->all());
        return redirect()->route('especialidades.index');
    }

    public function destroy($id)
    {
        $especialidad = Especialidad::findOrFail($id);
        $especialidad->delete();
        return redirect()->route('especialidades.index');
    }
}
