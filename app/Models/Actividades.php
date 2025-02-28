<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Actividades extends Model
{
    use HasFactory;

    protected $fillable = ['nombre', 'descripcion', 'calificacion', 'estado'];

    // Relación de muchos a muchos con Area
    public function areas()
    {
        return $this->belongsToMany(Area::class, 'areas_actividades', 'actividad_id', 'area_id');
    }
}
