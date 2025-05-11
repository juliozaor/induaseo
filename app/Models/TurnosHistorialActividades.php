<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TurnosHistorialActividades extends Model
{
    use HasFactory;

    protected $table = 'turnos_historial_actividades';

    protected $fillable = [
        'turnos_areas_actividades_id',
        'estado',
        'calificacion',
        'created_at',
        'updated_at'
    ];

    public function turnoAreaActividad()
    {
        return $this->belongsTo(TurnosAreasActividades::class, 'turnos_areas_actividades_id');
    }
}
