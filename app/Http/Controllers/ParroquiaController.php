<?php

namespace App\Http\Controllers;

use App\Models\Parroquia;
use App\Models\Municipio;
use App\Models\Estado;
use Illuminate\Http\Request;
use RealRashid\SweetAlert\Facades\Alert;
use Illuminate\Validation\Rule;

class ParroquiaController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax() && $request->has('draw')) {
            return $this->dataTableResponse($request);
        }

        $estados = Estado::all();
        confirmDelete('¿Eliminar parroquia?', 'Esta acción no se puede deshacer.');
        return view('parroquias.listaParroquias', compact('estados'));
    }

    private function dataTableResponse(Request $request)
    {
        $query = Parroquia::with('municipio.estado');

        $totalRecords = $query->count();

        if ($search = $request->get('search')['value']) {
            $query->where(function ($q) use ($search) {
                $q->where('nombre', 'ILIKE', "%{$search}%")
                    ->orWhereHas('municipio', function ($q2) use ($search) {
                        $q2->where('nombre', 'ILIKE', "%{$search}%")
                            ->orWhereHas('estado', function ($q3) use ($search) {
                                $q3->where('nombre', 'ILIKE', "%{$search}%");
                            });
                    });
            });
        }

        $filteredRecords = $query->count();

        $orderColumn = $request->get('order')[0]['column'] ?? 0;
        $orderDir = $request->get('order')[0]['dir'] ?? 'asc';
        $columns = ['nombre', 'municipio_id'];
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
            $btnDelete = '<a href="' . route('parroquias.destroy', $row->id) . '" class="btn btn-xs btn-square btn-neutral text-danger-hover border-danger-hover" data-confirm-delete="true"><i class="bi bi-trash"></i></a>';
            $acciones = '<div class="hstack gap-2 justify-content-end">' . $btnShow . $btnEdit . $btnDelete . '</div>';

            $dataFormatted[] = [
                $row->nombre,
                $row->municipio->nombre ?? 'N/A',
                $row->municipio->estado->nombre ?? 'N/A',
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
        $parroquiaToShow = Parroquia::with('municipio.estado')->findOrFail($id);
        return response()->json($parroquiaToShow);
    }

    public function getMunicipiosPorEstado($estado_id)
    {
        $municipios = Municipio::where('estado_id', $estado_id)->get(['id', 'nombre']);
        return response()->json($municipios);
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre' => [
                'required',
                'string',
                'max:255',
                Rule::unique('parroquias')->where(function ($query) use ($request) {
                    return $query->where('municipio_id', $request->municipio_id);
                })
            ],
            'municipio_id' => 'required|exists:municipios,id',
        ]);

        Parroquia::create($request->only(['nombre', 'municipio_id']));

        Alert::success('Parroquia creada exitosamente.');
        return redirect()->route('parroquias.index');
    }

    public function edit($id)
    {
        $parroquiaToEdit = Parroquia::with('municipio.estado')->findOrFail($id);
        return response()->json($parroquiaToEdit);
    }

    public function update(Request $request, $id)
    {
        $parroquia = Parroquia::findOrFail($id);
        $request->validate([
            'nombre' => [
                'required',
                'string',
                'max:255',
                Rule::unique('parroquias')->where(function ($query) use ($request, $id) {
                    return $query->where('municipio_id', $request->municipio_id)->where('id', '!=', $id);
                })
            ],
            'municipio_id' => 'required|exists:municipios,id',
        ]);

        $parroquia->update($request->only(['nombre', 'municipio_id']));

        Alert::success('Parroquia actualizada exitosamente.');
        return redirect()->route('parroquias.index');
    }

    public function destroy($id)
    {
        $parroquia = Parroquia::findOrFail($id);
        $parroquia->delete();

        Alert::success('Parroquia eliminada exitosamente.');
        return redirect()->route('parroquias.index');
    }

    public function getParroquias($municipio_id)
    {
        $parroquias = Parroquia::where('municipio_id', $municipio_id)->get();

        return response()->json($parroquias);
    }
}