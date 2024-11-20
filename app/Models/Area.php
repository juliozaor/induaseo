<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Area extends Model
{
    use HasFactory;

    protected $fillable = [
        'nombre',
        'sede_id',
        'estado',
        'creador_id',
        'actualizador_id',
    ];

    public function tareas()
    {
        return $this->hasMany(Tarea::class);
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
        return $this->belongsTo(Sede::class, 'sede_id');
    }
}