<?php

namespace App\Notifications;

use App\Models\Medico;
use Illuminate\Notifications\Notification;

class CuposProximosAgotarse extends Notification
{

    public function __construct(public Medico $medico) {}

    public function via($notifiable): array
    {
        return ['database'];
    }

    public function toDatabase($notifiable): array
    {
        return [
            'title' => 'Cupos próximos a agotarse',
            'body' => "Dr. {$this->medico->nombre} {$this->medico->apellido} — {$this->medico->especialidad->nombre} solo tiene cupos disponibles hasta los próximos 14 días.",
            'action_url' => '/calendario',
        ];
    }
}
