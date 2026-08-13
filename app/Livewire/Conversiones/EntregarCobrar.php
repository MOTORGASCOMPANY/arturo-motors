<?php

namespace App\Livewire\Conversiones;

use App\Models\ServiceOrder;
use App\Models\SesionCaja;
use App\Models\MovimientoCaja;
use App\Models\Comprobante;
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

        $this->validate(['metodoPago' => 'required|in:efectivo,tarjeta,transferencia,otro']);

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
                ]);

                $comprobante = Comprobante::create([
                    'service_order_id' => $this->orden->id,
                    'folio' => Comprobante::generarFolio(),
                    'monto' => $this->orden->precio_final,
                    'metodo_pago' => $this->metodoPago,
                    'emitido_por' => Auth::id(),
                ]);

                $this->folioGenerado = $comprobante->folio;
            });

            $this->completado = true;
            $this->dispatch('conversion-entregada', ordenId: $this->orden->id);
            $this->dispatch('minToast', titulo: '¡Cobro realizado!', mensaje: 'El vehículo fue entregado con éxito.', icono: 'success');

        } catch (\Throwable $e) {
            report($e);
            $this->addError('caja', 'Ocurrió un error al procesar el cobro. Intenta de nuevo.');
            $this->dispatch('minToast', titulo: 'Error', mensaje: 'Ocurrió un error al procesar el cobro.', icono: 'error');
            return;
        }
    }

    public function render()
    {
        return view('livewire.conversiones.entregar-cobrar');
    }
}
