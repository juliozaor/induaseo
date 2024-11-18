<?php
namespace Spatie\Permission\Models;

use Illuminate\Database\Eloquent\Model;

class Roles extends Model
{
    protected $fillable = ['name', 'guard_name'];
}
