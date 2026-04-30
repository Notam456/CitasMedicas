<?php

namespace App\Http\Controllers;

use App\Models\Municipio;
use App\Models\Estado;
use Illuminate\Http\Request;

class MunicipioController extends Controller
{
    public function index()
    {
        $municipios = Municipio::with('estado')->get();
        $estados = Estado::all();

        $title = '¿Estas seguro de que deseas eliminar este municipio?';
        $texrt = 'Esta acción no se puede deshacer.';
        confirmDelete($title, $texrt);

        return view('municipios.listaMunicipios', compact('municipios', 'estados'));
    }

    public function create()
    {
        // El registro se hace desde el modal en la misma vista.
    }

    public function show(int $id)
    {
        $municipioToshow = Municipio::with('estado')->findOrFail($id);
        $municipios = Municipio::with('estado')->get();
        $estados = Estado::all();

        return view('municipios.listaMunicipios', compact('municipios', 'estados', 'municipioToshow'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'estado_id' => 'required|exists:estados,id',
        ]);

        Municipio::create($request->only(['nombre', 'estado_id']));

        alert()->success('Municipio creado exitosamente.');
        return redirect()->route('municipios.index');
    }

    public function edit(int  $id)
    {
        $municipioToEdit = Municipio::with('estado')->findOrFail($id);
        $municipios = Municipio::with('estado')->get();
        $estados = Estado::all();

        return view('municipios.listaMunicipios', compact('municipios', 'estados', 'municipioToEdit'));
    }

    public function update(Request $request, int $id)
    {
        $municipio = Municipio::findOrFail($id);

        $request->validate([
            'nombre' => 'required|string|max:255',
            'estado_id' => 'required|exists:estados,id',
        ]);

        $municipio->update($request->only(['nombre', 'estado_id']));

        alert()->success('Municipio actualizado exitosamente.');
        return redirect()->route('municipios.index');
    }

    public function destroy(int $id)
    {
        $municipio = Municipio::findOrFail($id);
        $municipio->delete();

        alert()->success('Municipio eliminado exitosamente.');
        return redirect()->route('municipios.index');
    }
}