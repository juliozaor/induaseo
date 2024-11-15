<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tarea extends Model
{
    use HasFactory;

    protected $fillable = ['descripcion', 'tipo_servicio_id', 'supervisor_id', 'estado', 'fecha_inicio', 'fecha_fin', 'evidencias'];

    public function tipoDeServicio()
    {
        return $this->belongsTo(TipoDeServicio::class);
    }

    public function supervisor()
    {
        return $this->belongsTo(Usuario::class, 'supervisor_id');
    }

    public function centroDeTrabajo()
    {
        return $this->belongsTo(CentroDeTrabajo::class);
    }
}
