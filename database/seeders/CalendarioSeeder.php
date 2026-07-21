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
        $especialidades = \App\Models\Especialidad::all();
        $startDate = now()->startOfDay()->subDay();
        $endDate = now()->addDays(90)->endOfDay();
        $hoy = now()->format('Y-m-d');

        for ($date = $startDate->clone(); $date->lte($endDate); $date->addDay()) {

            $fechaFormateada = $date->format('Y-m-d');
            $isToday = ($fechaFormateada === $hoy);

            if ($date->isWeekend() && ! $isToday) {
                continue;
            }

            foreach ($especialidades as $especialidad) {
                $allowsAnyDoctor = ($especialidad->id % 2 == 0); 

                if ($allowsAnyDoctor && rand(1, 100) <= 20) {
                    Calendario::updateOrCreate(
                        [
                            'medico_id' => null,
                            'especialidad_id' => $especialidad->id,
                            'fecha' => $fechaFormateada,
                        ],
                        [
                            'hora_inicio' => '08:00:00',
                            'hora_fin' => '12:00:00',
                            'cupos_primera_vez' => rand(10, 20),
                            'cupos_sucesivos' => rand(10, 20),
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]
                    );
                }
            }

            foreach ($medicos as $medico) {

                if ($medico->horario && count($medico->horario) > 0) {
                    if (!in_array($date->dayOfWeekIso, array_map('intval', $medico->horario))) {
                        continue;
                    }
                }

                // 60% de probabilidad de tener cupos ese día
                if ($isToday || rand(1, 100) <= 60) {
                    Calendario::updateOrCreate(
                        [
                            'medico_id' => $medico->id,
                            'especialidad_id' => $medico->especialidad_id,
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
