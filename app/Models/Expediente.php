<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Expediente extends Model
{
    protected $primaryKey = "id_expediente";

    public function paciente()
    {
        return $this->belongsTo(Paciente::class, 'id_paciente');
    }

    protected $fillable = ['numero_expediente', 'fecha_apertura', 'id_paciente'];
}
