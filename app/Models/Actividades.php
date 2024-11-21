<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Actividades extends Model
{
    use HasFactory;

    protected $fillable = ['nombre', 'descripcion', 'turno_id', 'calificacion'];

    public function turno()
    {
        return $this->belongsTo(Turno::class);
    }
}
