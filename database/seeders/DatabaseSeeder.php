<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Seeders base (ya existentes)
        $this->call([
            RoleSeeder::class,
            ServicesSeeder::class,
            CategoriasAlmacenSeeder::class,
            TipoDocumentoSeeder::class,
        ]);

        // Seeders de datos de prueba (nuevos)
        $this->call([
            SedeSeeder::class,
            ClienteSeeder::class,
            VehiculoSeeder::class,
            ClienteVehiculoSeeder::class,
            ServiceOrderSeeder::class,
            SesionCajaSeeder::class,
        ]);
    }
}
