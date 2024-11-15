<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PaisesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('paises')->insert([
            ['nombre' => 'Colombia'],
            ['nombre' => 'México'],
            ['nombre' => 'Argentina'],
        ]);
    }
}
