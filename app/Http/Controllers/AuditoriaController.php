<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use OwenIt\Auditing\Models\Audit;
use App\Models\User;
use App\Models\Paciente;
use App\Models\Medico;
use App\Models\Especialidad;
use App\Models\Patologia;
use App\Models\Calendario;
use App\Models\Estado;
use App\Models\Municipio;
use App\Models\Parroquia;
use App\Models\Distrito;

class AuditoriaController extends Controller
{
   

    private function translateEvent(string $event): string
    {
        return match (strtolower($event)) {
            'created' => 'Creado',
            'updated' => 'Actualizado',
            'deleted' => 'Eliminado',
            'restored' => 'Restaurado',
            default   => ucfirst($event),
        };
    }

   
    private function translateModel(string $auditableType): string
    {
        $baseName = class_basename($auditableType);

        return match ($baseName) {
            'User'             => 'Usuario',
            'Paciente'         => 'Paciente',
            'Medico'           => 'Médico',
            'Especialidad'     => 'Especialidad',
            'Patologia'        => 'Patología',
            'Cita'             => 'Cita',
            'Calendario'       => 'Planificación / Calendario',
            'SuspensionMedico' => 'Suspensión de Médico',
            'Expediente'       => 'Expediente',
            'Estado'           => 'Estado',
            'Municipio'        => 'Municipio',
            'Parroquia'        => 'Parroquia',
            'Distrito'         => 'Distrito',
            default            => $baseName,
        };
    }

   
    private function translateFieldKey(string $key): string
    {
        return match ($key) {
            'id'                => 'ID',
            'paciente_id'       => 'Paciente',
            'user_id'           => 'Usuario Registrador',
            'atendido_por'      => 'Atendido Por',
            'medico_id'         => 'Médico',
            'suplente_id'       => 'Médico Suplente',
            'especialidad_id'   => 'Especialidad',
            'patologia_id'      => 'Patología',
            'calendario_id'     => 'Planificación / Turno',
            'parroquia_id'      => 'Parroquia',
            'municipio_id'      => 'Municipio',
            'estado_id'         => 'Estado',
            'distrito_id'       => 'Distrito',
            'fecha_registro'    => 'Fecha de Registro',
            'fecha_cita'        => 'Fecha de Cita',
            'fecha_nacimiento'  => 'Fecha de Nacimiento',
            'fecha_inicio'      => 'Fecha de Inicio',
            'fecha_fin'         => 'Fecha de Fin',
            'hora_inicio'       => 'Hora de Inicio',
            'hora_fin'          => 'Hora de Fin',
            'tipo_paciente'     => 'Tipo de Paciente',
            'historia_traida'   => 'Historia Traída',
            'diagnostico_libre' => 'Diagnóstico Libre',
            'cupos_primera_vez' => 'Cupos Primera Vez',
            'cupos_sucesivos'   => 'Cupos Sucesivos',
            'observacion'       => 'Observación',
            'motivo'            => 'Motivo',
            'estado'            => 'Estado / Estatus',
            'nombre'            => 'Nombre',
            'apellido'          => 'Apellido',
            'cedula'            => 'Cédula',
            'rif'               => 'RIF',
            'telefono'          => 'Teléfono',
            'email'             => 'Correo Electrónico',
            'direccion'         => 'Dirección',
            'sexo'              => 'Sexo',
            'created_at'        => 'Fecha de Creación',
            'updated_at'        => 'Fecha de Actualización',
            'deleted_at'        => 'Fecha de Eliminación',
            default             => str_replace('_', ' ', ucfirst($key)),
        };
    }

    
    private function resolveValue(string $key, mixed $value): mixed
    {
        if ($value === null) {
            return null;
        }

        return match ($key) {
            'paciente_id' => (function ($val) {
                $p = Paciente::find($val);
                return $p ? "{$p->nombre} {$p->apellido} (C.I: {$p->cedula})" : "ID: {$val}";
            })($value),
            'user_id', 'atendido_por' => (function ($val) {
                $u = User::find($val);
                return $u ? $u->name : "ID: {$val}";
            })($value),
            'medico_id', 'suplente_id' => (function ($val) {
                $m = Medico::find($val);
                return $m ? "Dr(a). {$m->nombre} {$m->apellido}" : "ID: {$val}";
            })($value),
            'especialidad_id' => (function ($val) {
                $e = Especialidad::find($val);
                return $e ? $e->nombre : "ID: {$val}";
            })($value),
            'patologia_id' => (function ($val) {
                $p = Patologia::find($val);
                return $p ? $p->nombre : "ID: {$val}";
            })($value),
            'parroquia_id' => (function ($val) {
                $p = Parroquia::find($val);
                return $p ? $p->nombre : "ID: {$val}";
            })($value),
            'municipio_id' => (function ($val) {
                $m = Municipio::find($val);
                return $m ? $m->nombre : "ID: {$val}";
            })($value),
            'estado_id' => (function ($val) {
                $e = Estado::find($val);
                return $e ? $e->nombre : "ID: {$val}";
            })($value),
            'distrito_id' => (function ($val) {
                $d = Distrito::find($val);
                return $d ? $d->nombre : "ID: {$val}";
            })($value),
            'calendario_id' => (function ($val) {
                $c = Calendario::with(['medico', 'especialidad'])->find($val);
                if (!$c) {
                    return "ID: {$val}";
                }
                if ($c->medico) {
                    return "Dr(a). {$c->medico->nombre} {$c->medico->apellido} - {$c->fecha}";
                }
                $esp = $c->especialidad ? $c->especialidad->nombre : 'Cualquier médico';
                return "Cualquier médico ({$esp}) - {$c->fecha}";
            })($value),
            'tipo_paciente' => (function ($val) {
                return match ($val) {
                    'primera_vez' => 'Primera Vez',
                    'sucesivo'    => 'Sucesivo',
                    default       => ucfirst((string)$val),
                };
            })($value),
            'historia_traida' => (function ($val) {
                return $val ? 'Sí' : 'No';
            })($value),
            default => $value,
        };
    }

    
    private function transformValues(?array $values): array
    {
        if (!$values) {
            return [];
        }

        $transformed = [];
        foreach ($values as $key => $val) {
            $label = $this->translateFieldKey($key);
            $resolvedValue = $this->resolveValue($key, $val);
            $transformed[] = [
                'field_key' => $key,
                'label'     => $label,
                'value'     => $resolvedValue,
            ];
        }

        return $transformed;
    }

   
    public function index(Request $request)
    {
        if ($request->ajax() && $request->has('draw')) {
            return $this->dataTableResponse($request);
        }

        $users = User::orderBy('name')->get();
        $events = [
            'created' => 'Creado',
            'updated' => 'Actualizado',
            'deleted' => 'Eliminado',
            'restored' => 'Restaurado',
        ];

        return view('auditoria.index', compact('users', 'events'));
    }

  
    private function groupAudits($rawAudits)
    {
        $grouped = [];

        foreach ($rawAudits as $audit) {
            if ($audit->auditable_type === 'App\Models\Calendario') {
                // Group key: user_id, event, and timestamp up to minute
                $timeKey = $audit->created_at ? $audit->created_at->format('Y-m-d H:i') : 'none';
                $groupKey = 'calendario_' . $audit->user_id . '_' . $audit->event . '_' . $timeKey;

                if (!isset($grouped[$groupKey])) {
                    $grouped[$groupKey] = [
                        'is_group'       => true,
                        'ids'            => [$audit->id],
                        'primary_audit'  => $audit,
                        'user'           => $audit->user,
                        'user_id'        => $audit->user_id,
                        'event'          => $audit->event,
                        'auditable_type' => $audit->auditable_type,
                        'created_at'     => $audit->created_at,
                        'count'          => 1,
                    ];
                } else {
                    $grouped[$groupKey]['ids'][] = $audit->id;
                    $grouped[$groupKey]['count']++;
                }
            } else {
                $grouped[] = [
                    'is_group'       => false,
                    'ids'            => [$audit->id],
                    'primary_audit'  => $audit,
                    'user'           => $audit->user,
                    'user_id'        => $audit->user_id,
                    'event'          => $audit->event,
                    'auditable_type' => $audit->auditable_type,
                    'created_at'     => $audit->created_at,
                    'count'          => 1,
                ];
            }
        }

        return array_values($grouped);
    }

   
    private function dataTableResponse(Request $request)
    {
        $query = Audit::with('user');

        // Search filter
        if ($search = $request->get('search')['value']) {
            $query->where(function ($q) use ($search) {
                $q->where('event', 'ILIKE', "%{$search}%")
                  ->orWhere('auditable_type', 'ILIKE', "%{$search}%")
                  ->orWhere('ip_address', 'ILIKE', "%{$search}%")
                  ->orWhereHas('user', function ($uQuery) use ($search) {
                      $uQuery->where('name', 'ILIKE', "%{$search}%")
                             ->orWhere('email', 'ILIKE', "%{$search}%");
                  });
            });
        }

        // Custom filters
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        if ($request->filled('event')) {
            $query->where('event', $request->event);
        }

        if ($request->filled('fecha_desde')) {
            $query->whereDate('created_at', '>=', $request->fecha_desde);
        }

        if ($request->filled('fecha_hasta')) {
            $query->whereDate('created_at', '<=', $request->fecha_hasta);
        }

        // Ordering
        $orderColumn = $request->get('order')[0]['column'] ?? 3;
        $orderDir = $request->get('order')[0]['dir'] ?? 'desc';
        $columns = ['user_id', 'event', 'auditable_type', 'created_at'];

        if (isset($columns[$orderColumn])) {
            $query->orderBy($columns[$orderColumn], $orderDir);
        } else {
            $query->orderBy('created_at', 'desc');
        }

        $allMatchingAudits = $query->get();
        $groupedAudits = $this->groupAudits($allMatchingAudits);

        $totalRecords = Audit::count();
        $filteredRecords = count($groupedAudits);

        $start = $request->get('start', 0);
        $length = $request->get('length', 10);

        $pagedGrouped = ($length > 0)
            ? array_slice($groupedAudits, $start, $length)
            : $groupedAudits;

        $dataFormatted = [];
        foreach ($pagedGrouped as $item) {
            $primaryAudit = $item['primary_audit'];
            $userName = $item['user'] ? $item['user']->name : ($item['user_id'] ? 'Usuario #' . $item['user_id'] : 'Sistema');
            $eventSpanish = $this->translateEvent($item['event']);
            $modelSpanish = $this->translateModel($item['auditable_type']);

            $badgeClass = match (strtolower($item['event'])) {
                'created'  => 'bg-success',
                'updated'  => 'bg-info text-dark',
                'deleted'  => 'bg-danger',
                'restored' => 'bg-warning text-dark',
                default    => 'bg-secondary',
            };

            $eventBadge = '<span class="badge ' . $badgeClass . '">' . e($eventSpanish) . '</span>';
            $fechaHora = $item['created_at'] ? $item['created_at']->format('d/m/Y h:i A') : 'N/A';

            if ($item['is_group'] && $item['count'] > 1) {
                $modeloTexto = e($modelSpanish) . ' <span class="badge bg-primary ms-1">' . $item['count'] . ' registros creados</span>';
                $idsParam = implode(',', $item['ids']);
                $actionBtn = '<div class="hstack gap-2 justify-content-center">';
                $actionBtn .= '<button type="button" data-group-ids="' . $idsParam . '" class="btn-show btn btn-xs btn-square btn-neutral" title="Ver lote"><i class="bi bi-eye"></i></button>';
                $actionBtn .= '</div>';
            } else {
                $modeloTexto = e($modelSpanish) . ' (ID: ' . e($primaryAudit->auditable_id) . ')';
                $actionBtn = '<div class="hstack gap-2 justify-content-center">';
                $actionBtn .= '<button type="button" data-id="' . $primaryAudit->id . '" class="btn-show btn btn-xs btn-square btn-neutral" title="Ver detalles"><i class="bi bi-eye"></i></button>';
                $actionBtn .= '</div>';
            }

            $dataFormatted[] = [
                e($userName),
                $eventBadge,
                $modeloTexto,
                $fechaHora,
                $actionBtn,
            ];
        }

        return response()->json([
            'draw' => intval($request->draw),
            'recordsTotal' => $totalRecords,
            'recordsFiltered' => $filteredRecords,
            'data' => $dataFormatted,
        ]);
    }

  
    public function show(string $id)
    {
        $ids = explode(',', $id);

        if (count($ids) > 1) {
            $audits = Audit::with('user')->whereIn('id', $ids)->get();
            $first = $audits->first();

            $allNewValues = [];
            foreach ($audits as $idx => $audit) {
                $transformed = $this->transformValues($audit->new_values);
                foreach ($transformed as $item) {
                    $allNewValues[] = [
                        'field_key' => "Registro #" . ($idx + 1) . " - " . $item['field_key'],
                        'label'     => "Registro #" . ($idx + 1) . " - " . $item['label'],
                        'value'     => $item['value'],
                    ];
                }
            }

            return response()->json([
                'id'             => $id,
                'usuario'         => $first->user ? $first->user->name : ($first->user_id ? 'Usuario #' . $first->user_id : 'Sistema'),
                'evento'          => $this->translateEvent($first->event),
                'modelo'          => $this->translateModel($first->auditable_type) . " (Lote de " . count($audits) . " registros)",
                'modelo_id'       => "Lote completo",
                'old_values'      => [],
                'new_values'      => $allNewValues,
                'ip'              => $first->ip_address ?? 'N/A',
                'user_agent'      => $first->user_agent ?? 'N/A',
                'fecha'           => $first->created_at ? $first->created_at->format('d/m/Y h:i:s A') : 'N/A',
            ]);
        }

        $audit = Audit::with('user')->findOrFail((int)$id);

        $oldTransferred = $this->transformValues($audit->old_values);
        $newTransferred = $this->transformValues($audit->new_values);

        return response()->json([
            'id'             => $audit->id,
            'usuario'         => $audit->user ? $audit->user->name : ($audit->user_id ? 'Usuario #' . $audit->user_id : 'Sistema'),
            'evento'          => $this->translateEvent($audit->event),
            'modelo'          => $this->translateModel($audit->auditable_type),
            'modelo_id'       => $audit->auditable_id,
            'old_values'      => $oldTransferred,
            'new_values'      => $newTransferred,
            'ip'              => $audit->ip_address ?? 'N/A',
            'user_agent'      => $audit->user_agent ?? 'N/A',
            'fecha'           => $audit->created_at ? $audit->created_at->format('d/m/Y h:i:s A') : 'N/A',
        ]);
    }
}
