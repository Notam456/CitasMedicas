<?php

namespace Database\Seeders;

use App\Models\Calendario;
use App\Models\Cita;
use App\Models\Paciente;
use App\Models\User;
use Illuminate\Database\Seeder;

class CitaSeeder extends Seeder
{
    public function run()
    {
        $pacientes = Paciente::all();
        $calendarios = Calendario::all();
        $userAdmin = User::first();

        $estados = ['Agendada', 'Atendida', 'Cancelada'];
        $tipos_paciente = ['primera_vez', 'control'];

        foreach ($calendarios as $calendario) {
            $citasExistentes = Cita::where('calendario_id', $calendario->id)
                ->pluck('paciente_id')
                ->toArray();

            $pacientesDisponibles = $pacientes->whereNotIn('id', $citasExistentes);


            if ($pacientesDisponibles->isEmpty()) {
                continue;
            }

            $numCitas = rand(0, min(3, $calendario->cupos_primera_vez));

            for ($i = 0; $i < $numCitas; $i++) {
                $paciente = $pacientesDisponibles->random();

                
                $pacientesDisponibles = $pacientesDisponibles->except($paciente->id);

                Cita::create([
                    'paciente_id' => $paciente->id,
                    'calendario_id' => $calendario->id,
                    'user_id' => $userAdmin->id ?? 1,
                    'fecha_registro' => now()->subDays(rand(0, 30))->format('Y-m-d'),
                    'fecha_cita' => $calendario->fecha,
                    'estado' => $estados[array_rand($estados)],
                    'tipo_paciente' => $tipos_paciente[array_rand($tipos_paciente)],
                    'observacion' => 'Observación de la cita',
                    'diagnostico_libre' => null,
                    'atendido_por' => null,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                // Si se agotan los pacientes disponibles mientras estamos en el loop, salimos
                if ($pacientesDisponibles->isEmpty()) {
                    break;
                }
            }
        }
    }
}
