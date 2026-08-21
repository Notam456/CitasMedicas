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

    protected $fillable = ['nombre', 'estado'];
    
    protected $attributes = ['estado' => true,];

    public function esSoloFemenino(): bool
    {
        return in_array($this->nombre, config('citas.especialidades_solo_femenino', []));
    }

}