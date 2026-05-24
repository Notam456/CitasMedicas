<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Patologia extends Model
{
    protected $fillable = ['especialidad_id', 'nombre', 'descripcion'];

    public function especialidad()
    {
        return $this->belongsTo(Especialidad::class);
    }

    public function diagnosticos()
    {
        return $this->hasMany(Diagnostico::class);
    }
}