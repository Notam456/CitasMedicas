<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CitaTratamiento extends Model
{
    protected $table = 'cita_tratamientos';
    protected $fillable = ['cita_id', 'medicamento_id', 'dosis', 'duracion', 'indicaciones'];

    public function cita()
    {
        return $this->belongsTo(Cita::class);
    }

    public function medicamento()
    {
        return $this->belongsTo(Medicamento::class);
    }
}
