<?php

namespace App\Console\Commands;

use App\Models\Medico;
use App\Models\User;
use App\Notifications\CuposProximosAgotarse;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Notification;

class VerificarCuposProximos extends Command
{
    protected $signature = 'notificaciones:verificar-cupos';
    protected $description = 'Notifica cuando un médico solo tiene cupos disponibles en los próximos 14 días';

    public function handle()
    {
        $fechaLimite = now()->addDays(14);

        $medicos = Medico::whereHas('calendarios', function ($q) use ($fechaLimite) {
            $q->whereBetween('fecha', [now()->startOfDay(), $fechaLimite])
              ->where(function ($q2) {
                  $q2->where('cupos_primera_vez', '>', 0)
                     ->orWhere('cupos_sucesivos', '>', 0);
              });
        })->get();

        $notificados = 0;

        foreach ($medicos as $medico) {
            $tieneMasAlla = $medico->calendarios()
                ->where('fecha', '>', $fechaLimite)
                ->exists();

            if (!$tieneMasAlla) {
                $usuarios = User::all();
                Notification::send($usuarios, new CuposProximosAgotarse($medico));
                $notificados++;
            }
        }

        $this->info("Notificación enviada para {$notificados} médicos con cupos próximos a agotarse.");

        return Command::SUCCESS;
    }
}
