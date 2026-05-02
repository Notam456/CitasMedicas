<?php

namespace App\Http\Controllers;

use App\Models\Parroquia;
use App\Models\Municipio;
use App\Models\Estado;
use Illuminate\Http\Request;
use RealRashid\SweetAlert\Facades\Alert;
use Illuminate\Validation\Rule;

class ParroquiaController extends Controller
{
    public function index(){
        
        $parroquias = Parroquia::with('municipio.estado')->get();
        $estados = Estado::all();
        confirmDelete('¿Eliminar parroquia?', 'Esta acción no se puede deshacer.');
        return view('parroquias.listaParroquias', compact('parroquias', 'estados'));
    }

    public function show($id)
    {
        $parroquiaToShow = Parroquia::with('municipio.estado')->findOrFail($id);
        return response()->json($parroquiaToShow);
    }

    public function getMunicipiosPorEstado($estado_id){

        $municipios = Municipio::where('estado_id', $estado_id)->get(['id', 'nombre']);
        return response()->json($municipios);
    }

    public function store(Request $request){

        $request->validate([
            'nombre' => [
                'required',
                'string',
                'max:255',
                Rule::unique('parroquias')->where(function ($query) use ($request) {
                    return $query->where('municipio_id', $request->municipio_id);
                })
            ],
            'municipio_id' => 'required|exists:municipios,id',
        ]);

        Parroquia::create($request->only(['nombre', 'municipio_id']));

        Alert::success('Parroquia creada exitosamente.');
        return redirect()->route('parroquias.index');
    }

    public function edit($id){

        $parroquiaToEdit = Parroquia::with('municipio.estado')->findOrFail($id);
        return response()->json($parroquiaToEdit);
    }

    public function update(Request $request, $id){

        $parroquia = Parroquia::findOrFail($id);
        $request->validate([
            'nombre' => [
                'required',
                'string',
                'max:255',
                Rule::unique('parroquias')->where(function ($query) use ($request, $id) {
                    return $query->where('municipio_id', $request->municipio_id)->where('id', '!=', $id);
                })
            ],
            'municipio_id' => 'required|exists:municipios,id',
        ]);

        $parroquia->update($request->only(['nombre', 'municipio_id']));

        Alert::success('Parroquia actualizada exitosamente.');
        return redirect()->route('parroquias.index');
    }

    public function destroy($id){

        $parroquia = Parroquia::findOrFail($id);
        $parroquia->delete();

        Alert::success('Parroquia eliminada exitosamente.');
        return redirect()->route('parroquias.index');
    }
}