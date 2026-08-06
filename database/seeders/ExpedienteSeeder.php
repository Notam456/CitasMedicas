<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Expediente;
use App\Models\Paciente;

class ExpedienteSeeder extends Seeder
{
    public function run()
    {
        $pacientes = Paciente::all();
        foreach ($pacientes as $paciente) {
            Expediente::create([
                'paciente_id' => $paciente->id,
                'numero_expediente' => rand(0, 99) . '-'. rand(0, 99) . '-' . rand(0, 99),
                'fecha_apertura' => $paciente->created_at->format('Y-m-d'),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
