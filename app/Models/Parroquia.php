<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Parroquia extends Model
{


    public function pacientes()
    {
        return $this->hasMany(Paciente::class, 'id_parroquia');
    }

    protected $table = 'parroquias';
    protected $primaryKey = 'id';
    public $timestamps = true;

    protected $fillable = ['nombre', 'municipio_id'];

    public function municipio()
    {
        return $this->belongsTo(Municipio::class, 'municipio_id');
    }
}