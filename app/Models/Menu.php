<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Menu extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'route', 'icon'];

    public function roles()
    {
        return $this->belongsToMany(Role::class);
    }
}