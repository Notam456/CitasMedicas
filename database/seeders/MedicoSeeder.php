<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Medico;
use App\Models\Especialidad;
use Faker\Factory as Faker;

class MedicoSeeder extends Seeder
{
    public function run()
    {
        $faker = Faker::create('es_ES');
        $especialidades = Especialidad::all();

        foreach ($especialidades as $especialidad) {
            $numMedicos = rand(1, 3);
            for ($i = 0; $i < $numMedicos; $i++) {
                $medico = Medico::create([
                    'especialidad_id' => $especialidad->id,
                    'nombre' => $faker->firstName(),
                    'apellido' => $faker->lastName(),
                    'cedula' => (string) rand(10000000, 30000000),
                    'telefono' => '0414' . rand(100000, 999999),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                if (rand(1, 100) <= 70) {
                    $dias = collect([1, 2, 3, 4, 5, 6, 7])->random(rand(2, 5))->values()->all();
                    foreach ($dias as $dia) {
                        $medico->horarios()->create([
                            'dia_semana' => $dia,
                            'hora_entrada' => '08:00:00',
                            'hora_salida' => '12:00:00',
                        ]);
                    }
                }
            }
        }
    }
}