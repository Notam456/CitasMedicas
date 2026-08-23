<?php

namespace App\Enums;

enum CitaTipoPaciente: string
{
    case PrimeraVez = 'primera_vez';
    case Control = 'control';
    case OrdenMedica = 'orden_medica';

    public function label(): string
    {
        return match ($this) {
            self::PrimeraVez => 'Primera Vez',
            self::Control => 'Sucesiva',
            self::OrdenMedica => 'Orden Médica',
        };
    }

    public function badgeClass(): string
    {
        return match ($this) {
            self::PrimeraVez => 'bg-info',
            self::Control => 'bg-warning text-dark',
            self::OrdenMedica => 'bg-secondary',
        };
    }
}
