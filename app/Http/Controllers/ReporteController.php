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
use App\Exports\MedicosExport;
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

        $pdf = Pdf::loadView('reportes.medicos_por_especialidad_pdf', compact('especialidad', 'medicos', 'membrete'));

        $nombreArchivo = $especialidad ? 'medicos_' . $especialidad->nombre : 'todos_los_medicos';
        return $pdf->stream($nombreArchivo . '.pdf');
    }

    public function exportarMedicosExcel()
    {
        return Excel::download(new MedicosExport, 'medicos_hospital.xlsx');
    }

    public function procedenciaPacientes(Request $request)
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
            $titulo = 'Procedencia de Pacientes - ' . $fecha->translatedFormat('F Y'); // mes en ingles
        } else {
            $fecha_desde = $request->fecha_desde;
            $fecha_hasta = $request->fecha_hasta;
            $titulo = 'Procedencia de Pacientes - ' . Carbon::parse($fecha_desde)->format('d/m/Y') . ' al ' . Carbon::parse($fecha_hasta)->format('d/m/Y');
        }

        // Obtener todos los distritos (excepto 'Otros Estados' que se manejará aparte, pero lo incluimos como id=6)
        $distritos = Distrito::orderBy('id')->get();
        
        // Obtener todos los municipios con su distrito
        $municipios = Municipio::with('distrito')->orderBy('nombre')->get();
        
        // Estructura de datos: agrupar por distrito y luego municipio
        // Primero, crear un array con todos los distritos (incluyendo dos especiales: 'Otros Estados' e 'Ignorado')
        $distritosEspeciales = [
            (object)['id' => 6, 'nombre' => 'Otros Estados'],
            (object)['id' => null, 'nombre' => 'Ignorado']
        ];
        $todosLosDistritos = $distritos->concat($distritosEspeciales);
        
        // Inicializar el reporte con ceros para cada distrito y sus municipios
        $reporte = [];
        foreach ($todosLosDistritos as $distrito) {
            $distritoNombre = $distrito->nombre;
            $distritoId = $distrito->id;
            $reporte[$distritoNombre] = [
                'distrito_id' => $distritoId,
                'municipios' => [],
                'subtotal' => ['agendadas' => 0, 'atendidas' => 0, 'total' => 0]
            ];
            // Obtener municipios reales de este distrito (solo para distritos que existen en la tabla)
            if ($distritoId && $distritoId != 6) { // Evitar incluir municipios en 'Otros Estados'
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
        
        // Ahora consultar los conteos reales de citas en el rango
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

        // Procesar los conteos y actualizar el reporte
        foreach ($conteos as $row) {
            // Determinar el distrito
            if (is_null($row->distrito_id)) {
                $distritoNombre = 'Ignorado';
            } elseif ($row->distrito_id == 6) {
                $distritoNombre = 'Otros Estados';
            } else {
                $distritoNombre = $row->distrito_nombre;
            }
            $municipioNombre = $row->municipio_nombre ?? 'Sin municipio';
            
            // Si el distrito existe en el reporte (debería)
            if (isset($reporte[$distritoNombre])) {
                // Si el municipio no está en la estructura predefinida (ej: municipio nuevo que no estaba en la base de municipios), agregarlo
                if (!isset($reporte[$distritoNombre]['municipios'][$municipioNombre])) {
                    $reporte[$distritoNombre]['municipios'][$municipioNombre] = ['agendadas' => 0, 'atendidas' => 0, 'total' => 0];
                }
                $reporte[$distritoNombre]['municipios'][$municipioNombre]['agendadas'] = $row->agendadas;
                $reporte[$distritoNombre]['municipios'][$municipioNombre]['atendidas'] = $row->atendidas;
                $reporte[$distritoNombre]['municipios'][$municipioNombre]['total'] = $row->total_pacientes;
                
                // Actualizar subtotales
                $reporte[$distritoNombre]['subtotal']['agendadas'] += $row->agendadas;
                $reporte[$distritoNombre]['subtotal']['atendidas'] += $row->atendidas;
                $reporte[$distritoNombre]['subtotal']['total'] += $row->total_pacientes;
            }
        }
        
        $orden = ['Distrito I', 'Distrito II', 'Distrito III', 'Distrito IV', 'Distrito V', 'Otros Estados', 'Ignorado'];
        $reporteFinal = [];
        foreach ($orden as $nombreDistrito) {
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
                usort($municipiosArray, function($a, $b) {
                    return strcmp($a['nombre'], $b['nombre']);
                });
                $reporteFinal[] = [
                    'distrito' => $nombreDistrito,
                    'municipios' => $municipiosArray,
                    'subtotal' => $reporte[$nombreDistrito]['subtotal']
                ];
            }
        }
        
        // Agregar cualquier otro distrito que no esté en el orden (por si hay nuevos)
        foreach ($reporte as $nombreDistrito => $data) {
            if (!in_array($nombreDistrito, $orden)) {
                $municipiosArray = [];
                foreach ($data['municipios'] as $munNombre => $datos) {
                    $municipiosArray[] = [
                        'nombre' => $munNombre,
                        'agendadas' => $datos['agendadas'],
                        'atendidas' => $datos['atendidas'],
                        'total' => $datos['total']
                    ];
                }
                usort($municipiosArray, function($a, $b) {
                    return strcmp($a['nombre'], $b['nombre']);
                });
                $reporteFinal[] = [
                    'distrito' => $nombreDistrito,
                    'municipios' => $municipiosArray,
                    'subtotal' => $data['subtotal']
                ];
            }
        }
        
        // Calcular totales globales
        $totalesGlobales = ['agendadas' => 0, 'atendidas' => 0, 'todos' => 0];
        foreach ($reporteFinal as $item) {
            $totalesGlobales['agendadas'] += $item['subtotal']['agendadas'];
            $totalesGlobales['atendidas'] += $item['subtotal']['atendidas'];
            $totalesGlobales['todos'] += $item['subtotal']['total'];
        }
        
        $membrete = $this->getMembreteBase64();
        $pdf = Pdf::loadView('reportes.procedencia_pacientes_pdf', compact('reporteFinal', 'totalesGlobales', 'titulo', 'membrete', 'fecha_desde', 'fecha_hasta'));
        return $pdf->stream('procedencia_pacientes.pdf');
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
}
