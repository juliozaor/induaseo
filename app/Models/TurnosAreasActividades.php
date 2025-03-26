<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TurnosAreasActividades extends Model
{
    use HasFactory;

    protected $table = 'turnos_areas_actividades';

    protected $fillable = [
        'turnos_areas_id',
        'actividad_id',
        'estado',
        'calificacion',
    ];

    public function turnoArea()
    {
        return $this->belongsTo(TurnoArea::class, 'turnos_areas_id');
    }

    public function actividad()
    {
        return $this->belongsTo(Actividades::class, 'actividad_id');
    }
}
