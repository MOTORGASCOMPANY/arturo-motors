<?php

namespace Database\Seeders;

use App\Models\Sede;
use Illuminate\Database\Seeder;

class SedeSeeder extends Seeder
{
    public function run(): void
    {
        $sedes = [
            [
                'nombre' => 'Callao',
                'direccion' => 'Av. Perú N.° 5176, Callao',
                'telefono' => '01-555-1001',
                'estado' => true,
            ],
            [
                'nombre' => 'Ancón',
                'direccion' => 'Panamericana Norte Km 35, Ancón',
                'telefono' => '01-555-1002',
                'estado' => true,
            ],
            [
                'nombre' => 'Villa María',
                'direccion' => 'Av. Los Precursores 1200, Villa María del Triunfo',
                'telefono' => '01-555-1003',
                'estado' => true,
            ],
        ];

        foreach ($sedes as $sede) {
            Sede::updateOrCreate(
                ['nombre' => $sede['nombre']],
                $sede
            );
        }
    }
}
