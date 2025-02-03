<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TipoMultimedias extends Model
{
    use HasFactory;

    protected $table = 'tipo_multimedias';

    protected $fillable = [
        'nombre',
        'estado',
    ];

    public function informaciones()
    {
        return $this->hasMany(Informacion::class, 'tipo_multimedia_id');
    }
}
