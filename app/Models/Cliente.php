<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cliente extends Model
{
    use HasFactory;

    protected $fillable = [
        'tipo_documento_id',
        'numero_documento',
        'ciudad_id',
        'creador_id',
        'actualizador_id',
        'sector_economico_id',
        'nombre',
        'direccion',
        'correo',
        'celular',
        'estado'
    ];

    public function ciudad()
    {
        return $this->belongsTo(ciudades::class);
    }

    public function tipoDocumento()
    {
        return $this->belongsTo(tiposDocumento::class, 'tipo_documento_id');
    }

    public function sectorEconomico()
    {
        return $this->belongsTo(SectoresEconomico::class, 'sector_economico_id');
    }

    public function centrosDeTrabajo()
    {
        return $this->hasMany(CentroDeTrabajo::class);
    }

    public function encuestasDeSatisfaccion()
    {
        return $this->hasMany(EncuestaDeSatisfaccion::class);
    }

    public function reportes()
    {
        return $this->hasMany(Reporte::class);
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
