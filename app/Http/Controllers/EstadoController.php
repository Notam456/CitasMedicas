<?php

namespace App\Http\Controllers;

use App\Models\Estado;
use Illuminate\Http\Request;
use RealRashid\SweetAlert\Facades\Alert;

class EstadoController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax() && $request->has('draw')) {
            return $this->dataTableResponse($request);
        }

        $title = '¿Estás seguro de eliminar este estado?';
        $text = 'Esta acción no se puede deshacer.';
        confirmDelete($title, $text);
        return view('estados.listaEstados');
    }

    private function dataTableResponse(Request $request)
    {
        $query = Estado::query();

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
            $btnDelete = '<a href="'.route('estados.destroy', $row->id).'" class="btn btn-xs btn-square btn-neutral text-danger-hover border-danger-hover" data-confirm-delete="true"><i class="bi bi-trash"></i></a>';
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

    public function show($id)
    {
        $estadoToShow = Estado::findOrFail($id);
        return response()->json($estadoToShow);
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:255|unique:estados,nombre|regex:/^[a-zA-ZáéíóúÁÉÍÓÚñÑüÜ\s]+$/u',
        ]);

        Estado::create($request->only('nombre'));

        Alert::success('Estado creado exitosamente.');
        return redirect()->route('estados.index');
    }

    public function edit($id)
    {
        $estadoToEdit = Estado::findOrFail($id);
        return response()->json($estadoToEdit);
    }

    public function update(Request $request, $id)
    {
        $estado = Estado::findOrFail($id);
        $request->validate([
            'nombre' => 'required|string|max:255|unique:estados,nombre,' . $id . '|regex:/^[a-zA-ZáéíóúÁÉÍÓÚñÑüÜ\s]+$/u',
        ]);

        $estado->update($request->only('nombre'));

        Alert::success('Estado actualizado exitosamente.');
        return redirect()->route('estados.index');
    }

    public function destroy($id)
    {
        $estado = Estado::findOrFail($id);
        $estado->delete();

        Alert::success('Estado eliminado exitosamente.');
        return redirect()->route('estados.index');
    }

    public function getEstados()
    {
        $estados = Estado::orderBy('nombre', 'asc')->get(['id', 'nombre']);
        return response()->json($estados, 200);
    }
}
