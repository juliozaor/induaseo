<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class Usuario extends Authenticatable
{
    use HasFactory, Notifiable, HasRoles;

    /**
     * Los atributos que se pueden asignar en masa.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'tipo_documento_id',
        'numero_documento',
        'nombres',
        'apellidos',
        'fecha_nacimiento',
        'telefono',
        'email',
        'cargo',
        'password',
        'estado',
        'rol'
    ];

    /**
     * Relación con la tabla maestra de tipos de documentos.
     */
    public function tipoDocumento()
    {
        return $this->belongsTo(TiposDocumento::class, 'tipo_documento_id');
    }

    /* public function rol()
    {
        return $this->belongsTo(Roles::class, 'rol');
    }
 */
    public function clientes()
    {
        return $this->belongsToMany(Cliente::class, 'cliente_usuario');
    }

    /**
     * Ocultar atributos para arrays.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Los atributos que se deben convertir a tipos nativos.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'fecha_nacimiento' => 'date',
    ];
}
