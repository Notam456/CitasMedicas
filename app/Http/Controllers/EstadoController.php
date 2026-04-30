<?php

namespace App\Http\Controllers;

use App\Models\Estado;
use Illuminate\Http\Request;

class EstadoController extends Controller
{
    public function index()
    {
        $estados = Estado::all();

        $title = '¿Estas seguro de que deseas eliminar este estado?';
        $texrt = 'Esta acción no se puede deshacer.';
        confirmDelete($title, $texrt);

        return view('estados.listaEstados', compact('estados'));
    }

    public function create()
    {
        // El registro se hace desde el modal en la misma vista.
    }

    public function show(int $id)
    {
        $estadoToshow = Estado::findOrFail($id);
        $estados = Estado::all();

        return view('estados.listaEstados', compact('estados', 'estadoToshow'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:255|unique:estados,nombre',
        ]);

        Estado::create($request->only(['nombre']));

        alert()->success('Estado creado exitosamente.');
        return redirect()->route('estados.index');
    }

    public function edit(int $id)
    {
        $estadoToEdit = Estado::findOrFail($id);
        $estados = Estado::all();

        return view('estados.listaEstados', compact('estados', 'estadoToEdit'));
    }

    public function update(Request $request, int $id)
    {
        $estado = Estado::findOrFail($id);

        $request->validate([
            'nombre' => 'required|string|max:255|unique:estados,nombre,' . $id,
        ]);

        $estado->update($request->only(['nombre']));

        alert()->success('Estado actualizado exitosamente.');
        return redirect()->route('estados.index');
    }

    public function destroy(int $id)
    {
        $estado = Estado::findOrFail($id);
        $estado->delete();

        alert()->success('Estado eliminado exitosamente.');
        return redirect()->route('estados.index');
    }
}