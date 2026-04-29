<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Paciente extends Model
{

    protected $primaryKey = "id_paciente";

    public function citas()
    {
        return $this->hasMany(Cita::class, 'id_paciente');
    }

    public function parroquias()
    {
        return $this->belongsTo(Parroquia::class, 'id_parroquia');
    }

    public function Expediente()
    {
        return $this->hasOne(Expediente::class, 'id_paciente');
    }

    protected $fillable = ['nombre', 'apellido', 'cedula', 'fecha_nacimiento', 'telefono', 'id_parroquia'];

    
}

