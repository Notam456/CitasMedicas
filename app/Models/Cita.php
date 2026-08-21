<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class Cita extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;
    protected $table = 'citas';

  

    protected $fillable = [
        'user_id',
        'paciente_id',
        'calendario_id',
        'fecha_registro',
        'fecha_cita',
        'estado',
        'tipo_paciente',
        'observacion',
        'historia_traida',
        'diagnostico_libre',
        'atendido_por',
    ];

    protected $casts = [
        'historia_traida' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function atendidoPor()
    {
        return $this->belongsTo(User::class, 'atendido_por');
    }

    public function paciente()
    {
        return $this->belongsTo(Paciente::class);
    }

    public function calendario()
    {
        return $this->belongsTo(Calendario::class);
    }

    public function medico()
    {
        return $this->hasOneThrough(Medico::class, Calendario::class, 'id', 'id', 'calendario_id', 'medico_id');
    }

    public function especialidad()
    {
        return $this->hasOneThrough(Especialidad::class, Calendario::class, 'id', 'id', 'calendario_id', 'especialidad_id');
    }

    public function patologias()
    {
        return $this->belongsToMany(Patologia::class, 'cita_patologias')->withTimestamps();
    }

    public function aroDato()
    {
        return $this->hasOne(AroCitaDato::class);
    }

    public function cancelacion()
    {
        return $this->hasOne(CitaCancelacion::class);
    }
}
