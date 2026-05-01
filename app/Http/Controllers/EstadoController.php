<?php

namespace App\Http\Controllers;

use App\Models\Estado;
use Illuminate\Http\Request;
use RealRashid\SweetAlert\Facades\Alert;
use Yajra\DataTables\Facades\DataTables;

class EstadoController extends Controller
{
    public function index()
    {
        $title = '¿Estás seguro de eliminar este estado?';
        $text = 'Esta acción no se puede deshacer.';
        confirmDelete($title, $text);
        return view('estados.listaEstados');
    }

    public function getEstadosData(Request $request)
    {
        $estados = Estado::select(['id', 'nombre']);
        return DataTables::of($estados)
            ->addColumn('action', function($row) {
                $actionBtn = '<div class="hstack gap-2 justify-content-end">';
                $actionBtn .= '<a href="'. route('estados.show', $row->id) .'" class="btn btn-xs btn-square btn-neutral"><i class="bi bi-eye"></i></a>';
                $actionBtn .= '<a href="'. route('estados.edit', $row->id) .'" class="btn btn-xs btn-square btn-neutral"><i class="bi bi-pencil"></i></a>';
                $actionBtn .= '<a href="'. route('estados.destroy', $row->id) .'" class="btn btn-xs btn-square btn-neutral text-danger-hover border-danger-hover" data-confirm-delete="true"><i class="bi bi-trash"></i></a>';
                $actionBtn .= '</div>';
                return $actionBtn;
            })
            ->rawColumns(['action'])
            ->make(true);
    }

    public function show($id)
    {
        $estadoToShow = Estado::findOrFail($id);
        return view('estados.listaEstados', compact('estadoToShow'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:255|unique:estados,nombre',
        ]);

        Estado::create($request->only('nombre'));

        Alert::success('Estado creado exitosamente.');
        return redirect()->route('estados.index');
    }

    public function edit($id)
    {
        $estadoToEdit = Estado::findOrFail($id);
        return view('estados.listaEstados', compact('estadoToEdit'));
    }

    public function update(Request $request, $id)
    {
        $estado = Estado::findOrFail($id);
        $request->validate([
            'nombre' => 'required|string|max:255|unique:estados,nombre,' . $id,
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
}
