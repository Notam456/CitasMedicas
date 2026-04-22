<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TestReportes extends Seeder
{
    public function run()
    {
        DB::table('especialidad')->insert([
            ['nombre' => 'Cardiología', 'descripcion' => 'Corazón', 'estado' => true],
            ['nombre' => 'Dermatología', 'descripcion' => 'Piel', 'estado' => true],
            ['nombre' => 'Pediatría', 'descripcion' => 'Niños', 'estado' => true],
        ]);

        DB::table('medico')->insert([
            ['nombres' => 'Juan', 'apellidos' => 'Perez', 'cedula' => 'V12345678', 'telefono' => '04141234567', 'id_especialidad' => 1, 'estado' => true],
            ['nombres' => 'Maria', 'apellidos' => 'Lopez', 'cedula' => 'V87654321', 'telefono' => '04147654321', 'id_especialidad' => 2, 'estado' => true],
        ]);
    }
}