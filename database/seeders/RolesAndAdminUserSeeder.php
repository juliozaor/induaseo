<?php

namespace Database\Seeders;

use App\Models\TipoDocumento;
use Illuminate\Database\Seeder;
use App\Models\Usuario;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;

class RolesAndAdminUserSeeder extends Seeder
{
    public function run()
    {
         // Crear roles solo si no existen
         $adminRole = Role::firstOrCreate(['name' => 'Administrador']);
         $supervisorRole = Role::firstOrCreate(['name' => 'Supervisor']);
         $clientRole = Role::firstOrCreate(['name' => 'Cliente']);
 
         // Crear usuario administrador
         $adminUser = Usuario::create([
             'tipo_documento_id' => 1, // Asegúrate de que exista un tipo_documento con ID 1 en la tabla `tipos_documentos`
             'numero_documento' => '123456789',
             'nombres' => 'Admin',
             'apellidos' => 'User',
             'fecha_nacimiento' => '1980-01-01',
             'telefono' => '123456789',
             'email' => 'juliojimmeza@gmail.com',
             'cargo' => 'Administrador',
             'password' => Hash::make('123456789'), // Cambia a una contraseña segura
         ]);
 
         // Asignar el rol de Administrador al usuario
         $adminUser->assignRole($adminRole);
    }
}
