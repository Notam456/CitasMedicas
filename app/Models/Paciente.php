<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class Paciente extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;

    public function citas()
    {
        return $this->hasMany(Cita::class, 'paciente_id');
    }

    public function parroquia()
    {
        return $this->belongsTo(Parroquia::class, 'parroquia_id');
    }

    public function expediente()
    {
        return $this->hasOne(Expediente::class, 'paciente_id');
    }

    public function historicoNumeros()
    {
        return $this->hasMany(HistoricoNumero::class, 'paciente_id')->orderByDesc('fecha_asignacion')->orderByDesc('id');
    }

    protected $fillable = ['nombre', 'apellido', 'cedula', 'rif', 'fecha_nacimiento', 'telefono', 'parroquia_id', 'direccion', 'sexo', 'estado', 'estado_motivo', 'fecha_baja'];

    
}

