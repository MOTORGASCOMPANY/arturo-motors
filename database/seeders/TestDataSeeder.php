<?php

namespace Database\Seeders;

use App\Models\Cliente;
use App\Models\Comprobante;
use App\Models\MovimientoCaja;
use App\Models\SesionCaja;
use App\Models\Service;
use App\Models\ServiceOrder;
use App\Models\Vehiculo;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TestDataSeeder extends Seeder
{
    private array $metodosPago = ['efectivo', 'tarjeta', 'transferencia', 'fise'];

    public function run(): void
    {
        $clientes = Cliente::all();
        $vehiculos = Vehiculo::all();
        $servicios = Service::all();

        $userId1 = 1; // Felipe Guerrero 417 (admin)
        $userId2 = 2; // Felipe Guerrero 419 (tecnico)

        // Semana: lunes 31 ago - viernes 4 sep 2026
        $diasSemana = [
            now()->setDate(2026, 8, 31)->startOfDay(), // lunes
            now()->setDate(2026, 9, 1)->startOfDay(),  // martes
            now()->setDate(2026, 9, 2)->startOfDay(),  // miércoles
            now()->setDate(2026, 9, 3)->startOfDay(),  // jueves
            now()->setDate(2026, 9, 4)->startOfDay(),  // viernes (hoy)
        ];

        // ========================================
        // 1. SERVICE ORDERS (35 órdenes mixtas)
        // ========================================
        $folio = 1000;
        $ordenes = [];

        for ($i = 0; $i < 35; $i++) {
            $dia = $diasSemana[array_rand($diasSemana)];
            $servicio = $servicios->random();
            $cliente = $clientes->random();
            $vehiculo = $vehiculos->random();

            $precioLista = (float) $servicio->precio_base;
            $descuento = $i % 6 === 0 ? rand(10, 25) : 0;
            $precioFinal = round($precioLista - ($precioLista * $descuento / 100), 2);

            $creadoEn = $dia->copy()->addHours(rand(8, 16))->addMinutes(rand(0, 59));

            // Estado:mayoría finalizadas/entregadas para que haya comprobantes
            $estados = ['creada', 'evaluada', 'en_proceso', 'finalizada', 'entregada'];
            $estado = $i < 5 ? 'creada' : ($i < 10 ? 'evaluada' : 'entregada');

            $orden = ServiceOrder::create([
                'cliente_id' => $cliente->id,
                'vehiculo_id' => $vehiculo->id,
                'service_id' => $servicio->id,
                'estado' => $estado,
                'precio_lista' => $precioLista,
                'precio_final' => $precioFinal,
                'descuento_motivo' => $descuento > 0 ? 'Descuento especial' : null,
                'tecnico_id' => $userId2,
                'creado_por' => $userId1,
                'created_at' => $creadoEn,
                'updated_at' => $creadoEn,
            ]);

            $ordenes[] = $orden;

            // Comprobante para finalizadas/entregadas
            if (in_array($estado, ['finalizada', 'entregada'])) {
                $folio++;
                Comprobante::create([
                    'service_order_id' => $orden->id,
                    'folio' => 'F-' . $folio,
                    'monto' => $precioFinal,
                    'metodo_pago' => $this->metodosPago[array_rand($this->metodosPago)],
                    'emitido_por' => $userId1,
                    'created_at' => $creadoEn->copy()->addHours(rand(1, 4)),
                    'updated_at' => $creadoEn->copy()->addHours(rand(1, 4)),
                ]);
            }
        }

        // ========================================
        // 2. SESIONES DE CAJA (10 sesiones, 2 por día)
        // ========================================
        for ($d = 0; $d < 5; $d++) {
            $dia = $diasSemana[$d];

            // Sesión mañana
            $aperturaManana = $dia->copy()->setTime(8, rand(0, 30));
            $cierreManana = $aperturaManana->copy()->addHours(rand(5, 8));
            $montoApertura = [100, 200, 300][array_rand([100, 200, 300])];

            $sesionM = SesionCaja::create([
                'abierta_por' => $userId1,
                'monto_apertura' => $montoApertura,
                'abierta_en' => $aperturaManana,
                'cerrada_en' => $cierreManana,
                'monto_cierre' => null, // se calcula abajo
                'monto_esperado' => null,
                'diferencia' => null,
                'cerrada_por' => $userId1,
                'estado' => 'cerrada',
            ]);

            $ingresosM = $this->crearMovimientos($sesionM, $aperturaManana, $userId1);
            $egresosM = $this->crearEgresos($sesionM, $aperturaManana, $userId1);
            $esperadoM = $montoApertura + $ingresosM - $egresosM;
            $diferenciaM = ($d === 2) ? rand(-30, 30) : 0; // miércoles con descuadre
            $sesionM->update([
                'monto_esperado' => $esperadoM,
                'monto_cierre' => $esperadoM + $diferenciaM,
                'diferencia' => $diferenciaM,
            ]);

            // Sesión tarde
            $aperturaTarde = $dia->copy()->setTime(14, rand(0, 30));
            $cierreTarde = $aperturaTarde->copy()->addHours(rand(4, 7));
            $montoApertura2 = [150, 250, 400][array_rand([150, 250, 400])];

            $sesionT = SesionCaja::create([
                'abierta_por' => $userId1,
                'monto_apertura' => $montoApertura2,
                'abierta_en' => $aperturaTarde,
                'cerrada_en' => $cierreTarde,
                'monto_cierre' => null,
                'monto_esperado' => null,
                'diferencia' => null,
                'cerrada_por' => $userId1,
                'estado' => 'cerrada',
            ]);

            $ingresosT = $this->crearMovimientos($sesionT, $aperturaTarde, $userId1);
            $egresosT = $this->crearEgresos($sesionT, $aperturaTarde, $userId1);
            $esperadoT = $montoApertura2 + $ingresosT - $egresosT;
            $diferenciaT = ($d === 4) ? rand(-20, 20) : 0; // viernes con descuadre
            $sesionT->update([
                'monto_esperado' => $esperadoT,
                'monto_cierre' => $esperadoT + $diferenciaT,
                'diferencia' => $diferenciaT,
            ]);
        }
    }

    private function crearMovimientos(SesionCaja $sesion, \Carbon\Carbon $fecha, int $userId): float
    {
        $conceptos = [
            'Cobro servicio simple',
            'Cobro conversión GNV',
            'Cobro conversión GLP',
            'Certificación vehicular',
            'Mantenimiento preventivo',
            'Cobro evaluación',
            'Pago por revisión',
        ];

        $total = 0;
        $numMovimientos = rand(4, 8);

        for ($j = 0; $j < $numMovimientos; $j++) {
            $metodo = $this->metodosPago[array_rand($this->metodosPago)];
            $monto = rand(100, 1800);
            $total += $monto;

            MovimientoCaja::create([
                'sesion_caja_id' => $sesion->id,
                'tipo' => 'ingreso',
                'metodo_pago' => $metodo,
                'monto' => $monto,
                'concepto' => $conceptos[array_rand($conceptos)],
                'usuario_id' => $userId,
                'created_at' => $fecha->copy()->addHours(rand(1, 6))->addMinutes(rand(0, 59)),
                'updated_at' => $fecha->copy()->addHours(rand(1, 6))->addMinutes(rand(0, 59)),
            ]);
        }

        return $total;
    }

    private function crearEgresos(SesionCaja $sesion, \Carbon\Carbon $fecha, int $userId): float
    {
        $conceptos = [
            'Compra de repuestos',
            'Pago a proveedor',
            'Gastos de oficina',
            'Reposición de efectivo',
            'Pago servicio básico',
        ];

        $total = 0;
        $numEgresos = rand(0, 2);

        for ($j = 0; $j < $numEgresos; $j++) {
            $monto = rand(30, 200);
            $total += $monto;

            MovimientoCaja::create([
                'sesion_caja_id' => $sesion->id,
                'tipo' => 'egreso',
                'metodo_pago' => 'efectivo',
                'monto' => $monto,
                'concepto' => $conceptos[array_rand($conceptos)],
                'usuario_id' => $userId,
                'created_at' => $fecha->copy()->addHours(rand(2, 8))->addMinutes(rand(0, 59)),
                'updated_at' => $fecha->copy()->addHours(rand(2, 8))->addMinutes(rand(0, 59)),
            ]);
        }

        return $total;
    }
}
