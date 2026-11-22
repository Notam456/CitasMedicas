<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Distrito extends Model{
    protected $fillable = ['nombre', 'es_agrupador', 'mostrar_desglose', 'orden'];

    protected $casts = [
        'es_agrupador' => 'boolean',
        'mostrar_desglose' => 'boolean',
        'orden' => 'integer',
    ];

    public function municipios(){
        
        return $this->hasMany(Municipio::class);
    }
}
