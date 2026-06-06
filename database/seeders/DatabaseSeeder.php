<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        $this->call([
            PermissionSeeder::class,
            EstadoMunicipioParroquiaSeeder::class,
            EspecialidadSeeder::class,
            MedicoSeeder::class,
            PacienteSeeder::class,
            ExpedienteSeeder::class,
            CalendarioSeeder::class,
            UserSeeder::class,
            CitaSeeder::class,
            MedicamentoSeeder::class,
            PatologiaDiagnosticoSeeder::class,
            NotificationSeeder::class,
        ]);
    }
}
