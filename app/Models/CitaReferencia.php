<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CitaReferencia extends Model
{
    protected $fillable = ['cita_id', 'especialidad_id', 'observaciones', 'fecha_referencia'];

    public function cita()
    {
        return $this->belongsTo(Cita::class);
    }

    public function especialidad()
    {
        return $this->belongsTo(Especialidad::class);
    }
}
