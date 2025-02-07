<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ImagenInventario extends Model
{
    use HasFactory;
    protected $table = 'imagenes_inventario';

    protected $fillable = [
        'inventario_id',
        'imagen',
    ];

    public function inventario()
    {
        return $this->belongsTo(Inventario::class);
    }
}
