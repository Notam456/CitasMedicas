<?php

namespace App\Notifications;

use App\Models\Patologia;
use Illuminate\Notifications\Notification;

class NuevaPatologia extends Notification
{

    public function __construct(public Patologia $patologia) {}

    public function via($notifiable): array
    {
        return ['database'];
    }

    public function toDatabase($notifiable): array
    {
        return [
            'title' => 'Nueva patología registrada',
            'body' => "{$this->patologia->nombre}",
            'action_url' => '/patologias',
        ];
    }
}
