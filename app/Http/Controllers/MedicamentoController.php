<?php

namespace App\Http\Controllers;

use App\Models\Medicamento;
use Illuminate\Http\Request;

class MedicamentoController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax() && $request->has('draw')) {
            return $this->dataTableResponse($request);
        }

        $title = '¿Estas seguro de que deseas eliminar este medicamento?';
        $text = 'Esta acción no se puede deshacer.';
        confirmDelete($title, $text);

        return view('medicamentos.index');
    }

    private function dataTableResponse(Request $request)
    {
        $query = Medicamento::query();

        $totalRecords = $query->count();

        if ($search = $request->get('search')['value']) {
            $query->where(function ($q) use ($search) {
                $q->where('nombre', 'ILIKE', "%{$search}%")
                  ->orWhere('descripcion', 'ILIKE', "%{$search}%");
            });
        }

        $filteredRecords = $query->count();

        $orderColumn = $request->get('order')[0]['column'] ?? 0;
        $orderDir = $request->get('order')[0]['dir'] ?? 'asc';
        $columns = ['nombre', 'descripcion'];
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
            $btnDelete = '<a href="'.route('medicamentos.destroy', $row->id).'" class="btn btn-xs btn-square btn-neutral text-danger-hover border-danger-hover" data-confirm-delete="true"><i class="bi bi-trash"></i></a>';
            $acciones = '<div class="hstack gap-2 justify-content-end">'.$btnShow.$btnEdit.$btnDelete.'</div>';

            $dataFormatted[] = [
                $row->nombre,
                $row->descripcion,
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
        $medicamentoToShow = Medicamento::findOrFail($id);
        return response()->json($medicamentoToShow);
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:255|unique:medicamentos,nombre|regex:/^[a-zA-ZáéíóúÁÉÍÓÚñÑüÜ\s]+$/u',
            'descripcion' => 'nullable|string|max:500',
        ]);

        Medicamento::create($request->only('nombre', 'descripcion'));

        alert()->success('Medicamento creado exitosamente.');
        return redirect()->route('medicamentos.index');
    }

    public function edit(int $id)
    {
        $medicamentoToEdit = Medicamento::findOrFail($id);
        return response()->json($medicamentoToEdit);
    }

    public function update(Request $request, int $id)
    {
        $request->validate([
            'nombre' => 'required|string|max:255|unique:medicamentos,nombre,' . $id . '|regex:/^[a-zA-ZáéíóúÁÉÍÓÚñÑüÜ\s]+$/u',
            'descripcion' => 'nullable|string|max:500',
        ]);

        $medicamento = Medicamento::findOrFail($id);
        $medicamento->update($request->only('nombre', 'descripcion'));

        alert()->success('Medicamento actualizado exitosamente.');
        return redirect()->route('medicamentos.index');
    }

    public function destroy(int $id)
    {
        $medicamento = Medicamento::findOrFail($id);
        $medicamento->delete();

        alert()->success('Medicamento eliminado exitosamente.');
        return redirect()->route('medicamentos.index');
    }
}
