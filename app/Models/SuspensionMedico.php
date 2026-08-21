<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class SuspensionMedico extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;
    protected $table = 'suspensiones_medicos';

    protected $fillable = [
        'medico_id',
        'fecha_inicio',
        'fecha_fin',
        'suplente_id',
        'motivo',
    ];

    protected $casts = [
        'fecha_inicio' => 'date:Y-m-d',
        'fecha_fin' => 'date:Y-m-d',
    ];

    public function medico()
    {
        return $this->belongsTo(Medico::class, 'medico_id');
    }

    public function suplente()
    {
        return $this->belongsTo(Medico::class, 'suplente_id');
    }
}
