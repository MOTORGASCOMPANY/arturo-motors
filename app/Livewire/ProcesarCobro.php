<?php

namespace App\Livewire;

use App\Models\Comprobante;
use App\Models\MovimientoCaja;
use App\Models\ServiceOrder;
use App\Models\SesionCaja;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class ProcesarCobro extends Component
{
    public ?ServiceOrder $orden = null;
    public float $monto = 0.00;
    public string $concepto = '';
    public string $nuevoEstado = 'entregado';
    public string $metodoPago = 'efectivo';
    public bool $completado = false;
    public ?string $folioGenerado = null;

    public function mount(?ServiceOrder $orden = null, float $monto = 0.00, string $concepto = '', string $nuevoEstado = 'entregado')
    {
        $this->orden = $orden;
        $this->monto = $orden ? $orden->precio_final : $monto;
        $this->concepto = $concepto;
        $this->nuevoEstado = $nuevoEstado;
    }

    public function procesar()
    {
        $sesion = SesionCaja::abierta()->orderByDesc('abierta_en')->first();

        if (!$sesion) {
            $this->addError('caja', 'No hay una caja abierta. Pide al cajero que abra caja antes de cobrar.');
            $this->dispatch('minToast', titulo: 'Caja cerrada', mensaje: 'No hay una caja abierta para registrar el cobro.', icono: 'error');
            return;
        }

        $this->validate([
            'metodoPago' => 'required|in:efectivo,tarjeta,transferencia,otro',
            'monto' => 'required|numeric|min:0.01',
        ]);

        try {
            DB::transaction(function () use ($sesion) {
                if ($this->orden) {
                    $this->orden->update(['estado' => $this->nuevoEstado]);
                }

                $ordenId = $this->orden ? $this->orden->id : null;

                MovimientoCaja::create([
                    'sesion_caja_id'   => $sesion->id,
                    'tipo'             => 'ingreso',
                    'monto'            => $this->monto,
                    'concepto'         => $this->concepto,
                    'service_order_id' => $ordenId,
                    'usuario_id'       => Auth::id(),
                    'metodo_pago'      => $this->metodoPago,
                ]);

                $comprobante = Comprobante::create([
                    'service_order_id' => $ordenId,
                    'folio'            => Comprobante::generarFolio(),
                    'monto'            => $this->monto,
                    'metodo_pago'      => $this->metodoPago,
                    'emitido_por'      => Auth::id(),
                ]);

                $this->folioGenerado = $comprobante->folio;
            });

            $this->completado = true;
            $this->dispatch('cobro-completado', ordenId: $this->orden?->id, folio: $this->folioGenerado);
            $this->dispatch('minToast', titulo: '¡Cobro realizado!', mensaje: 'El cobro se procesó con éxito.', icono: 'success');

        } catch (\Throwable $e) {
            report($e);
            $this->addError('caja', 'Ocurrió un error al procesar el cobro.');
            $this->dispatch('minToast', titulo: 'Error', mensaje: 'Ocurrió un error al procesar el cobro.', icono: 'error');
        }
    }

    public function render()
    {
        return view('livewire.procesar-cobro');
    }
}
