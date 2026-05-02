<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Paciente extends Model
{

    public function citas()
    {
        return $this->hasMany(Cita::class, 'id_paciente');
    }

    public function parroquia()
    {
        return $this->belongsTo(Parroquia::class, 'parroquia_id');
    }

    public function Expediente()
    {
        return $this->hasOne(Expediente::class, 'id_paciente');
    }

    protected $fillable = ['nombre', 'apellido', 'cedula', 'fecha_nacimiento', 'telefono', 'parroquia_id', 'direccion'];

    
}

