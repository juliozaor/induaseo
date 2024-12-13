<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Insumos extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'nombre_elemento',
        'marca',
        'codigo',
        'clasificacion_id',
        'cantidad',
        'estado_id',
        'estado',
        'creador_id',
        'actualizador_id',
        'proveedor',
        'telefono_proveedor',
        'imagen'
    ];

    public function clasificacion()
    {
        return $this->belongsTo(Clasificaciones::class);
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
}