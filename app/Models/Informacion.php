<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Informacion extends Model
{
    use HasFactory;
    protected $table = 'informacion';
    protected $fillable = [
        'url',
        'tipo_multimedia_id',
        'categoria_id',
        'fecha',
        'titulo',
        'descripcion',
        'sede_id'
    ];

    public function tipoMultimedia()
    {
        return $this->belongsTo(TipoMultimedias::class, 'tipo_multimedia_id');
    }

    public function sede()
    {
        return $this->belongsTo(Sede::class, 'sede_id');
    }

    public function categoria()
    {
        return $this->belongsTo(Categorias::class, 'categoria_id');
    }
}