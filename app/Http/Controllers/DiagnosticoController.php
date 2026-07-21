<?php

namespace App\Http\Controllers;

use App\Models\Cita;
use App\Models\Patologia;
use App\Models\Especialidad;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use RealRashid\SweetAlert\Facades\Alert;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DiagnosticoController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax() && $request->has('draw')) {
            return $this->dataTableResponse($request);
        }
        confirmDelete('¿Eliminar diagnóstico?', 'Esta acción no se puede deshacer.');
        $especialidades = Especialidad::where('estado', true)->get();
        return view('diagnosticos.index', compact('especialidades'));
    }

    private function dataTableResponse(Request $request)
    {
        $query = Cita::query()
            ->join('pacientes', 'citas.paciente_id', '=', 'pacientes.id')
            ->join('calendarios', 'citas.calendario_id', '=', 'calendarios.id')
            ->join('medicos', 'calendarios.medico_id', '=', 'medicos.id')
            ->join('especialidades', 'medicos.especialidad_id', '=', 'especialidades.id')
            ->leftJoin('users as atendido', 'citas.atendido_por', '=', 'atendido.id')
            ->leftJoin('users as creador', 'citas.user_id', '=', 'creador.id')
            ->select(
                'citas.id',
                'pacientes.nombre as paciente_nombre',
                'pacientes.apellido as paciente_apellido',
                'pacientes.cedula as paciente_cedula',
                'citas.fecha_cita',
                'especialidades.nombre as especialidad_nombre',
                'medicos.nombre as medico_nombre',
                'medicos.apellido as medico_apellido',
                'citas.diagnostico_libre',
                'citas.estado',
                'atendido.name as atendido_por_nombre',
                'creador.name as creado_por_nombre',
                'citas.created_at',
                DB::raw("(SELECT STRING_AGG(p.nombre, ', ') FROM cita_patologias cp JOIN patologias p ON p.id = cp.patologia_id WHERE cp.cita_id = citas.id) as patologias_nombres")
            )
            ->where('citas.estado', 'Atendida');
        // Aplicar filtros
        if ($request->filled('especialidad_id')) {
            $query->where('especialidades.id', $request->especialidad_id);
        }
        if ($request->filled('fecha_desde')) {
            $query->whereDate('citas.fecha_cita', '>=', $request->fecha_desde);
        }
        if ($request->filled('fecha_hasta')) {
            $query->whereDate('citas.fecha_cita', '<=', $request->fecha_hasta);
        }

        $totalRecords = $query->count();

        if ($search = $request->get('search')['value']) {
            $query->where(function ($q) use ($search) {
                $q->where('pacientes.nombre', 'ILIKE', "%{$search}%")
                    ->orWhere('pacientes.apellido', 'ILIKE', "%{$search}%")
                    ->orWhere('pacientes.cedula', 'ILIKE', "%{$search}%")
                    ->orWhere('medicos.nombre', 'ILIKE', "%{$search}%")
                    ->orWhere('medicos.apellido', 'ILIKE', "%{$search}%")
                    ->orWhere('especialidades.nombre', 'ILIKE', "%{$search}%")
                    ->orWhere('citas.diagnostico_libre', 'ILIKE', "%{$search}%")
                    ->orWhere('atendido.name', 'ILIKE', "%{$search}%")
                    ->orWhere('creador.name', 'ILIKE', "%{$search}%");
            });
        }

        $filteredRecords = $query->count();

        $orderColumn = $request->get('order')[0]['column'] ?? 2;
        $orderDir = $request->get('order')[0]['dir'] ?? 'desc';
        $columns = [
            0 => 'pacientes.nombre',
            1 => 'pacientes.cedula',
            2 => 'citas.fecha_cita',
            3 => 'especialidades.nombre',
            4 => 'medicos.nombre',
            5 => 'citas.created_at',
        ];
        if (isset($columns[$orderColumn])) {
            $query->orderBy($columns[$orderColumn], $orderDir);
        } else {
            $query->orderBy('citas.fecha_cita', 'desc');
        }

        $start = $request->get('start', 0);
        $length = $request->get('length', 10);
        $data = $query->skip($start)->take($length)->get();

        $dataFormatted = [];
        foreach ($data as $row) {
            $diagnosticoStr = '';
            if (!empty($row->patologias_nombres)) {
                $diagnosticoStr = $row->patologias_nombres;
                if ($row->diagnostico_libre) {
                    $diagnosticoStr .= ' - ' . $row->diagnostico_libre;
                }
            } else {
                $diagnosticoStr = $row->diagnostico_libre ?: 'Sin diagnóstico';
            }

            $btnShow = '<button type="button" data-id="' . $row->id . '" class="btn-show btn btn-xs btn-square btn-neutral"><i class="bi bi-eye"></i></button>';
            $btnEdit = '<button type="button" data-id="' . $row->id . '" class="btn-edit btn btn-xs btn-square btn-neutral"><i class="bi bi-pencil"></i></button>';
            //$btnDelete = '<a href="' . route('diagnosticos.destroy', $row->id) . '" class="btn btn-xs btn-square btn-neutral text-danger-hover border-danger-hover" data-confirm-delete="true"><i class="bi bi-trash"></i></a>';
            $acciones = '<div class="hstack gap-2 justify-content-end">' . $btnShow . $btnEdit . '</div>';

            $dataFormatted[] = [
                $row->paciente_nombre . ' ' . $row->paciente_apellido,
                $row->paciente_cedula,
                Carbon::parse($row->fecha_cita)->format('d/m/Y'),
                $row->especialidad_nombre,
                'Dr. ' . $row->medico_nombre . ' ' . $row->medico_apellido,
                $diagnosticoStr,
                $row->estado,
                $row->creado_por_nombre,
                Carbon::parse($row->created_at)->format('d/m/Y H:i'),
                $acciones,
            ];
        }

        return response()->json([
            'draw' => intval($request->draw),
            'recordsTotal' => $totalRecords,
            'recordsFiltered' => $filteredRecords,
            'data' => $dataFormatted,
        ]);
    }

    public function edit($id)
    {
        $cita = Cita::with([
            'paciente',
            'medico.especialidad',
            'patologias',
        ])->findOrFail($id);

        $patologiasDisponibles = Patologia::where('especialidad_id', $cita->medico->especialidad_id)->get();

        return response()->json([
            'cita' => $cita,
            'patologias_disponibles' => $patologiasDisponibles,
        ]);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'diagnostico_libre' => 'nullable|string|max:5000',
            'patologias' => 'array',
            'patologias.*' => 'exists:patologias,id',
        ]);

        DB::beginTransaction();
        try {
            $cita = Cita::findOrFail($id);
            $cita->update(['diagnostico_libre' => $request->diagnostico_libre]);
            $cita->patologias()->sync($request->patologias ?? []);

            DB::commit();
            Alert::success('Diagnóstico actualizado correctamente.');
        } catch (\Exception $e) {
            DB::rollBack();
            Alert::error('Error', 'No se pudo actualizar el diagnóstico: ' . $e->getMessage());
        }

        $redirect = $request->query('redirect_to', 'diagnosticos.index');
        return redirect()->route($redirect);
    }

   /* public function destroy($id)
    {
        $cita = Cita::findOrFail($id);
        $cita->delete();
        Alert::success('Cita y su diagnóstico eliminados correctamente.');
        return redirect()->route('diagnosticos.index');
    }
*/
    public function show($id)
    {
        $cita = Cita::with([
            'paciente',
            'medico.especialidad',
            'patologias',
            'atendidoPor'
        ])->findOrFail($id);
        return response()->json($cita);
    }

    public function atender(Cita $cita)
    {
        if ($cita->estado !== 'Agendada') {
            Alert::error('Error', 'Esta cita ya fue atendida o cancelada.');
            return redirect()->route('Citas.index');
        }

        $patologias = Patologia::where('especialidad_id', $cita->medico->especialidad_id)->get();

        return view('morbilidad.pendientes', compact('cita', 'patologias'));
    }

    public function store(Request $request, Cita $cita)
    {
        $request->validate([
            'diagnostico_libre' => 'nullable|string|max:5000',
            'patologias' => 'array',
            'patologias.*' => 'exists:patologias,id',
        ]);

        if ($cita->estado === 'Atendida') {
            Alert::error('Error', 'Esta cita ya tiene diagnóstico registrado.');
            return redirect()->route('morbilidad.pendientes');
        }

        DB::beginTransaction();
        try {
            $cita->update([
                'diagnostico_libre' => $request->diagnostico_libre,
                'atendido_por' => Auth::id(),
                'estado' => 'Atendida',
            ]);

            if ($request->has('patologias')) {
                $cita->patologias()->sync($request->patologias);
            }

            DB::commit();
            Alert::success('Éxito', 'Diagnóstico registrado correctamente.');
        } catch (\Exception $e) {
            DB::rollBack();
            Alert::error('Error', 'No se pudo guardar el diagnóstico: ' . $e->getMessage());
        }

        return redirect()->route('morbilidad.pendientes');
    }
}