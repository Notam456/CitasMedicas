<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Expediente;
use App\Models\Paciente;

class ExpedienteSeeder extends Seeder
{
    public function run()
    {
        $pacientes = Paciente::all();
        $usados = Expediente::pluck('numero_expediente')->flip()->all();

        foreach ($pacientes as $paciente) {
            $numero = $this->generarNumeroUnico($usados);
            $usados[$numero] = true;

            Expediente::create([
                'paciente_id' => $paciente->id,
                'numero_expediente' => $numero,
                'fecha_apertura' => $paciente->created_at->format('Y-m-d'),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    private function generarNumeroUnico(array &$usados): string
    {
        $maxIntentos = 50;

        for ($i = 0; $i < $maxIntentos; $i++) {
            $numero = random_int(10, 99) . '-' . random_int(10, 99) . '-' . random_int(10, 99);
            if (!isset($usados[$numero])) {
                return $numero;
            }
        }

        $ultimo = 0;
        foreach (array_keys($usados) as $existente) {
            if (preg_match('/^\d{2}-\d{2}-\d{2}$/', $existente)) {
                $ultimo = max($ultimo, (int) str_replace('-', '', $existente));
            }
        }

        if ($ultimo >= 999999) {
            throw new \RuntimeException('No hay más números de expediente disponibles en el formato 00-00-00.');
        }

        return sprintf('%02d-%02d-%02d', intdiv($ultimo + 1, 10000), intdiv($ultimo + 1, 100) % 100, ($ultimo + 1) % 100);
    }
}
