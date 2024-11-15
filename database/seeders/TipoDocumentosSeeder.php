<?php

namespace Database\Seeders;

use App\Models\TiposDocumento;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TipoDocumentosSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $tipos = [
            ['id' => 1, 'nombre' => 'Cédula de ciudadanía'],
            ['id' => 2, 'nombre' => 'Tarjeta de extranjería'],
            ['id' => 3, 'nombre' => 'Cédula de extranjería'],
            ['id' => 4, 'nombre' => 'Número de identificación tributaria'],
            ['id' => 5, 'nombre' => 'Pasaporte'],
            ['id' => 6, 'nombre' => 'Permiso especial de permanencia'],
            ['id' => 7, 'nombre' => 'Documento de identificación extranjero'],
            ['id' => 8, 'nombre' => 'NUIP'],
        ];

        foreach ($tipos as $tipo) {
            TiposDocumento::firstOrCreate(['nombre' => $tipo['nombre']], ['id' => $tipo['id']]);
        }
    }
}
