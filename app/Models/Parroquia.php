<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Parroquia extends Model
{
    protected $fillable = ['nombre', 'municipio_id'];

    // Una parroquia pertenece a un municipio
    public function municipio()
    {
        return $this->belongsTo(Municipio::class);
    }
}