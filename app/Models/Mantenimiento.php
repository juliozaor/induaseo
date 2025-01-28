<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Mantenimiento extends Model
{
    use HasFactory;

    protected $table = 'mantenimientos';
    protected $fillable = [
        'ultimo_mtto',
        'mtto_programado',
        'estado_id',
        'observaciones_reportadas',
        'sede_activo_id',
        'observaciones',
        'estado',
        'creador_id',
        'actualizador_id'
    ];

    public function sedes_activos()
    {
        return $this->belongsTo(SedesActivos::class, 'sede_activo_id');
    }

    public function estado()
    {
        return $this->belongsTo(Estados::class, 'estado_id');
    }

    public function creador()
    {
        return $this->belongsTo(Usuario::class, 'creador_id');
    }

    public function actualizador()
    {
        return $this->belongsTo(Usuario::class, 'actualizador_id');
    }

    public function sede()
    {
        return $this->hasOneThrough(Sede::class, SedesActivos::class, 'id', 'id', 'sede_activo_id', 'sede_id');
    }

    public function sedeActivo()
    {
        return $this->belongsTo(SedesActivos::class, 'sede_activo_id');
    }
}
