<?php

namespace App\Http\Controllers;

use App\Http\Requests\ReporteRequest;
use App\Models\Especialidad;
use App\Services\ReporteService;
use App\Support\Membrete;
use Mccarlosen\LaravelMpdf\Facades\LaravelMpdf as Pdf;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\MedicosPorEspecialidadExport;
use App\Exports\ProcedenciaPacientesExport;
use App\Exports\MovimientoConsultasExport;
use App\Exports\MovimientoConsultaAroExport;
use App\Exports\CausasPrincipalesExport;
use App\Models\Medico;

class ReporteController extends Controller
{
    public function index()
    {
        $especialidades = Especialidad::where('estado', true)->get();
        return view('reportes.index', compact('especialidades'));
    }

    public function medicosPorEspecialidad(ReporteRequest $request)
    {
        $especialidadId = $request->especialidad_id;
        $medicos = Medico::with('especialidad')
            ->when($especialidadId, fn ($q) => $q->where('especialidad_id', $especialidadId))
            ->get();
        $especialidad = $especialidadId ? Especialidad::find($especialidadId) : null;

        $pdf = Pdf::loadView('reportes.pdf.medicos_por_especialidad_pdf', [
            'especialidad' => $especialidad,
            'medicos' => $medicos,
            'membrete' => Membrete::base64(),
        ]);
        $nombreArchivo = $especialidad ? 'medicos_' . $especialidad->nombre : 'todos_los_medicos';
        return $pdf->stream($nombreArchivo . '.pdf');
    }

    public function exportarMedicosPorEspecialidadExcel(ReporteRequest $request)
    {
        $especialidadId = $request->especialidad_id;
        $medicos = Medico::with('especialidad')
            ->when($especialidadId, fn ($q) => $q->where('especialidad_id', $especialidadId))
            ->get();
        $especialidad = $especialidadId ? Especialidad::find($especialidadId) : null;
        $titulo = $especialidad ? 'Médicos de ' . $especialidad->nombre : 'Todos los médicos';

        return Excel::download(new MedicosPorEspecialidadExport($medicos, $especialidad, $titulo), 'medicos.xlsx');
    }

    public function procedenciaPacientes(ReporteRequest $request)
    {
        $data = ReporteService::procedencia($request->validated());
        $pdf = Pdf::loadView('reportes.pdf.procedencia_pacientes_pdf', array_merge($data, ['membrete' => Membrete::base64()]));
        return $pdf->stream('procedencia_pacientes.pdf');
    }

    public function exportarProcedenciaExcel(ReporteRequest $request)
    {
        $data = ReporteService::procedencia($request->validated());
        return Excel::download(new ProcedenciaPacientesExport(
            $data['reporteFinal'], $data['totalesGlobales'], $data['titulo'], $data['fecha_desde'], $data['fecha_hasta']
        ), 'procedencia_pacientes.xlsx');
    }

    public function movimientoConsultas(ReporteRequest $request)
    {
        $validated = $request->validated();
        $validated['tipo_paciente'] = $request->tipo_paciente;
        $validated['especialidad_id'] = $request->especialidad_id;

        $data = ReporteService::movimientoConsultas($validated);

        if ($request->has('excel')) {
            return Excel::download(new MovimientoConsultasExport(
                $data['queryData'], $data['titulo'], $data['tipoPacienteTexto'],
                $data['fechaTexto'], $data['columnas'], $data['especialidadNombre'],
                $data['totales'], $data['especialidadSeleccionada']
            ), 'movimiento_consultas.xlsx');
        }

        $pdf = Pdf::loadView('reportes.pdf.movimiento_consultas_pdf', [
            'data' => $data['queryData'], 'titulo' => $data['titulo'],
            'tipoPaciente' => $data['tipoPacienteTexto'], 'fechaTexto' => $data['fechaTexto'],
            'membrete' => Membrete::base64(), 'columnas' => $data['columnas'],
            'especialidadNombre' => $data['especialidadNombre'], 'totales' => $data['totales'],
            'especialidadSeleccionada' => $data['especialidadSeleccionada'],
        ]);
        return $pdf->stream('movimiento_consultas.pdf');
    }

    public function movimientoConsultasPdf(ReporteRequest $request)
    {
        return $this->movimientoConsultas($request);
    }

    public function movimientoConsultasExcel(ReporteRequest $request)
    {
        $request->merge(['excel' => true]);
        return $this->movimientoConsultas($request);
    }

    public function movimientoConsultaAro(ReporteRequest $request)
    {
        $data = ReporteService::movimientoConsultaAro($request->validated());

        if (!$data['aroEsp']) {
            alert()->error('Error', 'No se encontró la especialidad Aro (Embarazados).');
            return redirect()->route('reportes.index');
        }

        if ($request->has('excel')) {
            return Excel::download(new MovimientoConsultaAroExport($data['queryData'], $data['titulo'], $data['fechaTexto'], $data['totales']), 'movimiento_consulta_aro.xlsx');
        }

        $pdf = Pdf::loadView('reportes.pdf.movimiento_consulta_aro_pdf', [
            'data' => $data['queryData'], 'titulo' => $data['titulo'],
            'fechaTexto' => $data['fechaTexto'], 'membrete' => Membrete::base64(),
            'totales' => $data['totales'],
        ]);
        return $pdf->stream('movimiento_consulta_aro.pdf');
    }

    public function movimientoConsultaAroPdf(ReporteRequest $request)
    {
        return $this->movimientoConsultaAro($request);
    }

    public function movimientoConsultaAroExcel(ReporteRequest $request)
    {
        $request->merge(['excel' => true]);
        return $this->movimientoConsultaAro($request);
    }

    public function causasPrincipales(ReporteRequest $request)
    {
        $data = ReporteService::causasPrincipales($request->validated());

        if ($request->has('excel')) {
            return Excel::download(new CausasPrincipalesExport($data['queryData'], $data['titulo'], $data['fechaTexto']), '25_causas_principales.xlsx');
        }

        $pdf = Pdf::loadView('reportes.pdf.causas_principales_pdf', [
            'data' => $data['queryData'], 'titulo' => $data['titulo'],
            'fechaTexto' => $data['fechaTexto'], 'membrete' => Membrete::base64(),
        ]);
        return $pdf->stream('25_causas_principales.pdf');
    }

    public function causasPrincipalesPdf(ReporteRequest $request)
    {
        return $this->causasPrincipales($request);
    }

    public function causasPrincipalesExcel(ReporteRequest $request)
    {
        $request->merge(['excel' => true]);
        return $this->causasPrincipales($request);
    }
}
