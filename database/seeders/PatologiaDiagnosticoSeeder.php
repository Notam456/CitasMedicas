<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Especialidad;
use App\Models\Patologia;
use App\Models\Cita;
use App\Models\Medicamento;
use App\Models\User;
use Faker\Factory as Faker;

class PatologiaDiagnosticoSeeder extends Seeder
{
    public function run()
    {
        $faker = Faker::create('es_ES');
        $userAdmin = User::first();
        $medicamentos = Medicamento::all();

        $especialidades = Especialidad::all();
        $patologiasPorEspecialidad = [];

        // Crear patologías para cada especialidad
        foreach ($especialidades as $especialidad) {
            for ($i = 1; $i <= 2; $i++) {
                $patologia = Patologia::create([
                    'especialidad_id' => $especialidad->id,
                    'nombre' => $this->generarPatologia($especialidad->nombre, $i),
                    'descripcion' => $faker->sentence(10),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
                $patologiasPorEspecialidad[$especialidad->id][] = $patologia->id;
            }
        }

        // Obtener citas atendidas
        $citasAtendidas = Cita::where('estado', 'Atendida')->get();

        foreach ($citasAtendidas as $cita) {
            $especialidadId = $cita->medico->especialidad_id ?? null;
            if (!$especialidadId) continue;

            // Diagnóstico libre
            if ($faker->boolean(60)) {
                $cita->diagnostico_libre = $faker->sentence(6);
            }

            // Patologías
            $patologiaIds = $patologiasPorEspecialidad[$especialidadId] ?? [];
            if (!empty($patologiaIds)) {
                $numPatologias = $faker->numberBetween(1, min(3, count($patologiaIds)));
                $selectedPatologias = (array) $faker->randomElements($patologiaIds, $numPatologias);
                $cita->patologias()->sync($selectedPatologias);
            }

            // Medicamentos (dosis y duración numéricas)
            $numMedicamentos = $faker->numberBetween(0, 2);
            for ($i = 0; $i < $numMedicamentos; $i++) {
                $medicamento = $medicamentos->random();
                $cita->tratamientos()->create([
                    'medicamento_id' => $medicamento->id,
                    'dosis' => $faker->randomElement(['250', '500', '750', '1000']),
                    'duracion' => $faker->randomElement(['5', '7', '10', '14', '30']),
                    'indicaciones' => $faker->sentence(5),
                ]);
            }

            // Referencias
            $numReferencias = $faker->numberBetween(0, 2);
            $especialidadesDisponibles = Especialidad::where('id', '!=', $especialidadId)->get();
            if ($especialidadesDisponibles->count() > 0) {
                for ($i = 0; $i < $numReferencias; $i++) {
                    $especialidadRef = $especialidadesDisponibles->random();
                    $cita->referencias()->create([
                        'especialidad_id' => $especialidadRef->id,
                        'observaciones' => $faker->sentence(8),
                        'fecha_referencia' => $faker->optional()->dateTimeBetween('+1 week', '+1 month'),
                    ]);
                }
            }

            $cita->atendido_por = $userAdmin->id ?? 1;
            $cita->save();
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

        $genericas = ['Trastorno funcional', 'Síndrome inespecífico', 'Proceso inflamatorio', 'Dolor crónico'];
        return $genericas[($numero - 1) % count($genericas)];
    }
}