<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TurnoArea extends Model
{
    use HasFactory;

    protected $table = 'turnos_areas';

    protected $fillable = [
        'turno_id',
        'area_id',
    ];

    public function turno()
    {
        return $this->belongsTo(Turno::class);
    }

    public function area()
    {
        return $this->belongsTo(Area::class);
    }

    public function supervisorTurno()
    {
        return $this->belongsTo(SupervisorTurno::class);
    }
    public function actividades()
    {
        return $this->hasMany(TurnosAreasActividades::class, 'turnos_areas_id', 'id');
    }

}
