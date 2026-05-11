<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Especialidad;

class EspecialidadSeeder extends Seeder
{
    public function run()
    {
        $especialidades = [
            'Cirugia General', 'Cirugia Plastica', 'Pre Operatorio', 'Cirugia Mano',
            'Cirugia de Torax', 'Cirugia Oncologica', 'NeuroCirugia', 'Otorrino',
            'Oftalmologia', 'Traumatologia', 'Urología', 'Cardiologia', 'Dermatologia',
            'Diabetes', 'Audioprotesis', 'Gastroenterologia', 'Ginecologia Oncologica',
            'Fonoaudiologia', 'Medicina Interna', 'Medicina Fisica y Rehabilitación',
            'Menopausia', 'Nefrologia Adulto', 'Hepatobiliar', 'NeuroPediatria',
            'Neumonologia Adulto', 'Oncologia', 'Psiquiatria', 'Patologia Medica del Embarazo',
            'Infectologia', 'Aro (Embarazados)', 'Pre Anestesia', 'Patologia Vulva/Vaginal',
            'Planificación Familiar', 'Psicopedagogia', 'CAINI', 'Foniatria',
            'Nutricion Clinica', 'Medico Ocupacional', 'Odontologia', 'Electroencefalograma',
            'Ano Rectal', 'Cirugia de Mama', 'Podologia', 'Cirugia Max Facial',
            'Artroscopia', 'Pre Operatorio Emb', 'Reumatologia', 'Psicologia',
            'Espirometria', 'Post Operado Cirugia'
        ];

        foreach ($especialidades as $nombre) {
            Especialidad::create([
                'nombre' => $nombre,
                'estado' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
