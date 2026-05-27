<?php

namespace App\Http\Controllers;

use App\Models\Cita;
use App\Models\Patologia;
use App\Models\Diagnostico;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use RealRashid\SweetAlert\Facades\Alert;
use Carbon\Carbon;

class DiagnosticoController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax() && $request->has('draw')) {
            return $this->dataTableResponse($request);
        }
        confirmDelete('¿Eliminar diagnóstico?', 'Esta acción no se puede deshacer.');
        return view('diagnosticos.index');
    }

    private function dataTableResponse(Request $request)
    {
        $query = Diagnostico::query()
            ->join('citas', 'diagnosticos.cita_id', '=', 'citas.id')
            ->join('pacientes', 'citas.paciente_id', '=', 'pacientes.id')
            ->join('calendarios', 'citas.calendario_id', '=', 'calendarios.id')
            ->join('medicos', 'calendarios.medico_id', '=', 'medicos.id')
            ->join('especialidades', 'medicos.especialidad_id', '=', 'especialidades.id')
            ->leftJoin('patologias', 'diagnosticos.patologia_id', '=', 'patologias.id')
            ->join('users', 'diagnosticos.user_id', '=', 'users.id')
            ->select(
                'diagnosticos.id',
                'pacientes.nombre as paciente_nombre',
                'pacientes.apellido as paciente_apellido',
                'pacientes.cedula as paciente_cedula',
                'citas.fecha_cita',
                'especialidades.nombre as especialidad_nombre',
                'medicos.nombre as medico_nombre',
                'medicos.apellido as medico_apellido',
                'patologias.nombre as patologia_nombre',
                'diagnosticos.diagnostico_libre',
                'diagnosticos.asistio',
                'users.name as user_name',
                'diagnosticos.created_at'
            );

        $totalRecords = $query->count();

        if ($search = $request->get('search')['value']) {
            $query->where(function ($q) use ($search) {
                $q->where('pacientes.nombre', 'ILIKE', "%{$search}%")
                  ->orWhere('pacientes.apellido', 'ILIKE', "%{$search}%")
                  ->orWhere('pacientes.cedula', 'ILIKE', "%{$search}%")
                  ->orWhere('medicos.nombre', 'ILIKE', "%{$search}%")
                  ->orWhere('medicos.apellido', 'ILIKE', "%{$search}%")
                  ->orWhere('especialidades.nombre', 'ILIKE', "%{$search}%")
                  ->orWhere('patologias.nombre', 'ILIKE', "%{$search}%")
                  ->orWhere('diagnosticos.diagnostico_libre', 'ILIKE', "%{$search}%")
                  ->orWhere('users.name', 'ILIKE', "%{$search}%");
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
            5 => 'diagnosticos.created_at',
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
            $diagnostico = $row->patologia_nombre
                ? $row->patologia_nombre . ($row->diagnostico_libre ? ' - ' . $row->diagnostico_libre : '')
                : $row->diagnostico_libre;

            $btnShow = '<button type="button" data-id="'.$row->id.'" class="btn-show btn btn-xs btn-square btn-neutral"><i class="bi bi-eye"></i></button>';
            $btnEdit = '<button type="button" data-id="'.$row->id.'" class="btn-edit btn btn-xs btn-square btn-neutral"><i class="bi bi-pencil"></i></button>';
            $btnDelete = '<a href="'.route('diagnosticos.destroy', $row->id).'" class="btn btn-xs btn-square btn-neutral text-danger-hover border-danger-hover" data-confirm-delete="true"><i class="bi bi-trash"></i></a>';
            $acciones = '<div class="hstack gap-2 justify-content-end">'.$btnShow.$btnEdit.$btnDelete.'</div>';

            $dataFormatted[] = [
                $row->paciente_nombre . ' ' . $row->paciente_apellido,
                $row->paciente_cedula,
                Carbon::parse($row->fecha_cita)->format('d/m/Y'),
                $row->especialidad_nombre,
                'Dr. ' . $row->medico_nombre . ' ' . $row->medico_apellido,
                $diagnostico ?: 'Sin diagnóstico',
                $row->asistio ? 'Sí' : 'No',
                $row->user_name,
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
        $diagnostico = Diagnostico::with('cita.medico.especialidad')->findOrFail($id);
        $patologias = Patologia::where('especialidad_id', $diagnostico->cita->medico->especialidad_id)->get();
        return response()->json([
            'diagnostico' => $diagnostico,
            'patologias' => $patologias,
        ]);
    }

    public function update(Request $request, $id)
    {
        $diagnostico = Diagnostico::findOrFail($id);
        $request->validate([
            'patologia_id' => 'nullable|exists:patologias,id',
            'diagnostico_libre' => 'nullable|string',
        ]);
        $diagnostico->update([
            'patologia_id' => $request->patologia_id,
            'diagnostico_libre' => $request->diagnostico_libre,
            'asistio' => true,
        ]);
        Alert::success('Diagnóstico actualizado correctamente.');
        return redirect()->route('diagnosticos.index');
    }

    public function destroy($id)
    {
        $diagnostico = Diagnostico::findOrFail($id);
        $diagnostico->delete();
        Alert::success('Diagnóstico eliminado correctamente.');
        return redirect()->route('diagnosticos.index');
    }

    public function show($id)
    {
        $diagnostico = Diagnostico::with(['cita.paciente', 'cita.medico.especialidad', 'patologia', 'user'])->findOrFail($id);
        return response()->json($diagnostico);
    }

    public function atender(Cita $cita)
    {
        if ($cita->estado !== 'Agendada') {
            Alert::error('Error', 'Esta cita ya fue atendida o cancelada.');
            return redirect()->route('Citas.index');
        }

        $patologias = Patologia::whereHas('especialidad', function($q) use ($cita) {
            $q->where('id', $cita->medico->especialidad_id);
        })->get();

        return view('Cita.atender', compact('cita', 'patologias'));
    }

    public function store(Request $request, Cita $cita)
    {
        $request->validate([
            'patologia_id' => 'nullable|exists:patologias,id',
            'diagnostico_libre' => 'nullable|string',
        ]);

        if ($cita->diagnostico) {
            Alert::error('Error', 'Esta cita ya tiene diagnóstico registrado.');
            return redirect()->route('Citas.index');
        }

        Diagnostico::create([
            'cita_id' => $cita->id,
            'patologia_id' => $request->patologia_id,
            'diagnostico_libre' => $request->diagnostico_libre,
            'asistio' => true, 
            'user_id' => Auth::id(),
        ]);

        $cita->estado = 'Atendida';
        $cita->save();

        Alert::success('Éxito', 'Diagnóstico registrado correctamente.');
        return redirect()->route('morbilidad.pendientes');
    }
}
