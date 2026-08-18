<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CitaCancelacion extends Model
{
    protected $table = 'cita_cancelaciones';

    protected $fillable = [
        'cita_id',
        'motivo',
        'cancelada_por',
        'observacion',
        'fecha_cancelacion',
    ];

    protected $casts = [
        'fecha_cancelacion' => 'datetime',
    ];

    public function cita()
    {
        return $this->belongsTo(Cita::class, 'cita_id');
    }

    public function canceladaPor()
    {
        return $this->belongsTo(User::class, 'cancelada_por');
    }
}
