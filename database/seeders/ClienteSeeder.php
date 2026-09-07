<?php

namespace Database\Seeders;

use App\Models\Cliente;
use Illuminate\Database\Seeder;

class ClienteSeeder extends Seeder
{
    public function run(): void
    {
        $nombres = [
            ['nombre' => 'Carlos', 'apellido' => 'Mendoza', 'documento' => '45123678', 'telefono' => '998123456', 'email' => 'carlos.mendoza@gmail.com'],
            ['nombre' => 'María', 'apellido' => 'Torres', 'documento' => '46234789', 'telefono' => '998234567', 'email' => 'maria.torres@hotmail.com'],
            ['nombre' => 'Juan', 'apellido' => 'Ramírez', 'documento' => '47345890', 'telefono' => '998345678', 'email' => 'juan.ramirez@outlook.com'],
            ['nombre' => 'Ana', 'apellido' => 'García', 'documento' => '48456901', 'telefono' => '998456789', 'email' => 'ana.garcia@gmail.com'],
            ['nombre' => 'Pedro', 'apellido' => 'Sánchez', 'documento' => '49567012', 'telefono' => '998567890', 'email' => 'pedro.sanchez@yahoo.com'],
            ['nombre' => 'Lucía', 'apellido' => 'Flores', 'documento' => '41678123', 'telefono' => '998678901', 'email' => 'lucia.flores@gmail.com'],
            ['nombre' => 'Miguel', 'apellido' => 'Rivera', 'documento' => '42789234', 'telefono' => '998789012', 'email' => 'miguel.rivera@hotmail.com'],
            ['nombre' => 'Rosa', 'apellido' => 'Díaz', 'documento' => '43890345', 'telefono' => '998890123', 'email' => 'rosa.diaz@gmail.com'],
            ['nombre' => 'Fernando', 'apellido' => 'Cruz', 'documento' => '44901456', 'telefono' => '998901234', 'email' => 'fernando.cruz@outlook.com'],
            ['nombre' => 'Patricia', 'apellido' => 'López', 'documento' => '45012567', 'telefono' => '999012345', 'email' => 'patricia.lopez@gmail.com'],
            ['nombre' => 'Ricardo', 'apellido' => 'Morales', 'documento' => '46123678', 'telefono' => '999123456', 'email' => 'ricardo.morales@hotmail.com'],
            ['nombre' => 'Sandra', 'apellido' => 'Herrera', 'documento' => '47234789', 'telefono' => '999234567', 'email' => 'sandra.herrera@gmail.com'],
            ['nombre' => 'Jorge', 'apellido' => 'Castro', 'documento' => '48345890', 'telefono' => '999345678', 'email' => 'jorge.castro@yahoo.com'],
            ['nombre' => 'Claudia', 'apellido' => 'Vargas', 'documento' => '49456901', 'telefono' => '999456789', 'email' => 'claudia.vargas@gmail.com'],
            ['nombre' => 'Luis', 'apellido' => 'Ramos', 'documento' => '40567012', 'telefono' => '999567890', 'email' => 'luis.ramos@outlook.com'],
            ['nombre' => 'Teresa', 'apellido' => 'Silva', 'documento' => '41678124', 'telefono' => '999678901', 'email' => 'teresa.silva@gmail.com'],
            ['nombre' => 'Alberto', 'apellido' => 'Medina', 'documento' => '42789235', 'telefono' => '999789012', 'email' => 'alberto.medina@hotmail.com'],
            ['nombre' => 'Carmen', 'apellido' => 'Rojas', 'documento' => '43890346', 'telefono' => '999890123', 'email' => 'carmen.rojas@gmail.com'],
            ['nombre' => 'Roberto', 'apellido' => 'Guzmán', 'documento' => '44901457', 'telefono' => '999901234', 'email' => 'roberto.guzman@yahoo.com'],
            ['nombre' => 'Gloria', 'apellido' => 'Peña', 'documento' => '45012568', 'telefono' => '980123456', 'email' => 'gloria.pena@gmail.com'],
            ['nombre' => 'Francisco', 'apellido' => 'Torres', 'documento' => '46123679', 'telefono' => '981234567', 'email' => 'francisco.torres@outlook.com'],
            ['nombre' => 'Beatriz', 'apellido' => 'Córdova', 'documento' => '47234780', 'telefono' => '982345678', 'email' => 'beatriz.cordova@gmail.com'],
            ['nombre' => 'Eduardo', 'apellido' => 'Salazar', 'documento' => '48345891', 'telefono' => '983456789', 'email' => 'eduardo.salazar@hotmail.com'],
            ['nombre' => 'Verónica', 'apellido' => 'Aguilar', 'documento' => '49456902', 'telefono' => '984567890', 'email' => 'veronica.aguilar@gmail.com'],
            ['nombre' => 'Raúl', 'apellido' => 'Fernández', 'documento' => '40567013', 'telefono' => '985678901', 'email' => 'raul.fernandez@yahoo.com'],
            ['nombre' => 'Diana', 'apellido' => 'Mendoza', 'documento' => '41678125', 'telefono' => '986789012', 'email' => 'diana.mendoza@gmail.com'],
            ['nombre' => 'Sergio', 'apellido' => 'Vega', 'documento' => '42789236', 'telefono' => '987890123', 'email' => 'sergio.vega@outlook.com'],
            ['nombre' => 'Pilar', 'apellido' => 'Reyes', 'documento' => '43890347', 'telefono' => '988901234', 'email' => 'pilar.reyes@gmail.com'],
            ['nombre' => 'Andrés', 'apellido' => 'Campos', 'documento' => '44901458', 'telefono' => '989012345', 'email' => 'andres.campos@hotmail.com'],
            ['nombre' => 'Natalia', 'apellido' => 'Ortega', 'documento' => '45012569', 'telefono' => '990123456', 'email' => 'natalia.ortega@gmail.com'],
        ];

        foreach ($nombres as $i => $cliente) {
            Cliente::create(array_merge($cliente, [
                'tipo_persona' => 'NATURAL',
                'direccion' => 'Av. ' . ['Los Olivos', 'San Martín', 'La Molina', 'Pueblo Libre', 'Jesús María'][$i % 5] . ' ' . rand(100, 9999),
            ]));
        }
    }
}
