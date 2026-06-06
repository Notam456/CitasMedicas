<?php

namespace App\Console\Commands;

use App\Models\Cita;
use App\Models\User;
use App\Notifications\ResumenCitasDiario as ResumenCitasDiarioNotification;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Notification;

class EnviarResumenCitasDiario extends Command
{
    protected $signature = 'notificaciones:enviar-resumen-diario';
    protected $description = 'Envía un resumen de las citas del día a todos los usuarios';

    public function handle()
    {
        $hoy = now()->format('Y-m-d');

        $total = Cita::whereDate('fecha_cita', $hoy)->count();
        $atendidas = Cita::whereDate('fecha_cita', $hoy)
            ->where('estado', 'Atendida')
            ->count();

        $pendientes = Cita::whereDate('fecha_cita', $hoy)
            ->where('estado', 'Agendada')
            ->with('paciente')
            ->get();

        $nombresPendientes = $pendientes->map(function ($c) {
            return $c->paciente->nombre . ' ' . $c->paciente->apellido;
        })->toArray();

        $usuarios = User::all();

        Notification::send($usuarios, new ResumenCitasDiarioNotification(
            $atendidas,
            $pendientes->count(),
            $nombresPendientes,
        ));

        $this->info("Resumen diario enviado: {$atendidas} atendidas, {$pendientes->count()} pendientes.");

        return Command::SUCCESS;
    }
}
