<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Especialidad extends Model
{
    protected $table = 'especialidad';
    protected $primaryKey = 'id_especialidad';
    
    protected $fillable = [
        'nombre',
        'descripcion',
        'estado'
    ];

    public $timestamps = false;
}