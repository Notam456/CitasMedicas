<?php

namespace App\Notifications;

use App\Models\Medico;
use Illuminate\Notifications\Notification;

class PlanificacionCreada extends Notification
{

    public function __construct(
        public Medico $medico,
        public string $detalle,
    ) {}

    public function via($notifiable): array
    {
        return ['database'];
    }

    public function toDatabase($notifiable): array
    {
        return [
            'title' => 'Planificación de disponibilidad creada',
            'body' => "Dr. {$this->medico->nombre} {$this->medico->apellido} — {$this->medico->especialidad->nombre}: {$this->detalle}",
            'action_url' => '/calendario',
        ];
    }
}
