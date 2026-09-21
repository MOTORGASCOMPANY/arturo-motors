<?php

namespace Database\Seeders;

use App\Models\Sede;
use Illuminate\Database\Seeder;

class SedeSeeder extends Seeder
{
    public function run(): void
    {
        $sedes = [
            ['nombre' => 'Sede Principal - San Isidro', 'direccion' => 'Av. Javier Prado Este 4200, San Isidro', 'telefono' => '01-555-1001', 'estado' => true],
            ['nombre' => 'Sede Sur - Chorrillos', 'direccion' => 'Av. Walter Bruenger 1450, Chorrillos', 'telefono' => '01-555-1002', 'estado' => true],
            ['nombre' => 'Sede Norte - Comas', 'direccion' => 'Av. Túpac Amaru 3200, Comas', 'telefono' => '01-555-1003', 'estado' => true],
        ];

        foreach ($sedes as $sede) {
            Sede::create($sede);
        }
    }
}
