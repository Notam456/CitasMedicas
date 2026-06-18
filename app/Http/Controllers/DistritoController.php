<?php

namespace App\Http\Controllers;

use App\Models\Distrito;
use App\Models\Municipio;
use Illuminate\Http\Request;
use RealRashid\SweetAlert\Facades\Alert;

class DistritoController extends Controller
{
    public function index()
    {
        confirmDelete('¿Eliminar distrito?', 'Esta acción no se puede deshacer.');
        return view('distritos.listaDistritos');
    }

        public function show($id)
    {
        $distrito = Distrito::with('municipios')->findOrFail($id);
        return response()->json([
            'id' => $distrito->id,
            'nombre' => $distrito->nombre,
            'municipios' => $distrito->municipios->pluck('nombre')
        ]);
    }

    public function getDistritosData(Request $request)
    {
        $query = Distrito::select(['id', 'nombre', 'created_at']);

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
        }

        $start = $request->get('start', 0);
        $length = $request->get('length', 10);
        $distritos = $query->skip($start)->take($length)->get();

        $data = [];
        foreach ($distritos as $distrito) {
            $actionBtn = '<div class="hstack gap-2 justify-content-end">';
            $actionBtn .= '<button type="button" data-id="'.$distrito->id.'" class="btn-show btn btn-xs btn-square btn-neutral"><i class="bi bi-eye"></i></button>';
            $actionBtn .= '<button type="button" data-id="'.$distrito->id.'" class="btn-edit btn btn-xs btn-square btn-neutral"><i class="bi bi-pencil"></i></button>';
            $actionBtn .= '<a href="'. route('distritos.destroy', $distrito->id) .'" class="btn btn-xs btn-square btn-neutral text-danger-hover border-danger-hover" data-confirm-delete="true"><i class="bi bi-trash"></i></a>';
            $actionBtn .= '</div>';

            $data[] = [
                'nombre' => $distrito->nombre,
                'action' => $actionBtn,
            ];
        }

        return response()->json([
            'draw' => intval($request->draw),
            'recordsTotal' => $totalRecords,
            'recordsFiltered' => $filteredRecords,
            'data' => $data,
        ]);
    }

    public function store(Request $request)
    {
        $request->validate(['nombre' => 'required|string|max:255|unique:distritos,nombre|regex:/^[a-zA-ZáéíóúÁÉÍÓÚñÑüÜ\s]+$/u']);
        Distrito::create($request->only('nombre'));
        Alert::success('Distrito creado exitosamente.');
        return redirect()->route('distritos.index');
    }

    public function edit($id)
    {
        return response()->json(Distrito::findOrFail($id));
    }

    public function update(Request $request, $id)
    {
        $distrito = Distrito::findOrFail($id);
        $request->validate(['nombre' => 'required|string|max:255|unique:distritos,nombre,' . $id . '|regex:/^[a-zA-ZáéíóúÁÉÍÓÚñÑüÜ\s]+$/u']);
        $distrito->update($request->only('nombre'));
        Alert::success('Distrito actualizado exitosamente.');
        return redirect()->route('distritos.index');
    }

    public function destroy($id)
    {
        Distrito::findOrFail($id)->delete();
        Alert::success('Distrito eliminado exitosamente.');
        return redirect()->route('distritos.index');
    }
}
