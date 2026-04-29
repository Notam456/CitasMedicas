<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Especialidad extends Model
{
    
    protected $primaryKey = 'id_especialidad';
    
    protected $fillable = ['nombre'];

    public $timestamps = false;
}