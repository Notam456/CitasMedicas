<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HorarioMedico extends Model
{
    protected $table = 'horario_medico';

    protected $fillable = [
        'medico_id',
        'dia_semana',
        'hora_entrada',
        'hora_salida',
    ];

    public function medico()
    {
        return $this->belongsTo(Medico::class, 'medico_id');
    }
}
