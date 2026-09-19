<?php

namespace Database\Seeders;

use App\Models\CategoriaAlmacen;
use Illuminate\Database\Seeder;

class CategoriasAlmacenSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categorias = [
            // Serializados
            [
                'nombre' => 'Reductores',
                'es_serializado' => true,
                'esquema_atributos' => ['serie', 'marca', 'generacion', 'produce'],
            ],
            [
                'nombre' => 'Tanques',
                'es_serializado' => true,
                'esquema_atributos' => ['serie', 'marca', 'capacidad', 'produce'],
            ],
            [
                'nombre' => 'Computadoras',
                'es_serializado' => true,
                'esquema_atributos' => ['serie'],
            ],
            // Por cantidad
            [
                'nombre' => 'Kits',
                'es_serializado' => false,
                'es_kit' => true,
                'esquema_atributos' => null,
            ],
            [
                'nombre' => 'Componentes Kit',
                'es_serializado' => false,
                'es_kit' => false,
                'esquema_atributos' => null,
            ],
        ];

        foreach ($categorias as $categoria) {
            CategoriaAlmacen::updateOrCreate(
                ['nombre' => $categoria['nombre']],
                $categoria
            );
        }
    }
}
