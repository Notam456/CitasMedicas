<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Estado extends Model
{
    // Esto le permite al Seeder guardar el nombre
    protected $fillable = ['nombre'];

    // Esto conecta el estado con sus municipios
    public function municipios() 
    {
        return $this->hasMany(Municipio::class);
    }
}
