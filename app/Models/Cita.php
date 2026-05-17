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
        'tipo_paciente',
        'observacion',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'id_user');
    }

    public function paciente()
    {
        return $this->belongsTo(Paciente::class);
    }

    public function calendario()
    {
        return $this->belongsTo(Calendario::class);
    }

    public function medico()
    {
        return $this->hasOneThrough(Medico::class, Calendario::class, 'id', 'id', 'calendario_id', 'medico_id');
    }
    
    public function especialidad()
    {
        return $this->medico()->especialidad();
    }
}
