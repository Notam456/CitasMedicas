<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Morbilidad;
use App\Models\Cita;
use Faker\Factory as Faker;

class MorbilidadSeeder extends Seeder
{
    public function run()
    {
        $faker = Faker::create('es_ES');
        $citasAtendidas = Cita::where('estado', 'Atendida')->get();

        foreach ($citasAtendidas as $cita) {
            Morbilidad::create([
                'cita_id' => $cita->id,
                'diagnostico' => $faker->sentence(6),
                'observaciones' => $faker->paragraph(2),
                'asistio' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}