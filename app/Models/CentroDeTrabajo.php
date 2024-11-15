<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CentroDeTrabajo extends Model
{
    use HasFactory;

    protected $fillable = ['nombre', 'tipo', 'ubicacion', 'estado', 'cliente_id'];

    public function cliente()
    {
        return $this->belongsTo(Cliente::class);
    }

    public function tareas()
    {
        return $this->hasMany(Tarea::class);
    }

    public function activos()
    {
        return $this->hasMany(Activo::class);
    }

    public function pedidosDeInsumos()
    {
        return $this->hasMany(PedidoDeInsumos::class);
    }
}
