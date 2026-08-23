<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class Medico extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;
    protected $table = 'medicos';

    protected $fillable = [
        'nombre',
        'apellido',
        'cedula',
        'telefono',
        'especialidad_id',
    ];

    public function horarios()
    {
        return $this->hasMany(HorarioMedico::class, 'medico_id');
    }

    public function especialidad()
    {
        return $this->belongsTo(Especialidad::class, 'especialidad_id');
    }
    public function calendarios()
    {
        return $this->hasMany(Calendario::class);
    }

    public function suspensiones()
    {
        return $this->hasMany(SuspensionMedico::class, 'medico_id');
    }

    public function citas()
    {
        return $this->hasManyThrough(
            Cita::class,       
            Calendario::class, 
            'medico_id',       
            'calendario_id',   
            'id',              
            'id'               
        );
    }

    public function scopePorEspecialidad($query, int $especialidadId)
    {
        return $query->where('especialidad_id', $especialidadId);
    }
}
