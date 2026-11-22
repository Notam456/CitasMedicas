<?php

namespace App\Services;

use App\Models\Cita;
use App\Models\CitaPatologia;
use App\Models\Distrito;
use App\Models\Especialidad;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ReporteService
{
    /**
     * Etiqueta para las citas que cuelgan de un calendario sin medico
     * ("Cualquier Medico"). No se inventa un doctor: se muestra la categoria.
     */
    private const SIN_MEDICO = 'Sin médico asignado';

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

    /**
     * Procedencia de pacientes.
     *
     * Cuenta CITAS (no pacientes unicos) por municipio, separadas en una
     * particion cerrada: atendidas / canceladas / pendientes. La suma de las
     * tres columnas siempre equivale al total de la fila.
     *
     * La agregacion se hace por ID (distrito/municipio) y el nombre solo se usa
     * como etiqueta: renombrar un distrito o municipio no altera el
     * agrupamiento y dos municipios homonimos no se pisan.
     *
     * Las reglas de presentacion viven en la tabla distritos
     * (es_agrupador, mostrar_desglose, orden), no en el codigo. Los pacientes
     * sin distrito caen en una clave sintetica, no en un distrito ficticio.
     */
    public static function procedencia(array $data): array
    {
        $rangos = self::resolverRangoFechas($data);
        $fecha_desde = $rangos['fecha_desde'];
        $fecha_hasta = $rangos['fecha_hasta'];
        $titulo = 'Procedencia de Pacientes - ' . $rangos['titulo'];

        $claveIgnorado = '__sin_distrito__';
        $claveSinMunicipio = '__sin_municipio__';
        $cero = ['atendidas' => 0, 'canceladas' => 0, 'pendientes' => 0, 'total' => 0];

        // Catalogo en una sola consulta (evita el N+1 por distrito).
        $distritos = Distrito::with(['municipios' => fn ($q) => $q->orderBy('nombre')])
            ->orderBy('orden')
            ->orderBy('nombre')
            ->get();

        // Un solo agregado: municipio, subtotal por distrito y total global.
        // GROUPING() distingue subtotal de total general incluso cuando el
        // distrito es nulo (bucket "Ignorado").
        $conteos = DB::table('citas as c')
            ->leftJoin('pacientes as p', 'c.paciente_id', '=', 'p.id')
            ->leftJoin('parroquias as q', 'p.parroquia_id', '=', 'q.id')
            ->leftJoin('municipios as m', 'q.municipio_id', '=', 'm.id')
            ->leftJoin('distritos as d', 'm.distrito_id', '=', 'd.id')
            ->whereBetween('c.fecha_cita', [$fecha_desde, $fecha_hasta])
            ->selectRaw("
                d.id as distrito_id,
                m.id as municipio_id,
                m.nombre as municipio_nombre,
                COUNT(c.id) FILTER (WHERE c.estado = 'Atendida') as atendidas,
                COUNT(c.id) FILTER (WHERE c.estado = 'Cancelada') as canceladas,
                COUNT(c.id) FILTER (WHERE c.estado = 'Agendada') as pendientes,
                GROUPING(d.id) as es_total,
                GROUPING(m.id) as es_subtotal
            ")
            ->groupByRaw('GROUPING SETS ((d.id, d.nombre, m.id, m.nombre), (d.id, d.nombre), ())')
            ->get();

        $municipios = [];   // distritoKey => [municipioKey => fila]
        $subtotales = [];   // distritoKey => fila
        $totalesGlobales = $cero;

        foreach ($conteos as $row) {
            $fila = [
                'atendidas' => (int) $row->atendidas,
                'canceladas' => (int) $row->canceladas,
                'pendientes' => (int) $row->pendientes,
            ];
            $fila['total'] = $fila['atendidas'] + $fila['canceladas'] + $fila['pendientes'];

            if ((int) $row->es_total === 1) {
                $totalesGlobales = $fila;
                continue;
            }

            $distritoKey = $row->distrito_id !== null ? (int) $row->distrito_id : $claveIgnorado;

            if ((int) $row->es_subtotal === 1) {
                $subtotales[$distritoKey] = $fila;
                continue;
            }

            $municipioKey = $row->municipio_id !== null ? (int) $row->municipio_id : $claveSinMunicipio;
            $municipios[$distritoKey][$municipioKey] = [
                'nombre' => $row->municipio_nombre ?? 'Sin municipio',
            ] + $fila;
        }

        $reporteFinal = [];
        $sumaSubtotales = $cero;

        foreach ($distritos as $distrito) {
            $distritoKey = (int) $distrito->id;
            $subtotal = $subtotales[$distritoKey] ?? $cero;
            $filasMunicipios = [];

            if ($distrito->mostrar_desglose) {
                $vistos = [];
                foreach ($distrito->municipios as $municipio) {
                    $municipioKey = (int) $municipio->id;
                    $vistos[$municipioKey] = true;
                    $filasMunicipios[] = ['nombre' => $municipio->nombre]
                        + ($municipios[$distritoKey][$municipioKey] ?? $cero);
                }

                // Municipios con citas que no estuvieran en el catalogo leido.
                foreach (($municipios[$distritoKey] ?? []) as $municipioKey => $fila) {
                    if (!isset($vistos[$municipioKey])) {
                        $filasMunicipios[] = $fila;
                    }
                }

                usort($filasMunicipios, fn ($a, $b) => strcmp($a['nombre'], $b['nombre']));
            }

            $reporteFinal[] = [
                'distrito' => $distrito->nombre,
                'municipios' => $filasMunicipios,
                'subtotal' => $subtotal,
            ];

            foreach ($cero as $clave => $valor) {
                $sumaSubtotales[$clave] += $subtotal[$clave];
            }
        }

        $ignoradoSubtotal = $subtotales[$claveIgnorado] ?? $cero;
        $ignoradoMunicipios = array_values($municipios[$claveIgnorado] ?? []);
        usort($ignoradoMunicipios, fn ($a, $b) => strcmp($a['nombre'], $b['nombre']));

        $reporteFinal[] = [
            'distrito' => 'Ignorado',
            'municipios' => $ignoradoMunicipios,
            'subtotal' => $ignoradoSubtotal,
        ];

        foreach ($cero as $clave => $valor) {
            $sumaSubtotales[$clave] += $ignoradoSubtotal[$clave];
        }

        self::verificarCuadreProcedencia($reporteFinal, $sumaSubtotales, $totalesGlobales);

        return compact('reporteFinal', 'totalesGlobales', 'titulo', 'fecha_desde', 'fecha_hasta');
    }

    /**
     * Red de seguridad del reporte: los subtotales deben sumar el total global
     * y los municipios deben sumar su distrito. En local/testing falla rapido;
     * en produccion deja rastro en el log.
     */
    private static function verificarCuadreProcedencia(array $reporteFinal, array $sumaSubtotales, array $totalesGlobales): void
    {
        $descuadres = [];

        foreach ($totalesGlobales as $clave => $valor) {
            if ((int) $sumaSubtotales[$clave] !== (int) $valor) {
                $descuadres[] = "{$clave}: subtotales={$sumaSubtotales[$clave]} total={$valor}";
            }
        }

        foreach ($reporteFinal as $item) {
            if (count($item['municipios']) === 0) {
                continue;
            }

            $suma = ['atendidas' => 0, 'canceladas' => 0, 'pendientes' => 0, 'total' => 0];
            foreach ($item['municipios'] as $fila) {
                foreach ($suma as $clave => $valor) {
                    $suma[$clave] += $fila[$clave];
                }
            }

            foreach ($suma as $clave => $valor) {
                if ((int) $valor !== (int) $item['subtotal'][$clave]) {
                    $descuadres[] = "{$item['distrito']} / {$clave}: municipios={$valor} subtotal={$item['subtotal'][$clave]}";
                }
            }
        }

        if ($descuadres === []) {
            return;
        }

        $mensaje = 'Procedencia descuadrada: ' . implode(' | ', $descuadres);

        if (app()->environment(['local', 'testing'])) {
            throw new \RuntimeException($mensaje);
        }

        \Illuminate\Support\Facades\Log::error($mensaje);
    }

    /**
     * Movimiento de consulta externa.
     *
     * Columnas reconciliables:
     *   agendadas + atendidas = primera_vez + sucesivas + orden_medica
     *   total = agendadas + atendidas + ausentes + ausencia_medico + sin_motivo
     *
     * "adolescentes" es una vista superpuesta (solo atendidas de 10-19), no
     * forma parte de la particion.
     *
     * "Agendadas" es el estado pendiente (no el volumen historico).
     */
    public static function movimientoConsultas(array $data): array
    {
        $rangos = self::resolverRangoFechas($data);
        $fecha_desde = $rangos['fecha_desde'];
        $fecha_hasta = $rangos['fecha_hasta'];
        $fechaTexto = $rangos['titulo'];

        $tipoPaciente = $data['tipo_paciente'] ?? 'todas';
        $edadCondicion = match ($tipoPaciente) {
            'adulto' => '> 12',
            'pediatria' => '<= 12',
            default => null,
        };
        $edadCol = $edadCondicion ? "EXTRACT(YEAR FROM AGE(pacientes.fecha_nacimiento)) {$edadCondicion}" : null;
        $especialidadId = $data['especialidad_id'] ?? null;
        $especialidad = $especialidadId ? Especialidad::find($especialidadId) : null;

        $columnas = [
            'agendadas' => 'Citas Agendadas',
            'atendidas' => 'Citas Atendidas',
            'primera_vez' => 'Citas de Primera Vez',
            'sucesivas' => 'Citas Sucesivas',
            'orden_medica' => 'Citas por Orden Médica',
            'ausentes' => 'Ausencia del Paciente',
            'ausencia_medico' => 'Ausencia del Médico',
            'sin_motivo' => 'Canceladas sin Motivo',
            'adolescentes' => 'Adolescentes 10-19',
            'total' => 'Total Citas',
        ];

        $sum = function ($condicion) use ($edadCol) {
            return "SUM(CASE WHEN {$condicion}" . ($edadCol ? " AND {$edadCol}" : '') . ' THEN 1 ELSE 0 END)';
        };

        $selects = [
            'especialidades.nombre as especialidad',
            DB::raw($sum("citas.estado = 'Agendada'") . ' as agendadas'),
            DB::raw($sum("citas.estado = 'Atendida'") . ' as atendidas'),
            DB::raw($sum("citas.tipo_paciente = 'primera_vez' AND citas.estado IN ('Agendada', 'Atendida')") . ' as primera_vez'),
            DB::raw($sum("citas.tipo_paciente = 'control' AND citas.estado IN ('Agendada', 'Atendida')") . ' as sucesivas'),
            DB::raw($sum("citas.tipo_paciente = 'orden_medica' AND citas.estado IN ('Agendada', 'Atendida')") . ' as orden_medica'),
            DB::raw($sum("citas.estado = 'Cancelada' AND cc.motivo = 'ausencia_paciente'") . ' as ausentes'),
            DB::raw($sum("citas.estado = 'Cancelada' AND cc.motivo = 'ausencia_medico'") . ' as ausencia_medico'),
            DB::raw($sum("citas.estado = 'Cancelada' AND cc.motivo IS NULL") . ' as sin_motivo'),
            DB::raw("SUM(CASE WHEN citas.estado = 'Atendida' AND EXTRACT(YEAR FROM AGE(pacientes.fecha_nacimiento)) BETWEEN 10 AND 19 THEN 1 ELSE 0 END) as adolescentes"),
            DB::raw($sum('1 = 1') . ' as total'),
        ];

        $queryData = Cita::select($selects)
            ->join('calendarios', 'citas.calendario_id', '=', 'calendarios.id')
            ->join('especialidades', 'calendarios.especialidad_id', '=', 'especialidades.id')
            ->join('pacientes', 'citas.paciente_id', '=', 'pacientes.id')
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
        $titulo = 'Movimiento de Consulta Aro Mensual';

        $aroEsp = Especialidad::aro()->first();

        if (!$aroEsp) {
            return [
                'queryData' => [],
                'totales' => ['menos_13_sem' => 0, 'adolescentes' => 0, 'total_consultas' => 0],
                'titulo' => $titulo,
                'fechaTexto' => $fechaTexto,
                'aroEsp' => null,
            ];
        }

        $queryData = Cita::select(
                DB::raw("to_char(citas.fecha_cita, 'YYYY-MM') as mes"),
                DB::raw("COUNT(DISTINCT CASE WHEN acd.semanas_gestacion IS NOT NULL AND acd.semanas_gestacion < 13 AND citas.tipo_paciente = 'primera_vez' THEN citas.paciente_id END) as menos_13_sem"),
                DB::raw("COUNT(DISTINCT CASE WHEN EXTRACT(YEAR FROM AGE(pacientes.fecha_nacimiento)) BETWEEN 10 AND 19 AND citas.tipo_paciente = 'primera_vez' THEN citas.paciente_id END) as adolescentes"),
                DB::raw("COUNT(*) as total_consultas")
            )
            ->join('calendarios', 'citas.calendario_id', '=', 'calendarios.id')
            ->join('especialidades', 'calendarios.especialidad_id', '=', 'especialidades.id')
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

        return compact('queryData', 'totales', 'titulo', 'fechaTexto', 'aroEsp');
    }

    /**
     * 25 principales causas.
     *
     * Cuenta CONSULTAS distintas (una consulta con varias patologias suma 1 a
     * cada causa). Las cinco columnas de desglose suman el total de la fila.
     * El total general del reporte NO es la suma de filas: es el numero de
     * consultas atendidas con al menos una patologia en el periodo.
     */
    public static function causasPrincipales(array $data): array
    {
        $rangos = self::resolverRangoFechas($data);
        $fecha_desde = $rangos['fecha_desde'];
        $fecha_hasta = $rangos['fecha_hasta'];
        $fechaTexto = $rangos['titulo'];

        $queryData = CitaPatologia::select(
                'patologias.nombre as patologia',
                'especialidades.nombre as especialidad',
                DB::raw("COUNT(DISTINCT CASE WHEN pacientes.sexo = 'Masculino' AND citas.tipo_paciente = 'primera_vez' THEN citas.id END) as masculino_primera"),
                DB::raw("COUNT(DISTINCT CASE WHEN pacientes.sexo = 'Masculino' AND citas.tipo_paciente = 'control' THEN citas.id END) as masculino_sucesivas"),
                DB::raw("COUNT(DISTINCT CASE WHEN pacientes.sexo = 'Femenino' AND citas.tipo_paciente = 'primera_vez' THEN citas.id END) as femenino_primera"),
                DB::raw("COUNT(DISTINCT CASE WHEN pacientes.sexo = 'Femenino' AND citas.tipo_paciente = 'control' THEN citas.id END) as femenino_sucesivas"),
                DB::raw("COUNT(DISTINCT CASE WHEN citas.tipo_paciente = 'orden_medica' THEN citas.id END) as orden_medica"),
                DB::raw("COUNT(DISTINCT citas.id) as total")
            )
            ->join('citas', 'cita_patologias.cita_id', '=', 'citas.id')
            ->join('patologias', 'cita_patologias.patologia_id', '=', 'patologias.id')
            ->join('pacientes', 'citas.paciente_id', '=', 'pacientes.id')
            ->join('calendarios', 'citas.calendario_id', '=', 'calendarios.id')
            ->join('especialidades', 'calendarios.especialidad_id', '=', 'especialidades.id')
            ->whereBetween('citas.fecha_cita', [$fecha_desde, $fecha_hasta])
            ->where('citas.estado', 'Atendida')
            ->groupBy('especialidades.id', 'especialidades.nombre', 'patologias.id', 'patologias.nombre')
            ->orderByDesc(DB::raw('COUNT(DISTINCT citas.id)'))
            ->orderBy('patologias.nombre')
            ->limit(25)
            ->get()
            ->map(fn ($item) => [
                'patologia' => $item->patologia,
                'especialidad' => $item->especialidad,
                'masculino_primera' => (int) $item->masculino_primera,
                'masculino_sucesivas' => (int) $item->masculino_sucesivas,
                'femenino_primera' => (int) $item->femenino_primera,
                'femenino_sucesivas' => (int) $item->femenino_sucesivas,
                'orden_medica' => (int) $item->orden_medica,
                'total' => (int) $item->total,
            ])
            ->toArray();

        $totales = [
            'masculino_primera' => array_sum(array_column($queryData, 'masculino_primera')),
            'masculino_sucesivas' => array_sum(array_column($queryData, 'masculino_sucesivas')),
            'femenino_primera' => array_sum(array_column($queryData, 'femenino_primera')),
            'femenino_sucesivas' => array_sum(array_column($queryData, 'femenino_sucesivas')),
            'orden_medica' => array_sum(array_column($queryData, 'orden_medica')),
            'total' => array_sum(array_column($queryData, 'total')),
            'total_consultas' => Cita::query()
                ->join('calendarios', 'citas.calendario_id', '=', 'calendarios.id')
                ->join('especialidades', 'calendarios.especialidad_id', '=', 'especialidades.id')
                ->where('citas.estado', 'Atendida')
                ->whereBetween('citas.fecha_cita', [$fecha_desde, $fecha_hasta])
                ->whereExists(function ($q) {
                    $q->select(DB::raw(1))
                      ->from('cita_patologias')
                      ->whereColumn('cita_patologias.cita_id', 'citas.id');
                })
                ->count('citas.id'),
        ];

        $titulo = '25 Principales Causas de Consulta Externa';

        return compact('queryData', 'totales', 'titulo', 'fechaTexto');
    }

    /**
     * Inasistencias.
     *
     * Denominador: citas cerradas (atendidas + canceladas). Las pendientes se
     * muestran en su propia columna y no distorsionan las tasas. Las
     * cancelaciones sin motivo registrado ya no se atribuyen al paciente.
     */
    public static function inasistencias(array $data): array
    {
        $rangos = self::resolverRangoFechas($data);
        $fecha_desde = $rangos['fecha_desde'];
        $fecha_hasta = $rangos['fecha_hasta'];
        $titulo = 'Inasistencias en Citas - ' . $rangos['titulo'];
        $fechaTexto = $rangos['titulo'];

        $queryData = Cita::select(
                'especialidades.nombre as especialidad',
                DB::raw("COUNT(DISTINCT CASE WHEN citas.estado = 'Cancelada' AND cc.motivo = 'ausencia_paciente' THEN citas.id END) as ausencia_paciente"),
                DB::raw("COUNT(DISTINCT CASE WHEN citas.estado = 'Cancelada' AND cc.motivo = 'ausencia_medico' THEN citas.id END) as ausencia_medico"),
                DB::raw("COUNT(DISTINCT CASE WHEN citas.estado = 'Cancelada' AND cc.motivo IS NULL THEN citas.id END) as sin_motivo"),
                DB::raw("COUNT(DISTINCT CASE WHEN citas.estado IN ('Atendida', 'Cancelada') THEN citas.id END) as total_citas"),
                DB::raw("COUNT(DISTINCT CASE WHEN citas.estado = 'Agendada' THEN citas.id END) as pendientes")
            )
            ->join('calendarios', 'citas.calendario_id', '=', 'calendarios.id')
            ->join('especialidades', 'calendarios.especialidad_id', '=', 'especialidades.id')
            ->leftJoin('cita_cancelaciones as cc', 'cc.cita_id', '=', 'citas.id')
            ->whereBetween('citas.fecha_cita', [$fecha_desde, $fecha_hasta])
            ->groupBy('especialidades.id', 'especialidades.nombre')
            ->orderByDesc(DB::raw("COUNT(DISTINCT citas.id)"))
            ->get()
            ->map(function ($item) {
                $totalCitas = (int) $item->total_citas;
                $ausenciaPaciente = (int) $item->ausencia_paciente;
                $ausenciaMedico = (int) $item->ausencia_medico;

                return [
                    'especialidad' => $item->especialidad,
                    'ausencia_paciente' => $ausenciaPaciente,
                    'ausencia_paciente_pct' => $totalCitas > 0 ? round(($ausenciaPaciente / $totalCitas) * 100, 1) : 0,
                    'ausencia_medico' => $ausenciaMedico,
                    'ausencia_medico_pct' => $totalCitas > 0 ? round(($ausenciaMedico / $totalCitas) * 100, 1) : 0,
                    'sin_motivo' => (int) $item->sin_motivo,
                    'total_inasistencias' => $ausenciaPaciente + $ausenciaMedico,
                    'total_citas' => $totalCitas,
                    'pendientes' => (int) $item->pendientes,
                    'tasa_inasistencia' => $totalCitas > 0 ? round((($ausenciaPaciente + $ausenciaMedico) / $totalCitas) * 100, 1) : 0,
                ];
            })
            ->toArray();

        $totales = [
            'ausencia_paciente' => array_sum(array_column($queryData, 'ausencia_paciente')),
            'ausencia_medico' => array_sum(array_column($queryData, 'ausencia_medico')),
            'sin_motivo' => array_sum(array_column($queryData, 'sin_motivo')),
            'total_inasistencias' => array_sum(array_column($queryData, 'total_inasistencias')),
            'total_citas' => array_sum(array_column($queryData, 'total_citas')),
            'pendientes' => array_sum(array_column($queryData, 'pendientes')),
        ];
        $totales['ausencia_paciente_pct'] = $totales['total_citas'] > 0 ? round(($totales['ausencia_paciente'] / $totales['total_citas']) * 100, 1) : 0;
        $totales['ausencia_medico_pct'] = $totales['total_citas'] > 0 ? round(($totales['ausencia_medico'] / $totales['total_citas']) * 100, 1) : 0;
        $totales['tasa_inasistencia'] = $totales['total_citas'] > 0 ? round(($totales['total_inasistencias'] / $totales['total_citas']) * 100, 1) : 0;

        return compact('queryData', 'totales', 'titulo', 'fechaTexto');
    }

    /**
     * Productividad por medico.
     *
     * Capa 1: dias de agenda (no depende de cupos).
     * Capa 2: cupos ofrecidos / perdidos por suspension y ocupacion.
     *
     * Los dias cubiertos por una suspension se excluyen de la capacidad
     * ofrecida porque el suplente hereda los mismos cupos y se duplicaria.
     */
    public static function productividadMedico(array $data): array
    {
        $rangos = self::resolverRangoFechas($data);
        $fecha_desde = $rangos['fecha_desde'];
        $fecha_hasta = $rangos['fecha_hasta'];
        $fechaTexto = $rangos['titulo'];
        $especialidadId = $data['especialidad_id'] ?? null;
        $especialidad = $especialidadId ? Especialidad::find($especialidadId) : null;

        $citasPorMedico = Cita::select(
                'medicos.id as medico_id',
                'medicos.nombre as medico_nombre',
                'medicos.apellido as medico_apellido',
                'especialidades.nombre as especialidad',
                DB::raw("COUNT(*) as total_citas"),
                DB::raw("SUM(CASE WHEN citas.estado = 'Atendida' THEN 1 ELSE 0 END) as atendidas"),
                DB::raw("SUM(CASE WHEN citas.estado = 'Agendada' THEN 1 ELSE 0 END) as agendadas"),
                DB::raw("SUM(CASE WHEN citas.estado = 'Cancelada' THEN 1 ELSE 0 END) as canceladas")
            )
            ->join('calendarios', 'citas.calendario_id', '=', 'calendarios.id')
            ->join('medicos', 'calendarios.medico_id', '=', 'medicos.id')
            ->join('especialidades', 'calendarios.especialidad_id', '=', 'especialidades.id')
            ->when($especialidadId, fn ($q) => $q->where('especialidades.id', $especialidadId))
            ->whereBetween('citas.fecha_cita', [$fecha_desde, $fecha_hasta])
            ->groupBy('medicos.id', 'medicos.nombre', 'medicos.apellido', 'especialidades.id', 'especialidades.nombre')
            ->get()
            ->keyBy('medico_id');

        $capacidad = DB::table('calendarios')
            ->join('medicos', 'calendarios.medico_id', '=', 'medicos.id')
            ->join('especialidades', 'calendarios.especialidad_id', '=', 'especialidades.id')
            ->when($especialidadId, fn ($q) => $q->where('especialidades.id', $especialidadId))
            ->whereBetween('calendarios.fecha', [$fecha_desde, $fecha_hasta])
            ->selectRaw("
                medicos.id as medico_id,
                medicos.nombre as medico_nombre,
                medicos.apellido as medico_apellido,
                especialidades.nombre as especialidad,
                COUNT(DISTINCT calendarios.fecha) FILTER (WHERE NOT EXISTS (
                    SELECT 1 FROM suspensiones_medicos s
                    WHERE s.medico_id = medicos.id
                      AND s.deleted_at IS NULL
                      AND calendarios.fecha BETWEEN s.fecha_inicio AND s.fecha_fin
                )) as dias_agenda,
                COUNT(DISTINCT calendarios.fecha) FILTER (WHERE EXISTS (
                    SELECT 1 FROM suspensiones_medicos s
                    WHERE s.medico_id = medicos.id
                      AND s.deleted_at IS NULL
                      AND calendarios.fecha BETWEEN s.fecha_inicio AND s.fecha_fin
                )) as dias_suspendidos,
                COALESCE(SUM(calendarios.cupos_primera_vez + calendarios.cupos_sucesivos) FILTER (WHERE NOT EXISTS (
                    SELECT 1 FROM suspensiones_medicos s
                    WHERE s.medico_id = medicos.id
                      AND s.deleted_at IS NULL
                      AND calendarios.fecha BETWEEN s.fecha_inicio AND s.fecha_fin
                )), 0) as cupos_ofrecidos,
                COALESCE(SUM(calendarios.cupos_primera_vez + calendarios.cupos_sucesivos) FILTER (WHERE EXISTS (
                    SELECT 1 FROM suspensiones_medicos s
                    WHERE s.medico_id = medicos.id
                      AND s.deleted_at IS NULL
                      AND calendarios.fecha BETWEEN s.fecha_inicio AND s.fecha_fin
                )), 0) as cupos_perdidos
            ")
            ->groupBy('medicos.id', 'medicos.nombre', 'medicos.apellido', 'especialidades.id', 'especialidades.nombre')
            ->get()
            ->keyBy('medico_id');

        $ids = $citasPorMedico->keys()->merge($capacidad->keys())->unique()->values();

        $queryData = $ids->map(function ($id) use ($citasPorMedico, $capacidad) {
            $cita = $citasPorMedico->get($id);
            $cap = $capacidad->get($id);

            $atendidas = (int) ($cita->atendidas ?? 0);
            $agendadas = (int) ($cita->agendadas ?? 0);
            $canceladas = (int) ($cita->canceladas ?? 0);
            $cerradas = $atendidas + $canceladas;
            $cupos = (int) ($cap->cupos_ofrecidos ?? 0);

            $nombre = trim(($cita->medico_nombre ?? $cap->medico_nombre) . ' ' . ($cita->medico_apellido ?? $cap->medico_apellido));

            return [
                'medico' => $nombre,
                'especialidad' => $cita->especialidad ?? $cap->especialidad,
                'dias_agenda' => (int) ($cap->dias_agenda ?? 0),
                'dias_suspendidos' => (int) ($cap->dias_suspendidos ?? 0),
                'cupos_ofrecidos' => $cupos,
                'cupos_perdidos' => (int) ($cap->cupos_perdidos ?? 0),
                'total_citas' => (int) ($cita->total_citas ?? 0),
                'atendidas' => $atendidas,
                'agendadas' => $agendadas,
                'canceladas' => $canceladas,
                'tasa_atencion' => $cerradas > 0 ? round($atendidas * 100 / $cerradas, 1) : 0.0,
                'ocupacion' => $cupos > 0 ? round($cerradas * 100 / $cupos, 1) : 0.0,
            ];
        })->sortBy([
            ['total_citas', 'desc'],
            ['medico', 'asc'],
        ])->values()->toArray();

        $totales = [
            'dias_agenda' => array_sum(array_column($queryData, 'dias_agenda')),
            'dias_suspendidos' => array_sum(array_column($queryData, 'dias_suspendidos')),
            'cupos_ofrecidos' => array_sum(array_column($queryData, 'cupos_ofrecidos')),
            'cupos_perdidos' => array_sum(array_column($queryData, 'cupos_perdidos')),
            'total_citas' => array_sum(array_column($queryData, 'total_citas')),
            'atendidas' => array_sum(array_column($queryData, 'atendidas')),
            'agendadas' => array_sum(array_column($queryData, 'agendadas')),
            'canceladas' => array_sum(array_column($queryData, 'canceladas')),
        ];

        $cerradas = $totales['atendidas'] + $totales['canceladas'];
        $totales['tasa_atencion'] = $cerradas > 0 ? round($totales['atendidas'] * 100 / $cerradas, 1) : 0;
        $totales['ocupacion'] = $totales['cupos_ofrecidos'] > 0
            ? round($cerradas * 100 / $totales['cupos_ofrecidos'], 1)
            : 0;

        $titulo = 'Productividad por Médico - ' . $rangos['titulo'];
        $especialidadNombre = $especialidad ? $especialidad->nombre : 'Todas';

        return compact('queryData', 'totales', 'titulo', 'fechaTexto', 'especialidadNombre');
    }

    /**
     * Citas atendidas sin patologia registrada.
     *
     * El diagnostico se registra en diagnostico_libre y/o en cita_patologias
     * (multi-select). Este reporte vigila el registro de PATOLOGIAS, que es
     * el dato que alimenta el reporte de causas.
     */
    public static function citasSinDiagnostico(array $data): array
    {
        $rangos = self::resolverRangoFechas($data);
        $fecha_desde = $rangos['fecha_desde'];
        $fecha_hasta = $rangos['fecha_hasta'];
        $fechaTexto = $rangos['titulo'];
        $especialidadId = $data['especialidad_id'] ?? null;
        $especialidad = $especialidadId ? Especialidad::find($especialidadId) : null;

        $queryData = Cita::select(
                'medicos.nombre as medico_nombre',
                'medicos.apellido as medico_apellido',
                'especialidades.nombre as especialidad',
                'pacientes.nombre as paciente_nombre',
                'pacientes.apellido as paciente_apellido',
                'pacientes.cedula as paciente_cedula',
                'citas.fecha_cita',
                'citas.observacion'
            )
            ->join('calendarios', 'citas.calendario_id', '=', 'calendarios.id')
            ->leftJoin('medicos', 'calendarios.medico_id', '=', 'medicos.id')
            ->join('especialidades', 'calendarios.especialidad_id', '=', 'especialidades.id')
            ->join('pacientes', 'citas.paciente_id', '=', 'pacientes.id')
            ->where('citas.estado', 'Atendida')
            ->whereDoesntHave('patologias')
            ->when($especialidadId, fn ($q) => $q->where('especialidades.id', $especialidadId))
            ->whereBetween('citas.fecha_cita', [$fecha_desde, $fecha_hasta])
            ->orderBy('especialidades.nombre')
            ->orderBy('medicos.apellido')
            ->orderBy('citas.fecha_cita')
            ->get()
            ->map(fn ($item) => [
                'medico' => trim($item->medico_nombre . ' ' . $item->medico_apellido) ?: self::SIN_MEDICO,
                'especialidad' => $item->especialidad,
                'paciente' => $item->paciente_nombre . ' ' . $item->paciente_apellido,
                'cedula' => $item->paciente_cedula,
                'fecha_cita' => $item->fecha_cita,
                'observacion' => $item->observacion,
            ])
            ->toArray();

        $totales = [
            'sin_diagnostico' => count($queryData),
        ];

        $titulo = 'Citas sin Diagnóstico - ' . $rangos['titulo'];
        $especialidadNombre = $especialidad ? $especialidad->nombre : 'Todas';

        return compact('queryData', 'totales', 'titulo', 'fechaTexto', 'especialidadNombre');
    }

    /**
     * Eficiencia de atencion.
     *
     * Las tasas se calculan sobre citas cerradas (atendidas + canceladas);
     * las pendientes se informan aparte. El % de historia traida usa como
     * denominador las atendidas, porque el campo solo se llena al atender.
     * "Dias promedio" es antelacion de agenda (fecha_cita - fecha_registro)
     * medida solo sobre atendidas.
     */
    public static function eficienciaAtencion(array $data): array
    {
        $rangos = self::resolverRangoFechas($data);
        $fecha_desde = $rangos['fecha_desde'];
        $fecha_hasta = $rangos['fecha_hasta'];
        $fechaTexto = $rangos['titulo'];

        $pct = fn ($parte, $total) => $total > 0 ? round(($parte / $total) * 100, 1) : 0;

        $queryData = Cita::select(
                DB::raw("TO_CHAR(citas.fecha_cita, 'YYYY-MM') as mes"),
                DB::raw("COUNT(*) as total"),
                DB::raw("SUM(CASE WHEN citas.estado = 'Atendida' THEN 1 ELSE 0 END) as atendidas"),
                DB::raw("SUM(CASE WHEN citas.estado = 'Cancelada' THEN 1 ELSE 0 END) as canceladas"),
                DB::raw("SUM(CASE WHEN citas.estado = 'Agendada' THEN 1 ELSE 0 END) as pendientes"),
                DB::raw("SUM(CASE WHEN citas.tipo_paciente = 'primera_vez' AND citas.estado IN ('Atendida', 'Cancelada') THEN 1 ELSE 0 END) as primera_vez"),
                DB::raw("SUM(CASE WHEN citas.tipo_paciente = 'control' AND citas.estado IN ('Atendida', 'Cancelada') THEN 1 ELSE 0 END) as control"),
                DB::raw("SUM(CASE WHEN citas.tipo_paciente = 'orden_medica' AND citas.estado IN ('Atendida', 'Cancelada') THEN 1 ELSE 0 END) as orden_medica"),
                DB::raw("SUM(CASE WHEN citas.historia_traida = true AND citas.estado = 'Atendida' THEN 1 ELSE 0 END) as historia_traida"),
                DB::raw("ROUND(AVG(citas.fecha_cita - citas.fecha_registro) FILTER (WHERE citas.estado = 'Atendida'), 1) as promedio_dias_espera")
            )
            ->whereBetween('citas.fecha_cita', [$fecha_desde, $fecha_hasta])
            ->groupBy(DB::raw("TO_CHAR(citas.fecha_cita, 'YYYY-MM')"))
            ->orderBy('mes')
            ->get()
            ->map(function ($item) use ($pct) {
                $atendidas = (int) $item->atendidas;
                $canceladas = (int) $item->canceladas;
                $cerradas = $atendidas + $canceladas;

                return [
                    'mes' => $item->mes,
                    'total' => (int) $item->total,
                    'cerradas' => $cerradas,
                    'atendidas' => $atendidas,
                    'tasa_atencion' => $pct($atendidas, $cerradas),
                    'canceladas' => $canceladas,
                    'tasa_cancelacion' => $pct($canceladas, $cerradas),
                    'pendientes' => (int) $item->pendientes,
                    'primera_vez' => (int) $item->primera_vez,
                    'pct_primera_vez' => $pct((int) $item->primera_vez, $cerradas),
                    'control' => (int) $item->control,
                    'pct_control' => $pct((int) $item->control, $cerradas),
                    'orden_medica' => (int) $item->orden_medica,
                    'historia_traida' => (int) $item->historia_traida,
                    'pct_historia_traida' => $pct((int) $item->historia_traida, $atendidas),
                    'promedio_dias_espera' => (float) $item->promedio_dias_espera,
                ];
            })
            ->toArray();

        $totales = [
            'total' => array_sum(array_column($queryData, 'total')),
            'cerradas' => array_sum(array_column($queryData, 'cerradas')),
            'atendidas' => array_sum(array_column($queryData, 'atendidas')),
            'canceladas' => array_sum(array_column($queryData, 'canceladas')),
            'pendientes' => array_sum(array_column($queryData, 'pendientes')),
            'primera_vez' => array_sum(array_column($queryData, 'primera_vez')),
            'control' => array_sum(array_column($queryData, 'control')),
            'orden_medica' => array_sum(array_column($queryData, 'orden_medica')),
            'historia_traida' => array_sum(array_column($queryData, 'historia_traida')),
        ];
        $totales['tasa_atencion'] = $pct($totales['atendidas'], $totales['cerradas']);
        $totales['tasa_cancelacion'] = $pct($totales['canceladas'], $totales['cerradas']);
        $totales['pct_primera_vez'] = $pct($totales['primera_vez'], $totales['cerradas']);
        $totales['pct_control'] = $pct($totales['control'], $totales['cerradas']);
        $totales['pct_historia_traida'] = $pct($totales['historia_traida'], $totales['atendidas']);
        $totales['promedio_dias_espera'] = $totales['atendidas'] > 0
            ? round(array_sum(array_map(
                fn ($r) => $r['promedio_dias_espera'] * $r['atendidas'],
                $queryData
            )) / $totales['atendidas'], 1)
            : 0;

        $titulo = 'Eficiencia de Atención - ' . $rangos['titulo'];

        return compact('queryData', 'totales', 'titulo', 'fechaTexto');
    }
}
