<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SatisfaccionServicio extends Model
{
    use HasFactory;

    protected $table = 'satisfaccion_servicio';

    protected $fillable = [
        'supervisor_turnos_id',
        'total_puntos',
        'total_encuestas',
        'promedio',
    ];
}
