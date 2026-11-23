<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EstadoMunicipioParroquiaSeeder extends Seeder
{
    public function run()
    {
 
        // Sin id explicito: al insertar con id fijo la secuencia no avanza y el
        // primer alta manual desde la interfaz choca con la PK (SQLSTATE 23505).
        DB::table('estados')->updateOrInsert(
            ['nombre' => 'Yaracuy'],
            ['created_at' => now(), 'updated_at' => now()]
        );

        $estadoId = DB::table('estados')->where('nombre', 'Yaracuy')->value('id');


        $distritos = [
            ['nombre' => 'Distrito I', 'created_at' => now(), 'updated_at' => now()],
            ['nombre' => 'Distrito II', 'created_at' => now(), 'updated_at' => now()],
            ['nombre' => 'Distrito III', 'created_at' => now(), 'updated_at' => now()],
            ['nombre' => 'Distrito IV', 'created_at' => now(), 'updated_at' => now()],
            ['nombre' => 'Distrito V', 'created_at' => now(), 'updated_at' => now()],
            ['nombre' => 'Otros Estados', 'created_at' => now(), 'updated_at' => now()],
        ];
        DB::table('distritos')->upsert($distritos, ['nombre'], ['created_at', 'updated_at']);
        $distritoIds = DB::table('distritos')->pluck('id', 'nombre')->toArray();


        $municipiosConDistrito = [

            'San Felipe' => 'Distrito I',
            'Independencia' => 'Distrito I',
            'Cocorote' => 'Distrito I',
            'Marín' => 'Distrito I',
            'Albarico' => 'Distrito I',
            'Farriar' => 'Distrito I',
            'El Guayabo' => 'Distrito I',
            'Boraure' => 'Distrito I',

            'Chivacoa' => 'Distrito II',
            'Campo Elias' => 'Distrito II',
            'San Pablo' => 'Distrito II',
            'Guama' => 'Distrito II',

            'Yaritagua' => 'Distrito III',
            'Urachiche' => 'Distrito III',
            'Sabana de Parra' => 'Distrito III',
  
            'Nirgua' => 'Distrito IV',

            'Yumare' => 'Distrito V',
            'Aroa' => 'Distrito V',

            'José Antonio Páez' => 'Distrito V',
            'Peña' => 'Distrito V',
            'Sucre' => 'Distrito V',
            'La Trinidad' => 'Distrito V',
            'Veroes' => 'Distrito V',
            'Arístides Bastidas' => 'Distrito V',
            'Bolívar' => 'Distrito V',
        ];

        foreach ($municipiosConDistrito as $nombre => $distritoNombre) {
            $distritoId = $distritoIds[$distritoNombre] ?? null;
            DB::table('municipios')->updateOrInsert(
                ['nombre' => $nombre, 'estado_id' => $estadoId],
                [
                    'distrito_id' => $distritoId,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }

        $municipios = DB::table('municipios')->where('estado_id', $estadoId)->get(['id', 'nombre']);
        foreach ($municipios as $municipio) {
            DB::table('parroquias')->updateOrInsert(
                ['nombre' => $municipio->nombre, 'municipio_id' => $municipio->id],
                ['created_at' => now(), 'updated_at' => now()]
            );
        }
    }
}
