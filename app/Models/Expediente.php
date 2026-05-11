<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Expediente extends Model
{
    protected $primaryKey = "id";

    public function paciente()
    {
        return $this->belongsTo(Paciente::class, 'paciente_id');
    }

    protected $fillable = ['numero_expediente', 'fecha_apertura', 'paciente_id'];
}
