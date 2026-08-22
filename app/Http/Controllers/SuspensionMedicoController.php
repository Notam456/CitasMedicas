<?php

namespace App\Http\Controllers;

use App\Models\Calendario;
use App\Models\Cita;
use App\Models\Medico;
use App\Models\SuspensionMedico;
use App\Models\User;
use App\Notifications\CitaCancelada;
use Carbon\Carbon;
use App\Http\Requests\StoreSuspensionRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;
use RealRashid\SweetAlert\Facades\Alert;

class SuspensionMedicoController extends Controller
{
    public function index(Request $request)
    {
        $especialidades = \App\Models\Especialidad::all();

        if ($request->ajax() && $request->has('draw')) {
            return $this->dataTableResponse($request);
        }

        return view('medicos.inactivos', compact('especialidades'));
    }

    private function dataTableResponse(Request $request)
    {
        $query = SuspensionMedico::with(['medico.especialidad', 'suplente'])->select('suspensiones_medicos.*');

        $totalRecords = $query->count();

        if ($search = $request->get('search')['value']) {
            $query->where(function ($q) use ($search) {
                $q->whereHas('medico', function ($q2) use ($search) {
                    $q2->where('nombre', 'ILIKE', "%{$search}%")
                        ->orWhere('apellido', 'ILIKE', "%{$search}%")
                        ->orWhereHas('especialidad', function ($q3) use ($search) {
                            $q3->where('nombre', 'ILIKE', "%{$search}%");
                        });
                })
                ->orWhereHas('suplente', function ($q2) use ($search) {
                    $q2->where('nombre', 'ILIKE', "%{$search}%")
                        ->orWhere('apellido', 'ILIKE', "%{$search}%");
                })
                ->orWhere('motivo', 'ILIKE', "%{$search}%");
            });
        }

        $filteredRecords = $query->count();

        $orderColumn = $request->get('order')[0]['column'] ?? 0;
        $orderDir = $request->get('order')[0]['dir'] ?? 'desc';
        $columns = ['fecha_inicio', 'fecha_fin', 'medico_id', 'suplente_id', 'motivo'];
        if (isset($columns[$orderColumn])) {
            $query->orderBy($columns[$orderColumn], $orderDir);
        } else {
            $query->orderBy('fecha_inicio', 'desc');
        }

        $start = $request->get('start', 0);
        $length = $request->get('length', 10);
        $data = $query->skip($start)->take($length)->get();

        $dataFormatted = [];
        foreach ($data as $row) {
            $btnReactivar = '<button type="button" data-id="' . $row->id . '" class="btn-reactivar btn btn-xs btn-neutral text-success-hover border-success-hover"><i class="bi bi-person-check-fill me-1"></i> Reactivar</button>';
            $acciones = '<div class="hstack gap-2 justify-content-end">' . $btnReactivar . '</div>';

            $medicoNombre = $row->medico ? ($row->medico->nombre . ' ' . $row->medico->apellido) : 'N/A';
            $especialidadNombre = ($row->medico && $row->medico->especialidad) ? $row->medico->especialidad->nombre : 'N/A';
            $suplenteNombre = $row->suplente ? ($row->suplente->nombre . ' ' . $row->suplente->apellido) : '<span class="text-muted">Ninguno (Citas canceladas)</span>';

            $dataFormatted[] = [
                $medicoNombre,
                $especialidadNombre,
                Carbon::parse($row->fecha_inicio)->format('d/m/Y'),
                Carbon::parse($row->fecha_fin)->format('d/m/Y'),
                $suplenteNombre,
                $row->motivo ?? '<span class="text-muted">—</span>',
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

    public function store(StoreSuspensionRequest $request)
    {
        $medico_id = $request->medico_id;
        $fecha_inicio = $request->fecha_inicio;
        $fecha_fin = $request->fecha_fin;
        $suplente_id = $request->suplente_id;

        // Validar que el suplente (si se selecciona) no esté suspendido en el mismo rango de fechas
        if ($suplente_id) {
            if ($suplente_id == $medico_id) {
                return response()->json(['error' => true, 'message' => 'El médico suplente no puede ser el mismo médico suspendido.'], 422);
            }

            $suplenteSuspendido = SuspensionMedico::where('medico_id', $suplente_id)
                ->where(function($q) use ($fecha_inicio, $fecha_fin) {
                    $q->where('fecha_inicio', '<=', $fecha_fin)
                      ->where('fecha_fin', '>=', $fecha_inicio);
                })
                ->exists();

            if ($suplenteSuspendido) {
                return response()->json(['error' => true, 'message' => 'El médico suplente seleccionado ya se encuentra suspendido en ese rango de fechas.'], 422);
            }
        }

        // Validar que el médico a suspender no tenga ya una suspensión que se solape
        $overlapping = SuspensionMedico::where('medico_id', $medico_id)
            ->where(function($q) use ($fecha_inicio, $fecha_fin) {
                $q->where('fecha_inicio', '<=', $fecha_fin)
                  ->where('fecha_fin', '>=', $fecha_inicio);
            })
            ->exists();

        if ($overlapping) {
            return response()->json(['error' => true, 'message' => 'Este médico ya tiene una suspensión registrada que se solapa con el rango de fechas seleccionado.'], 422);
        }

        DB::beginTransaction();
        try {
            $suspension = SuspensionMedico::create([
                'medico_id' => $medico_id,
                'fecha_inicio' => $fecha_inicio,
                'fecha_fin' => $fecha_fin,
                'suplente_id' => $suplente_id,
                'motivo' => $request->motivo,
            ]);

            // Obtener citas agendadas de este médico en el rango de fechas
            $citas = Cita::whereHas('calendario', function($q) use ($medico_id) {
                $q->where('medico_id', $medico_id);
            })
            ->whereBetween('fecha_cita', [$fecha_inicio, $fecha_fin])
            ->where('estado', 'Agendada')
            ->get();

            if ($suplente_id) {
                // Reasignar citas al suplente
                $medico = Medico::findOrFail($medico_id);
                $especialidad_id = $medico->especialidad_id;

                foreach ($citas as $cita) {
                    $originalCalendario = $cita->calendario;

                    // Buscar o crear planificación para el suplente en esa fecha
                    $newCalendario = Calendario::firstOrCreate(
                        [
                            'medico_id' => $suplente_id,
                            'fecha' => $cita->fecha_cita,
                            'especialidad_id' => $especialidad_id,
                        ],
                        [
                            'hora_inicio' => $originalCalendario->hora_inicio,
                            'hora_fin' => $originalCalendario->hora_fin,
                            'cupos_primera_vez' => $originalCalendario->cupos_primera_vez,
                            'cupos_sucesivos' => $originalCalendario->cupos_sucesivos,
                        ]
                    );

                    $cita->update([
                        'calendario_id' => $newCalendario->id,
                        'tipo_paciente' => 'orden_medica', // Cambiar tipo a Orden Médica
                    ]);
                }
            } else {
                // Cancelar citas y notificar a administradores
                $admins = User::role('administrador')->get();
                foreach ($citas as $cita) {
                    $cita->update(['estado' => 'Cancelada']);
                    $cita->cancelacion()->create([
                        'motivo' => 'ausencia_medico',
                        'cancelada_por' => auth()->id(),
                        'observacion' => $request->motivo ?: 'Médico suspendido sin suplente.',
                        'fecha_cancelacion' => now(),
                    ]);
                    Notification::send($admins, new CitaCancelada($cita, auth()->user()));
                }
            }

            DB::commit();
            return response()->json(['success' => true, 'message' => 'Médico suspendido exitosamente.']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => true, 'message' => 'Error al registrar la suspensión: ' . $e->getMessage()], 500);
        }
    }

    public function destroy($id)
    {
        DB::beginTransaction();
        try {
            $suspension = SuspensionMedico::findOrFail($id);
            $suspension->delete();

            DB::commit();
            return response()->json(['success' => true, 'message' => 'Médico reactivado exitosamente.']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => true, 'message' => 'Error al reactivar el médico: ' . $e->getMessage()], 500);
        }
    }

    public function getSuplentesDisponibles(Request $request, $medico_id)
    {
        $request->validate([
            'fecha_inicio' => 'required|date',
            'fecha_fin' => 'required|date',
        ]);

        $fecha_inicio = $request->fecha_inicio;
        $fecha_fin = $request->fecha_fin;

        $medico = Medico::findOrFail($medico_id);
        $especialidad_id = $medico->especialidad_id;

        // Obtener suplentes de la misma especialidad que no estén suspendidos en ese rango
        $suplentes = Medico::where('especialidad_id', $especialidad_id)
            ->where('id', '!=', $medico_id)
            ->whereDoesntHave('suspensiones', function($q) use ($fecha_inicio, $fecha_fin) {
                $q->where('fecha_inicio', '<=', $fecha_fin)
                  ->where('fecha_fin', '>=', $fecha_inicio);
            })
            ->get();

        return response()->json($suplentes);
    }

    public function getActiveSuspensions($medico_id)
    {
        $suspensions = SuspensionMedico::where('medico_id', $medico_id)
            ->where('fecha_fin', '>=', now()->toDateString())
            ->orderBy('fecha_inicio', 'asc')
            ->get();

        return response()->json($suspensions);
    }

    public function getCitasActivasCount(Request $request, $medico_id)
    {
        $request->validate([
            'fecha_inicio' => 'required|date',
            'fecha_fin' => 'required|date',
        ]);

        $count = Cita::whereHas('calendario', function($q) use ($medico_id) {
            $q->where('medico_id', $medico_id);
        })
        ->whereBetween('fecha_cita', [$request->fecha_inicio, $request->fecha_fin])
        ->where('estado', 'Agendada')
        ->count();

        return response()->json(['count' => $count]);
    }
}
