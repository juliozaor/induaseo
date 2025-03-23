<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Actividades extends Model
{
    use HasFactory;

    protected $fillable = ['nombre', 'descripcion', 'calificacion', 'estado', 'frecuencia_id'];

    // Relación de muchos a muchos con Area
    public function areas()
    {
        return $this->belongsToMany(Area::class, 'areas_actividades', 'actividad_id', 'area_id');
    }

    // Relación de uno a muchos con Frecuencia
    public function frecuencia()
    {
        return $this->belongsTo(Frecuencia::class);
    }

    // Relación de uno a muchos con ImagenesActividades
    public function imagenes()
    {
        return $this->hasMany(ImagenesActividades::class, 'actividad_id');
    }
}
