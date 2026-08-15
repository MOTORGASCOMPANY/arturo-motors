<?php

namespace App\Livewire\Conversiones;

use App\Models\ServiceOrder;
use App\Models\ItemSerializado;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Realizar extends Component
{
    public ServiceOrder $orden;

    public function mount(int $ordenId)
    {
        $this->orden = ServiceOrder::with(['cliente', 'vehiculo', 'service', 'items.producto.categoria'])->findOrFail($ordenId);

        abort_unless($this->orden->tecnico_id === Auth::id(), 403, 'Esta orden no está asignada a ti.');
        abort_unless(in_array($this->orden->estado, ['en_conversion', 'conversion_completada']), 403, 'Esta orden no está en etapa de conversión.');
    }

    public function iniciar()
    {
        if ($this->orden->fecha_inicio_conversion) return;

        $this->orden->update(['fecha_inicio_conversion' => now()]);

        $this->orden->refresh();
        $this->dispatch('minToast', titulo: 'Proceso iniciado', mensaje: 'Se registró el inicio de la conversión.', icono: 'info');
    }

    public function finalizar()
    {
        if (!$this->orden->fecha_inicio_conversion) {
            $this->addError('general', 'Primero debes iniciar la conversión.');
            $this->dispatch('minToast', titulo: 'Atención', mensaje: 'Debes iniciar la conversión antes de finalizar.', icono: 'warning');
            return;
        }

        try {
            DB::transaction(function () {
                // Marca todos los equipos asignados como instalados
                ItemSerializado::where('service_order_id', $this->orden->id)
                    ->where('estado', 'asignado')
                    ->update(['estado' => 'instalado']);

                $this->orden->update([
                    'fecha_fin_conversion' => now(),
                    'estado' => 'conversion_completada',
                ]);
            });

            $this->orden->refresh();
            //session()->flash('mensaje', 'Conversión finalizada. Queda pendiente la entrega y cobro.');
            $this->dispatch('minToast', titulo: '¡Éxito!', mensaje: 'Conversión finalizada. Queda pendiente la entrega y cobro.', icono: 'success');

        } catch (\Throwable $e) {
            report($e);
            $this->addError('general', 'Ocurrió un error al finalizar. Intenta de nuevo.');
            $this->dispatch('minToast', titulo: 'Error', mensaje: 'No se pudo completar el proceso.', icono: 'error');
            return;
        }
    }

    public function render()
    {
        return view('livewire.conversiones.realizar');
    }
}
