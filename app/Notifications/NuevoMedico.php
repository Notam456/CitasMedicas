<?php

namespace App\Notifications;

use App\Models\Medico;
use Illuminate\Notifications\Notification;

class NuevoMedico extends Notification
{

    public function __construct(public Medico $medico) {}

    public function via($notifiable): array
    {
        return ['database'];
    }

    public function toDatabase($notifiable): array
    {
        return [
            'title' => 'Nuevo médico incorporado',
            'body' => "Dr. {$this->medico->nombre} {$this->medico->apellido} — {$this->medico->especialidad->nombre}",
            'action_url' => '/medicos',
        ];
    }
}
