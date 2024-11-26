<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SedesActivos extends Model
{
    use HasFactory;

    protected $table = 'sedes_activos';
    protected $fillable = ['sede_id', 'activo_id',  'cantidad','estado_id','estado', 'creador_id', 'actualizador_id'];

    public function activo()
    {
        return $this->belongsTo(Activos::class);
    }

    public function sede()
    {
        return $this->belongsTo(Sede::class, 'sede_id');
    }

    public function estados()
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

    public function imagenes()
    {
        return $this->hasMany(ImagenSedeActivo::class, 'sede_activo_id');
    }

}