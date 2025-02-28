<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AreaActividad extends Model
{
    use HasFactory;

    protected $table = 'areas_actividades';

    protected $fillable = [
        'area_id',
        'actividad_id',
        'descripcion',
        'estado',
        'calificacion',
    ];

    public function area()
    {
        return $this->belongsTo(Area::class, 'area_id');
    }

    public function actividad()
    {
        return $this->belongsTo(Actividades::class, 'actividad_id');
    }
}
