<?php

namespace App\Http\Controllers;

use App\Models\Municipio;
use App\Models\Estado;
use Illuminate\Http\Request;
use RealRashid\SweetAlert\Facades\Alert;
use Illuminate\Validation\Rule;

class MunicipioController extends Controller
{
    public function index() {

        $municipios = Municipio::with('estado')->get();
        $estados = Estado::all(); // para el select en el modal
        confirmDelete('¿Eliminar municipio?', 'Esta acción no se puede deshacer.');
        return view('municipios.listaMunicipios', compact('municipios', 'estados'));
    }

    public function store(Request $request){

        $request->validate([
            'nombre' => [
                'required', 
                'string', 
                'max:255',
                Rule::unique('municipios')->where(function ($query) use ($request) {
                    return $query->where('estado_id', $request->estado_id);
                })
            ],
            'estado_id' => 'required|exists:estados,id',
        ]);

        Municipio::create($request->only(['nombre', 'estado_id']));

        Alert::success('Municipio creado exitosamente.');
        return redirect()->route('municipios.index');
    }

    public function edit($id){

        $municipioToEdit = Municipio::findOrFail($id);
        $municipios = Municipio::with('estado')->get();
        $estados = Estado::all();
        return view('municipios.listaMunicipios', compact('municipios', 'municipioToEdit', 'estados'));
    }

    public function update(Request $request, $id){

        $municipio = Municipio::findOrFail($id);
        $request->validate([
            'nombre' => [
                'required',
                'string',
                'max:255',
                Rule::unique('municipios')->where(function ($query) use ($request, $id) {
                    return $query->where('estado_id', $request->estado_id)->where('id', '!=', $id);
                })
            ],
            'estado_id' => 'required|exists:estados,id',
        ]);

        $municipio->update($request->only(['nombre', 'estado_id']));

        Alert::success('Municipio actualizado exitosamente.');
        return redirect()->route('municipios.index');
    }

    public function destroy($id){
        
        $municipio = Municipio::findOrFail($id);
        $municipio->delete();

        Alert::success('Municipio eliminado exitosamente.');
        return redirect()->route('municipios.index');
    }
}