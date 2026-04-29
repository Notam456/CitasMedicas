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

    // 1. Top 5 municipios (Usando la nueva estructura geográfica)
    $municipios = DB::table('citas')
        ->join('pacientes', 'citas.paciente_id', '=', 'pacientes.id')
        ->join('parroquias', 'pacientes.parroquia_id', '=', 'parroquias.id')
        ->join('municipios', 'parroquias.municipio_id', '=', 'municipios.id')
        ->whereMonth('citas.fecha_cita', $mesActual)
        ->whereYear('citas.fecha_cita', $anioActual)
        ->select('municipios.nombre as municipio', DB::raw('COUNT(DISTINCT pacientes.id) as total'))
        ->groupBy('municipios.nombre')
        ->orderByDesc('total')
        ->limit(5)
        ->get();

    // 2. Top 5 especialidades
    $especialidades = DB::table('citas')
        ->join('calendarios', 'citas.calendario_id', '=', 'calendarios.id')
        ->join('medicos', 'calendarios.medico_id', '=', 'medicos.id')
        ->whereMonth('citas.fecha_cita', $mesActual)
        ->whereYear('citas.fecha_cita', $anioActual)
        ->select('medicos.nombre', DB::raw('COUNT(citas.id) as total'))
        ->groupBy('medicos.nombre')
        ->orderByDesc('total')
        ->limit(5)
        ->get();

    // 3. Estadísticas básicas
    $especialidadDemanda = $especialidades->first()->nombre ?? 'N/A';
    
    $pacientesAtendidosMes = DB::table('citas')
        ->whereMonth('citas.fecha_cita', $mesActual)
        ->whereYear('citas.fecha_cita', $anioActual)
        ->distinct('paciente_id')
        ->count('paciente_id');
    
    $pacientesDelDia = DB::table('citas')
        ->whereDate('citas.fecha_cita', $hoy)
        ->count('paciente_id');
    
    $procedenciaMasPacientes = $municipios->first()->municipio ?? 'N/A';

    return view('dashboard', [
        'municipiosLabels'        => $municipios->pluck('municipio'),
        'municipiosData'          => $municipios->pluck('total'),
        'medicosLabels'    => $especialidades->pluck('nombre'),
        'medicosData'      => $especialidades->pluck('total'),
        'medicosDemanda'     => $especialidadDemanda,
        'pacientesAtendidosMes'   => $pacientesAtendidosMes,
        'pacientesDelDia'         => $pacientesDelDia,
        'procedenciaMasPacientes' => $procedenciaMasPacientes,
    ]);
}
}