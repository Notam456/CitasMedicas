<?php

namespace App\Http\Controllers;

use App\Models\Municipio;
use App\Models\Estado;
use App\Models\Distrito; 
use Illuminate\Http\Request;
use RealRashid\SweetAlert\Facades\Alert;
use Illuminate\Validation\Rule;

class MunicipioController extends Controller
{
    public function index() {
        $municipios = Municipio::with('estado', 'distrito')->get();
        $estados = Estado::all();
        $distritos = Distrito::all(); 
        confirmDelete('¿Eliminar municipio?', 'Esta acción no se puede deshacer.');
        return view('municipios.listaMunicipios', compact('municipios', 'estados', 'distritos'));
    }
    
    public function show($id)
    {
        $municipio = Municipio::with('estado', 'distrito')->findOrFail($id);
        return response()->json([
            'id' => $municipio->id,
            'nombre' => $municipio->nombre,
            'estado' => $municipio->estado->nombre ?? null,
            'distrito' => $municipio->distrito->nombre ?? 'Sin distrito'
        ]);
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
            'distrito_id' => 'nullable|exists:distritos,id',
        ]);

        Municipio::create($request->only(['nombre', 'estado_id', 'distrito_id']));

        Alert::success('Municipio creado exitosamente.');
        return redirect()->route('municipios.index');
    }

    public function edit($id){
        $municipioToEdit = Municipio::findOrFail($id);
        return response()->json($municipioToEdit);
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
            'distrito_id' => 'nullable|exists:distritos,id',
        ]);

        $municipio->update($request->only(['nombre', 'estado_id', 'distrito_id']));

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