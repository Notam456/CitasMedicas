<?php

namespace App\Notifications;

use App\Models\Especialidad;
use Illuminate\Notifications\Notification;

class NuevaEspecialidad extends Notification
{

    public function __construct(public Especialidad $especialidad) {}

    public function via($notifiable): array
    {
        return ['database'];
    }

    public function toDatabase($notifiable): array
    {
        return [
            'title' => 'Nueva especialidad registrada',
            'body' => "Especialidad: {$this->especialidad->nombre}",
            'action_url' => '/especialidades',
        ];
    }
}
