<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Perfil extends Model
{
    use HasFactory;

    protected $fillable = ['nombre_perfil'];

    public function usuarios()
    {
        return $this->hasMany(Usuario::class);
    }
}
