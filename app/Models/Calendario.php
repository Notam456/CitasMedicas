<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Calendario extends Model
{
    public function medico()
    {
        return $this->belongsTo(Medico::class, 'medico_id');
    }

    public $fillable = ['medico_id', 'hora_inicio', 'hora_fin', 'fecha', 'cupos_disponibles'];
}
