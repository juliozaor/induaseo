<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CiudadesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Obtener los IDs de los países
        $colombiaId = DB::table('paises')->where('nombre', 'Colombia')->value('id');
        $mexicoId = DB::table('paises')->where('nombre', 'México')->value('id');
        $argentinaId = DB::table('paises')->where('nombre', 'Argentina')->value('id');

        DB::table('ciudades')->insert([
            ['nombre' => 'Bogotá', 'pais_id' => $colombiaId],
            ['nombre' => 'Medellín', 'pais_id' => $colombiaId],
            ['nombre' => 'Ciudad de México', 'pais_id' => $mexicoId],
            ['nombre' => 'Guadalajara', 'pais_id' => $mexicoId],
            ['nombre' => 'Buenos Aires', 'pais_id' => $argentinaId],
            ['nombre' => 'Córdoba', 'pais_id' => $argentinaId],
        ]);
    }
}
