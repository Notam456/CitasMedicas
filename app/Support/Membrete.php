<?php

namespace App\Support;

class Membrete
{
    public static function base64(): string
    {
        $ruta = public_path('assets/img/membreteMPPS2.png');
        if (file_exists($ruta)) {
            return 'data:image/png;base64,' . base64_encode(file_get_contents($ruta));
        }
        return '';
    }
}
