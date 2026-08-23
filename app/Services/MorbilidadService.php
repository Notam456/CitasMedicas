<?php

namespace App\Services;

use App\Models\Cita;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MorbilidadService
{
    public static function buildBaseQuery(Request $request)
    {
        $query = Cita::query()
            ->join('pacientes', 'citas.paciente_id', '=', 'pacientes.id')
            ->join('calendarios', 'citas.calendario_id', '=', 'calendarios.id')
            ->join('medicos', 'calendarios.medico_id', '=', 'medicos.id')
            ->join('especialidades', 'medicos.especialidad_id', '=', 'especialidades.id')
            ->leftJoin('expedientes', 'pacientes.id', '=', 'expedientes.paciente_id')
            ->leftJoin(DB::raw("(SELECT cp.cita_id, STRING_AGG(p.nombre, ', ' ORDER BY p.nombre) as patologias_nombres FROM cita_patologias cp JOIN patologias p ON p.id = cp.patologia_id GROUP BY cp.cita_id) as pats"), 'pats.cita_id', '=', 'citas.id')
            ->select(
                'citas.id',
                'citas.paciente_id',
                'citas.historia_traida',
                'expedientes.numero_expediente',
                'pacientes.nombre as paciente_nombre',
                'pacientes.apellido as paciente_apellido',
                'pacientes.cedula as paciente_cedula',
                'citas.fecha_cita',
                'citas.observacion as cita_observacion',
                'medicos.nombre as medico_nombre',
                'medicos.apellido as medico_apellido',
                'especialidades.nombre as especialidad_nombre',
                'especialidades.id as especialidad_id',
                'citas.diagnostico_libre',
                'citas.estado',
                'citas.tipo_paciente',
                'citas.created_at',
                'pats.patologias_nombres'
            );

        if ($request->filled('especialidad_id')) {
            $query->where('especialidades.id', $request->especialidad_id);
        }
        if ($request->filled('fecha_desde')) {
            $query->whereDate('citas.fecha_cita', '>=', $request->fecha_desde);
        }
        if ($request->filled('fecha_hasta')) {
            $query->whereDate('citas.fecha_cita', '<=', $request->fecha_hasta);
        }
        if ($request->filled('tipo_paciente')) {
            $query->where('citas.tipo_paciente', $request->tipo_paciente);
        }
        if ($request->filled('estado')) {
            $query->where('citas.estado', $request->estado);
        }
        if ($request->filled('fecha_registro_desde')) {
            $query->whereDate('citas.created_at', '>=', $request->fecha_registro_desde);
        }
        if ($request->filled('fecha_registro_hasta')) {
            $query->whereDate('citas.created_at', '<=', $request->fecha_registro_hasta);
        }
        if ($request->filled('medico_id')) {
            $query->where('medicos.id', $request->medico_id);
        }

        return $query;
    }

    public static function buildBasePendientes(Request $request)
    {
        $query = Cita::query()
            ->join('pacientes', 'citas.paciente_id', '=', 'pacientes.id')
            ->join('calendarios', 'citas.calendario_id', '=', 'calendarios.id')
            ->join('medicos', 'calendarios.medico_id', '=', 'medicos.id')
            ->join('especialidades', 'medicos.especialidad_id', '=', 'especialidades.id')
            ->leftJoin('expedientes', 'pacientes.id', '=', 'expedientes.paciente_id')
            ->select(
                'citas.id',
                'citas.paciente_id',
                'pacientes.nombre as paciente_nombre',
                'pacientes.apellido as paciente_apellido',
                'pacientes.cedula as paciente_cedula',
                'citas.fecha_cita',
                'medicos.nombre as medico_nombre',
                'medicos.apellido as medico_apellido',
                'especialidades.nombre as especialidad_nombre',
                'expedientes.numero_expediente'
            )
            ->where('citas.estado', 'Agendada')
            ->whereDate('citas.fecha_cita', now()->today());

        if ($request->filled('especialidad_id')) {
            $query->where('especialidades.id', $request->especialidad_id);
        }
        return $query;
    }
}
