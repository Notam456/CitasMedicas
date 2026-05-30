<?php

namespace Database\Seeders;

use App\Models\Calendario;
use App\Models\Medico;
use Illuminate\Database\Seeder;

class CalendarioSeeder extends Seeder
{
    public function run()
    {
        $medicos = Medico::all();
        $startDate = now()->startOfDay()->subDay();
        $endDate = now()->addDays(90)->endOfDay();
        $hoy = now()->format('Y-m-d');

        for ($date = $startDate->clone(); $date->lte($endDate); $date->addDay()) {

            $fechaFormateada = $date->format('Y-m-d');
            $isToday = ($fechaFormateada === $hoy);

            // No todos los médicos trabajan todos los días, solo algunos días laborales (lunes a viernes)
            if ($date->isWeekend() && ! $isToday) {
                continue;
            }
            foreach ($medicos as $medico) {
                // 60% de probabilidad de tener cupos ese día
                if ($isToday || rand(1, 100) <= 60) {
                    Calendario::updateOrCreate(
                        [
                            'medico_id' => $medico->id,
                            'fecha' => $fechaFormateada,
                        ],
                        [
                            'hora_inicio' => '08:00:00',
                            'hora_fin' => '12:00:00',
                            'cupos_primera_vez' => rand(5, 15),
                            'cupos_sucesivos' => rand(5, 15),
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]
                    );
                }
            }
        }
    }
}
