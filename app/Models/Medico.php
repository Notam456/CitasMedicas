<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Medico extends Model
{
    protected $table = 'medico';
    protected $primaryKey = 'id_medico';
    
    protected $fillable = [
        'nombres',
        'apellidos',
        'cedula',
        'telefono',
        'id_especialidad',
        'estado'
    ];
    
    public function especialidad()
    {
        return $this->belongsTo(Especialidad::class, 'id_especialidad', 'id_especialidad');
    }

    public $timestamps = false;
}