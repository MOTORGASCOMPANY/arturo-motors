<?php

namespace Database\Seeders;

use App\Models\Cliente;
use App\Models\Comprobante;
use App\Models\ServiceOrder;
use App\Models\Service;
use App\Models\User;
use App\Models\Vehiculo;
use Illuminate\Database\Seeder;

class ServiceOrderSeeder extends Seeder
{
    private array $metodosPago = ['efectivo', 'tarjeta', 'transferencia', 'fise'];

    /**
     * Estados reales que escribe la aplicación. No inventar vocabulario:
     * el reporte de servicios y los KPIs se leen de esta misma lista.
     * (Antes usaba evaluada/en_proceso/finalizada/entregada, que no existen en el pipeline.)
     */
    private array $estadosConversion = [
        'creada',
        'en_evaluacion',
        'evaluacion_rechazada',
        'aprobado_conversion',
        'en_conversion',
        'conversion_completada',
        ServiceOrder::ESTADO_ENTREGADO,
    ];

    private array $estadosSimple = ['creada', ServiceOrder::ESTADO_ENTREGADO, 'cancelada'];

    public function run(): void
    {
        $clientes = Cliente::all();
        $vehiculos = Vehiculo::all();
        $users = User::all();
        $servicios = Service::all();

        if ($clientes->isEmpty() || $vehiculos->isEmpty() || $users->isEmpty() || $servicios->isEmpty()) {
            return;
        }

        $vendedores = $users->filter(fn ($u) => $u->hasRole('Vendedor'));
        $tecnicos = $users->filter(fn ($u) => $u->hasRole('Tecnico'));
        $cajeros = $users->filter(fn ($u) => $u->hasRole('Cajero'));

        if ($vendedores->isEmpty()) $vendedores = $users;
        if ($tecnicos->isEmpty()) $tecnicos = $users;
        if ($cajeros->isEmpty()) $cajeros = $users;

        $folio = 1000;

        for ($i = 0; $i < 30; $i++) {
            $cliente = $clientes->random();
            $vehiculo = $vehiculos->random();
            $servicio = $servicios->random();
            $creadoPor = $vendedores->random();
            $tecnico = $tecnicos->random();
            $estados = $servicio->tipo === 'conversion' ? $this->estadosConversion : $this->estadosSimple;
            $estado = $estados[array_rand($estados)];

            $precioLista = (float) $servicio->precio_base;
            $descuento = $i % 5 === 0 ? rand(10, 20) : 0;
            $precioFinal = $precioLista - ($precioLista * $descuento / 100);

            $diasAtras = rand(0, 60);
            $fechaCreacion = now()->subDays($diasAtras)->subHours(rand(0, 12));

            $orden = ServiceOrder::create([
                'cliente_id' => $cliente->id,
                'vehiculo_id' => $vehiculo->id,
                'service_id' => $servicio->id,
                'estado' => $estado,
                'precio_lista' => $precioLista,
                'precio_final' => $precioFinal,
                'descuento_motivo' => $descuento > 0 ? 'Descuento por antigüedad' : null,
                'tecnico_id' => $tecnico->id,
                'creado_por' => $creadoPor->id,
                'created_at' => $fechaCreacion,
                'updated_at' => $fechaCreacion,
            ]);

            // Comprobante solo para órdenes efectivamente cobradas y entregadas
            if ($estado === ServiceOrder::ESTADO_ENTREGADO) {
                $metodo = $this->metodosPago[array_rand($this->metodosPago)];
                $folio++;

                Comprobante::create([
                    'service_order_id' => $orden->id,
                    'folio' => 'F-' . $folio,
                    'monto' => $precioFinal,
                    'metodo_pago' => $metodo,
                    'emitido_por' => $cajeros->random()->id,
                    'created_at' => $fechaCreacion->addHours(rand(1, 48)),
                    'updated_at' => $fechaCreacion->addHours(rand(1, 48)),
                ]);
            }
        }
    }
}
