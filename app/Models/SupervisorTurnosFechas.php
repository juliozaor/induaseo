<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SupervisorTurnosFechas extends Model
{
    use HasFactory;

    protected $table = 'supervisor_turnos_fechas';

    protected $fillable = [
        'supervisor_turno_id',
        'fecha_inicio',
        'fecha_fin'
    ];

    public function supervisorTurno()
    {
        return $this->belongsTo(SupervisorTurno::class, 'supervisor_turno_id');
    }
}
