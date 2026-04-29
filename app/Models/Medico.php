<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Medico extends Model
{
    protected $table = 'medicos';
    protected $primaryKey = 'id_medico';
    
    protected $fillable = [
        'nombres',
        'apellidos',
        'cedula',
        'telefono',
        'id_especialidad',
    ];
    
    public function especialidad()
    {
        return $this->belongsTo(Especialidad::class,'id_especialidad');
    }

    public $timestamps = false;
}