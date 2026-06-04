<?php

namespace App\Http\Controllers;

use App\Models\Especialidad;
use App\Models\Medico;
use App\Models\Cita;
use App\Models\Paciente;
use App\Models\Parroquia;
use App\Models\Municipio;
use App\Models\Distrito;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Exports\MedicosPorEspecialidadExport;
use App\Exports\ProcedenciaPacientesExport;
use App\Exports\MovimientoConsultasExport;
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
            (object)['id' => 999, 'nombre' => 'Otros Estados'],
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
            if ($distritoId <= 5) { 
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
        
        // 5. Ordenar distritos: primero los reales por id, luego Otros Estados, luego Ignorado
        $ordenDistritos = [];
        foreach ($distritosReales as $d) {
            $ordenDistritos[] = $d->nombre;
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
            'tipo_paciente' => 'required|in:adulto,pediatria',
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

        $tipoPaciente = $request->tipo_paciente;
        $edadCondicion = $tipoPaciente == 'adulto' ? '>= 18' : '< 18';

        $data = Cita::select(
                'especialidades.nombre as especialidad',
                DB::raw("COUNT(CASE WHEN citas.tipo_paciente = 'primera_vez' THEN 1 END) as primera_vez"),
                DB::raw("COUNT(CASE WHEN citas.tipo_paciente = 'control' THEN 1 END) as sucesivas"),
                DB::raw("COUNT(*) as total")
            )
            ->join('calendarios', 'citas.calendario_id', '=', 'calendarios.id')
            ->join('medicos', 'calendarios.medico_id', '=', 'medicos.id')
            ->join('especialidades', 'medicos.especialidad_id', '=', 'especialidades.id')
            ->join('pacientes', 'citas.paciente_id', '=', 'pacientes.id')
            ->whereBetween('citas.fecha_cita', [$fecha_desde, $fecha_hasta])
            ->whereRaw("EXTRACT(YEAR FROM AGE(pacientes.fecha_nacimiento)) {$edadCondicion}")
            ->whereIn('citas.estado', ['Atendida', 'Agendada'])
            ->groupBy('especialidades.id', 'especialidades.nombre')
            ->orderBy('especialidades.nombre')
            ->get()
            ->map(function ($item) {
                return [
                    'especialidad' => $item->especialidad,
                    'primera_vez' => (int) $item->primera_vez,
                    'sucesivas' => (int) $item->sucesivas,
                    'total' => (int) $item->total,
                ];
            })
            ->toArray();

        $titulo = 'Movimiento de Consulta Externa - ' . ($tipoPaciente == 'adulto' ? 'Adultos' : 'Pediatría');

        if ($request->has('excel')) {
            return Excel::download(new MovimientoConsultasExport($data, $titulo, $tipoPaciente, $fechaTexto), 'movimiento_consultas.xlsx');
        }

        $membrete = $this->getMembreteBase64();
        $pdf = Pdf::loadView('reportes.pdf.movimiento_consultas_pdf', compact('data', 'titulo', 'tipoPaciente', 'fechaTexto', 'membrete'));
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
}
