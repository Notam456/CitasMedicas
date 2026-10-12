<?php

namespace App\Http\Controllers;

use App\Models\Paciente;
use App\Models\HistoricoNumero;
use App\Models\Parroquia;
use App\Models\User;
use App\Notifications\NuevoPaciente;
use App\Notifications\PacienteModificado;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
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
            $acciones = '<div class="hstack gap-2 justify-content-end">' . $btnShow . $btnEdit . $btnDelete;

            $puedeLiberar = auth()->user()?->can('Liberar Historia');
            if ($puedeLiberar && $row->expediente && $row->expediente->numero_expediente) {
                $acciones .= '<button type="button" data-id="' . $row->id . '" data-numero="' . e($row->expediente->numero_expediente) . '" class="btn-liberar btn btn-xs btn-square btn-neutral text-warning-hover border-warning-hover" title="Liberar número de historia"><i class="bi bi-file-earmark-break"></i></button>';
            }
            $acciones .= '</div>';

            $inactivo = $row->estado === 'inactivo';
            $nombreCol = $row->nombre . ($inactivo ? ' <span class="badge bg-secondary" title="Paciente inactivo">Inactivo</span>' : '');
            $numeroCol = $inactivo
                ? '<span class="text-muted">Liberado</span>'
                : ($row->expediente->numero_expediente ?? 'Sin asignar');

            $dataFormatted[] = [
                $nombreCol,
                $row->apellido,
                $row->cedula,
                $row->direccion ?? '',
                $numeroCol,
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
     * Buscar paciente por cédula o número de historia.
     */
    public function buscarPaciente(Request $request)
    {
        $q = mb_strtoupper(trim((string) $request->get('q', '')));

        if ($q === '') {
            return response()->json(['encontrado' => false]);
        }

        $digits = preg_replace('/\D/', '', $q);

        // Desambiguación cédula vs. N° de historia:
        // - Cédula: prefijo V/E o 7+ dígitos.
        // - Historia: formato XX-XX-XX o exactamente 6 dígitos.
        $tipo = 'invalido';
        $valor = null;
        $buscarCedula = null;
        $buscarHistoria = null;

        if (preg_match('/^([VE])-?(\d+)$/', $q, $m)) {
            $tipo = 'cedula';
            $valor = $m[1] . '-' . $m[2];
            $buscarCedula = $m[2];
        } elseif (preg_match('/^\d{2}-\d{2}-\d{2}$/', $q)) {
            $tipo = 'historia';
            $valor = $q;
            $buscarHistoria = $q;
        } elseif (preg_match('/^\d+$/', $q)) {
            if (strlen($digits) >= 7) {
                $tipo = 'cedula';
                $valor = $digits;
                $buscarCedula = $digits;
            } elseif (strlen($digits) === 6) {
                $tipo = 'historia';
                $valor = implode('-', str_split($digits, 2));
                $buscarHistoria = $valor;
            } else {
                // Número incompleto: se intenta buscar por ambos lados.
                $tipo = 'incompleto';
                $buscarCedula = $digits;
                $buscarHistoria = $digits;
            }
        } else {
            $tipo = 'historia';
            $valor = $q;
            $buscarHistoria = $q;
        }

        $consulta = Paciente::with(['parroquia.municipio.estado', 'expediente']);

        $consulta->where(function ($query) use ($buscarCedula, $buscarHistoria) {
            if ($buscarCedula !== null && $buscarCedula !== '') {
                $query->where('cedula', 'LIKE', '%-' . $buscarCedula);
            }

            if ($buscarHistoria !== null && $buscarHistoria !== '') {
                $metodo = ($buscarCedula !== null && $buscarCedula !== '') ? 'orWhereHas' : 'whereHas';
                $query->{$metodo}('expediente', function ($historia) use ($buscarHistoria) {
                    $historia->where('numero_expediente', $buscarHistoria);
                });
            }
        });

        $paciente = $consulta->first();

        if ($paciente) {
            return response()->json(['encontrado' => true, 'datos' => $paciente]);
        }

        // ¿El número de historia existió y fue liberado?
        if ($buscarHistoria !== null && $buscarHistoria !== '') {
            $historico = HistoricoNumero::with('paciente')
                ->where('numero_expediente', $buscarHistoria)
                ->where('vigente', false)
                ->latest('fecha_liberacion')
                ->first();

            if ($historico && $historico->paciente) {
                return response()->json([
                    'encontrado' => false,
                    'liberado' => true,
                    'tipo' => 'historia',
                    'valor' => $buscarHistoria,
                    'numero' => $buscarHistoria,
                    'paciente' => $historico->paciente->nombre . ' ' . $historico->paciente->apellido,
                    'motivo' => $historico->motivo,
                    'fecha_liberacion' => optional($historico->fecha_liberacion)->format('d/m/Y'),
                ]);
            }
        }

        return response()->json([
            'encontrado' => false,
            'tipo' => $tipo,
            'valor' => $valor,
        ]);
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
        $pacienteToShow = Paciente::with('parroquia.municipio.estado', 'expediente', 'historicoNumeros')->findOrFail($id);
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

    /**
     * Liberar el número de historia de un paciente, dejando el número libre
     * para otro paciente. El paciente queda marcado como inactivo con motivo.
     */
    public function liberarNumero(Request $request, Paciente $paciente)
    {
        $motivos = ['fallecido', 'sin_retorno', 'trasladado'];

        $request->validate([
            'motivo' => 'required|in:' . implode(',', $motivos),
        ]);

        $esAjax = $request->expectsJson() || $request->ajax();

        if ($paciente->estado === 'inactivo') {
            return $this->respuestaLiberacion(
                $esAjax,
                'Paciente inactivo',
                'Este paciente ya fue dado de baja.',
                409
            );
        }

        if (!$paciente->expediente || !$paciente->expediente->numero_expediente) {
            return $this->respuestaLiberacion(
                $esAjax,
                'Sin número de historia',
                'El paciente no tiene un número de historia asignado.',
                422
            );
        }

        if ($paciente->citas()->agendadas()->exists()) {
            return $this->respuestaLiberacion(
                $esAjax,
                'No se puede liberar',
                'El paciente tiene citas programadas pendientes de atender.',
                409
            );
        }

        return DB::transaction(function () use ($paciente, $request, $esAjax) {
            $numero = $paciente->expediente->numero_expediente;
            HistoricoNumero::liberar($paciente, $numero, $request->motivo);

            $paciente->expediente->update(['numero_expediente' => null]);

            $paciente->update([
                'estado' => 'inactivo',
                'estado_motivo' => $request->motivo,
                'fecha_baja' => now()->toDateString(),
            ]);

            if ($esAjax) {
                return response()->json([
                    'success' => true,
                    'message' => 'El número de historia quedó libre para otro paciente.',
                ]);
            }

            Alert::success('Número liberado', 'El número de historia quedó libre para otro paciente.');
            return redirect()->route('paciente.index');
        });
    }

    /**
     * Uniforma la respuesta de liberación según el tipo de petición:
     * JSON (AJAX) o redirección clásica con alerta.
     */
    private function respuestaLiberacion(bool $esAjax, string $titulo, string $mensaje, int $status)
    {
        if ($esAjax) {
            return response()->json(['success' => false, 'message' => $mensaje], $status);
        }

        Alert::error($titulo, $mensaje);
        return redirect()->route('paciente.index');
    }
}
