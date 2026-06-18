<?php

namespace App\Notifications;

use App\Models\Cita;
use App\Models\User;
use Illuminate\Notifications\Notification;

class CitaCancelada extends Notification
{

    public function __construct(
        public Cita $cita,
        public User $usuario,
    ) {}

    public function via($notifiable): array
    {
        return ['database'];
    }

    public function toDatabase($notifiable): array
    {
        return [
            'title' => 'Cita cancelada',
            'body' => "{$this->usuario->name} canceló cita de {$this->cita->paciente->nombre} {$this->cita->paciente->apellido} para el {$this->cita->fecha_cita}.",
            'action_url' => '/Citas',
        ];
    }
}
