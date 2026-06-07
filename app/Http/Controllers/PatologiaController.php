<?php

namespace App\Http\Controllers;

use App\Models\Patologia;
use App\Models\Especialidad;
use App\Models\User;
use App\Notifications\NuevaPatologia;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Notification;
use RealRashid\SweetAlert\Facades\Alert;

class PatologiaController extends Controller
{
    public function index(Request $request)
    {
        $especialidades = Especialidad::where('estado', true)->get();

        if ($request->ajax() && $request->has('draw')) {
            return $this->dataTableResponse($request);
        }

        confirmDelete('¿Eliminar patología?', 'Esta acción no se puede deshacer.');
        return view('patologias.index', compact('especialidades'));
    }

    private function dataTableResponse(Request $request)
    {
        $query = Patologia::with('especialidad')->select('patologias.*');

        $totalRecords = $query->count();

        if ($search = $request->get('search')['value']) {
            $query->where(function ($q) use ($search) {
                $q->where('nombre', 'ILIKE', "%{$search}%")
                  ->orWhere('descripcion', 'ILIKE', "%{$search}%")
                  ->orWhereHas('especialidad', function ($q2) use ($search) {
                      $q2->where('nombre', 'ILIKE', "%{$search}%");
                  });
            });
        }

        $filteredRecords = $query->count();

        $orderColumn = $request->get('order')[0]['column'] ?? 0;
        $orderDir = $request->get('order')[0]['dir'] ?? 'asc';
        $columns = ['nombre', 'especialidad_id', 'descripcion'];
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
            $btnDelete = '<a href="'.route('patologias.destroy', $row->id).'" class="btn btn-xs btn-square btn-neutral text-danger-hover border-danger-hover" data-confirm-delete="true"><i class="bi bi-trash"></i></a>';
            $acciones = '<div class="hstack gap-2 justify-content-end">'.$btnShow.$btnEdit.$btnDelete.'</div>';

            $dataFormatted[] = [
                $row->nombre,
                $row->especialidad->nombre ?? 'N/A',
                $row->descripcion ?? '',
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

    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:255|unique:patologias,nombre',
            'especialidad_id' => 'required|exists:especialidades,id',
            'descripcion' => 'nullable|string',
        ]);

        $patologia = Patologia::create($request->only(['nombre', 'especialidad_id', 'descripcion']));

        Notification::send(User::all(), new NuevaPatologia($patologia));

        Alert::success('Patología creada exitosamente.');
        return redirect()->route('patologias.index');
    }

    public function edit($id)
    {
        return response()->json(Patologia::findOrFail($id));
    }

    public function update(Request $request, $id)
    {
        $patologia = Patologia::findOrFail($id);
        $request->validate([
            'nombre' => 'required|string|max:255|unique:patologias,nombre,' . $id,
            'especialidad_id' => 'required|exists:especialidades,id',
            'descripcion' => 'nullable|string',
        ]);

        $patologia->update($request->only(['nombre', 'especialidad_id', 'descripcion']));

        Alert::success('Patología actualizada exitosamente.');
        return redirect()->route('patologias.index');
    }

    public function destroy($id)
    {
        $patologia = Patologia::findOrFail($id);
        $patologia->delete();

        Alert::success('Patología eliminada exitosamente.');
        return redirect()->route('patologias.index');
    }

    public function show($id)
    {
        $patologia = Patologia::with('especialidad')->findOrFail($id);
        return response()->json($patologia);
    }
}
