<?php

namespace App\Http\Controllers;

use App\Models\Parroquia;
use App\Models\Municipio;
use Illuminate\Http\Request;

class ParroquiaController extends Controller
{
    public function index()
    {
        $parroquias = Parroquia::with('municipio.estado')->get();
        $municipios = Municipio::with('estado')->get();

        $title = '¿Estas seguro de que deseas eliminar esta parroquia?';
        $texrt = 'Esta acción no se puede deshacer.';
        confirmDelete($title, $texrt);

        return view('parroquias.listaParroquias', compact('parroquias', 'municipios'));
    }

    public function create()
    {
        // El registro se hace desde el modal en la misma vista.
    }

    public function show(int $id)
    {
        $parroquiaToshow = Parroquia::with('municipio.estado')->findOrFail($id);
        $parroquias = Parroquia::with('municipio.estado')->get();
        $municipios = Municipio::with('estado')->get();

        return view('parroquias.listaParroquias', compact('parroquias', 'municipios', 'parroquiaToshow'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'municipio_id' => 'required|exists:municipios,id',
        ]);

        Parroquia::create($request->only(['nombre', 'municipio_id']));

        alert()->success('Parroquia creada exitosamente.');
        return redirect()->route('parroquias.index');
    }

    public function edit(int $id)
    {
        $parroquiaToEdit = Parroquia::with('municipio.estado')->findOrFail($id);
        $parroquias = Parroquia::with('municipio.estado')->get();
        $municipios = Municipio::with('estado')->get();

        return view('parroquias.listaParroquias', compact('parroquias', 'municipios', 'parroquiaToEdit'));
    }

    public function update(Request $request, int $id)
    {
        $parroquia = Parroquia::findOrFail($id);

        $request->validate([
            'nombre' => 'required|string|max:255',
            'municipio_id' => 'required|exists:municipios,id',
        ]);

        $parroquia->update($request->only(['nombre', 'municipio_id']));

        alert()->success('Parroquia actualizada exitosamente.');
        return redirect()->route('parroquias.index');
    }

    public function destroy(int $id)
    {
        $parroquia = Parroquia::findOrFail($id);
        $parroquia->delete();

        alert()->success('Parroquia eliminada exitosamente.');
        return redirect()->route('parroquias.index');
    }
}
