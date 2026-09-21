<?php

namespace App\Livewire\Conversiones;

use App\Models\ServiceOrder;
use App\Models\ItemSerializado;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class RegistrarSeries extends Component
{
    public ServiceOrder $orden;

    // Items asignados a esta orden que necesitan registro de serie
    public array $items = [];
    // Estructura: [['id' => X, 'producto_nombre' => '...', 'serie_instalada' => '']]

    public function mount(int $ordenId)
    {
        $this->orden = ServiceOrder::with(['items.producto', 'vehiculo'])->findOrFail($ordenId);

        // Cargar items asignados que son serializados
        $this->items = $this->orden->items
            ->where('estado', 'asignado')
            ->map(fn ($item) => [
                'id' => $item->id,
                'producto_nombre' => $item->producto->nombre,
                'serie_actual' => $item->serie,
                'serie_instalada' => '', // nueva serie a registrar
            ])
            ->values()
            ->toArray();
    }

    public function guardar()
    {
        // Validar que todas las series estén registradas
        foreach ($this->items as $item) {
            if (empty(trim($item['serie_instalada']))) {
                $this->addError('general', "Debes registrar la serie instalada de: {$item['producto_nombre']}");
                return;
            }
        }

        try {
            DB::transaction(function () {
                foreach ($this->items as $itemData) {
                    $item = ItemSerializado::find($itemData['id']);
                    if ($item) {
                        $item->update([
                            'serie' => trim($itemData['serie_instalada']),
                            'vehiculo_instalado_id' => $this->orden->vehiculo_id,
                            'fecha_instalacion_reportada' => now(),
                        ]);
                    }
                }
            });
        } catch (\Throwable $e) {
            report($e);
            $this->dispatch('minToast', titulo: 'Error', mensaje: 'Ocurrió un error al guardar las series.', icono: 'error');
            return;
        }

        $this->dispatch('minToast', titulo: '¡Series registradas!', mensaje: 'Se registraron ' . count($this->items) . ' serie(s) instaladas.', icono: 'success');
        $this->redirect(route('conversiones.realizar', $this->orden->id));
    }

    public function render()
    {
        return view('livewire.conversiones.registrar-series');
    }
}
