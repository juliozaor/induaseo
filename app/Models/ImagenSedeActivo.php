<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ImagenSedeActivo extends Model
{
    use HasFactory;

    protected $table = 'imagenes_sedes_activos';
    protected $fillable = ['sede_activo_id', 'imagen'];

    public function sedeActivo()
    {
        return $this->belongsTo(SedesActivos::class);
    }
}