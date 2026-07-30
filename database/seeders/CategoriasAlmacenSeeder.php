<?php

namespace Database\Seeders;

use App\Models\CategoriaAlmacen;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CategoriasAlmacenSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categorias = [
            [
                'nombre' => 'Reductores',
                'es_serializado' => true,
                'esquema_atributos' => ['serie', 'marca', 'generacion'],
            ],
            [
                'nombre' => 'Tanques',
                'es_serializado' => true,
                'esquema_atributos' => ['serie', 'marca', 'capacidad'],
            ],
            [
                'nombre' => 'Kits de inyección',
                'es_serializado' => true,
                'esquema_atributos' => ['serie', 'marca'],
            ],
            [
                'nombre' => 'Repuestos varios',
                'es_serializado' => false,
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
