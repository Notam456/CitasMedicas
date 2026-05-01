<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Especialidad extends Model
{
    protected $table = 'especialidades';

    protected $fillable = ['nombre'];
    
    protected $attributes = ['estado' => true,];

    public $timestamps = false;
}