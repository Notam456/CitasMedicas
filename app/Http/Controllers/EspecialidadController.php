<?php

namespace App\Http\Controllers;

use App\Models\Especialidad;
use Illuminate\Http\Request;
use RealRashid\SweetAlert\Facades\Alert;

class EspecialidadController extends Controller
{
    public function index()
    {
        $especialidades = Especialidad::all();

        $title = '¿Estas seguro de que deseas eliminar esta especialidad?';
        $texrt = 'Esta acción no se puede deshacer.';
        confirmDelete($title, $texrt);

        return view('especialidades.listaEspecialidades', compact('especialidades'));
    }

    public function create()
    {
        //
    }

    public function show(int $id)
    {
        $especialidadToshow = Especialidad::findOrFail($id);
        $especialidades = Especialidad::all();

        return view('especialidades.listaEspecialidades', compact('especialidades', 'especialidadToshow'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
        ]);

        Especialidad::create($request->all());

        alert()->success('Especialidad creada exitosamente.');
        return redirect()->route('especialidades.index');
    }

    public function edit(int $id)
    {
        $especialidadToEdit = Especialidad::findOrFail($id);
        $especialidades = Especialidad::all();
        return view('especialidades.listaEspecialidades', compact('especialidades', 'especialidadToEdit'));
    }

    public function update(Request $request,int $id)
    {
            $request->validate([
                'nombre' => 'required|string|max:255',
            ]);

        $especialidad = Especialidad::findOrFail($id);
        $especialidad->update($request->all());

        alert()->success('Especialidad actualizada exitosamente.');
        return redirect()->route('especialidades.index');
    }

    public function destroy(int $id)
    {
        $especialidad = Especialidad::findOrFail($id);
        $especialidad->delete();

        alert()->success('Especialidad eliminada exitosamente.');
        return redirect()->route('especialidades.index');
    }
}