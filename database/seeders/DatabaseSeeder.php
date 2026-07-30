<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        /*ser::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);*/
    
        // Crea el usuario solo si no existe
        User::firstOrCreate(
            ['email' => 'test@example.com'],
            [
                'name' => 'Test User',
                'password' => bcrypt('password'), // Aseguras una contraseña de prueba
            ]
        );

        $this->call([
            RoleSeeder::class,
            ServicesSeeder::class,
            CategoriasAlmacenSeeder::class,
            TipoDocumentoSeeder::class,
        ]);
    }
}
