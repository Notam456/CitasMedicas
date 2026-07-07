<?php

namespace App\Http\Controllers;

use App\Models\Expediente;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ExpedienteController extends Controller
{
    public function asignarNumero(Request $request)
    {
        $request->validate([
            'paciente_id' => 'required|exists:pacientes,id',
            'numero_expediente' => 'required|string|max:255|unique:expedientes,numero_expediente',
        ]);

        $exists = Expediente::where('paciente_id', $request->paciente_id)->exists();
        if ($exists) {
            return response()->json([
                'success' => false,
                'message' => 'El paciente ya tiene un número de historia asignado.',
            ], 422);
        }

        try {
            $expediente = Expediente::create([
                'paciente_id' => $request->paciente_id,
                'numero_expediente' => $request->numero_expediente,
                'fecha_apertura' => now(),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Número de historia asignado correctamente.',
                'numero_expediente' => $expediente->numero_expediente,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al asignar el número de historia: ' . $e->getMessage(),
            ], 500);
        }
    }
}
