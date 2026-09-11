<?php

namespace App\Livewire\Conversiones;

use App\Models\ServiceOrder;
use App\Models\ItemSerializado;
use App\Models\Producto;
use App\Models\Sede;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class RegistrarItemsKit extends Component
{
    public ServiceOrder $orden;
    public array $items = [];
    public bool $guardado = false;
    public bool $sinKit = false;

    // Productos que necesitan registro de serie
    const PRODUCTOS_SERIALIZABLES = [
        'Vaporizador',
        'Computadora',
        'Tanque',
    ];

    public function mount(int $ordenId)
    {
        $this->orden = ServiceOrder::with([
            'cliente', 
            'vehiculo', 
            'service',
        ])->findOrFail($ordenId);

        abort_unless($this->orden->tecnico_id === Auth::id(), 403, 'Esta orden no está asignada a ti.');
        abort_unless($this->orden->estado === 'en_conversion', 403, 'Esta orden no está en etapa de conversión.');

        $this->cargarItems();
    }

    /**
     * Cargar los items serializables de la orden
     */
    public function cargarItems()
    {
        $sedeId = $this->orden->sede_id ?? Sede::activas()->orderBy('id')->first()?->id ?? 1;

        // Buscar kit asignado a esta orden
        $kitItem = $this->orden->items()
            ->whereHas('producto.categoria', fn ($q) => $q->where('es_kit', true))
            ->first();

        if (!$kitItem) {
            $kitItem = ItemSerializado::where('service_order_id', $this->orden->id)
                ->whereHas('producto.categoria', fn ($q) => $q->where('es_kit', true))
                ->first();
        }

        if (!$kitItem) {
            $this->sinKit = true;
            return;
        }

        // Obtener IDs de componentes REALES de este kit
        $componenteIds = $kitItem->producto->componentes->pluck('producto_componente_id')->toArray();

        // Solo productos serializables que sean componentes de ESTE kit
        $productosSerializables = Producto::with('categoria')->where(function ($q) use ($componenteIds) {
            $q->whereIn('id', $componenteIds);
        })->get()->filter(fn($p) => $this->esSerializable($p->nombre));

        foreach ($productosSerializables as $producto) {
            // Buscar si ya existe un item para este producto en esta orden
            $itemExistente = ItemSerializado::where('service_order_id', $this->orden->id)
                ->where('producto_id', $producto->id)
                ->first();

            if (!$itemExistente) {
                // Buscar item en stock de esta sede
                $itemEnStock = ItemSerializado::where('producto_id', $producto->id)
                    ->where('estado', 'en_stock')
                    ->where('sede_id', $sedeId)
                    ->first();

                if ($itemEnStock) {
                    // Asignar item existente
                    $itemEnStock->update([
                        'estado' => 'asignado',
                        'service_order_id' => $this->orden->id,
                        'kit_padre_id' => $kitItem->id,
                    ]);
                    $itemExistente = $itemEnStock;
                } else {
                    // Crear nuevo item (pendiente de serie)
                    $itemExistente = ItemSerializado::create([
                        'producto_id' => $producto->id,
                        'serie' => null,
                        'estado' => 'asignado',
                        'service_order_id' => $this->orden->id,
                        'kit_padre_id' => $kitItem->id,
                        'sede_id' => $sedeId,
                        'atributos' => [
                            'creado_automaticamente' => true,
                            'creado_por' => 'registrar-items-kit',
                            'creado_en' => now()->toDateTimeString(),
                        ],
                    ]);
                }
            }

            $this->items[] = [
                'id' => $itemExistente->id,
                'producto_nombre' => $producto->nombre,
                'categoria' => $producto->categoria->nombre ?? 'Kit',
                'serie_actual' => $itemExistente->serie,
                'serie_nueva' => '',
                'observaciones' => '',
            ];
        }
    }

    /**
     * Verificar si un producto es serializable
     */
    private function esSerializable(string $nombre): bool
    {
        foreach (self::PRODUCTOS_SERIALIZABLES as $patron) {
            if (stripos($nombre, $patron) !== false) {
                return true;
            }
        }
        return false;
    }

    /**
     * Obtener IDs de productos serializables
     */
    private function getIdsProductosSerializables(): array
    {
        return Producto::where(function ($q) {
            foreach (self::PRODUCTOS_SERIALIZABLES as $nombre) {
                $q->orWhere('nombre', 'LIKE', "%{$nombre}%");
            }
        })->pluck('id')->toArray();
    }

    /**
     * Verificar si todos los items tienen serie registrada
     */
    public function getTodosRegistradosProperty(): bool
    {
        foreach ($this->items as $item) {
            if (empty(trim($item['serie_nueva']))) {
                return false;
            }
        }
        return true;
    }

    /**
     * Guardar las series registradas
     */
    public function guardar()
    {
        // Validar que todas las series estén registradas
        foreach ($this->items as $index => $item) {
            if (empty(trim($item['serie_nueva']))) {
                $this->addError('general', "Debes registrar la serie de: {$item['producto_nombre']}");
                return;
            }
        }

        try {
            DB::transaction(function () {
                foreach ($this->items as $itemData) {
                    $item = ItemSerializado::find($itemData['id']);
                    if ($item) {
                        $item->update([
                            'serie' => trim($itemData['serie_nueva']),
                            'atributos' => array_merge($item->atributos ?? [], [
                                'serie_original' => $item->serie,
                                'serie_registrada_por' => Auth::id(),
                                'serie_registrada_en' => now()->toDateTimeString(),
                                'observaciones_instalacion' => $itemData['observaciones'] ?? null,
                            ]),
                        ]);
                    }
                }
            });
        } catch (\Throwable $e) {
            report($e);
            $this->dispatch('minToast', titulo: 'Error', mensaje: 'Ocurrió un error al guardar.', icono: 'error');
            return;
        }

        $this->guardado = true;
        
        $this->dispatch('minToast', 
            titulo: '¡Items registrados!', 
            mensaje: 'Series registradas. Ya puedes iniciar la conversión.', 
            icono: 'success'
        );
    }

    /**
     * Ir a iniciar conversión
     */
    public function irAIniciar()
    {
        $this->redirect(route('conversiones.realizar', $this->orden->id));
    }

    /**
     * Ir a asignar equipos (cuando no hay kit)
     */
    public function irAAsignarEquipos()
    {
        $this->redirect(route('conversiones.asignar-equipos', $this->orden->id));
    }

    public function render()
    {
        return view('livewire.conversiones.registrar-items-kit');
    }
}
