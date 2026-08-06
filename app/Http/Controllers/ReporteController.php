<?php

namespace App\Http\Controllers;

use App\Models\Especialidad;
use App\Models\Medico;
use App\Models\Cita;
use App\Models\Paciente;
use App\Models\Parroquia;
use App\Models\Municipio;
use App\Models\Distrito;
use App\Models\CitaPatologia;
use Mccarlosen\LaravelMpdf\Facades\LaravelMpdf as Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Exports\MedicosPorEspecialidadExport;
use App\Exports\ProcedenciaPacientesExport;
use App\Exports\MovimientoConsultasExport;
use App\Exports\CausasPrincipalesExport;
use Maatwebsite\Excel\Facades\Excel;
use Carbon\Carbon;

class ReporteController extends Controller
{
    public function index()
    {
        $especialidades = Especialidad::where('estado', true)->get();
        return view('reportes.index', compact('especialidades'));
    }

    public function medicosPorEspecialidad(Request $request)
{
    $request->validate([
        'especialidad_id' => 'nullable|exists:especialidades,id'
    ]);

    $especialidadId = $request->especialidad_id;

    $medicos = Medico::with('especialidad')
        ->when($especialidadId, function ($query) use ($especialidadId) {
            return $query->where('especialidad_id', $especialidadId);
        })
        ->get();

    $especialidad = $especialidadId ? Especialidad::find($especialidadId) : null;

    $membrete = $this->getMembreteBase64();
    $pdf = Pdf::loadView('reportes.pdf.medicos_por_especialidad_pdf', compact('especialidad', 'medicos', 'membrete'));
    $nombreArchivo = $especialidad ? 'medicos_' . $especialidad->nombre : 'todos_los_medicos';
    return $pdf->stream($nombreArchivo . '.pdf');
}

public function exportarMedicosPorEspecialidadExcel(Request $request)
{
    $request->validate([
        'especialidad_id' => 'nullable|exists:especialidades,id'
    ]);

    $especialidadId = $request->especialidad_id;

    $medicos = Medico::with('especialidad')
        ->when($especialidadId, function ($query) use ($especialidadId) {
            return $query->where('especialidad_id', $especialidadId);
        })
        ->get();

    $especialidad = $especialidadId ? Especialidad::find($especialidadId) : null;
    $titulo = $especialidad ? 'Médicos de ' . $especialidad->nombre : 'Todos los médicos';

    return Excel::download(new MedicosPorEspecialidadExport($medicos, $especialidad, $titulo), 'medicos.xlsx');
}
    // Método privado para obtener los datos del reporte (reutilizado para PDF y Excel)
    private function getProcedenciaData(Request $request)
    {
        if ($request->tipo_rango == 'mes') {
            $fecha = Carbon::createFromFormat('Y-m', $request->mes);
            $fecha_desde = $fecha->copy()->startOfMonth()->toDateString();
            $fecha_hasta = $fecha->copy()->endOfMonth()->toDateString();
            Carbon::setLocale('es');
            $titulo = 'Procedencia de Pacientes - ' . $fecha->translatedFormat('F Y');
        } else {
            $fecha_desde = $request->fecha_desde;
            $fecha_hasta = $request->fecha_hasta;
            $titulo = 'Procedencia de Pacientes - ' . Carbon::parse($fecha_desde)->format('d/m/Y') . ' al ' . Carbon::parse($fecha_hasta)->format('d/m/Y');
        }

        // select de distritos orderby id
        $distritosReales = Distrito::orderBy('id')->get();

        $distritosEspeciales = [
            (object)['id' => 1000, 'nombre' => 'Ignorado']
        ];

        // Unir todos los distritos (reales + especiales)
        $todosLosDistritos = $distritosReales->concat($distritosEspeciales);

        // Estructura inicial del reporte (ceros)
        $reporte = [];
        foreach ($todosLosDistritos as $distrito) {
            $distritoNombre = $distrito->nombre;
            $distritoId = $distrito->id;
            $reporte[$distritoNombre] = [
                'distrito_id' => $distritoId,
                'municipios' => [],
                'subtotal' => ['agendadas' => 0, 'atendidas' => 0, 'total' => 0]
            ];
            // Para distritos reales (no especiales), obtener todos sus municipios desde la tabla
            if (!in_array($distritoId, [6, 1000])) { 
                $municipiosDelDistrito = Municipio::where('distrito_id', $distritoId)->orderBy('nombre')->get();
                foreach ($municipiosDelDistrito as $mun) {
                    $reporte[$distritoNombre]['municipios'][$mun->nombre] = [
                        'agendadas' => 0,
                        'atendidas' => 0,
                        'total' => 0
                    ];
                }
            }
        }
        $reporte['Ignorado']['municipios']['Sin municipio'] = ['agendadas' => 0, 'atendidas' => 0, 'total' => 0];

        // 3. Consultar conteos reales de citas en el rango
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

        // 4. Procesar conteos y actualizar el reporte
        foreach ($conteos as $row) {
            // Determinar a qué distrito pertenece esta fila
            if (is_null($row->distrito_id)) {
                $distritoNombre = 'Ignorado';
            } elseif ($row->distrito_id == 6) {
                $distritoNombre = 'Otros Estados';
            } else {
                $distritoNombre = $row->distrito_nombre;
            }
            $municipioNombre = $row->municipio_nombre ?? 'Sin municipio';
            
            if (isset($reporte[$distritoNombre])) {
                // Si el municipio no existe en la estructura predefinida, lo agregamos (escalable)
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
        
        // 5. Ordenar distritos: reales primero (excluye Otros Estados), luego Otros Estados, luego Ignorado
        $ordenDistritos = [];
        foreach ($distritosReales as $d) {
            if ($d->nombre !== 'Otros Estados') {
                $ordenDistritos[] = $d->nombre;
            }
        }
        $ordenDistritos[] = 'Otros Estados';
        $ordenDistritos[] = 'Ignorado';
        
        // Construir array final ordenado
        $reporteFinal = [];
        foreach ($ordenDistritos as $nombreDistrito) {
            if (isset($reporte[$nombreDistrito])) {
                // Ordenar municipios alfabéticamente
                $municipiosArray = [];
                foreach ($reporte[$nombreDistrito]['municipios'] as $munNombre => $datos) {
                    $municipiosArray[] = [
                        'nombre' => $munNombre,
                        'agendadas' => $datos['agendadas'],
                        'atendidas' => $datos['atendidas'],
                        'total' => $datos['total']
                    ];
                }
                usort($municipiosArray, fn($a, $b) => strcmp($a['nombre'], $b['nombre']));
                $reporteFinal[] = [
                    'distrito' => $nombreDistrito,
                    'municipios' => $municipiosArray,
                    'subtotal' => $reporte[$nombreDistrito]['subtotal']
                ];
            }
        }
        
        // Totales globales
        $totalesGlobales = ['agendadas' => 0, 'atendidas' => 0, 'todos' => 0];
        foreach ($reporteFinal as $item) {
            $totalesGlobales['agendadas'] += $item['subtotal']['agendadas'];
            $totalesGlobales['atendidas'] += $item['subtotal']['atendidas'];
            $totalesGlobales['todos'] += $item['subtotal']['total'];
        }
        
        return compact('reporteFinal', 'totalesGlobales', 'titulo', 'fecha_desde', 'fecha_hasta');
    }

    public function procedenciaPacientes(Request $request)
    {
        $request->validate([
            'tipo_rango' => 'required|in:mes,rango',
            'mes' => 'required_if:tipo_rango,mes|nullable|date_format:Y-m',
            'fecha_desde' => 'required_if:tipo_rango,rango|nullable|date',
            'fecha_hasta' => 'required_if:tipo_rango,rango|nullable|date|after_or_equal:fecha_desde',
        ]);

        $data = $this->getProcedenciaData($request);
        $membrete = $this->getMembreteBase64();
        
        $pdf = Pdf::loadView('reportes.pdf.procedencia_pacientes_pdf', array_merge($data, ['membrete' => $membrete]));
        return $pdf->stream('procedencia_pacientes.pdf');
    }

    public function exportarProcedenciaExcel(Request $request)
    {
        $request->validate([
            'tipo_rango' => 'required|in:mes,rango',
            'mes' => 'required_if:tipo_rango,mes|nullable|date_format:Y-m',
            'fecha_desde' => 'required_if:tipo_rango,rango|nullable|date',
            'fecha_hasta' => 'required_if:tipo_rango,rango|nullable|date|after_or_equal:fecha_desde',
        ]);

        $data = $this->getProcedenciaData($request);
        return Excel::download(new ProcedenciaPacientesExport(
            $data['reporteFinal'],
            $data['totalesGlobales'],
            $data['titulo'],
            $data['fecha_desde'],
            $data['fecha_hasta']
        ), 'procedencia_pacientes.xlsx');
    }

    private function getMembreteBase64()
    {
        $logoRuta = public_path('assets/img/membreteMPPS2.png');
        if (file_exists($logoRuta)) {
            $logoData = base64_encode(file_get_contents($logoRuta));
            return 'data:image/png;base64,' . $logoData;
        }
        return '';
    }

    public function movimientoConsultas(Request $request)
    {
        $request->validate([
            'tipo_paciente' => 'required|in:adulto,pediatria,todas',
            'tipo_rango' => 'required|in:mes,rango',
            'mes' => 'required_if:tipo_rango,mes|nullable|date_format:Y-m',
            'fecha_desde' => 'required_if:tipo_rango,rango|nullable|date',
            'fecha_hasta' => 'required_if:tipo_rango,rango|nullable|date|after_or_equal:fecha_desde',
            'especialidad_id' => 'nullable|exists:especialidades,id',
        ]);

        if ($request->tipo_rango == 'mes') {
            $fecha = Carbon::createFromFormat('Y-m', $request->mes);
            $fecha_desde = $fecha->copy()->startOfMonth()->toDateString();
            $fecha_hasta = $fecha->copy()->endOfMonth()->toDateString();
            $fechaTexto = $fecha->locale('es')->translatedFormat('F Y');
        } else {
            $fecha_desde = $request->fecha_desde;
            $fecha_hasta = $request->fecha_hasta;
            $fechaTexto = Carbon::parse($fecha_desde)->format('d/m/Y') . ' al ' . Carbon::parse($fecha_hasta)->format('d/m/Y');
        }

        $tipoPaciente = $request->tipo_paciente;
        $edadCondicion = match ($tipoPaciente) {
            'adulto' => '> 12',
            'pediatria' => '<= 12',
            default => null,
        };
        $edadCol = $edadCondicion ? "EXTRACT(YEAR FROM AGE(pacientes.fecha_nacimiento)) {$edadCondicion}" : null;
        $especialidadId = $request->especialidad_id;
        $especialidad = $especialidadId ? Especialidad::find($especialidadId) : null;
        $aroId = Especialidad::where('nombre', 'Aro (Embarazados)')->value('id');

        $columnas = [
            'agendadas' => 'Citas Agendadas',
            'atendidas' => 'Citas Atendidas',
            'primera_vez' => 'Citas de Primera Vez',
            'sucesivas' => 'Citas Sucesivas',
            'ausentes' => 'Citados Ausentes',
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
            DB::raw($sum("citas.estado = 'Cancelada'") . ' as ausentes'),
            DB::raw("SUM(CASE WHEN citas.estado = 'Atendida' AND EXTRACT(YEAR FROM AGE(pacientes.fecha_nacimiento)) BETWEEN 10 AND 19 THEN 1 ELSE 0 END) as adolescentes"),
        ];

        if (isset($columnas['menos_13_sem'])) {
            $selects[] = DB::raw("SUM(CASE WHEN acd.semanas_gestacion IS NOT NULL AND acd.semanas_gestacion < 13 AND citas.estado = 'Atendida' AND especialidades.id = {$aroId} THEN 1 ELSE 0 END) as menos_13_sem");
        }

        $data = Cita::select($selects)
            ->join('calendarios', 'citas.calendario_id', '=', 'calendarios.id')
            ->join('medicos', 'calendarios.medico_id', '=', 'medicos.id')
            ->join('especialidades', 'medicos.especialidad_id', '=', 'especialidades.id')
            ->join('pacientes', 'citas.paciente_id', '=', 'pacientes.id')
            ->leftJoin('aro_cita_datos as acd', 'acd.cita_id', '=', 'citas.id')
            ->when($especialidadId, function ($query) use ($especialidadId) {
                return $query->where('especialidades.id', $especialidadId);
            })
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
            $totales[$clave] = array_sum(array_column($data, $clave));
        }

        $tipoPacienteTexto = match ($tipoPaciente) {
            'adulto' => 'Mayores de 12 años',
            'pediatria' => 'Pediatría (12 años o menos)',
            default => 'Todas las Edades',
        };
        $titulo = 'Movimiento de Consulta Externa - ' . $tipoPacienteTexto;
        $especialidadNombre = $especialidad ? $especialidad->nombre : 'Todas';
        $especialidadSeleccionada = (bool) $especialidadId;

        if ($request->has('excel')) {
            return Excel::download(new MovimientoConsultasExport($data, $titulo, $tipoPacienteTexto, $fechaTexto, $columnas, $especialidadNombre, $totales, $especialidadSeleccionada), 'movimiento_consultas.xlsx');
        }

        $membrete = $this->getMembreteBase64();
        $pdf = Pdf::loadView('reportes.pdf.movimiento_consultas_pdf', [
            'data' => $data,
            'titulo' => $titulo,
            'tipoPaciente' => $tipoPacienteTexto,
            'fechaTexto' => $fechaTexto,
            'membrete' => $membrete,
            'columnas' => $columnas,
            'especialidadNombre' => $especialidadNombre,
            'totales' => $totales,
            'especialidadSeleccionada' => $especialidadSeleccionada,
        ]);
        return $pdf->stream('movimiento_consultas.pdf');
    }

    public function movimientoConsultasPdf(Request $request)
    {
        return $this->movimientoConsultas($request);
    }

    public function movimientoConsultasExcel(Request $request)
    {
        $request->merge(['excel' => true]);
        return $this->movimientoConsultas($request);
    }

    public function movimientoConsultaAro(Request $request)
    {
        $request->validate([
            'tipo_rango' => 'required|in:mes,rango',
            'mes' => 'required_if:tipo_rango,mes|nullable|date_format:Y-m',
            'fecha_desde' => 'required_if:tipo_rango,rango|nullable|date',
            'fecha_hasta' => 'required_if:tipo_rango,rango|nullable|date|after_or_equal:fecha_desde',
        ]);

        if ($request->tipo_rango == 'mes') {
            $fecha = Carbon::createFromFormat('Y-m', $request->mes);
            $fecha_desde = $fecha->copy()->startOfMonth()->toDateString();
            $fecha_hasta = $fecha->copy()->endOfMonth()->toDateString();
            $fechaTexto = $fecha->locale('es')->translatedFormat('F Y');
        } else {
            $fecha_desde = $request->fecha_desde;
            $fecha_hasta = $request->fecha_hasta;
            $fechaTexto = Carbon::parse($fecha_desde)->format('d/m/Y') . ' al ' . Carbon::parse($fecha_hasta)->format('d/m/Y');
        }

        $aroEsp = Especialidad::where('nombre', 'Aro (Embarazados)')->first();
        if (!$aroEsp) {
            Alert::error('Error', 'No se encontró la especialidad Aro (Embarazados).');
            return redirect()->route('reportes.index');
        }

        $data = Cita::select(
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
            ->map(function ($item) {
                return [
                    'mes' => $item->mes,
                    'menos_13_sem' => (int) $item->menos_13_sem,
                    'adolescentes' => (int) $item->adolescentes,
                    'total_consultas' => (int) $item->total_consultas,
                ];
            })
            ->toArray();

        $totales = [
            'menos_13_sem' => array_sum(array_column($data, 'menos_13_sem')),
            'adolescentes' => array_sum(array_column($data, 'adolescentes')),
            'total_consultas' => array_sum(array_column($data, 'total_consultas')),
        ];

        $titulo = 'Movimiento de Consulta Aro Mensual';

        if ($request->has('excel')) {
            return Excel::download(new \App\Exports\MovimientoConsultaAroExport($data, $titulo, $fechaTexto, $totales), 'movimiento_consulta_aro.xlsx');
        }

        $membrete = $this->getMembreteBase64();
        $pdf = Pdf::loadView('reportes.pdf.movimiento_consulta_aro_pdf', compact('data', 'titulo', 'fechaTexto', 'membrete', 'totales'));
        return $pdf->stream('movimiento_consulta_aro.pdf');
    }

    public function movimientoConsultaAroPdf(Request $request)
    {
        return $this->movimientoConsultaAro($request);
    }

    public function movimientoConsultaAroExcel(Request $request)
    {
        $request->merge(['excel' => true]);
        return $this->movimientoConsultaAro($request);
    }

    public function causasPrincipales(Request $request)
    {
        $request->validate([
            'tipo_rango' => 'required|in:mes,rango',
            'mes' => 'required_if:tipo_rango,mes|nullable|date_format:Y-m',
            'fecha_desde' => 'required_if:tipo_rango,rango|nullable|date',
            'fecha_hasta' => 'required_if:tipo_rango,rango|nullable|date|after_or_equal:fecha_desde',
        ]);

        if ($request->tipo_rango == 'mes') {
            $fecha = Carbon::createFromFormat('Y-m', $request->mes);
            $fecha_desde = $fecha->copy()->startOfMonth()->toDateString();
            $fecha_hasta = $fecha->copy()->endOfMonth()->toDateString();
            $fechaTexto = $fecha->locale('es')->translatedFormat('F Y');
        } else {
            $fecha_desde = $request->fecha_desde;
            $fecha_hasta = $request->fecha_hasta;
            $fechaTexto = Carbon::parse($fecha_desde)->format('d/m/Y') . ' al ' . Carbon::parse($fecha_hasta)->format('d/m/Y');
        }

        $data = CitaPatologia::select(
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
            ->map(function ($item) {
                return [
                    'patologia' => $item->patologia,
                    'especialidad' => $item->especialidad,
                    'masculino_primera' => (int) $item->masculino_primera,
                    'masculino_sucesivas' => (int) $item->masculino_sucesivas,
                    'femenino_primera' => (int) $item->femenino_primera,
                    'femenino_sucesivas' => (int) $item->femenino_sucesivas,
                    'total' => (int) $item->total,
                ];
            })
            ->toArray();

        $titulo = '25 Principales Causas de Consulta Externa';

        if ($request->has('excel')) {
            return Excel::download(new CausasPrincipalesExport($data, $titulo, $fechaTexto), '25_causas_principales.xlsx');
        }

        $membrete = $this->getMembreteBase64();
        $pdf = Pdf::loadView('reportes.pdf.causas_principales_pdf', compact('data', 'titulo', 'fechaTexto', 'membrete'));
        return $pdf->stream('25_causas_principales.pdf');
    }

    public function causasPrincipalesPdf(Request $request)
    {
        return $this->causasPrincipales($request);
    }

    public function causasPrincipalesExcel(Request $request)
    {
        $request->merge(['excel' => true]);
        return $this->causasPrincipales($request);
    }
}
