<?php

namespace App\Http\Controllers;

use App\Models\Especialidad;
use App\Models\User;
use App\Notifications\NuevaEspecialidad;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;
use RealRashid\SweetAlert\Facades\Alert;

class EspecialidadController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax() && $request->has('draw')) {
            return $this->dataTableResponse($request);
        }

        $title = '¿Estas seguro de que deseas eliminar esta especialidad?';
        $text = 'Esta acción no se puede deshacer.';
        confirmDelete($title, $text);

        return view('especialidades.listaEspecialidades');
    }

    private function dataTableResponse(Request $request)
    {
        $query = Especialidad::query();

        $totalRecords = $query->count();

        if ($search = $request->get('search')['value']) {
            $query->where('nombre', 'ILIKE', "%{$search}%");
        }

        $filteredRecords = $query->count();

        $orderColumn = $request->get('order')[0]['column'] ?? 0;
        $orderDir = $request->get('order')[0]['dir'] ?? 'asc';
        $columns = ['nombre'];
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
            $btnShow = '<button type="button" data-id="'.$row->id.'" class="btn-show btn btn-xs btn-square btn-neutral"><i class="bi bi-eye"></i></button>';
            $btnEdit = '<button type="button" data-id="'.$row->id.'" class="btn-edit btn btn-xs btn-square btn-neutral"><i class="bi bi-pencil"></i></button>';
            $btnDelete = '<a href="'.route('especialidades.destroy', $row->id).'" class="btn btn-xs btn-square btn-neutral text-danger-hover border-danger-hover" data-confirm-delete="true"><i class="bi bi-trash"></i></a>';
            $acciones = '<div class="hstack gap-2 justify-content-end">'.$btnShow.$btnEdit.$btnDelete.'</div>';

            $dataFormatted[] = [
                $row->nombre,
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
        //
    }

    public function show(int $id)
    {
        $especialidadToShow = Especialidad::findOrFail($id);
        return response()->json($especialidadToShow);
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:255|unique:especialidades,nombre|regex:/^[a-zA-ZáéíóúÁÉÍÓÚñÑüÜ\s]+$/u',
        ]);

        $especialidad = Especialidad::create($request->only('nombre'));

        Notification::send(User::all(), new NuevaEspecialidad($especialidad));

        alert()->success('Especialidad creada exitosamente.');
        return redirect()->route('especialidades.index');
    }

    public function edit(int $id)
    {
        $especialidadToEdit = Especialidad::findOrFail($id);
        return response()->json($especialidadToEdit);
    }

    public function update(Request $request, int $id)
    {
        $request->validate([
            'nombre' => 'required|string|max:255|unique:especialidades,nombre,' . $id . '|regex:/^[a-zA-ZáéíóúÁÉÍÓÚñÑüÜ\s]+$/u',
        ]);

        $especialidad = Especialidad::findOrFail($id);
        $especialidad->update($request->only('nombre'));

        alert()->success('Especialidad actualizada exitosamente.');
        return redirect()->route('especialidades.index');
    }

    public function destroy(int $id)
    {
        $especialidad = Especialidad::findOrFail($id);
        $especialidad->delete();

        alert()->success('Especialidad eliminada exitosamente.');
        return redirect()->route('especialidades.index');
    }
}