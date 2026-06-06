<?php

namespace App\Notifications;

use App\Models\Paciente;
use Illuminate\Notifications\Notification;

class NuevoPaciente extends Notification
{

    public function __construct(public Paciente $paciente) {}

    public function via($notifiable): array
    {
        return ['database'];
    }

    public function toDatabase($notifiable): array
    {
        return [
            'title' => 'Nuevo paciente registrado',
            'body' => "{$this->paciente->nombre} {$this->paciente->apellido} (C.I. {$this->paciente->cedula})",
            'action_url' => '/paciente',
        ];
    }
}
