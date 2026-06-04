<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cita extends Model
{
    protected $table = 'citas';

    public $timestamps = true;

    protected $fillable = [
        'user_id',
        'paciente_id',
        'calendario_id',
        'fecha_registro',
        'fecha_cita',
        'estado',
        'tipo_paciente',
        'observacion',
        'reagendada_contador',
        'diagnostico_libre',
        'atendido_por',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function atendidoPor()
    {
        return $this->belongsTo(User::class, 'atendido_por');
    }

    public function paciente()
    {
        return $this->belongsTo(Paciente::class);
    }

    public function calendario()
    {
        return $this->belongsTo(Calendario::class);
    }

    public function medico()
    {
        return $this->hasOneThrough(Medico::class, Calendario::class, 'id', 'id', 'calendario_id', 'medico_id');
    }

    public function especialidad()
    {
        return $this->hasOneThrough(
            Especialidad::class,
            Calendario::class,
            'id',             
            'id',             
            'calendario_id',  
            'medico_id'      
        );
    }

    // Nuevas relaciones
    public function patologias()
    {
        return $this->belongsToMany(Patologia::class, 'cita_patologias')->withTimestamps();
    }

    public function referencias()
    {
        return $this->hasMany(CitaReferencia::class);
    }

    public function tratamientos()
    {
        return $this->hasMany(CitaTratamiento::class);
    }

    public function medicamentos()
    {
        return $this->belongsToMany(Medicamento::class, 'cita_tratamientos')
            ->withPivot('dosis', 'duracion', 'indicaciones')
            ->withTimestamps();
    }
}
