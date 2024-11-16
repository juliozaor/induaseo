<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sede extends Model
{
    use HasFactory;
    protected $fillable = [
        'cliente_id', 'ciudad_id', 'direccion', 'telefono', 
        'horario_inicio', 'horario_fin', 'estado', 'creador_id', 'actualizador_id'
    ];

    public function cliente()
    {
        return $this->belongsTo(Cliente::class);
    }

    public function ciudad()
    {
        return $this->belongsTo(Ciudades::class);
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
