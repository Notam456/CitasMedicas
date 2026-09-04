<?php

namespace App\Http\Controllers;

use App\Models\LanzadorSesion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class LanzadorController extends Controller
{
    /**
     * Cierra la sesion asociada al token del lanzador (llamado al cerrar
     * la ventana de la app). No requiere autenticacion ni permiso.
     */
    public function cerrarSesion(Request $request)
    {
        $registro = LanzadorSesion::where('token', (string) $request->input('token'))->first();

        if ($registro) {
            Session::getHandler()->destroy($registro->session_id);
            $registro->delete();
        }

        return response()->noContent();
    }
}
