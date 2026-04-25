<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Paciente extends Model
{
    use HasFactory;

    // Campos que Laravel tiene permiso de escribir en la tabla pacientes
    protected $fillable = [
        'nombre',
        'cedula',
        'estado_id',
        'municipio_id',
        'parroquia_id'
    ];

    /**
     * Obtener el estado asociado al paciente.
     */
    public function estado()
    {
        return $this->belongsTo(Estado::class, 'estado_id');
    }

    /**
     * Obtener el municipio asociado al paciente.
     */
    public function municipio()
    {
        return $this->belongsTo(Municipio::class, 'municipio_id');
    }

    /**
     * Obtener la parroquia asociada al paciente.
     */
    public function parroquia()
    {
        return $this->belongsTo(Parroquia::class, 'parroquia_id');
    }
}
