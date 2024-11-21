<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ClasificacionesSeeder extends Seeder
{
    public function run()
    {
        DB::table('clasificaciones')->insert([
            ['nombre' => 'propio', 'estado' => true],
            ['nombre' => 'alquilado', 'estado' => true],
        ]);
    }
}