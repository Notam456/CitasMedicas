<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class Patologia extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;
    protected $fillable = ['especialidad_id', 'nombre', 'descripcion'];

    public function especialidad()
    {
        return $this->belongsTo(Especialidad::class);
    }

    // Relación con citas a través de cita_patologias
    public function citas()
    {
        return $this->belongsToMany(Cita::class, 'cita_patologias')->withTimestamps();
    }
}