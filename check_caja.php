<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\SesionCaja;
use App\Models\MovimientoCaja;

echo "=== SESIONES CERRADAS CON DIFERENCIA ===\n";
$sesiones = SesionCaja::where('estado', 'cerrada')
    ->with('abiertaPor')
    ->orderByDesc('cerrada_en')
    ->get();

$totalDiferencia = 0;
foreach ($sesiones as $s) {
    $dif = (float) $s->diferencia;
    $totalDiferencia += $dif;
    echo "ID:{$s->id} | Apertura:S/{$s->monto_apertura} | Esperado:S/{$s->monto_esperado} | Cierre:S/{$s->monto_cierre} | Dif:S/{$s->diferencia} | Cajero:{$s->abiertaPor?->name} | {$s->cerrada_en}\n";
}

echo "\n=== TOTAL DIFERENCIAS ACUMULADAS: S/{$totalDiferencia} ===\n";

echo "\n=== SESION ACTUAL (ABIERTA) ===\n";
$abierta = SesionCaja::where('estado', 'abierta')->with('movimientos', 'abiertaPor')->first();
if ($abierta) {
    echo "ID:{$abierta->id} | Apertura:S/{$abierta->monto_apertura} | Cajero:{$abierta->abiertaPor?->name}\n";
    
    $ingresos = $abierta->movimientos->where('tipo', 'ingreso');
    $egresos = $abierta->movimientos->where('tipo', 'egreso');
    
    echo "\n--- INGRESOS ---\n";
    $totalIng = 0;
    foreach ($ingresos as $m) {
        echo "  {$m->metodo_pago} | S/{$m->monto} | {$m->concepto}\n";
        $totalIng += (float) $m->monto;
    }
    echo "  TOTAL INGRESOS: S/{$totalIng}\n";
    
    echo "\n--- EGRESOS ---\n";
    $totalEgr = 0;
    foreach ($egresos as $m) {
        echo "  {$m->metodo_pago} | S/{$m->monto} | {$m->concepto}\n";
        $totalEgr += (float) $m->monto;
    }
    echo "  TOTAL EGRESOS: S/{$totalEgr}\n";
    
    $soloEfectivo = $ingresos->where('metodo_pago', 'efectivo')->sum('monto');
    $esperado = (float) $abierta->monto_apertura + $soloEfectivo - $totalEgr;
    
    echo "\n--- CUADRE ---\n";
    echo "Apertura: S/{$abierta->monto_apertura}\n";
    echo "Ingresos efectivo: S/{$soloEfectivo}\n";
    echo "Egresos: S/{$totalEgr}\n";
    echo "ESPERADO (apertura + efectivo - egresos): S/{$esperado}\n";
    
    echo "\n--- POR METODO DE PAGO ---\n";
    foreach (['efectivo','tarjeta','transferencia','otro','fise'] as $met) {
        $sum = $ingresos->where('metodo_pago', $met)->sum('monto');
        echo "  {$met}: S/{$sum}\n";
    }
} else {
    echo "No hay sesion abierta\n";
}
