<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EstadoMunicipioParroquiaSeeder extends Seeder
{
    public function run()
    {
        DB::table('estados')->insert([
            'id' => 1,
            'nombre' => 'Yaracuy',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $municipios = [
            ['id' => 1, 'estado_id' => 1, 'nombre' => 'San Felipe'],
            ['id' => 2, 'estado_id' => 1, 'nombre' => 'Independencia'],
            ['id' => 3, 'estado_id' => 1, 'nombre' => 'José Antonio Páez'],
            ['id' => 4, 'estado_id' => 1, 'nombre' => 'Peña'],
            ['id' => 5, 'estado_id' => 1, 'nombre' => 'Urachiche'],
            ['id' => 6, 'estado_id' => 1, 'nombre' => 'Nirgua'],
            ['id' => 7, 'estado_id' => 1, 'nombre' => 'Sucre'],
            ['id' => 8, 'estado_id' => 1, 'nombre' => 'La Trinidad'],
            ['id' => 9, 'estado_id' => 1, 'nombre' => 'Cocorote'],
            ['id' =>10, 'estado_id' => 1, 'nombre' => 'Veroes'],
            ['id' =>11, 'estado_id' => 1, 'nombre' => 'Arístides Bastidas'],
            ['id' =>12, 'estado_id' => 1, 'nombre' => 'Bolívar'],
        ];
        DB::table('municipios')->insert($municipios);

        $parroquias = [
            ['id' => 1, 'municipio_id' => 1, 'nombre' => 'San Felipe'],
            ['id' => 2, 'municipio_id' => 2, 'nombre' => 'Independencia'],
            ['id' => 3, 'municipio_id' => 3, 'nombre' => 'José Antonio Páez'],
            ['id' => 4, 'municipio_id' => 4, 'nombre' => 'Peña'],
            ['id' => 5, 'municipio_id' => 5, 'nombre' => 'Urachiche'],
            ['id' => 6, 'municipio_id' => 6, 'nombre' => 'Nirgua'],
            ['id' => 7, 'municipio_id' => 7, 'nombre' => 'Sucre'],
            ['id' => 8, 'municipio_id' => 8, 'nombre' => 'La Trinidad'],
            ['id' => 9, 'municipio_id' => 9, 'nombre' => 'Cocorote'],
            ['id' =>10, 'municipio_id' =>10, 'nombre' => 'Veroes'],
            ['id' =>11, 'municipio_id' =>11, 'nombre' => 'Arístides Bastidas'],
            ['id' =>12, 'municipio_id' =>12, 'nombre' => 'Bolívar'],
        ];
        DB::table('parroquias')->insert($parroquias);
    }
}
