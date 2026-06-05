<?php

namespace App\Http\Controllers;

use App\Models\Paciente;
use App\Models\Parroquia;
use Illuminate\Http\Request;
use RealRashid\SweetAlert\Facades\Alert;

class PacienteController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if ($request->ajax() && $request->has('draw')) {
            return $this->dataTableResponse($request);
        }

        $title = '¿Estas seguro de que deseas eliminar este paciente?';
        $text = 'Esta acción no se puede deshacer.';
        confirmDelete($title, $text);

        return view('paciente.listapacientes');
    }

    /**
     * DataTable server-side response.
     */
    private function dataTableResponse(Request $request)
    {
        $query = Paciente::with('parroquia.municipio.estado');

        $totalRecords = $query->count();

        if ($search = $request->get('search')['value']) {
            $query->where(function ($q) use ($search) {
                $q->where('nombre', 'ILIKE', "%{$search}%")
                  ->orWhere('apellido', 'ILIKE', "%{$search}%")
                  ->orWhere('cedula', 'ILIKE', "%{$search}%")
                  ->orWhere('rif', 'ILIKE', "%{$search}%")
                  ->orWhere('direccion', 'ILIKE', "%{$search}%");
            });
        }

        $filteredRecords = $query->count();

        $orderColumn = $request->get('order')[0]['column'] ?? 0;
        $orderDir = $request->get('order')[0]['dir'] ?? 'asc';
        $columns = ['nombre', 'apellido', 'cedula', 'rif', 'direccion'];
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
            $btnDelete = '<a href="'.route('paciente.destroy', $row->id).'" class="btn btn-xs btn-square btn-neutral text-danger-hover border-danger-hover" data-confirm-delete="true"><i class="bi bi-trash"></i></a>';
            $acciones = '<div class="hstack gap-2 justify-content-end">'.$btnShow.$btnEdit.$btnDelete.'</div>';

            $dataFormatted[] = [
                $row->nombre,
                $row->apellido,
                $row->cedula,
                $row->rif ?? '',
                $row->direccion ?? '',
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

    /**
     * Show the form for creating a new resource.
     */
    public function create() {}

    /**
     * Buscar paciente por cédula.
     */
    public function buscarPorCedula($cedula)
    {
        $paciente = Paciente::with(['parroquia.municipio.estado'])->where('cedula', $cedula)->first();

        if ($paciente) {
            return response()->json(['encontrado' => true, 'datos' => $paciente]);
        } else {
            return response()->json(['encontrado' => false]);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'apellido' => 'required|string|max:255',
            'cedula_tipo' => 'required|in:V,E',
            'cedula' => 'required|string|min:7|max:20|unique:pacientes,cedula',
            'rif' => 'required|string|max:20',
            'fecha_nacimiento' => 'required|date',
            'telefono' => 'required|string|min:7|max:15',
            'parroquia_id' => 'required|exists:parroquias,id',
            'direccion' => 'nullable|string|max:255',
            'sexo' => 'required|in:Masculino,Femenino',
        ]);

        Paciente::create([
            'cedula' => $request->cedula_tipo . '-' . $request->cedula,
            'rif' => 'J-' . $request->rif,
            'nombre' => $request->nombre,
            'apellido' => $request->apellido,
            'fecha_nacimiento' => $request->fecha_nacimiento,
            'telefono' => $request->telefono,
            'parroquia_id' => $request->parroquia_id,
            'direccion' => $request->direccion,
            'sexo' => $request->sexo,
        ]);

        Alert::success('Paciente creado exitosamente.');

        return redirect()->route('paciente.index');
    }

    /**
     * Display the specified resource.
     */
    public function show(int $id)
    {
        $pacienteToShow = Paciente::with('parroquia.municipio.estado')->findOrFail($id);
        return response()->json($pacienteToShow);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Paciente $paciente)
    {
        $pacienteToEdit = Paciente::findOrFail($paciente->id);
        return response()->json($pacienteToEdit);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, int $id)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'apellido' => 'required|string|max:255',
            'cedula_tipo' => 'required|in:V,E',
            'cedula' => 'required|string|min:7|max:20|unique:pacientes,cedula,' . $id,
            'rif' => 'required|string|max:20',
            'fecha_nacimiento' => 'required|date',
            'telefono' => 'required|string|min:7|max:15',
            'parroquia_id' => 'required|exists:parroquias,id',
            'direccion' => 'nullable|string|max:255',
            'sexo' => 'required|in:Masculino,Femenino',
        ]);

        $paciente = Paciente::findOrFail($id);
        $paciente->update([
            'cedula' => $request->cedula_tipo . '-' . $request->cedula,
            'rif' => 'J-' . $request->rif,
            'nombre' => $request->nombre,
            'apellido' => $request->apellido,
            'fecha_nacimiento' => $request->fecha_nacimiento,
            'telefono' => $request->telefono,
            'parroquia_id' => $request->parroquia_id,
            'direccion' => $request->direccion,
            'sexo' => $request->sexo,
        ]);

        Alert::success('Paciente actualizado exitosamente.');

        return redirect()->route('paciente.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(int $id)
    {
        $paciente = Paciente::findOrFail($id);
        $paciente->delete();

        Alert::success('Paciente eliminado exitosamente.');

        return redirect()->route('paciente.index');
    }
}
