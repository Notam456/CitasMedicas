<?php

namespace App\Enums;

enum CitaEstado: string
{
    case Agendada = 'Agendada';
    case Atendida = 'Atendida';
    case Cancelada = 'Cancelada';

    public function label(): string
    {
        return match ($this) {
            self::Agendada => 'Agendada',
            self::Atendida => 'Atendida',
            self::Cancelada => 'Cancelada',
        };
    }

    public function badgeClass(): string
    {
        return match ($this) {
            self::Agendada => 'bg-success',
            self::Atendida => 'bg-primary',
            self::Cancelada => 'bg-danger',
        };
    }
}
