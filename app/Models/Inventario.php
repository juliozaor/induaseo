<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Inventario extends Model
{
    use HasFactory;

    protected $fillable = [
        'sede_id',
        'item_id',
        'cantidad',
        'estado_id',
        'estado',
        'creador_id',
        'actualizador_id',
    ];

    public function sede()
    {
        return $this->belongsTo(Sede::class, 'sede_id');
    }

    public function item()
    {
        return $this->belongsTo(Insumos::class, 'insumo_id');
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

    public function imagenes()
    {
        return $this->hasMany(ImagenInventario::class);
    }
}
