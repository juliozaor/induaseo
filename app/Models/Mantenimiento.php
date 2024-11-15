<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Mantenimiento extends Model
{
    use HasFactory;

    protected $fillable = ['activo_id', 'tipo_mantenimiento', 'fecha_programada', 'fecha_realizada', 'descripcion', 'estado'];

    public function activo()
    {
        return $this->belongsTo(Activo::class);
    }
}
