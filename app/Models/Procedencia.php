<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Procedencia extends Model
{
    use HasFactory;

    // 1. Definimos el nombre de la tabla (opcional si sigue el estándar)
    protected $table = 'procedencias';

    // 2. Definimos los campos que se pueden llenar (Asignación masiva)
    protected $fillable = [
        'estado_id',
        'municipio_id',
        'parroquia_id',
    ];

    // 3. Relaciones Eloquent (The Laravel Way)
    
    public function estado()
    {
        return $this->belongsTo(Estado::class);
    }

    public function municipio()
    {
        return $this->belongsTo(Municipio::class);
    }

    public function parroquia()
    {
        return $this->belongsTo(Parroquia::class);
    }
}
