<?php

namespace App\Http\Controllers;

use App\Models\Municipio;
use App\Models\Estado;
use App\Models\Distrito;
use Illuminate\Http\Request;
use RealRashid\SweetAlert\Facades\Alert;
use Illuminate\Validation\Rule;

class MunicipioController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax() && $request->has('draw')) {
            return $this->dataTableResponse($request);
        }

        $estados = Estado::all();
        $distritos = Distrito::all();
        confirmDelete('¿Eliminar municipio?', 'Esta acción no se puede deshacer.');
        return view('municipios.listaMunicipios', compact('estados', 'distritos'));
    }

    private function dataTableResponse(Request $request)
    {
        $query = Municipio::with('estado', 'distrito');

        $totalRecords = $query->count();

        if ($search = $request->get('search')['value']) {
            $query->where(function ($q) use ($search) {
                $q->where('nombre', 'ILIKE', "%{$search}%")
                  ->orWhereHas('estado', function ($q2) use ($search) {
                      $q2->where('nombre', 'ILIKE', "%{$search}%");
                  })
                  ->orWhereHas('distrito', function ($q3) use ($search) {
                      $q3->where('nombre', 'ILIKE', "%{$search}%");
                  });
            });
        }

        $filteredRecords = $query->count();

        $orderColumn = $request->get('order')[0]['column'] ?? 0;
        $orderDir = $request->get('order')[0]['dir'] ?? 'asc';
        $columns = ['nombre', 'estado_id', 'distrito_id'];
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
            $btnDelete = '<a href="'.route('municipios.destroy', $row->id).'" class="btn btn-xs btn-square btn-neutral text-danger-hover border-danger-hover" data-confirm-delete="true"><i class="bi bi-trash"></i></a>';
            $acciones = '<div class="hstack gap-2 justify-content-end">'.$btnShow.$btnEdit.$btnDelete.'</div>';

            $dataFormatted[] = [
                $row->nombre,
                $row->estado->nombre ?? 'N/A',
                $row->distrito->nombre ?? 'Sin distrito',
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
        $municipio = Municipio::with('estado', 'distrito')->findOrFail($id);
        return response()->json([
            'id' => $municipio->id,
            'nombre' => $municipio->nombre,
            'estado' => $municipio->estado->nombre ?? null,
            'distrito' => $municipio->distrito->nombre ?? 'Sin distrito'
        ]);
    }

    public function store(Request $request)
    {
        $request->merge(['nombre' => mb_convert_case(trim($request->nombre), MB_CASE_TITLE, 'UTF-8')]);
        $request->validate([
            'nombre' => [
                'required', 'string', 'max:255',
                'regex:/^[a-zA-ZáéíóúÁÉÍÓÚñÑüÜ\s]+$/u',
                Rule::unique('municipios')->where(function ($query) use ($request) {
                    return $query->where('estado_id', $request->estado_id);
                })
            ],
            'estado_id' => 'required|exists:estados,id',
            'distrito_id' => 'nullable|exists:distritos,id',
        ]);

        Municipio::create($request->only(['nombre', 'estado_id', 'distrito_id']));

        Alert::success('Municipio creado exitosamente.');
        return redirect()->route('municipios.index');
    }

    public function edit($id)
    {
        $municipioToEdit = Municipio::findOrFail($id);
        return response()->json($municipioToEdit);
    }

    public function update(Request $request, $id)
    {
        $municipio = Municipio::findOrFail($id);
        $request->merge(['nombre' => mb_convert_case(trim($request->nombre), MB_CASE_TITLE, 'UTF-8')]);
        $request->validate([
            'nombre' => [
                'required', 'string', 'max:255',
                'regex:/^[a-zA-ZáéíóúÁÉÍÓÚñÑüÜ\s]+$/u',
                Rule::unique('municipios')->where(function ($query) use ($request, $id) {
                    return $query->where('estado_id', $request->estado_id)->where('id', '!=', $id);
                })
            ],
            'estado_id' => 'required|exists:estados,id',
            'distrito_id' => 'nullable|exists:distritos,id',
        ]);

        $municipio->update($request->only(['nombre', 'estado_id', 'distrito_id']));

        Alert::success('Municipio actualizado exitosamente.');
        return redirect()->route('municipios.index');
    }

    public function destroy($id)
    {
        $municipio = Municipio::findOrFail($id);
        $municipio->delete();
        Alert::success('Municipio eliminado exitosamente.');
        return redirect()->route('municipios.index');
    }

    public function getMunicipios($estado_id)
    {
        $municipios = Municipio::where('estado_id', $estado_id)->get();
        
        return response()->json($municipios);
    }
}