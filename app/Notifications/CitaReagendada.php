<?php

namespace App\Notifications;

use App\Models\Cita;
use App\Models\User;
use Illuminate\Notifications\Notification;

class CitaReagendada extends Notification
{

    public function __construct(
        public Cita $cita,
        public User $usuario,
        public string $fechaOriginal,
    ) {}

    public function via($notifiable): array
    {
        return ['database'];
    }

    public function toDatabase($notifiable): array
    {
        return [
            'title' => 'Cita reagendada',
            'body' => "{$this->usuario->name} reagendó cita de {$this->cita->paciente->nombre} {$this->cita->paciente->apellido} del {$this->fechaOriginal} al {$this->cita->fecha_cita}.",
            'action_url' => '/Citas',
        ];
    }
}
