<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Cita;
use App\Models\Paciente;
use App\Models\Calendario;
use App\Models\User;

class CitaSeeder extends Seeder
{
    public function run()
    {
        $pacientes = Paciente::all();
        $calendarios = Calendario::all();
        $userAdmin = User::first(); // asumimos que existe un usuario admin

        $estados = ['Agendada', 'Atendida', 'Cancelada'];

        foreach ($calendarios as $calendario) {
            // Máximo 3 citas por calendario según cupos
            $numCitas = rand(0, min(3, $calendario->cupos_disponibles));
            for ($i = 0; $i < $numCitas; $i++) {
                $paciente = $pacientes->random();
                $estado = $estados[array_rand($estados)];
                Cita::create([
                    'paciente_id' => $paciente->id,
                    'calendario_id' => $calendario->id,
                    'user_id' => $userAdmin->id ?? 1,
                    'fecha_registro' => now()->subDays(rand(0, 30))->format('Y-m-d'),
                    'fecha_cita' => $calendario->fecha,
                    'estado' => $estado,
                    'observacion' => 'Observación de la cita',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
                // Reducir cupos disponibles en el calendario
                $calendario->decrement('cupos_disponibles');
            }
        }
    }
}
