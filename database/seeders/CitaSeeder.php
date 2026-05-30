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
        $tipos_paciente = ['primera_vez', 'control'];

        foreach ($calendarios as $calendario) {
            $numCitas = rand(0, min(3, $calendario->cupos_primera_vez));
            for ($i = 0; $i < $numCitas; $i++) {
                $paciente = $pacientes->random();
                $estado = $estados[array_rand($estados)];
                $tipo_paciente = $tipos_paciente[array_rand($tipos_paciente)];
                Cita::create([
                    'paciente_id' => $paciente->id,
                    'calendario_id' => $calendario->id,
                    'user_id' => $userAdmin->id ?? 1,
                    'fecha_registro' => now()->subDays(rand(0, 30))->format('Y-m-d'),
                    'fecha_cita' => $calendario->fecha,
                    'estado' => $estado,
                    'tipo_paciente' => $tipo_paciente,
                    'observacion' => 'Observación de la cita',
                    'diagnostico_libre' => null,   // inicialmente null
                    'atendido_por' => null,        // inicialmente null
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }
}
