<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Estado extends Model
{

    public function municipios() 
    {
        return $this->hasMany(Municipio::class);
    }

    protected $table = 'estados';
    protected $primaryKey = 'id';
    public $timestamps = true;

    protected $fillable = ['nombre'];
}
