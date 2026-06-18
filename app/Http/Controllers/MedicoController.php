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
            $btnDelete = '<a href="' . route('medicos.destroy', $row->id) . '" class="btn btn-xs btn-square btn-neutral text-danger-hover border-danger-hover" data-confirm-delete="true"><i class="bi bi-trash"></i></a>';
            $acciones = '<div class="hstack gap-2 justify-content-end">' . $btnShow . $btnEdit . $btnDelete . '</div>';

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
        $medicoToShow = Medico::with('especialidad')->findOrFail($id);
        return response()->json($medicoToShow);
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:255|regex:/^[a-zA-ZáéíóúÁÉÍÓÚñÑüÜ\s]+$/u',
            'apellido' => 'required|string|max:255|regex:/^[a-zA-ZáéíóúÁÉÍÓÚñÑüÜ\s]+$/u',
            'cedula' => 'required|string|unique:medicos,cedula',
            'telefono' => 'required|string|max:20',
            'especialidad_id' => 'required|exists:especialidades,id',
        ]);

        $medico = Medico::create($request->only([
            'nombre',
            'apellido',
            'cedula',
            'telefono',
            'especialidad_id',
        ]));

        Notification::send(User::all(), new NuevoMedico($medico));

        alert()->success('Médico creado exitosamente.');
        return redirect()->route('medicos.index');
    }

    public function edit(int $id)
    {
        $medicoToEdit = Medico::with('especialidad')->findOrFail($id);
        return response()->json($medicoToEdit);
    }

    public function update(Request $request, int $id)
    {
        $medico = Medico::findOrFail($id);

        $request->validate([
            'nombre' => 'required|string|max:255|regex:/^[a-zA-ZáéíóúÁÉÍÓÚñÑüÜ\s]+$/u',
            'apellido' => 'required|string|max:255|regex:/^[a-zA-ZáéíóúÁÉÍÓÚñÑüÜ\s]+$/u',
            'cedula' => 'required|string|unique:medicos,cedula,' . $id,
            'telefono' => 'required|string|max:20',
            'especialidad_id' => 'required|exists:especialidades,id',
        ]);

        $medico->update($request->only([
            'nombre',
            'apellido',
            'cedula',
            'telefono',
            'especialidad_id',
        ]));

        if (!auth()->user()->hasRole('administrador')) {
            $admins = User::role('administrador')->get();
            Notification::send($admins, new MedicoModificado($medico, auth()->user()));
        }

        alert()->success('Médico actualizado exitosamente.');
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