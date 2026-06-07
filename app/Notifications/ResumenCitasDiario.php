<?php

namespace App\Notifications;

use Illuminate\Notifications\Notification;

class ResumenCitasDiario extends Notification
{

    public function __construct(
        public int $atendidas,
        public int $pendientes,
        public array $nombresPendientes = [],
    ) {}

    public function via($notifiable): array
    {
        return ['database'];
    }

    public function toDatabase($notifiable): array
    {
        if ($this->pendientes === 0) {
            $body = "Todas las citas del día fueron atendidas ({$this->atendidas} en total).";
        } else {
            $lista = implode(', ', $this->nombresPendientes);
            $body = "Quedan {$this->pendientes} citas por atender: {$lista}.";
        }

        return [
            'title' => 'Resumen diario de citas',
            'body' => $body,
            'action_url' => '/morbilidad/pendientes',
        ];
    }
}
