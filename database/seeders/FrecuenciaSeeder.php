<?php

namespace Database\Seeders;

use App\Models\Frecuencia;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class FrecuenciaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $frecuencias = [
            ['nombre' => 'Diaria', 'detalle' => 1, 'estado' => 1],
            ['nombre' => 'Quincenal', 'detalle' => 15, 'estado' => 1],
            ['nombre' => 'Mensual', 'detalle' => 30, 'estado' => 1],
            ['nombre' => 'Trimestral', 'detalle' => 90, 'estado' => 1],
            ['nombre' => 'Otro', 'detalle' => null, 'estado' => 1],
        ];
    
        foreach ($frecuencias as $frecuencia) {
            Frecuencia::firstOrCreate(['nombre' => $frecuencia['nombre']], $frecuencia);
        }
    }
}
