<?php

use App\Http\Middleware\RegistrarLanzador;
use Illuminate\Contracts\Auth\Middleware\AuthenticatesRequests;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->web(append: [
            RegistrarLanzador::class,
        ]);

        $middleware->prependToPriorityList(
            AuthenticatesRequests::class,
            RegistrarLanzador::class,
        );

        $middleware->validateCsrfTokens(except: [
            'lanzador/cerrar-sesion',
        ]);
    })
    ->withSchedule(function (\Illuminate\Console\Scheduling\Schedule $schedule) {
        $schedule->command('notificaciones:verificar-cupos')->dailyAt('08:00');
        $schedule->command('notificaciones:enviar-resumen-diario')->dailyAt('19:00');
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
