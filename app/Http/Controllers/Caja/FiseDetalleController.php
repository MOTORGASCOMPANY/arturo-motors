<?php

namespace App\Http\Controllers\Caja;

use App\Http\Controllers\Controller;
use App\Models\SesionCaja;
use App\Models\FisePago;
use App\Models\MovimientoCaja;

class FiseDetalleController extends Controller
{
    public function show(SesionCaja $sesion)
    {
        // Cargar SOLO movimientos FISE
        $movimientos = $sesion->movimientos()
            ->where('metodo_pago', 'fise')
            ->with(['serviceOrder.vehiculo', 'serviceOrder.cliente', 'usuario'])
            ->orderBy('created_at')
            ->get();

        // Estados de cobro FISE
        $serviceOrderIds = $movimientos->pluck('service_order_id')->filter()->unique();
        
        $fisePagos = FisePago::whereIn('service_order_id', $serviceOrderIds)
            ->get()
            ->keyBy('service_order_id');

        // Resumen FISE
        $resumen = [
            'total_fise'      => $movimientos->sum('monto'),
            'cobrado'         => $fisePagos->where('estado', 'pagado')->sum('monto_pagado'),
            'pendiente'       => $fisePagos->where('estado', 'pendiente')->sum('monto_total'),
            'cantidad'        => $movimientos->count(),
            'cantidad_cobrada' => $fisePagos->where('estado', 'pagado')->count(),
            'cantidad_pendiente' => $fisePagos->where('estado', 'pendiente')->count(),
        ];

        // Resumen de caja completo (incluye FISE)
        $totalIngresos = $sesion->movimientos()->where('tipo', 'ingreso')->sum('monto');
        $totalEgresos = $sesion->movimientos()->where('tipo', 'egreso')->sum('monto');
        $cajaConvencional = $sesion->movimientos()->where('tipo', 'ingreso')->where('metodo_pago', '!=', 'fise')->sum('monto');
        $caja = [
            'apertura'      => $sesion->monto_apertura,
            'ingresos'      => $totalIngresos,
            'egresos'       => $totalEgresos,
            'monto_esperado' => $sesion->monto_esperado,
            'monto_cierre'  => $sesion->monto_cierre,
            'diferencia'    => $sesion->diferencia,
            'caja_convencional' => $cajaConvencional,
        ];

        return view('caja.fise-detalle', compact('sesion', 'movimientos', 'fisePagos', 'resumen', 'caja'));
    }
}