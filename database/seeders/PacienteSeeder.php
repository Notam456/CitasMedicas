<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Paciente;
use App\Models\Parroquia;
use Faker\Factory as Faker;

class PacienteSeeder extends Seeder
{
    public function run()
    {
        $faker = Faker::create('es_ES');
        $parroquias = Parroquia::pluck('id')->toArray();

        for ($i = 0; $i < 50; $i++) {
            Paciente::create([
                'parroquia_id' => $faker->randomElement($parroquias),
                'nombre' => $faker->firstName(),
                'apellido' => $faker->lastName(),
                'cedula' => (string) rand(5000000, 28000000),
                'fecha_nacimiento' => $faker->dateTimeBetween('-80 years', '-18 years')->format('Y-m-d'),
                'telefono' => '0412' . rand(100000, 999999),
                'direccion' => $faker->streetAddress(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
