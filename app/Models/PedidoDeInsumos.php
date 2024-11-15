<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PedidoDeInsumos extends Model
{
    use HasFactory;

    protected $fillable = ['descripcion', 'cantidad', 'centro_trabajo_id', 'fecha_pedido', 'estado'];

    public function centroDeTrabajo()
    {
        return $this->belongsTo(CentroDeTrabajo::class);
    }
}
