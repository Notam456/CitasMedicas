<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HistoricoNumero extends Model
{
    protected $table = 'historico_numeros';

    protected $fillable = [
        'paciente_id',
        'numero_expediente',
        'motivo',
        'fecha_asignacion',
        'fecha_liberacion',
        'vigente',
    ];

    protected $casts = [
        'vigente' => 'boolean',
        'fecha_asignacion' => 'date',
        'fecha_liberacion' => 'date',
    ];

    public function paciente()
    {
        return $this->belongsTo(Paciente::class, 'paciente_id');
    }

    public static function asignar(Paciente $paciente, string $numeroExpediente): self
    {
        self::where('paciente_id', $paciente->id)
            ->where('vigente', true)
            ->update([
                'vigente' => false,
                'fecha_liberacion' => now()->toDateString(),
            ]);

        return self::create([
            'paciente_id' => $paciente->id,
            'numero_expediente' => $numeroExpediente,
            'fecha_asignacion' => now()->toDateString(),
            'vigente' => true,
        ]);
    }

    public static function liberar(Paciente $paciente, string $numeroExpediente, string $motivo): self
    {
        $vigente = self::where('paciente_id', $paciente->id)
            ->where('numero_expediente', $numeroExpediente)
            ->where('vigente', true)
            ->first();

        if ($vigente) {
            $vigente->update([
                'motivo' => $motivo,
                'fecha_liberacion' => now()->toDateString(),
                'vigente' => false,
            ]);

            return $vigente;
        }

        return self::create([
            'paciente_id' => $paciente->id,
            'numero_expediente' => $numeroExpediente,
            'motivo' => $motivo,
            'fecha_asignacion' => now()->toDateString(),
            'fecha_liberacion' => now()->toDateString(),
            'vigente' => false,
        ]);
    }
}
