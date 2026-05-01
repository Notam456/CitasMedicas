<?php

namespace App\Http\Controllers;

use App\Models\Estado;
use Illuminate\Http\Request;
use RealRashid\SweetAlert\Facades\Alert;

class EstadoController extends Controller
{
    public function index()
    {
        $estados = Estado::all();
        $title = '¿Estás seguro de eliminar este estado?';
        $text = 'Esta acción no se puede deshacer.';
        confirmDelete($title, $text);
        return view('estados.listaEstados', compact('estados'));
    }

        public function show($id)
    {
        $estadoToShow = Estado::findOrFail($id);
        $estados = Estado::all();
        return view('estados.listaEstados', compact('estados', 'estadoToShow'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:255|unique:estados,nombre',
        ]);

        Estado::create($request->only('nombre'));

        Alert::success('Estado creado exitosamente.');
        return redirect()->route('estados.index');
    }

    public function edit($id)
    {
        $estadoToEdit = Estado::findOrFail($id);
        $estados = Estado::all();
        return view('estados.listaEstados', compact('estados', 'estadoToEdit'));
    }

    public function update(Request $request, $id)
    {
        $estado = Estado::findOrFail($id);
        $request->validate([
            'nombre' => 'required|string|max:255|unique:estados,nombre,' . $id,
        ]);

        $estado->update($request->only('nombre'));

        Alert::success('Estado actualizado exitosamente.');
        return redirect()->route('estados.index');
    }

    public function destroy($id)
    {
        $estado = Estado::findOrFail($id);
        $estado->delete();

        Alert::success('Estado eliminado exitosamente.');
        return redirect()->route('estados.index');
    }
}
