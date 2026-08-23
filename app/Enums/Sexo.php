<?php

namespace App\Enums;

enum Sexo: string
{
    case Masculino = 'Masculino';
    case Femenino = 'Femenino';

    public function label(): string
    {
        return match ($this) {
            self::Masculino => 'Masculino',
            self::Femenino => 'Femenino',
        };
    }
}
