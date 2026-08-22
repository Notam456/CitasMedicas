<?php

namespace App\Services;

use App\Models\Cita;
use App\Models\CitaPatologia;
use App\Models\Distrito;
use App\Models\Especialidad;
use App\Models\Municipio;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ReporteService
{
    public static function resolverRangoFechas(array $data): array
    {
        if (($data['tipo_rango'] ?? 'mes') === 'mes') {
            $fecha = Carbon::createFromFormat('Y-m', $data['mes']);
            $fecha_desde = $fecha->copy()->startOfMonth()->toDateString();
            $fecha_hasta = $fecha->copy()->endOfMonth()->toDateString();
            Carbon::setLocale('es');
            $titulo = $fecha->translatedFormat('F Y');
        } else {
            $fecha_desde = $data['fecha_desde'];
            $fecha_hasta = $data['fecha_hasta'];
            $titulo = Carbon::parse($fecha_desde)->format('d/m/Y') . ' al ' . Carbon::parse($fecha_hasta)->format('d/m/Y');
        }

        return compact('fecha_desde', 'fecha_hasta', 'titulo');
    }

    public static function procedencia(array $data): array
    {
        $rangos = self::resolverRangoFechas($data);
        $fecha_desde = $rangos['fecha_desde'];
        $fecha_hasta = $rangos['fecha_hasta'];
        $titulo = 'Procedencia de Pacientes - ' . $rangos['titulo'];

        $distritosReales = Distrito::orderBy('id')->get();
        $distritosEspeciales = [
            (object) ['id' => 1000, 'nombre' => 'Ignorado']
        ];
        $todosLosDistritos = $distritosReales->concat($distritosEspeciales);

        $reporte = [];
        foreach ($todosLosDistritos as $distrito) {
            $distritoNombre = $distrito->nombre;
            $distritoId = $distrito->id;
            $reporte[$distritoNombre] = [
                'distrito_id' => $distritoId,
                'municipios' => [],
                'subtotal' => ['agendadas' => 0, 'atendidas' => 0, 'total' => 0]
            ];
            if (!in_array($distritoId, [6, 1000])) {
                $municipiosDelDistrito = Municipio::where('distrito_id', $distritoId)->orderBy('nombre')->get();
                foreach ($municipiosDelDistrito as $mun) {
                    $reporte[$distritoNombre]['municipios'][$mun->nombre] = [
                        'agendadas' => 0, 'atendidas' => 0, 'total' => 0
                    ];
                }
            }
        }
        $reporte['Ignorado']['municipios']['Sin municipio'] = ['agendadas' => 0, 'atendidas' => 0, 'total' => 0];

        $conteos = Cita::select(
                'distritos.id as distrito_id',
                'distritos.nombre as distrito_nombre',
                'municipios.nombre as municipio_nombre',
                DB::raw("COUNT(DISTINCT CASE WHEN citas.estado = 'Agendada' THEN citas.paciente_id END) as agendadas"),
                DB::raw("COUNT(DISTINCT CASE WHEN citas.estado = 'Atendida' THEN citas.paciente_id END) as atendidas"),
                DB::raw("COUNT(DISTINCT CASE WHEN citas.estado IN ('Agendada', 'Atendida') THEN citas.paciente_id END) as total_pacientes")
            )
            ->leftJoin('pacientes', 'citas.paciente_id', '=', 'pacientes.id')
            ->leftJoin('parroquias', 'pacientes.parroquia_id', '=', 'parroquias.id')
            ->leftJoin('municipios', 'parroquias.municipio_id', '=', 'municipios.id')
            ->leftJoin('distritos', 'municipios.distrito_id', '=', 'distritos.id')
            ->whereBetween('citas.fecha_cita', [$fecha_desde, $fecha_hasta])
            ->groupBy('distritos.id', 'distritos.nombre', 'municipios.nombre')
            ->get();

        foreach ($conteos as $row) {
            if (is_null($row->distrito_id)) {
                $distritoNombre = 'Ignorado';
            } elseif ($row->distrito_id == 6) {
                $distritoNombre = 'Otros Estados';
            } else {
                $distritoNombre = $row->distrito_nombre;
            }
            $municipioNombre = $row->municipio_nombre ?? 'Sin municipio';

            if (isset($reporte[$distritoNombre])) {
                if (!isset($reporte[$distritoNombre]['municipios'][$municipioNombre])) {
                    $reporte[$distritoNombre]['municipios'][$municipioNombre] = ['agendadas' => 0, 'atendidas' => 0, 'total' => 0];
                }
                $reporte[$distritoNombre]['municipios'][$municipioNombre]['agendadas'] = $row->agendadas;
                $reporte[$distritoNombre]['municipios'][$municipioNombre]['atendidas'] = $row->atendidas;
                $reporte[$distritoNombre]['municipios'][$municipioNombre]['total'] = $row->total_pacientes;

                $reporte[$distritoNombre]['subtotal']['agendadas'] += $row->agendadas;
                $reporte[$distritoNombre]['subtotal']['atendidas'] += $row->atendidas;
                $reporte[$distritoNombre]['subtotal']['total'] += $row->total_pacientes;
            }
        }

        $ordenDistritos = [];
        foreach ($distritosReales as $d) {
            if ($d->nombre !== 'Otros Estados') {
                $ordenDistritos[] = $d->nombre;
            }
        }
        $ordenDistritos[] = 'Otros Estados';
        $ordenDistritos[] = 'Ignorado';

        $reporteFinal = [];
        foreach ($ordenDistritos as $nombreDistrito) {
            if (isset($reporte[$nombreDistrito])) {
                $municipiosArray = [];
                foreach ($reporte[$nombreDistrito]['municipios'] as $munNombre => $datos) {
                    $municipiosArray[] = [
                        'nombre' => $munNombre,
                        'agendadas' => $datos['agendadas'],
                        'atendidas' => $datos['atendidas'],
                        'total' => $datos['total']
                    ];
                }
                usort($municipiosArray, fn ($a, $b) => strcmp($a['nombre'], $b['nombre']));
                $reporteFinal[] = [
                    'distrito' => $nombreDistrito,
                    'municipios' => $municipiosArray,
                    'subtotal' => $reporte[$nombreDistrito]['subtotal']
                ];
            }
        }

        $totalesGlobales = ['agendadas' => 0, 'atendidas' => 0, 'todos' => 0];
        foreach ($reporteFinal as $item) {
            $totalesGlobales['agendadas'] += $item['subtotal']['agendadas'];
            $totalesGlobales['atendidas'] += $item['subtotal']['atendidas'];
            $totalesGlobales['todos'] += $item['subtotal']['total'];
        }

        return compact('reporteFinal', 'totalesGlobales', 'titulo', 'fecha_desde', 'fecha_hasta');
    }

    public static function movimientoConsultas(array $data): array
    {
        $rangos = self::resolverRangoFechas($data);
        $fecha_desde = $rangos['fecha_desde'];
        $fecha_hasta = $rangos['fecha_hasta'];

        $tipoPaciente = $data['tipo_paciente'];
        $edadCondicion = match ($tipoPaciente) {
            'adulto' => '> 12',
            'pediatria' => '<= 12',
            default => null,
        };
        $edadCol = $edadCondicion ? "EXTRACT(YEAR FROM AGE(pacientes.fecha_nacimiento)) {$edadCondicion}" : null;
        $especialidadId = $data['especialidad_id'] ?? null;
        $especialidad = $especialidadId ? Especialidad::find($especialidadId) : null;
        $aroId = Especialidad::where('nombre', 'Aro (Embarazados)')->value('id');

        $columnas = [
            'agendadas' => 'Citas Agendadas',
            'atendidas' => 'Citas Atendidas',
            'primera_vez' => 'Citas de Primera Vez',
            'sucesivas' => 'Citas Sucesivas',
            'ausentes' => 'Ausencia del Paciente',
            'ausencia_medico' => 'Ausencia del Médico',
            'adolescentes' => 'Adolescentes 10-19',
        ];

        if ($especialidad && $especialidad->nombre === 'Aro (Embarazados)') {
            $columnas['menos_13_sem'] = '13 Semanas Gestación';
        }

        $sum = function ($condicion) use ($edadCol) {
            return "SUM(CASE WHEN {$condicion}" . ($edadCol ? " AND {$edadCol}" : '') . ' THEN 1 ELSE 0 END)';
        };

        $selects = [
            'especialidades.nombre as especialidad',
            DB::raw($sum("citas.estado = 'Agendada'") . ' as agendadas'),
            DB::raw($sum("citas.estado = 'Atendida'") . ' as atendidas'),
            DB::raw($sum("citas.tipo_paciente = 'primera_vez'") . ' as primera_vez'),
            DB::raw($sum("citas.tipo_paciente = 'control'") . ' as sucesivas'),
            DB::raw($sum("citas.estado = 'Cancelada' AND COALESCE(cc.motivo, 'ausencia_paciente') = 'ausencia_paciente'") . ' as ausentes'),
            DB::raw($sum("citas.estado = 'Cancelada' AND cc.motivo = 'ausencia_medico'") . ' as ausencia_medico'),
            DB::raw("SUM(CASE WHEN citas.estado = 'Atendida' AND EXTRACT(YEAR FROM AGE(pacientes.fecha_nacimiento)) BETWEEN 10 AND 19 THEN 1 ELSE 0 END) as adolescentes"),
        ];

        if (isset($columnas['menos_13_sem'])) {
            $selects[] = DB::raw("SUM(CASE WHEN acd.semanas_gestacion IS NOT NULL AND acd.semanas_gestacion < 13 AND citas.estado = 'Atendida' AND especialidades.id = {$aroId} THEN 1 ELSE 0 END) as menos_13_sem");
        }

        $queryData = Cita::select($selects)
            ->join('calendarios', 'citas.calendario_id', '=', 'calendarios.id')
            ->join('medicos', 'calendarios.medico_id', '=', 'medicos.id')
            ->join('especialidades', 'medicos.especialidad_id', '=', 'especialidades.id')
            ->join('pacientes', 'citas.paciente_id', '=', 'pacientes.id')
            ->leftJoin('aro_cita_datos as acd', 'acd.cita_id', '=', 'citas.id')
            ->leftJoin('cita_cancelaciones as cc', 'cc.cita_id', '=', 'citas.id')
            ->when($especialidadId, fn ($q) => $q->where('especialidades.id', $especialidadId))
            ->whereBetween('citas.fecha_cita', [$fecha_desde, $fecha_hasta])
            ->groupBy('especialidades.id', 'especialidades.nombre')
            ->orderBy('especialidades.nombre')
            ->get()
            ->map(function ($item) use ($columnas) {
                $fila = ['especialidad' => $item->especialidad];
                foreach ($columnas as $clave => $label) {
                    $fila[$clave] = (int) $item->{$clave};
                }
                return $fila;
            })
            ->toArray();

        $totales = [];
        foreach ($columnas as $clave => $label) {
            $totales[$clave] = array_sum(array_column($queryData, $clave));
        }

        $tipoPacienteTexto = match ($tipoPaciente) {
            'adulto' => 'Mayores de 12 años',
            'pediatria' => 'Pediatría (12 años o menos)',
            default => 'Todas las Edades',
        };
        $titulo = 'Movimiento de Consulta Externa - ' . $tipoPacienteTexto;
        $especialidadNombre = $especialidad ? $especialidad->nombre : 'Todas';
        $especialidadSeleccionada = (bool) $especialidadId;

        return compact('queryData', 'totales', 'titulo', 'fecha_desde', 'fecha_hasta', 'tipoPaciente', 'tipoPacienteTexto', 'fechaTexto', 'columnas', 'especialidadNombre', 'especialidadSeleccionada');
    }

    public static function movimientoConsultaAro(array $data): array
    {
        $rangos = self::resolverRangoFechas($data);
        $fecha_desde = $rangos['fecha_desde'];
        $fecha_hasta = $rangos['fecha_hasta'];
        $fechaTexto = $rangos['titulo'];

        $aroEsp = Especialidad::where('nombre', 'Aro (Embarazados)')->first();

        $queryData = Cita::select(
                DB::raw("to_char(citas.fecha_cita, 'YYYY-MM') as mes"),
                DB::raw("COUNT(DISTINCT CASE WHEN acd.semanas_gestacion IS NOT NULL AND acd.semanas_gestacion < 13 AND citas.tipo_paciente = 'primera_vez' THEN citas.paciente_id END) as menos_13_sem"),
                DB::raw("COUNT(DISTINCT CASE WHEN EXTRACT(YEAR FROM AGE(pacientes.fecha_nacimiento)) BETWEEN 10 AND 19 AND citas.tipo_paciente = 'primera_vez' THEN citas.paciente_id END) as adolescentes"),
                DB::raw("COUNT(*) as total_consultas")
            )
            ->join('calendarios', 'citas.calendario_id', '=', 'calendarios.id')
            ->join('medicos', 'calendarios.medico_id', '=', 'medicos.id')
            ->join('especialidades', 'medicos.especialidad_id', '=', 'especialidades.id')
            ->join('pacientes', 'citas.paciente_id', '=', 'pacientes.id')
            ->leftJoin('aro_cita_datos as acd', 'acd.cita_id', '=', 'citas.id')
            ->where('especialidades.id', $aroEsp->id)
            ->whereIn('citas.estado', ['Atendida', 'Agendada'])
            ->whereBetween('citas.fecha_cita', [$fecha_desde, $fecha_hasta])
            ->groupBy(DB::raw("to_char(citas.fecha_cita, 'YYYY-MM')"))
            ->orderBy('mes')
            ->get()
            ->map(fn ($item) => [
                'mes' => $item->mes,
                'menos_13_sem' => (int) $item->menos_13_sem,
                'adolescentes' => (int) $item->adolescentes,
                'total_consultas' => (int) $item->total_consultas,
            ])
            ->toArray();

        $totales = [
            'menos_13_sem' => array_sum(array_column($queryData, 'menos_13_sem')),
            'adolescentes' => array_sum(array_column($queryData, 'adolescentes')),
            'total_consultas' => array_sum(array_column($queryData, 'total_consultas')),
        ];

        $titulo = 'Movimiento de Consulta Aro Mensual';

        return compact('queryData', 'totales', 'titulo', 'fechaTexto', 'aroEsp');
    }

    public static function causasPrincipales(array $data): array
    {
        $rangos = self::resolverRangoFechas($data);
        $fecha_desde = $rangos['fecha_desde'];
        $fecha_hasta = $rangos['fecha_hasta'];
        $fechaTexto = $rangos['titulo'];

        $queryData = CitaPatologia::select(
                'patologias.nombre as patologia',
                'especialidades.nombre as especialidad',
                DB::raw("COUNT(CASE WHEN pacientes.sexo = 'Masculino' AND citas.tipo_paciente = 'primera_vez' THEN 1 END) as masculino_primera"),
                DB::raw("COUNT(CASE WHEN pacientes.sexo = 'Masculino' AND citas.tipo_paciente = 'control' THEN 1 END) as masculino_sucesivas"),
                DB::raw("COUNT(CASE WHEN pacientes.sexo = 'Femenino' AND citas.tipo_paciente = 'primera_vez' THEN 1 END) as femenino_primera"),
                DB::raw("COUNT(CASE WHEN pacientes.sexo = 'Femenino' AND citas.tipo_paciente = 'control' THEN 1 END) as femenino_sucesivas"),
                DB::raw("COUNT(*) as total")
            )
            ->join('citas', 'cita_patologias.cita_id', '=', 'citas.id')
            ->join('patologias', 'cita_patologias.patologia_id', '=', 'patologias.id')
            ->join('pacientes', 'citas.paciente_id', '=', 'pacientes.id')
            ->join('calendarios', 'citas.calendario_id', '=', 'calendarios.id')
            ->join('medicos', 'calendarios.medico_id', '=', 'medicos.id')
            ->join('especialidades', 'medicos.especialidad_id', '=', 'especialidades.id')
            ->whereBetween('citas.fecha_cita', [$fecha_desde, $fecha_hasta])
            ->whereIn('citas.estado', ['Atendida', 'Agendada'])
            ->groupBy('especialidades.id', 'especialidades.nombre', 'patologias.id', 'patologias.nombre')
            ->orderByDesc(DB::raw('COUNT(*)'))
            ->limit(25)
            ->get()
            ->map(fn ($item) => [
                'patologia' => $item->patologia,
                'especialidad' => $item->especialidad,
                'masculino_primera' => (int) $item->masculino_primera,
                'masculino_sucesivas' => (int) $item->masculino_sucesivas,
                'femenino_primera' => (int) $item->femenino_primera,
                'femenino_sucesivas' => (int) $item->femenino_sucesivas,
                'total' => (int) $item->total,
            ])
            ->toArray();

        $titulo = '25 Principales Causas de Consulta Externa';

        return compact('queryData', 'titulo', 'fechaTexto');
    }
}
