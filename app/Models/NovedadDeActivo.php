<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NovedadDeActivo extends Model
{
    use HasFactory;

    protected $fillable = ['activo_id', 'tipo_novedad', 'descripcion', 'fecha'];

    public function activo()
    {
        return $this->belongsTo(Activo::class);
    }
}
