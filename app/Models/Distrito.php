<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Distrito extends Model{
    protected $fillable = ['nombre'];

    public function municipios(){
        
        return $this->hasMany(Municipio::class);
    }
}