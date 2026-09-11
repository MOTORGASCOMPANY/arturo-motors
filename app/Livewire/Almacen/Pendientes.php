<?php

namespace App\Livewire\Almacen;

use App\Models\ItemSerializado;
use App\Models\KitComponente;
use App\Models\Sede;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Pendientes extends Component
{
    public $solicitudes = [];
    public ?int $solicitudSeleccionada = null;
    public ?ItemSerializado $itemSolicitado = null;
    public array $kitsDisponibles = [];
    public ?int $kitSeleccionadoId = null;
    public bool $procesando = false;

    public function mount()
    {
        $this->cargarSolicitudes();
    }

    public function cargarSolicitudes()
    {
        $this->solicitudes = ItemSerializado::with(['producto.categoria', 'serviceOrder.cliente', 'serviceOrder.vehiculo'])
            ->where('atributos->necesita_reemplazo', true)
            ->where('atributos->almacen_procesado', null)
            ->orderBy('atributos->solicitado_en', 'asc')
            ->get()
            ->toArray();
    }

    public function seleccionarSolicitud(int $itemId)
    {
        $this->solicitudSeleccionada = $itemId;
        $this->itemSolicitado = ItemSerializado::with(['producto.categoria', 'serviceOrder.cliente', 'serviceOrder.vehiculo'])->find($itemId);
        
        // Buscar kits disponibles (productos cuya categoría tiene es_kit = true)
        $this->kitsDisponibles = ItemSerializado::with('producto.categoria')
            ->whereHas('producto.categoria', fn ($q) => $q->where('es_kit', true))
            ->where('estado', 'en_stock')
            ->where('sede_id', $this->itemSolicitado->sede_id)
            ->get()
            ->map(fn ($kit) => [
                'id' => $kit->id,
                'nombre' => $kit->producto->nombre,
                'serie' => $kit->serie,
                'proveedor' => $kit->atributos['proveedor'] ?? 'N/A',
            ])
            ->toArray();
    }

    public function abrirKit()
    {
        if (!$this->solicitudSeleccionada || !$this->kitSeleccionadoId || !$this->itemSolicitado) return;

        $this->procesando = true;

        try {
            DB::transaction(function () {
                // 1. Obtener el kit a abrir
                $kit = ItemSerializado::where('id', $this->kitSeleccionadoId)
                    ->where('estado', 'en_stock')
                    ->lockForUpdate()
                    ->first();

                if (!$kit) {
                    throw new \Exception('El kit ya no está disponible.');
                }

                // 2. Marcar kit como "abierto"
                $kit->update(['estado' => 'abierto']);

                // 3. Buscar el componente del kit que coincida con lo solicitado
                $componentes = KitComponente::where('producto_kit_id', $kit->producto_id)->get();
                
                $componenteEncontrado = null;
                foreach ($componentes as $comp) {
                    $productoComponente = \App\Models\Producto::find($comp->producto_componente_id);
                    if ($productoComponente && $productoComponente->id === $this->itemSolicitado->producto_id) {
                        $componenteEncontrado = $productoComponente;
                        break;
                    }
                }

                if (!$componenteEncontrado) {
                    throw new \Exception('El kit no contiene esta pieza.');
                }

                // 4. Crear item serializado para la pieza obtenida del kit
                $nuevaPieza = ItemSerializado::create([
                    'producto_id' => $componenteEncontrado->id,
                    'serie' => 'KIT-' . $kit->serie . '-' . strtoupper(uniqid()),
                    'estado' => 'en_stock',
                    'sede_id' => $this->itemSolicitado->sede_id,
                    'atributos' => [
                        'origen' => 'apertura_kit',
                        'kit_abierto_id' => $kit->id,
                        'aberto_por' => Auth::id(),
                        'aberto_en' => now()->toDateTimeString(),
                    ],
                ]);

                // 5. Actualizar item solicitado con la nueva pieza
                $this->itemSolicitado->update([
                    'atributos' => array_merge($this->itemSolicitado->atributos ?? [], [
                        'almacen_procesado' => true,
                        'pieza_asignada_id' => $nuevaPieza->id,
                        'procesado_en' => now()->toDateTimeString(),
                        'procesado_por' => Auth::id(),
                    ]),
                ]);

                // 6. Asignar pieza a la orden de servicio
                $nuevaPieza->update([
                    'estado' => 'asignado',
                    'service_order_id' => $this->itemSolicitado->service_order_id,
                ]);

                // 7. Devolver pieza que no calza al stock
                $this->itemSolicitado->update([
                    'estado' => 'en_stock',
                    'service_order_id' => null,
                    'atributos' => array_merge($this->itemSolicitado->atributos ?? [], [
                        'devuelta_por_no_calzar' => true,
                        'devuelta_en' => now()->toDateTimeString(),
                    ]),
                ]);
            });

            $this->dispatch('minToast', titulo: '¡Kit abierto!', mensaje: 'La pieza fue obtenida del kit y asignada a la conversión.', icono: 'success');
            
        } catch (\Throwable $e) {
            report($e);
            $this->dispatch('minToast', titulo: 'Error', mensaje: $e->getMessage(), icono: 'error');
        }

        $this->procesando = false;
        $this->solicitudSeleccionada = null;
        $this->itemSolicitado = null;
        $this->kitsDisponibles = [];
        $this->kitSeleccionadoId = null;
        
        $this->cargarSolicitudes();
    }

    public function rechazarSolicitud()
    {
        if (!$this->solicitudSeleccionada || !$this->itemSolicitado) return;

        $this->itemSolicitado->update([
            'atributos' => array_merge($this->itemSolicitado->atributos ?? [], [
                'almacen_procesado' => true,
                'rechazado' => true,
                'rechazado_en' => now()->toDateTimeString(),
                'rechazado_por' => Auth::id(),
            ]),
        ]);

        $this->dispatch('minToast', titulo: 'Solicitud rechazada', icono: 'info');
        
        $this->solicitudSeleccionada = null;
        $this->itemSolicitado = null;
        $this->kitsDisponibles = [];
        
        $this->cargarSolicitudes();
    }

    public function render()
    {
        return view('livewire.almacen.pendientes');
    }
}
