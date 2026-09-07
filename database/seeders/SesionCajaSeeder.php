<?php

namespace Database\Seeders;

use App\Models\MovimientoCaja;
use App\Models\SesionCaja;
use App\Models\User;
use Illuminate\Database\Seeder;

class SesionCajaSeeder extends Seeder
{
    private array $metodosPago = ['efectivo', 'tarjeta', 'transferencia', 'fise'];

    public function run(): void
    {
        $users = User::all();

        if ($users->isEmpty()) return;

        $cajeros = $users->filter(fn ($u) => $u->hasRole('Cajero') || $u->hasRole('Administrador del sistema'));
        if ($cajeros->isEmpty()) $cajeros = $users->take(3);

        // Crear 30 sesiones en los últimos 60 días
        for ($i = 0; $i < 30; $i++) {
            $cajero = $cajeros->random();
            $diasAtras = $i * 2;
            $fechaApertura = now()->subDays($diasAtras)->setTime(rand(7, 9), rand(0, 59));
            $montoApertura = [100, 200, 300, 500][array_rand([100, 200, 300, 500])];

            $cerrada = $i > 0; // La primera queda abierta
            $fechaCierre = $cerrada ? $fechaApertura->copy()->addHours(rand(6, 10)) : null;

            // Calcular ingresos y egresos simulados
            $ingresos = rand(500, 5000);
            $egresos = rand(0, 500);
            $montoEsperado = $montoApertura + $ingresos - $egresos;
            // A veces hay descuadre intencional
            $diferencia = $cerrada ? ($i % 7 === 0 ? rand(-50, 50) : 0) : null;
            $montoCierre = $cerrada ? $montoEsperado + $diferencia : null;

            $sesion = SesionCaja::create([
                'abierta_por' => $cajero->id,
                'monto_apertura' => $montoApertura,
                'abierta_en' => $fechaApertura,
                'cerrada_en' => $fechaCierre,
                'monto_cierre' => $montoCierre,
                'monto_esperado' => $cerrada ? $montoEsperado : null,
                'diferencia' => $diferencia,
                'cerrada_por' => $cerrada ? $cajero->id : null,
                'estado' => $cerrada ? 'cerrada' : 'abierta',
            ]);

            // Crear movimientos para cada sesión
            $this->crearMovimientos($sesion, $fechaApertura, $cajero->id);
        }
    }

    private function crearMovimientos(SesionCaja $sesion, \Carbon\Carbon $fecha, int $userId): void
    {
        $conceptosIngreso = [
            'Cobro servicio simple',
            'Cobro conversión GNV',
            'Cobro conversión GLP',
            'Certificación vehicular',
            'Mantenimiento preventivo',
            'Cobro por评估',
            'Pago cliente contado',
        ];

        $conceptosEgreso = [
            'Compra de repuestos',
            'Pago a proveedor',
            'Gastos de oficina',
            'Reposición de efectivo',
            'Pago servicio básico',
            'Mantenimiento herramientas',
        ];

        // 3-8 ingresos por sesión
        $numIngresos = rand(3, 8);
        for ($j = 0; $j < $numIngresos; $j++) {
            $metodo = $this->metodosPago[array_rand($this->metodosPago)];
            $monto = rand(80, 2500);

            MovimientoCaja::create([
                'sesion_caja_id' => $sesion->id,
                'tipo' => 'ingreso',
                'metodo_pago' => $metodo,
                'monto' => $monto,
                'concepto' => $conceptosIngreso[array_rand($conceptosIngreso)],
                'usuario_id' => $userId,
                'created_at' => $fecha->copy()->addHours(rand(1, 8))->addMinutes(rand(0, 59)),
                'updated_at' => $fecha->copy()->addHours(rand(1, 8))->addMinutes(rand(0, 59)),
            ]);
        }

        // 0-3 egresos por sesión
        $numEgresos = rand(0, 3);
        for ($j = 0; $j < $numEgresos; $j++) {
            $monto = rand(20, 300);

            MovimientoCaja::create([
                'sesion_caja_id' => $sesion->id,
                'tipo' => 'egreso',
                'metodo_pago' => 'efectivo',
                'monto' => $monto,
                'concepto' => $conceptosEgreso[array_rand($conceptosEgreso)],
                'usuario_id' => $userId,
                'created_at' => $fecha->copy()->addHours(rand(2, 10))->addMinutes(rand(0, 59)),
                'updated_at' => $fecha->copy()->addHours(rand(2, 10))->addMinutes(rand(0, 59)),
            ]);
        }
    }
}
