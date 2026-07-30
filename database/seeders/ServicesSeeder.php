<?php

namespace Database\Seeders;

use App\Models\Service;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ServicesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $servicios = [
            ['nombre' => 'Conversión GLP', 'tipo' => 'conversion', 'precio_base' => 800.00],
            ['nombre' => 'Conversión GNV', 'tipo' => 'conversion', 'precio_base' => 900.00],
            ['nombre' => 'Servicio de garantía', 'tipo' => 'simple', 'precio_base' => 0.00],
            ['nombre' => 'Anual GLP', 'tipo' => 'simple', 'precio_base' => 50.00],
            ['nombre' => 'Anual GNV', 'tipo' => 'simple', 'precio_base' => 50.00],
            ['nombre' => 'Prueba a Quinquenal', 'tipo' => 'simple', 'precio_base' => 250.00],
            ['nombre' => 'Mantenimiento externo de GNV', 'tipo' => 'simple', 'precio_base' => 160.00],
        ];

        foreach ($servicios as $servicio) {
            Service::updateOrCreate(
                ['nombre' => $servicio['nombre']],
                $servicio + ['activo' => true]
            );
        }
    }
}
