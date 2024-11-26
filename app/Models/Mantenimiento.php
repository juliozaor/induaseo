<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Mantenimiento extends Model
{
    use HasFactory;

    protected $table = 'mantenimientos';
    protected $fillable = ['sede_id', 'descripcion', 'fecha'];

    public function sede()
    {
        return $this->belongsTo(Sede::class);
    }
}
