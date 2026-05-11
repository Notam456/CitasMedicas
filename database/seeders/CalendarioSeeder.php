<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Calendario;
use App\Models\Medico;

class CalendarioSeeder extends Seeder
{
    public function run()
    {
        $medicos = Medico::all();
        $startDate = now()->startOfDay();
        $endDate = now()->addDays(90)->endOfDay();

        for ($date = $startDate; $date->lte($endDate); $date->addDay()) {
            // No todos los médicos trabajan todos los días, solo algunos días laborales (lunes a viernes)
            if ($date->isWeekend()) continue;

            foreach ($medicos as $medico) {
                // 60% de probabilidad de tener cupos ese día
                if (rand(1, 100) <= 60) {
                    Calendario::updateOrCreate(
                        [
                            'medico_id' => $medico->id,
                            'fecha' => $date->format('Y-m-d'),
                        ],
                        [
                            'hora_inicio' => '08:00:00',
                            'hora_fin' => '12:00:00',
                            'cupos_disponibles' => rand(5, 15),
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]
                    );
                }
            }
        }
    }
}
