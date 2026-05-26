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

        /* select al nombre del municipio y cuenta pacientes uniques (count distinct) como total de citas de ese municipio en la tabla
        citas, inner joins para la idempotencia y el where month, year = variable definida.
        agrupados por el nombre del municipio ordenados desc de la suma total.

            SELECT 
                municipios.nombre AS municipio, 
                COUNT(DISTINCT pacientes.id) AS total
            FROM citas
                INNER JOIN pacientes ON citas.paciente_id = pacientes.id
                INNER JOIN parroquias ON pacientes.parroquia_id = parroquias.id
                INNER JOIN municipios ON parroquias.municipio_id = municipios.id
            WHERE 
                EXTRACT(MONTH FROM citas.fecha_cita) = $mesActual    
                AND EXTRACT(YEAR FROM citas.fecha_cita) = $anioActual 
            GROUP BY municipios.nombre
            ORDER BY total DESC
            LIMIT 5; 
        */

    $especialidades = DB::table('citas')
        ->join('calendarios', 'citas.calendario_id', '=', 'calendarios.id')
        ->join('medicos', 'calendarios.medico_id', '=', 'medicos.id')
        ->join('especialidades', 'medicos.especialidad_id', '=', 'especialidades.id')
        ->whereMonth('citas.fecha_cita', $mesActual)
        ->whereYear('citas.fecha_cita', $anioActual)
        ->select('especialidades.nombre', DB::raw('COUNT(citas.id) as total'))
        ->groupBy('especialidades.nombre')
        ->orderByDesc('total')
        ->limit(5)
        ->get();

        /* select nombre de especialidad y cuenta citas como total, inner join para idempotencia con calendarios, medicos y especialidades
        where month, year = variable definida con carbon.
        agrupado por nombre de especialidades y ordenado desc total.
        
            SELECT 
                especialidades.nombre, 
                COUNT(citas.id) AS total
            FROM citas
                INNER JOIN calendarios ON citas.calendario_id = calendarios.id
                INNER JOIN medicos ON calendarios.medico_id = medicos.id
                INNER JOIN especialidades ON medicos.especialidad_id = especialidades.id
            WHERE 
                EXTRACT(MONTH FROM citas.fecha_cita) = :mesActual
                AND EXTRACT(YEAR FROM citas.fecha_cita) = :anioActual
            GROUP BY especialidades.nombre
            ORDER BY total DESC
            LIMIT 5;  
        */

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

    $ultimasCitas = DB::table('citas')
            ->join('pacientes', 'citas.paciente_id', '=', 'pacientes.id')
            ->join('calendarios', 'citas.calendario_id', '=', 'calendarios.id')
            ->join('medicos', 'calendarios.medico_id', '=', 'medicos.id')
            ->join('especialidades', 'medicos.especialidad_id', '=', 'especialidades.id')
            ->select(
                'citas.id',
                'citas.fecha_cita',
                'pacientes.nombre as paciente_nombre',
                'pacientes.apellido as paciente_apellido',
                'especialidades.nombre as especialidad_nombre',
                'citas.estado'
            )
            ->orderBy('citas.fecha_registro', 'desc')
            ->limit(5)
            ->get();

            /* select normal de info de las ultimas 5 citas (limit 5)

                SELECT 
                    citas.id,
                    citas.fecha_cita,
                    pacientes.nombre AS paciente_nombre,
                    pacientes.apellido AS paciente_apellido,
                    especialidades.nombre AS especialidad_nombre,
                    citas.estado
                FROM citas
                    INNER JOIN pacientes ON citas.paciente_id = pacientes.id
                    INNER JOIN calendarios ON citas.calendario_id = calendarios.id
                    INNER JOIN medicos ON calendarios.medico_id = medicos.id
                    INNER JOIN especialidades ON medicos.especialidad_id = especialidades.id
                ORDER BY citas.fecha_registro DESC
                LIMIT 5;
            */

    return view('dashboard', [
        'municipiosLabels'        => $municipios->pluck('municipio'),
        'municipiosData'          => $municipios->pluck('total'),
        'especialidadesLabels' => $especialidades->pluck('nombre'),
        'especialidadesData' => $especialidades->pluck('total'),
        'especialidadDemanda' => $especialidadDemanda,
        'pacientesAtendidosMes'   => $pacientesAtendidosMes,
        'pacientesDelDia'         => $pacientesDelDia,
        'procedenciaMasPacientes' => $procedenciaMasPacientes,
        'ultimasCitas'            => $ultimasCitas,
    ]);
}
}