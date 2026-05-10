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
            $numMedicos = rand(1, 2);
            for ($i = 0; $i < $numMedicos; $i++) {
                Medico::create([
                    'especialidad_id' => $especialidad->id,
                    'nombre' => $faker->firstName(),
                    'apellido' => $faker->lastName(),
                    'cedula' => (string) rand(10000000, 30000000),
                    'telefono' => '0414' . rand(100000, 999999),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }
}