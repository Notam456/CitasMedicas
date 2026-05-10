<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Morbilidad extends Model
{
    protected $table = 'morbilidades';
    
    protected $fillable = [
        'cita_id',
        'diagnostico',
        'observaciones',
        'asistio',
        'created_at',
        'updated_at'
    ];

    // Relación con Cita
    public function cita()
    {
        return $this->belongsTo(Cita::class);
    }
}
