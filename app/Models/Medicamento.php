<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Medicamento extends Model
{
    protected $fillable = ['nombre', 'descripcion'];

    public function citas()
    {
        return $this->belongsToMany(Cita::class, 'cita_tratamiento')
                    ->withPivot('dosis', 'duracion', 'indicaciones')
                    ->withTimestamps();
    }
}