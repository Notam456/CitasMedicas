<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Medicamento;

class MedicamentoSeeder extends Seeder
{
    public function run()
    {
        $medicamentos = [
            ['nombre' => 'Paracetamol', 'descripcion' => 'Analgésico y antipirético.'],
            ['nombre' => 'Ibuprofeno', 'descripcion' => 'Antiinflamatorio no esteroideo.'],
            ['nombre' => 'Amoxicilina', 'descripcion' => 'Antibiótico de amplio espectro.'],
            ['nombre' => 'Losartán', 'descripcion' => 'Antihipertensivo.'],
            ['nombre' => 'Metformina', 'descripcion' => 'Antidiabético.'],
            ['nombre' => 'Omeprazol', 'descripcion' => 'Inhibidor de bomba de protones.'],
            ['nombre' => 'Salbutamol', 'descripcion' => 'Broncodilatador.'],
            ['nombre' => 'Enalapril', 'descripcion' => 'Inhibidor de la ECA.'],
            ['nombre' => 'Atorvastatina', 'descripcion' => 'Hipolipemiante.'],
            ['nombre' => 'Diazepam', 'descripcion' => 'Ansiolítico.'],
        ];

        foreach ($medicamentos as $med) {
            Medicamento::create($med);
        }
    }
}
