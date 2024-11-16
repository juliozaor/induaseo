<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Regionales;

class RegionalesSeeder extends Seeder
{
    public function run()
    {
        $regionales = [
            ['nombre' => 'Regional Cundinamarca', 'estado' => true],
            ['nombre' => 'Regional Caldas', 'estado' => true],
            ['nombre' => 'Regional Casanares', 'estado' => true],
            ['nombre' => 'No tiene', 'estado' => false],
        ];

        foreach ($regionales as $regional) {
            Regionales::create($regional);
        }
    }
}
