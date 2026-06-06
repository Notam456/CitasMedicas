<?php

namespace App\Notifications;

use App\Models\Paciente;
use App\Models\User;
use Illuminate\Notifications\Notification;

class PacienteModificado extends Notification
{

    public function __construct(
        public Paciente $paciente,
        public User $usuario,
    ) {}

    public function via($notifiable): array
    {
        return ['database'];
    }

    public function toDatabase($notifiable): array
    {
        return [
            'title' => 'Datos de paciente modificados',
            'body' => "{$this->usuario->name} modificó datos del paciente {$this->paciente->nombre} {$this->paciente->apellido} (C.I. {$this->paciente->cedula}).",
            'action_url' => '/paciente',
        ];
    }
}
