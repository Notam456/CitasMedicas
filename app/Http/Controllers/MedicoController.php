<?php

namespace App\Http\Controllers;

use App\Models\Medico;
use App\Models\Especialidad;
use App\Models\User;
use App\Notifications\NuevoMedico;
use App\Notifications\MedicoModificado;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Notification;

class MedicoController extends Controller
{
    public function index(Request $request)
    {
        $especialidades = Especialidad::all();

        if ($request->ajax() && $request->has('draw')) {
            return $this->dataTableResponse($request);
        }

        $title = '¿Estas seguro de que deseas eliminar este médico?';
        $texrt = 'Esta acción no se puede deshacer.';
        confirmDelete($title, $texrt);

        return view('medicos.listaMedicos', compact('especialidades'));
    }

    private function dataTableResponse(Request $request)
    {
        $query = Medico::with('especialidad')->select('medicos.*');

        $totalRecords = $query->count();

        if ($search = $request->get('search')['value']) {
            $query->where(function ($q) use ($search) {
                $q->where('nombre', 'ILIKE', "%{$search}%")
                    ->orWhere('apellido', 'ILIKE', "%{$search}%")
                    ->orWhere('cedula', 'ILIKE', "%{$search}%")
                    ->orWhere('telefono', 'ILIKE', "%{$search}%")
                    ->orWhereHas('especialidad', function ($q2) use ($search) {
                        $q2->where('nombre', 'ILIKE', "%{$search}%");
                    });
            });
        }

        $filteredRecords = $query->count();

        $orderColumn = $request->get('order')[0]['column'] ?? 0;
        $orderDir = $request->get('order')[0]['dir'] ?? 'asc';
        $columns = ['nombre', 'apellido', 'cedula', 'telefono', 'especialidad_id'];
        if (isset($columns[$orderColumn])) {
            $query->orderBy($columns[$orderColumn], $orderDir);
        } else {
            $query->orderBy('nombre', 'asc');
        }

        $start = $request->get('start', 0);
        $length = $request->get('length', 10);
        $data = $query->skip($start)->take($length)->get();

        $dataFormatted = [];
        foreach ($data as $row) {
            $btnShow = '<button type="button" data-id="' . $row->id . '" class="btn-show btn btn-xs btn-square btn-neutral"><i class="bi bi-eye"></i></button>';
            $btnEdit = '<button type="button" data-id="' . $row->id . '" class="btn-edit btn btn-xs btn-square btn-neutral"><i class="bi bi-pencil"></i></button>';
            // $btnDelete = '<a href="' . route('medicos.destroy', $row->id) . '" class="btn btn-xs btn-square btn-neutral text-danger-hover border-danger-hover" data-confirm-delete="true"><i class="bi bi-trash"></i></a>';
            $acciones = '<div class="hstack gap-2 justify-content-end">' . $btnShow . $btnEdit . /* $btnDelete . */ '</div>';

            $dataFormatted[] = [
                $row->nombre,
                $row->apellido,
                $row->cedula,
                $row->telefono,
                $row->especialidad->nombre ?? 'N/A',
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

    public function create()
    {
        // El registro se hace desde el modal en la misma vista.
    }

    public function show(int $id)
    {
        $medicoToShow = Medico::with('especialidad', 'horarios')->findOrFail($id);
        return response()->json($medicoToShow);
    }

    public function store(Request $request)
    {
        $request->merge([
            'nombre' => mb_convert_case(trim($request->nombre), MB_CASE_TITLE, 'UTF-8'),
            'apellido' => mb_convert_case(trim($request->apellido), MB_CASE_TITLE, 'UTF-8'),
        ]);
        $request->validate([
            'nombre' => 'required|string|max:255|regex:/^[a-zA-ZáéíóúÁÉÍÓÚñÑüÜ\s]+$/u',
            'apellido' => 'required|string|max:255|regex:/^[a-zA-ZáéíóúÁÉÍÓÚñÑüÜ\s]+$/u',
            'cedula' => 'required|string|unique:medicos,cedula',
            'telefono' => 'required|string|max:20|regex:/^[\d\-\(\)\s\+]+$/',
            'especialidad_id' => 'required|exists:especialidades,id',
            'horarios' => 'nullable|array',
            'horarios.*.checked' => 'nullable|in:1',
            'horarios.*.hora_entrada' => 'required_if:horarios.*.checked,1|nullable|date_format:H:i',
            'horarios.*.hora_salida' => 'required_if:horarios.*.checked,1|nullable|date_format:H:i|after:horarios.*.hora_entrada',
        ], [
            'horarios.*.hora_entrada.required_if' => 'La hora de entrada es obligatoria para los días seleccionados.',
            'horarios.*.hora_salida.required_if' => 'La hora de salida es obligatoria para los días seleccionados.',
            'horarios.*.hora_salida.after' => 'La hora de salida debe ser posterior a la hora de entrada.',
            'horarios.*.hora_entrada.date_format' => 'El formato de la hora de entrada debe ser HH:MM.',
            'horarios.*.hora_salida.date_format' => 'El formato de la hora de salida debe ser HH:MM.',
        ]);

        $medico = Medico::create($request->only([
            'nombre',
            'apellido',
            'cedula',
            'telefono',
            'especialidad_id',
        ]));

        if ($request->has('horarios')) {
            foreach ($request->input('horarios') as $dia => $h) {
                if (isset($h['checked']) && $h['checked'] == '1') {
                    $medico->horarios()->create([
                        'dia_semana' => $dia,
                        'hora_entrada' => $h['hora_entrada'],
                        'hora_salida' => $h['hora_salida'],
                    ]);
                }
            }
        }

        Notification::send(User::all(), new NuevoMedico($medico));

        alert()->success('Médico creado exitosamente.');
        return redirect()->route('medicos.index');
    }

    public function edit(int $id)
    {
        $medicoToEdit = Medico::with('especialidad', 'horarios')->findOrFail($id);
        return response()->json($medicoToEdit);
    }

    public function update(Request $request, int $id)
    {
        $medico = Medico::findOrFail($id);

        $request->merge([
            'nombre' => mb_convert_case(trim($request->nombre), MB_CASE_TITLE, 'UTF-8'),
            'apellido' => mb_convert_case(trim($request->apellido), MB_CASE_TITLE, 'UTF-8'),
        ]);
        $request->validate([
            'nombre' => 'required|string|max:255|regex:/^[a-zA-ZáéíóúÁÉÍÓÚñÑüÜ\s]+$/u',
            'apellido' => 'required|string|max:255|regex:/^[a-zA-ZáéíóúÁÉÍÓÚñÑüÜ\s]+$/u',
            'cedula' => 'required|string|unique:medicos,cedula,' . $id,
            'telefono' => 'required|string|max:20|regex:/^[\d\-\(\)\s\+]+$/',
            'especialidad_id' => 'required|exists:especialidades,id',
            'horarios' => 'nullable|array',
            'horarios.*.checked' => 'nullable|in:1',
            'horarios.*.hora_entrada' => 'required_if:horarios.*.checked,1|nullable|date_format:H:i',
            'horarios.*.hora_salida' => 'required_if:horarios.*.checked,1|nullable|date_format:H:i|after:horarios.*.hora_entrada',
        ], [
            'horarios.*.hora_entrada.required_if' => 'La hora de entrada es obligatoria para los días seleccionados.',
            'horarios.*.hora_salida.required_if' => 'La hora de salida es obligatoria para los días seleccionados.',
            'horarios.*.hora_salida.after' => 'La hora de salida debe ser posterior a la hora de entrada.',
            'horarios.*.hora_entrada.date_format' => 'El formato de la hora de entrada debe ser HH:MM.',
            'horarios.*.hora_salida.date_format' => 'El formato de la hora de salida debe ser HH:MM.',
        ]);

        $conflictos = 0;
        $horariosInput = $request->input('horarios', []);
        $diasPermitidos = [];
        foreach ($horariosInput as $dia => $h) {
            if (isset($h['checked']) && $h['checked'] == '1') {
                $diasPermitidos[$dia] = [
                    'entrada' => $h['hora_entrada'],
                    'salida' => $h['hora_salida'],
                ];
            }
        }

        if (!empty($diasPermitidos)) {
            $planificaciones = $medico->calendarios()->where('fecha', '>=', now()->toDateString())->get();
            foreach ($planificaciones as $plan) {
                $diaSemana = Carbon::parse($plan->fecha)->dayOfWeekIso;
                if (!isset($diasPermitidos[$diaSemana])) {
                    $conflictos++;
                } else {
                    $planInicio = Carbon::parse($plan->hora_inicio)->format('H:i');
                    $planFin = Carbon::parse($plan->hora_fin)->format('H:i');
                    $limiteInicio = Carbon::parse($diasPermitidos[$diaSemana]['entrada'])->format('H:i');
                    $limiteFin = Carbon::parse($diasPermitidos[$diaSemana]['salida'])->format('H:i');

                    if ($planInicio < $limiteInicio || $planFin > $limiteFin) {
                        $conflictos++;
                    }
                }
            }
        }

        $medico->update($request->only([
            'nombre',
            'apellido',
            'cedula',
            'telefono',
            'especialidad_id',
        ]));

        $medico->horarios()->delete();
        if (!empty($diasPermitidos)) {
            foreach ($diasPermitidos as $dia => $h) {
                $medico->horarios()->create([
                    'dia_semana' => $dia,
                    'hora_entrada' => $h['entrada'],
                    'hora_salida' => $h['salida'],
                ]);
            }
        }

        if ($conflictos > 0) {
            alert()->warning('Médico actualizado', "Se detectaron $conflictos cupos planificados en días/horas que ahora no están permitidos en su nuevo horario.")->persistent();
        } else {
            alert()->success('Médico actualizado exitosamente.');
        }

        if (!auth()->user()->hasRole('administrador')) {
            $admins = User::role('administrador')->get();
            Notification::send($admins, new MedicoModificado($medico, auth()->user()));
        }

        return redirect()->route('medicos.index');
    }

    public function destroy(int $id)
    {
        $medico = Medico::withCount('citas')->findOrFail($id);
        if ($medico->citas_count > 0) {
            alert()->error('No se puede eliminar', 'Este médico tiene citas médicas asociadas en el sistema.');
            return redirect()->route('medicos.index');
        }
        $medico->delete();

        alert()->success('Médico eliminado exitosamente.');
        return redirect()->route('medicos.index');
    }
}