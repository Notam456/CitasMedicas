<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Estado;
use App\Models\Municipio;
use App\Models\Parroquia;

class UbicacionesYaracuySeeder extends Seeder
{
    public function run(): void
    {
        // 1. Creamos el Estado
        $estado = Estado::create(['nombre' => 'Yaracuy']);

        // 2. Definimos los 14 municipios
        $municipiosYaracuy = [
            'Aristides Bastidas', 'Bolívar', 'Bruzual', 'Cocorote', 
            'Independencia', 'José Antonio Páez', 'La Trinidad', 
            'Manuel Monge', 'Nirgua', 'Peña', 'San Felipe', 
            'Sucre', 'Urachiche', 'Veroes'
        ];

        // 3. Los guardamos asociados al estado y aprovechamos para capturarlos
        foreach ($municipiosYaracuy as $nombre) {
            $nuevoMunicipio = Municipio::create([
                'nombre' => $nombre,
                'estado_id' => $estado->id
            ]);

            // 4. Agregamos parroquias a municipios específicos para probar
            if ($nombre == 'San Felipe') {
                $parroquias = ['San Felipe', 'Albarico', 'San Javier'];
                foreach ($parroquias as $p) {
                    Parroquia::create(['nombre' => $p, 'municipio_id' => $nuevoMunicipio->id]);
                }
            }

            if ($nombre == 'Independencia') {
                Parroquia::create(['nombre' => 'Independencia', 'municipio_id' => $nuevoMunicipio->id]);
            }

            if ($nombre == 'Cocorote') {
                Parroquia::create(['nombre' => 'Cocorote', 'municipio_id' => $nuevoMunicipio->id]);
            }
            
            // Si no es ninguno de los de arriba, le creamos una parroquia con el mismo nombre por defecto
            if (!in_array($nombre, ['San Felipe', 'Independencia', 'Cocorote'])) {
                Parroquia::create(['nombre' => $nombre, 'municipio_id' => $nuevoMunicipio->id]);
            }
        }
    }
}
