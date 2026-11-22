<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class Especialidad extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;
    protected $table = 'especialidades';

    public function medicos()
    {
        return $this->hasMany(Medico::class, 'especialidad_id');
    }

    public function calendarios()
    {
        return $this->hasMany(Calendario::class, 'especialidad_id');
    }

    public function patologias()
    {
        return $this->hasMany(Patologia::class, 'especialidad_id');
    }

    protected $fillable = ['nombre', 'estado', 'es_aro'];
    
    protected $attributes = ['estado' => true,];

    protected $casts = ['estado' => 'boolean', 'es_aro' => 'boolean'];

    public function scopeAro($query)
    {
        return $query->where(function ($q) {
            $q->where('es_aro', true)
              ->orWhere('nombre', config('citas.especialidad_aro', 'Aro (Embarazados)'));
        });
    }

    public function esAro(): bool
    {
        return (bool) $this->es_aro
            || $this->nombre === config('citas.especialidad_aro', 'Aro (Embarazados)');
    }

    public function esSoloFemenino(): bool
    {
        return in_array($this->nombre, config('citas.especialidades_solo_femenino', []));
    }

}