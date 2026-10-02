<?php

namespace App\Http\Controllers\Caja;

use App\Http\Controllers\Controller;
use App\Models\SesionCaja;
use App\Models\MovimientoCaja;

class DetalleController extends Controller
{
    public function show(SesionCaja $sesion)
    {
        // Cargar movimientos SIN FISE.
        // Los egresos tienen metodo_pago NULL (RegistrarEgreso no lo setea) y
        // `metodo_pago != 'fise'` es NULL para NULL, no TRUE: los descartaba y
        // $resumen['egresos'] terminaba en 0.00 siempre.
        $movimientos = $sesion->movimientos()
            ->where(function ($q) {
                $q->where('metodo_pago', '!=', 'fise')
                    ->orWhereNull('metodo_pago');
            })
            ->where(function ($q) {
                $q->where('tipo', 'ingreso')
                    ->orWhere('tipo', 'egreso');
            })
            ->with(['serviceOrder.vehiculo', 'serviceOrder.cliente', 'usuario'])
            ->orderBy('created_at')
            ->get();

        // Resumen por método (convencionales)
        $resumen = [
            'efectivo'      => $movimientos->where('tipo', 'ingreso')->where('metodo_pago', 'efectivo')->sum('monto'),
            'tarjeta'       => $movimientos->where('tipo', 'ingreso')->where('metodo_pago', 'tarjeta')->sum('monto'),
            'transferencia' => $movimientos->where('tipo', 'ingreso')->where('metodo_pago', 'transferencia')->sum('monto'),
            'otro'          => $movimientos->where('tipo', 'ingreso')->where('metodo_pago', 'otro')->sum('monto'),
            'egresos'       => $movimientos->where('tipo', 'egreso')->sum('monto'),
            'total_ingresos' => $movimientos->where('tipo', 'ingreso')->sum('monto'),
        ];

        $resumen['neto'] = $resumen['total_ingresos'] - $resumen['egresos'];

        return view('caja.detalle', compact('sesion', 'movimientos', 'resumen'));
    }
}