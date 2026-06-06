<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Parroquia extends Model
{


    public function pacientes()
    {
        return $this->hasMany(Paciente::class, 'parroquia_id');
    }

    protected $table = 'parroquias';
    protected $primaryKey = 'id';

    protected $fillable = ['nombre', 'municipio_id'];

    public function municipio()
    {
        return $this->belongsTo(Municipio::class, 'municipio_id');
    }
}