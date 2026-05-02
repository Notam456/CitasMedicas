<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Especialidad extends Model
{
    protected $table = 'especialidades';

    public function medicos()
    {
        return $this->hasMany(Medico::class, 'especialidad_id');
    }

    protected $fillable = ['nombre'];
    
    protected $attributes = ['estado' => true,];

    public $timestamps = false;
}