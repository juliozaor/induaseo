<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ImagenesActividades extends Model
{
    use HasFactory;

    protected $table = 'imagenes_actividades';
    protected $fillable = ['actividad_id', 'imagen'];

    public function actividad()
    {
        return $this->belongsTo(Actividades::class);
    }
}