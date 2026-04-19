<?php

namespace App\Http\Controllers;

use App\Models\Estado;
use Illuminate\Http\Request;

class EstadoController extends Controller
{
    public function index() { return response()->json(Estado::all()); }

    public function store(Request $request) {
        $estado = Estado::create($request->all());
        return response()->json($estado, 201);
    }

    public function show($id) { return response()->json(Estado::find($id)); }

    public function update(Request $request, $id) {
        $estado = Estado::find($id);
        $estado->update($request->all());
        return response()->json($estado);
    }

    public function destroy($id) {
        Estado::destroy($id);
        return response()->json(['message' => 'Estado eliminado']);
    }
}