<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class MarcarDescargaReporte
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        $token = $request->query('descarga_token');

        if (! is_string($token) || ! preg_match('/^[A-Za-z0-9]{6,40}$/', $token)) {
            return $response;
        }

        $disposition = (string) $response->headers->get('Content-Disposition');

        if ($response->getStatusCode() !== 200 || stripos($disposition, 'attachment') === false) {
            return $response;
        }

        $response->headers->setCookie(cookie(
            'reporte_descarga_'.$token,
            '1',
            2,
            '/',
            null,
            $request->isSecure(),
            false,
            false,
            'lax'
        ));

        return $response;
    }
}
