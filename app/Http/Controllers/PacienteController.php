<?php

namespace App\Http\Controllers;

use App\Models\Paciente;
use App\Models\Parroquia;
use App\Models\User;
use App\Notifications\NuevoPaciente;
use App\Notifications\PacienteModificado;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;
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
        $query = Paciente::with('parroquia.municipio.estado', 'expediente');

        $totalRecords = $query->count();

        if ($search = $request->get('search')['value']) {
            $query->where(function ($q) use ($search) {
                $q->where('nombre', 'ILIKE', "%{$search}%")
                    ->orWhere('apellido', 'ILIKE', "%{$search}%")
                    ->orWhere('cedula', 'ILIKE', "%{$search}%")
                    ->orWhere('direccion', 'ILIKE', "%{$search}%");
            });
        }

        $filteredRecords = $query->count();

        $orderColumn = $request->get('order')[0]['column'] ?? 0;
        $orderDir = $request->get('order')[0]['dir'] ?? 'asc';
        $columns = ['nombre', 'apellido', 'cedula', 'direccion'];
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
            $btnDelete = '<a href="' . route('paciente.destroy', $row->id) . '" class="btn btn-xs btn-square btn-neutral text-danger-hover border-danger-hover" data-confirm-delete="true"><i class="bi bi-trash"></i></a>';
            $acciones = '<div class="hstack gap-2 justify-content-end">' . $btnShow . $btnEdit . $btnDelete . '</div>';

            $dataFormatted[] = [
                $row->nombre,
                $row->apellido,
                $row->cedula,
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
     * Buscar paciente por cédula.
     */
    public function buscarPorCedula($cedula)
    {
        $paciente = Paciente::with(['parroquia.municipio.estado', 'expediente'])->where('cedula', $cedula)->first();

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
        $request->merge([
            'nombre' => mb_convert_case(trim($request->nombre), MB_CASE_TITLE, 'UTF-8'),
            'apellido' => mb_convert_case(trim($request->apellido), MB_CASE_TITLE, 'UTF-8'),
        ]);

        if ($request->has(['cedula_tipo', 'cedula'])) {
            $request->merge([
                'cedula_completa' => $request->cedula_tipo . '-' . $request->cedula
            ]);
        }
        $request->validate([
            'nombre' => 'required|string|max:255|regex:/^[a-zA-ZáéíóúÁÉÍÓÚñÑüÜ\s]+$/u',
            'apellido' => 'required|string|max:255|regex:/^[a-zA-ZáéíóúÁÉÍÓÚñÑüÜ\s]+$/u',
            'cedula_tipo' => 'required|in:V,E',
            'cedula' => 'required|string|min:7|max:20|regex:/^[0-9]+$/',
            'cedula_completa' => 'required|string|unique:pacientes,cedula',
            'rif' => 'nullable|string|max:20',
            'fecha_nacimiento' => 'required|date',
            'telefono' => 'required|string|min:7|max:15|regex:/^[\d\-\(\)\s\+]+$/',
            'parroquia_id' => 'required|exists:parroquias,id',
            'direccion' => 'nullable|string|max:255',
            'sexo' => 'required|in:Masculino,Femenino',
        ]);

        $paciente = Paciente::create([
            'cedula' => $request->cedula_completa,
            'rif' => $request->rif ? 'J-' . $request->rif : '',
            'nombre' => $request->nombre,
            'apellido' => $request->apellido,
            'fecha_nacimiento' => $request->fecha_nacimiento,
            'telefono' => $request->telefono,
            'parroquia_id' => $request->parroquia_id,
            'direccion' => $request->direccion,
            'sexo' => $request->sexo,
        ]);

        Notification::send(User::all(), new NuevoPaciente($paciente));

        Alert::success('Paciente creado exitosamente.');

        return redirect()->route('paciente.index');
    }

    /**
     * Display the specified resource.
     */
    public function show(int $id)
    {
        $pacienteToShow = Paciente::with('parroquia.municipio.estado', 'expediente')->findOrFail($id);
        return response()->json($pacienteToShow);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Paciente $paciente)
    {
        $paciente->load('parroquia.municipio.estado', 'expediente');
        return response()->json($paciente);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, int $id)
    {
        $request->merge([
            'nombre' => mb_convert_case(trim($request->nombre), MB_CASE_TITLE, 'UTF-8'),
            'apellido' => mb_convert_case(trim($request->apellido), MB_CASE_TITLE, 'UTF-8'),
        ]);

        if ($request->has(['cedula_tipo', 'cedula'])) {
            $request->merge([
                'cedula_completa' => $request->cedula_tipo . '-' . $request->cedula
            ]);
        }
        $request->validate([
            'nombre' => 'required|string|max:255|regex:/^[a-zA-ZáéíóúÁÉÍÓÚñÑüÜ\s]+$/u',
            'apellido' => 'required|string|max:255|regex:/^[a-zA-ZáéíóúÁÉÍÓÚñÑüÜ\s]+$/u',
            'cedula_tipo' => 'required|in:V,E',
            'cedula_completa' => 'required|string|unique:pacientes,cedula,' . $id,
            'cedula' => 'required|string|min:7|max:20|regex:/^[0-9]+$/',
            'rif' => 'nullable|string|max:20',
            'fecha_nacimiento' => 'required|date',
            'telefono' => 'required|string|min:7|max:15|regex:/^[\d\-\(\)\s\+]+$/',
            'parroquia_id' => 'required|exists:parroquias,id',
            'direccion' => 'nullable|string|max:255',
            'sexo' => 'required|in:Masculino,Femenino',
        ]);

        $paciente = Paciente::findOrFail($id);
        $paciente->update([
            'cedula' => $request->cedula_completa,
            'rif' => $request->rif ? 'J-' . $request->rif : '',
            'nombre' => $request->nombre,
            'apellido' => $request->apellido,
            'fecha_nacimiento' => $request->fecha_nacimiento,
            'telefono' => $request->telefono,
            'parroquia_id' => $request->parroquia_id,
            'direccion' => $request->direccion,
            'sexo' => $request->sexo,
        ]);

        if (!auth()->user()->hasRole('administrador')) {
            $admins = User::role('administrador')->get();
            Notification::send($admins, new PacienteModificado($paciente, auth()->user()));
        }

        Alert::success('Paciente actualizado exitosamente.');

        return redirect()->route('paciente.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(int $id)
    {
        $paciente = Paciente::withCount('citas')->findOrFail($id);

        if ($paciente->citas_count > 0) {
            Alert::error('No se puede eliminar', 'Este paciente tiene citas médicas registradas en el sistema.');
            return redirect()->route('paciente.index');
        }

        $paciente->delete();
        Alert::success('Paciente eliminado exitosamente.');
        return redirect()->route('paciente.index');
    }
}
