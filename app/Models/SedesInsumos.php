<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SedesInsumos extends Model
{
    use HasFactory;

    protected $table = 'sedes_insumos';
    protected $fillable = ['sede_id', 'insumo_id',  'cantidad','estado', 'creador_id', 'actualizador_id'];

    public function insumo()
    {
        return $this->belongsTo(Insumos::class);
    }

    public function sede()
    {
        return $this->belongsTo(Sede::class, 'sede_id');
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