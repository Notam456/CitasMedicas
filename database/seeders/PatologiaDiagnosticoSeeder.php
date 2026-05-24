<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Especialidad;
use App\Models\Patologia;
use App\Models\Cita;
use App\Models\Diagnostico;
use App\Models\User;
use Faker\Factory as Faker;

class PatologiaDiagnosticoSeeder extends Seeder
{
    public function run()
    {
        $faker = Faker::create('es_ES');
        $userAdmin = User::first(); // asumimos que existe un usuario administrador

        $especialidades = Especialidad::all();
        $patologiasPorEspecialidad = [];

        foreach ($especialidades as $especialidad) {
            for ($i = 1; $i <= 2; $i++) {
                $patologia = Patologia::create([
                    'especialidad_id' => $especialidad->id,
                    'nombre' => $this->generarPatologia($especialidad->nombre, $i),
                    'descripcion' => $faker->sentence(10),
                    'activo' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
                $patologiasPorEspecialidad[$especialidad->id][] = $patologia->id;
            }
        }

        $citasAtendidas = Cita::where('estado', 'Atendida')->get();

        foreach ($citasAtendidas as $cita) {
            $especialidadId = $cita->medico->especialidad_id ?? null;
            if (!$especialidadId) continue;

            $patologiaIds = $patologiasPorEspecialidad[$especialidadId] ?? [];
            $patologiaId = !empty($patologiaIds) ? $faker->randomElement($patologiaIds) : null;

            Diagnostico::create([
                'cita_id' => $cita->id,
                'patologia_id' => $patologiaId,
                'diagnostico_libre' => $patologiaId ? null : $faker->sentence(6),
                'asistio' => $faker->boolean(90), 
                'user_id' => $userAdmin->id ?? 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    private function generarPatologia($especialidad, $numero)
    {
        $patologiasBase = [
            'Cardiologia' => ['Hipertensión arterial', 'Insuficiencia cardíaca', 'Arritmia', 'Cardiopatía isquémica'],
            'Dermatologia' => ['Acné vulgar', 'Dermatitis atópica', 'Psoriasis', 'Melanoma'],
            'Oftalmologia' => ['Cataratas', 'Glaucoma', 'Conjuntivitis', 'Degeneración macular'],
            'Traumatologia' => ['Fractura de cadera', 'Esguince de tobillo', 'Artrosis de rodilla', 'Luxación de hombro'],
            'Pediatria' => ['Bronquiolitis', 'Varicela', 'Otitis media', 'Gastroenteritis'],
            'Neurologia' => ['Migraña', 'Epilepsia', 'Accidente cerebrovascular', 'Esclerosis múltiple'],
        ];

        foreach ($patologiasBase as $key => $list) {
            if (stripos($especialidad, $key) !== false) {
                $index = ($numero - 1) % count($list);
                return $list[$index];
            }
        }

        // Patologías genéricas
        $genericas = ['Trastorno funcional', 'Síndrome inespecífico', 'Proceso inflamatorio', 'Dolor crónico'];
        return $genericas[($numero - 1) % count($genericas)];
    }
}