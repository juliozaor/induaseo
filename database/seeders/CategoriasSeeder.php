<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CategoriasSeeder extends Seeder
{
    public function run()
    {
        DB::table('categorias')->insert([
            ['nombre' => 'noticia', 'descripcion' => '', 'estado' => true],
            ['nombre' => 'boletin', 'descripcion' => '', 'estado' => true],
            ['nombre' => 'comunicado', 'descripcion' => '', 'estado' => true],
            ['nombre' => 'manual de operación', 'descripcion' => '', 'estado' => true],
        ]);
    }
}