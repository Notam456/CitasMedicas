<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $mesActual = Carbon::now()->month;
        $anioActual = Carbon::now()->year;
        $hoy = Carbon::now()->toDateString();

        // Top 5 municipios
        $municipios = DB::table('cita')
            ->join('paciente', 'cita.id_paciente', '=', 'paciente.id_paciente')
            ->whereRaw('EXTRACT(MONTH FROM cita.fecha_cita) = ?', [$mesActual])
            ->whereRaw('EXTRACT(YEAR FROM cita.fecha_cita) = ?', [$anioActual])
            ->select('paciente.direccion as municipio', DB::raw('COUNT(DISTINCT paciente.id_paciente) as total'))
            ->groupBy('paciente.direccion')
            ->orderByDesc('total')
            ->limit(5)
            ->get();

        // Top 5 especialidades
        $especialidades = DB::table('cita')
            ->join('calendario', 'cita.id_calendario', '=', 'calendario.id_calendario')
            ->join('especialidad', 'calendario.id_especialidad', '=', 'especialidad.id_especialidad')
            ->whereRaw('EXTRACT(MONTH FROM cita.fecha_cita) = ?', [$mesActual])
            ->whereRaw('EXTRACT(YEAR FROM cita.fecha_cita) = ?', [$anioActual])
            ->select('especialidad.nombre', DB::raw('COUNT(cita.id_cita) as total'))
            ->groupBy('especialidad.nombre')
            ->orderByDesc('total')
            ->limit(5)
            ->get();

        // Estadísticas básicas
        $especialidadDemanda = $especialidades->first()->nombre ?? 'N/A';
        
        $pacientesAtendidosMes = DB::table('cita')
            ->whereRaw('EXTRACT(MONTH FROM fecha_cita) = ?', [$mesActual])
            ->whereRaw('EXTRACT(YEAR FROM fecha_cita) = ?', [$anioActual])
            ->distinct('id_paciente')
            ->count('id_paciente');
        
        $pacientesDelDia = DB::table('cita')
            ->whereDate('fecha_cita', $hoy)
            ->distinct('id_paciente')
            ->count('id_paciente');
        
        $procedenciaMasPacientes = $municipios->first()->municipio ?? 'N/A';

        return view('dashboard', [
            'municipiosLabels' => $municipios->pluck('municipio'),
            'municipiosData'   => $municipios->pluck('total'),
            'especialidadesLabels' => $especialidades->pluck('nombre'),
            'especialidadesData'   => $especialidades->pluck('total'),
            'especialidadDemanda' => $especialidadDemanda,
            'pacientesAtendidosMes' => $pacientesAtendidosMes,
            'pacientesDelDia' => $pacientesDelDia,
            'procedenciaMasPacientes' => $procedenciaMasPacientes,
        ]);
    }
}