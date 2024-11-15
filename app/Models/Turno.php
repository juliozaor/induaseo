<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Turno extends Model
{
    use HasFactory;
    protected $fillable = [
        'nombre', 'frecuencia_id', 'frecuencia_cantidad', 
        'estado', 'creador_id', 'actualizador_id'
    ];

    public function frecuencia()
    {
        return $this->belongsTo(Frecuencia::class);
    }
}
