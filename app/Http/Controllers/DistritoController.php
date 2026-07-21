<?php

namespace App\Http\Controllers;

use App\Models\Distrito;
use App\Models\Municipio;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
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
        $query = Distrito::withCount('municipios')->select(['id', 'nombre', 'created_at']);

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
                'municipios_count' => $distrito->municipios_count,
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
        $request->validate([
            'nombre' => 'required|string|max:255|unique:distritos,nombre|regex:/^[a-zA-ZáéíóúÁÉÍÓÚñÑüÜ\s]+$/u',
            'municipios' => 'required|array|min:1',
            'municipios.*' => 'integer|exists:municipios,id',
        ]);

        $municipiosIds = $request->municipios;

        $ocupados = Municipio::whereIn('id', $municipiosIds)
            ->whereNotNull('distrito_id')
            ->pluck('id')
            ->toArray();

        if (!empty($ocupados)) {
            $nombresOcupados = Municipio::whereIn('id', $ocupados)->pluck('nombre')->implode(', ');
            Alert::error('Error', "Los siguientes municipios ya pertenecen a otro distrito: {$nombresOcupados}");
            return redirect()->back()->withInput();
        }

        DB::beginTransaction();
        try {
            $distrito = Distrito::create(['nombre' => $request->nombre]);

            Municipio::whereIn('id', $municipiosIds)->update(['distrito_id' => $distrito->id]);

            DB::commit();
            Alert::success('Distrito creado exitosamente.');
            return redirect()->route('distritos.index');
        } catch (\Exception $e) {
            DB::rollBack();
            Alert::error('Error', 'No se pudo crear el distrito. Intente de nuevo.');
            return redirect()->back()->withInput();
        }
    }

    public function edit($id)
    {
        $distrito = Distrito::with('municipios')->findOrFail($id);

        $municipiosActuales = $distrito->municipios->pluck('id')->toArray();

        $municipiosDisponibles = Municipio::whereNull('distrito_id')
            ->orWhere('distrito_id', $id)
            ->get(['id', 'nombre']);

        return response()->json([
            'id' => $distrito->id,
            'nombre' => $distrito->nombre,
            'municipios_actuales' => $municipiosActuales,
            'municipios_disponibles' => $municipiosDisponibles,
        ]);
    }

    public function update(Request $request, $id)
    {
        $distrito = Distrito::findOrFail($id);

        $request->validate([
            'nombre' => 'required|string|max:255|unique:distritos,nombre,' . $id . '|regex:/^[a-zA-ZáéíóúÁÉÍÓÚñÑüÜ\s]+$/u',
            'municipios' => 'required|array|min:1',
            'municipios.*' => 'integer|exists:municipios,id',
        ]);

        $municipiosIds = $request->municipios;

        $ocupados = Municipio::whereIn('id', $municipiosIds)
            ->whereNotNull('distrito_id')
            ->where('distrito_id', '!=', $id)
            ->pluck('id')
            ->toArray();

        if (!empty($ocupados)) {
            $nombresOcupados = Municipio::whereIn('id', $ocupados)->pluck('nombre')->implode(', ');
            Alert::error('Error', "Los siguientes municipios ya pertenecen a otro distrito: {$nombresOcupados}");
            return redirect()->back()->withInput();
        }

        DB::beginTransaction();
        try {
            $distrito->update(['nombre' => $request->nombre]);

            Municipio::where('distrito_id', $id)
                ->whereNotIn('id', $municipiosIds)
                ->update(['distrito_id' => null]);

            Municipio::whereIn('id', $municipiosIds)->update(['distrito_id' => $id]);

            DB::commit();
            Alert::success('Distrito actualizado exitosamente.');
            return redirect()->route('distritos.index');
        } catch (\Exception $e) {
            DB::rollBack();
            Alert::error('Error', 'No se pudo actualizar el distrito. Intente de nuevo.');
            return redirect()->back()->withInput();
        }
    }

    public function destroy($id)
    {
        DB::beginTransaction();
        try {
            Municipio::where('distrito_id', $id)->update(['distrito_id' => null]);

            Distrito::findOrFail($id)->delete();

            DB::commit();
            Alert::success('Distrito eliminado exitosamente.');
            return redirect()->route('distritos.index');
        } catch (\Exception $e) {
            DB::rollBack();
            Alert::error('Error', 'No se pudo eliminar el distrito. Intente de nuevo.');
            return redirect()->back();
        }
    }
}
