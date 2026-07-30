<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AroCitaDato extends Model
{
    protected $table = 'aro_cita_datos';

    protected $fillable = ['cita_id', 'semanas_gestacion'];

    public function cita()
    {
        return $this->belongsTo(Cita::class);
    }
}
