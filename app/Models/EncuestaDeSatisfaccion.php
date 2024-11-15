<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EncuestaDeSatisfaccion extends Model
{
    use HasFactory;

    protected $fillable = ['preguntas', 'cliente_id', 'fecha', 'resultado'];

    public function cliente()
    {
        return $this->belongsTo(Cliente::class);
    }
}
