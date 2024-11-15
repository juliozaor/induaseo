<?php

namespace Database\Seeders;

use App\Models\SectoresEconomico;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SectoresEconomicosSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $sectores = [
            ['nombre' => 'Aeronáutico'],
            ['nombre' => 'Agencia'],
            ['nombre' => 'Agropecuario'],
            ['nombre' => 'Alimentos'],
            ['nombre' => 'Asegurador'],
            ['nombre' => 'Tecnología'],
        ];

        foreach ($sectores as $sector) {
            SectoresEconomico::firstOrCreate(['nombre' => $sector['nombre']]);
        }
    }
}
