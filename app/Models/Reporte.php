<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reporte extends Model
{
    use HasFactory;

    protected $fillable = ['tipo_reporte', 'cliente_id', 'centro_trabajo_id', 'supervisor_id', 'fecha', 'estado', 'formato'];

    public function cliente()
    {
        return $this->belongsTo(Cliente::class);
    }

    public function centroDeTrabajo()
    {
        return $this->belongsTo(CentroDeTrabajo::class);
    }

    public function supervisor()
    {
        return $this->belongsTo(Usuario::class, 'supervisor_id');
    }
}
