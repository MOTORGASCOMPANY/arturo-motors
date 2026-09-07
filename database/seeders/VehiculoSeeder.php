<?php

namespace Database\Seeders;

use App\Models\Vehiculo;
use Illuminate\Database\Seeder;

class VehiculoSeeder extends Seeder
{
    public function run(): void
    {
        $marcas = ['Toyota', 'Hyundai', 'Kia', 'Nissan', 'Suzuki', 'Mazda', 'Mitsubishi', 'Chevrolet', 'Honda', 'Renault'];
        $modelos = ['Yaris', 'Accent', 'Rio', 'March', 'Swift', 'Mazda 2', 'Lancer', 'Spark', 'City', 'Duster'];
        $combustibles = ['Gasolina', 'GLP', 'GNV', 'Diésel'];
        $colores = ['Blanco', 'Negro', 'Gris', 'Plata', 'Rojo', 'Azul', 'Verde'];

        for ($i = 1; $i <= 30; $i++) {
            $marcaIndex = ($i - 1) % count($marcas);
            $anio = rand(2015, 2024);
            $placa = strtoupper(substr($marcas[$marcaIndex], 0, 2)) . '-' . rand(1000, 9999);

            Vehiculo::create([
                'placa' => $placa,
                'marca' => $marcas[$marcaIndex],
                'modelo' => $modelos[$marcaIndex],
                'anio' => $anio,
                'combustible' => $combustibles[array_rand($combustibles)],
                'serie' => strtoupper(bin2hex(random_bytes(6))),
                'color' => $colores[array_rand($colores)],
            ]);
        }
    }
}
