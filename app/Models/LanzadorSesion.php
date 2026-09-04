<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LanzadorSesion extends Model
{
    protected $table = 'lanzador_sesiones';

    protected $primaryKey = 'token';

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = ['token', 'session_id', 'user_id'];
}
