<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cita extends Model
{
    protected $table = 'citas';
    
    public $timestamps = false;

    protected $fillable = [
        'user_id',
        'paciente_id',
        'calendario_id',
        'fecha_registro',
        'fecha_cita',
        'estado',
        'observacion',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'id_user');
    }
}
