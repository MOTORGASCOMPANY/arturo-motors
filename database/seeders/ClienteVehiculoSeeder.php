<?php

namespace Database\Seeders;

use App\Models\Cliente;
use App\Models\Vehiculo;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ClienteVehiculoSeeder extends Seeder
{
    public function run(): void
    {
        $clientes = Cliente::all();
        $vehiculos = Vehiculo::all();

        // Asignar un vehículo principal a cada cliente
        foreach ($clientes as $i => $cliente) {
            if ($i >= $vehiculos->count()) break;

            DB::table('cliente_vehiculo')->insert([
                'cliente_id' => $cliente->id,
                'vehiculo_id' => $vehiculos[$i]->id,
                'es_principal' => true,
                'relacion' => 'Propietario',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // Algunos clientes con segundo vehículo (copropiedad)
        $extras = [
            [1, 15], [3, 20], [5, 25], [7, 28], [10, 12],
            [12, 18], [15, 22], [18, 30], [20, 10], [25, 5],
        ];

        foreach ($extras as [$clienteId, $vehiculoId]) {
            DB::table('cliente_vehiculo')->insert([
                'cliente_id' => $clienteId,
                'vehiculo_id' => $vehiculoId,
                'es_principal' => false,
                'relacion' => 'Copropietario',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
