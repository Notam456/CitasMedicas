<?php

namespace App\Enums;

enum CitaCancelacionMotivo: string
{
    case AusenciaPaciente = 'ausencia_paciente';
    case AusenciaMedico = 'ausencia_medico';

    public function label(): string
    {
        return match ($this) {
            self::AusenciaPaciente => 'Ausencia del Paciente',
            self::AusenciaMedico => 'Ausencia del Médico',
        };
    }
}
