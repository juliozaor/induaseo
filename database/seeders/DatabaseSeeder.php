<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            TipoDocumentosSeeder::class,
            RolesAndAdminUserSeeder::class,
            PaisesTableSeeder::class,
            CiudadesTableSeeder::class,
            SectoresEconomicosSeeder::class,
            ClientesTableSeeder::class,
            FrecuenciaSeeder::class,
        ]);
    }
}
