<?php

namespace App\Livewire\Almacen\Recepciones;

use App\Models\Producto;
use App\Models\ItemSerializado;
use App\Models\Sede;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class Crear extends Component
{
    public ?string $proveedorId = null;
    public ?int $sedeId = null;
    public string $notas = '';
    public array $cantidades = []; // producto_id => cantidad

    public array $proveedores = [
        'MOCAVIN',
        'AUTO TOP',
        'UNIGAS',
        "D'WILLIAMS",
    ];

    public function mount()
    {
        $this->sedeId = 1; // Callao por defecto
        // Inicializar cantidades en 0 para todos los kits
        foreach ($this->kitsDisponibles as $kit) {
            $this->cantidades[$kit->id] = 0;
        }
    }

    public function getKitsDisponiblesProperty()
    {
        return Producto::whereHas('categoria', fn ($q) => $q->where('es_kit', true))
            ->where('activo', true)
            ->get();
    }

    public function getTotalProperty(): int
    {
        return array_sum($this->cantidades);
    }

    public function guardar()
    {
        $this->validate([
            'proveedorId' => 'required|string',
            'sedeId' => 'required|exists:sedes,id',
        ]);

        $kitsARecibir = collect($this->cantidades)
            ->filter(fn ($cant) => $cant > 0)
            ->toArray();

        if (empty($kitsARecibir)) {
            $this->dispatch('minAlert', titulo: 'Atención', mensaje: 'Seleccioná al menos un kit con cantidad mayor a 0.', icono: 'warning');
            return;
        }

        $sedeId = $this->sedeId;
        $proveedorNombre = $this->proveedorId;

        try {
            DB::transaction(function () use ($kitsARecibir, $sedeId, $proveedorNombre) {
                foreach ($kitsARecibir as $productoId => $cantidad) {
                    $kit = Producto::find($productoId);
                    if (!$kit) continue;

                    for ($i = 0; $i < $cantidad; $i++) {
                        ItemSerializado::create([
                            'producto_id' => $kit->id,
                            'serie' => null, // Kits no tienen serie al recibir
                            'atributos' => [
                                'proveedor' => $proveedorNombre,
                                'recepcion_fecha' => now()->toDateString(),
                            ],
                            'estado' => 'en_stock',
                            'sede_id' => $sedeId,
                        ]);
                    }
                }
            });
        } catch (\Throwable $e) {
            report($e);
            $this->dispatch('minAlert', titulo: 'Error', mensaje: 'Ocurrió un error al guardar la recepción.', icono: 'error');
            return;
        }

        $total = $this->total;
        $this->dispatch('minToast', titulo: '¡Recepción registrada!', mensaje: "{$total} kit(s) recibido(s) correctamente.", icono: 'success');
        $this->redirect(route('almacen.recepciones.listado'));
    }

    public function render()
    {
        return view('livewire.almacen.recepciones.crear');
    }
}
