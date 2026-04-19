<?php

namespace App\Http\Controllers;

use App\Models\Municipio;
use Illuminate\Http\Request;

class MunicipioController extends Controller
{
    public function index() { return response()->json(Municipio::all()); }

    public function store(Request $request) {
        $municipio = Municipio::create($request->all());
        return response()->json($municipio, 201);
    }

    public function show($id) { return response()->json(Municipio::find($id)); }

    public function update(Request $request, $id) {
        $municipio = Municipio::find($id);
        $municipio->update($request->all());
        return response()->json($municipio);
    }

    public function destroy($id) {
        Municipio::destroy($id);
        return response()->json(['message' => 'Municipio eliminado']);
    }
}