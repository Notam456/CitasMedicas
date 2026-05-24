<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Diagnostico extends Model
{
    protected $fillable = ['cita_id', 'patologia_id', 'diagnostico_libre', 'asistio', 'user_id'];

    public function cita() { return $this->belongsTo(Cita::class); }
    public function patologia() { return $this->belongsTo(Patologia::class); }
    public function user() { return $this->belongsTo(User::class); }
}