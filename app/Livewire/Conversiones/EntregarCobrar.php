<?php

namespace App\Livewire\Conversiones;

use App\Models\ServiceOrder;
use App\Models\SesionCaja;
use App\Models\MovimientoCaja;
use App\Models\Comprobante;
use App\Models\FisePago;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class EntregarCobrar extends Component
{
    public ServiceOrder $orden;
    public string $metodoPago = 'efectivo';
    public bool $completado = false;
    public ?string $folioGenerado = null;

    public function mount(int $ordenId)
    {
        $this->orden = ServiceOrder::with(['cliente', 'vehiculo', 'service'])->findOrFail($ordenId);
        abort_unless($this->orden->estado === 'conversion_completada', 403, 'Esta orden no está lista para entrega y cobro.');
    }

    public function procesarCobro()
    {
        $sesion = SesionCaja::abierta()->orderByDesc('abierta_en')->first();

        if (!$sesion) {
            $this->addError('caja', 'No hay una caja abierta. Pide al cajero que abra caja antes de cobrar.');
            $this->dispatch('minToast', titulo: 'Caja cerrada', mensaje: 'No hay una caja abierta para registrar el cobro.', icono: 'error');
            return;
        }

        // REGLA: Validar método de pago contra la lista permitida
        $this->validate(
            ['metodoPago' => 'required|in:' . implode(',', MovimientoCaja::METODOS_PAGO)],
            ['metodoPago.in' => 'Método de pago no válido. Opciones: ' . implode(', ', MovimientoCaja::METODOS_PAGO)]
        );

        // REGLA: FISE solo para conversiones
        if ($this->metodoPago === 'fise' && $this->orden->service->tipo !== 'conversion') {
            $this->addError('caja', 'El FISE solo está disponible para conversiones de gas.');
            return;
        }

        // REGLA: El monto debe ser positivo
        if ($this->orden->precio_final <= 0) {
            $this->addError('caja', 'El precio de la orden debe ser mayor a cero.');
            return;
        }

        try {
            DB::transaction(function () use ($sesion) {
                $this->orden->update(['estado' => 'entregado']);

                MovimientoCaja::create([
                    'sesion_caja_id' => $sesion->id,
                    'tipo' => 'ingreso',
                    'monto' => $this->orden->precio_final,
                    'concepto' => 'Cobro conversión #' . $this->orden->id . ' - ' . $this->orden->service->nombre,
                    'service_order_id' => $this->orden->id,
                    'usuario_id' => Auth::id(),
                    'metodo_pago' => $this->metodoPago,
                ]);

                $comprobante = Comprobante::create([
                    'service_order_id' => $this->orden->id,
                    'folio' => Comprobante::generarFolio(),
                    'monto' => $this->orden->precio_final,
                    'metodo_pago' => $this->metodoPago,
                    'emitido_por' => Auth::id(),
                ]);

                if ($this->metodoPago === 'fise') {
                    FisePago::create([
                        'service_order_id' => $this->orden->id,
                        'monto_total'      => $this->orden->precio_final,
                        'monto_pagado'     => 0,
                        'fecha_pago'       => now()->toDateString(),
                        'pagado_por'       => Auth::id(),
                        'estado'           => 'pendiente',
                    ]);
                }

                $this->folioGenerado = $comprobante->folio;
            });

            $this->completado = true;
            $this->dispatch('conversion-entregada', ordenId: $this->orden->id);

            $mensaje = 'El vehículo fue entregado con éxito.';
            if ($this->metodoPago === 'efectivo') {
                $mensaje .= ' Efectivo registrado en caja.';
            } else {
                $mensaje .= ' Pago por ' . $this->metodoPago . '.';
            }

            $this->dispatch('minToast', titulo: '¡Cobro realizado!', mensaje: $mensaje, icono: 'success');

        } catch (\Throwable $e) {
            report($e);
            $this->addError('caja', 'Ocurrió un error al procesar el cobro. Intenta de nuevo.');
            $this->dispatch('minToast', titulo: 'Error', mensaje: 'Ocurrió un error al procesar el cobro.', icono: 'error');
        }
    }

    public function render()
    {
        return view('livewire.conversiones.entregar-cobrar');
    }
}
