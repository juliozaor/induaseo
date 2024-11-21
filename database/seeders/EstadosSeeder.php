<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EstadosSeeder extends Seeder
{
    public function run()
    {
        DB::table('estados')->insert([
            ['nombre' => 'bueno', 'estado' => true],
            ['nombre' => 'malo', 'estado' => true],
            ['nombre' => 'falló', 'estado' => true],
            ['nombre' => 'dado de baja', 'estado' => true],
            ['nombre' => 'activo', 'estado' => true],
            ['nombre' => 'inactivo', 'estado' => true],
        ]);
    }
}