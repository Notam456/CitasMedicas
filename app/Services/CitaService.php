<?php

namespace App\Services;

use App\Models\Calendario;
use App\Models\Cita;
use App\Models\Expediente;
use App\Models\Paciente;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CitaService
{
    public function crearCita(array $data): Cita
    {
        $cedulaCompleta = $data['cedula_tipo'] . '-' . $data['cedula'];
        $rifCompleto = $data['rif'] ? 'J-' . $data['rif'] : '';

        $paciente = Paciente::firstOrCreate(
            ['cedula' => $cedulaCompleta],
            [
                'rif' => $rifCompleto,
                'nombre' => $data['nombre'],
                'apellido' => $data['apellido'],
                'fecha_nacimiento' => $data['fecha_nacimiento'],
                'telefono' => $data['telefono'],
                'parroquia_id' => $data['parroquia_id'],
                'direccion' => $data['direccion'] ?? null,
                'sexo' => $data['sexo'],
            ]
        );

        session()->flash('paciente_id', $paciente->id);

        $this->manejarExpediente($paciente, $data['numero_expediente'] ?? null);

        return Cita::create([
            'paciente_id' => $paciente->id,
            'calendario_id' => $data['calendario_id'],
            'user_id' => Auth::id() ?? 1,
            'fecha_registro' => now()->toDateString(),
            'fecha_cita' => $data['fecha_cita'],
            'estado' => 'Agendada',
            'tipo_paciente' => $data['tipo_paciente'],
            'observacion' => $data['observacion'] ?? null,
        ]);
    }

    public function verificarCupos(int $calendarioId, string $tipoPaciente): bool
    {
        if ($tipoPaciente === 'orden_medica') {
            return true;
        }

        $calendario = Calendario::lockForUpdate()->findOrFail($calendarioId);

        $ocupados = Cita::where('calendario_id', $calendario->id)
            ->where('tipo_paciente', $tipoPaciente)
            ->whereIn('estado', ['Agendada', 'Atendida'])
            ->count();

        $capacidadMaxima = ($tipoPaciente === 'primera_vez')
            ? $calendario->cupos_primera_vez
            : $calendario->cupos_sucesivos;

        return $ocupados < $capacidadMaxima;
    }

    public function verificarPrimeraVez(int $pacienteId, int $especialidadId): bool
    {
        return Cita::where('paciente_id', $pacienteId)
            ->whereHas('calendario.medico', function ($q) use ($especialidadId) {
                $q->where('especialidad_id', $especialidadId);
            })
            ->whereIn('estado', ['Agendada', 'Atendida'])
            ->exists();
    }

    private function manejarExpediente(Paciente $paciente, ?string $numeroExpediente): void
    {
        if (!$numeroExpediente) {
            return;
        }

        $numeroExpediente = trim($numeroExpediente);

        $historiaDeOtro = Expediente::where('numero_expediente', $numeroExpediente)
            ->where('paciente_id', '!=', $paciente->id)
            ->exists();

        if ($historiaDeOtro) {
            abort(422, 'Número de Historia en uso');
        }

        if ($paciente->expediente) {
            if ($paciente->expediente->numero_expediente !== $numeroExpediente) {
                abort(422, 'Historia ya asignada');
            }
        } else {
            Expediente::create([
                'paciente_id' => $paciente->id,
                'numero_expediente' => $numeroExpediente,
                'fecha_apertura' => now()->toDateString(),
            ]);
        }
    }
}
