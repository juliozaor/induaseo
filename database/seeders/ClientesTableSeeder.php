<?php

namespace Database\Seeders;

use App\Models\Cliente;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ClientesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Cliente::create([
            'tipo_documento_id' => 1,
            'numero_documento' => '12345678',
            'ciudad_id' => 1,
            'creador_id' => 1,
            'actualizador_id' => 1,
            'sector_economico_id' => 1,
            'nombre' => 'Cliente Ejemplo',
            'direccion' => 'Dirección Ejemplo',
            'correo' => 'ejemplo@correo.com',
            'celular' => '3001234567',
            'estado' => 1
        ]);

        // Nuevo cliente
        Cliente::create([
            'tipo_documento_id' => 2,
            'numero_documento' => '87654321',
            'ciudad_id' => 1,
            'creador_id' => 1,
            'actualizador_id' => 1,
            'sector_economico_id' => 1,
            'nombre' => 'Cliente Ejemplo 2',
            'direccion' => 'Dirección Ejemplo 2',
            'correo' => 'ejemplo2@correo.com',
            'celular' => '3007654321',
            'estado' => 1
        ]);
    }
}
