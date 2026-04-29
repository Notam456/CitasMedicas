<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cita extends Model
{
    protected $table = 'citas';
    protected $primaryKey = 'id_cita';
    public $timestamps = false;

    protected $fillable = [
        'id_user',
        'fecha',
        'hora',
        'descripcion',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'id_user');
    }
}
