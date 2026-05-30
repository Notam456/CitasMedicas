<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CitaPatologia extends Model
{
    protected $table = 'cita_patologias';
    protected $fillable = ['cita_id', 'patologia_id'];

    public function cita()
    {
        return $this->belongsTo(Cita::class);
    }

    public function patologia()
    {
        return $this->belongsTo(Patologia::class);
    }
}
