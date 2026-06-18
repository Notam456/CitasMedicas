<?php

namespace App\Notifications;

use App\Models\Medico;
use App\Models\User;
use Illuminate\Notifications\Notification;

class MedicoModificado extends Notification
{

    public function __construct(
        public Medico $medico,
        public User $usuario,
    ) {}

    public function via($notifiable): array
    {
        return ['database'];
    }

    public function toDatabase($notifiable): array
    {
        return [
            'title' => 'Datos de médico modificados',
            'body' => "{$this->usuario->name} modificó datos del Dr. {$this->medico->nombre} {$this->medico->apellido}.",
            'action_url' => '/medicos',
        ];
    }
}
