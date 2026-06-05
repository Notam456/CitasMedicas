<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Paciente extends Model
{

    public function citas()
    {
        return $this->hasMany(Cita::class, 'paciente_id');
    }

    public function parroquia()
    {
        return $this->belongsTo(Parroquia::class, 'parroquia_id');
    }

    public function Expediente()
    {
        return $this->hasOne(Expediente::class, 'paciente_id');
    }

    protected $fillable = ['nombre', 'apellido', 'cedula', 'rif', 'fecha_nacimiento', 'telefono', 'parroquia_id', 'direccion', 'sexo'];

    
}

