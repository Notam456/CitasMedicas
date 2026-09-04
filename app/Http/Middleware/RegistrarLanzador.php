<?php

namespace App\Http\Middleware;

use App\Models\LanzadorSesion;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RegistrarLanzador
{
    /**
     * Vincula la sesion del lanzador (token ?_lanzador) con el id de sesion
     * actual para que al cerrar la ventana se pueda cerrar esa sesion.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $token = $request->query('_lanzador');

        if ($token) {
            $request->session()->put('_lanzador_token', $token);
            $this->registrar($token, $request);
        } elseif ($request->session()->has('_lanzador_token')) {
            $this->registrar($request->session()->get('_lanzador_token'), $request);
        }

        return $next($request);
    }

    private function registrar(string $token, Request $request): void
    {
        LanzadorSesion::updateOrCreate(
            ['token' => $token],
            ['session_id' => $request->session()->getId(), 'user_id' => $request->user()?->id]
        );
    }
}
