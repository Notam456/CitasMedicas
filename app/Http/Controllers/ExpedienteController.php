<?php

namespace App\Http\Controllers;

use App\Models\Expediente;
use App\Models\Paciente;
use Illuminate\Http\Request;

class ExpedienteController extends Controller
{
    public function guardar(Request $request, Paciente $paciente)
    {
        $request->validate([
            'numero_expediente' => 'required|regex:/^\d{2}-\d{2}-\d{2}$/',
        ]);

        $numero = trim($request->numero_expediente);

        $duplicado = Expediente::where('numero_expediente', $numero)
            ->where('paciente_id', '!=', $paciente->id)
            ->exists();

        if ($duplicado) {
            return response()->json([
                'message' => 'Ese número de historia ya está asignado a otro paciente.',
            ], 422);
        }

        $expediente = $paciente->expediente;

        if ($expediente) {
            $expediente->update(['numero_expediente' => $numero]);
        } else {
            Expediente::create([
                'paciente_id' => $paciente->id,
                'numero_expediente' => $numero,
                'fecha_apertura' => now()->toDateString(),
            ]);
        }

        return response()->json(['numero_expediente' => $numero]);
    }
}
