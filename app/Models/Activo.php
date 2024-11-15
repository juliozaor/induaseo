<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Activo extends Model
{
    use HasFactory;

    protected $fillable = ['nombre', 'tipo', 'estado', 'centro_trabajo_id', 'fecha_adquisicion', 'fecha_mantenimiento'];

    public function centroDeTrabajo()
    {
        return $this->belongsTo(CentroDeTrabajo::class);
    }

    public function novedades()
    {
        return $this->hasMany(NovedadDeActivo::class);
    }

    public function mantenimientos()
    {
        return $this->hasMany(Mantenimiento::class);
    }
}
