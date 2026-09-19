<?php

namespace App\Livewire\Conversiones;

use App\Models\ItemSerializado;
use App\Models\KitComponente;
use App\Models\MovimientoStock;
use App\Models\ReportePiezaNoEncajada;
use App\Models\ServiceOrder;
use App\Models\Sede;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class Realizar extends Component
{
    public ServiceOrder $orden;

    // Modal ver componentes
    public bool $modalPartesAbierto = false;

    protected function sedePrincipalId(): int
    {
        return Sede::activas()->orderBy('id')->first()?->id ?? 1;
    }

    public function mount(int $ordenId)
    {
        $this->orden = ServiceOrder::with([
            'cliente',
            'vehiculo',
            'service',
            'items.producto.categoria',
            'items.kitPadre.producto',
        ])->findOrFail($ordenId);

        abort_unless(
            in_array($this->orden->estado, ['en_conversion', 'conversion_completada']),
            403,
            'Esta orden no está en etapa de conversión.'
        );
    }

    // ═══════════════════════════════════════════════
    // PROPIEDADES COMPUTADAS
    // ═══════════════════════════════════════════════

    public function getKitPadreProperty(): ?ItemSerializado
    {
        return ItemSerializado::where('service_order_id', $this->orden->id)
            ->whereNull('kit_padre_id')
            ->whereHas('piezasEnKit')
            ->with(['producto.categoria', 'producto.componentes.componente.categoria'])
            ->first();
    }

    public function getGeneracionKitProperty(): string
    {
        $padre = $this->kitPadre;
        if (!$padre) return '';
        return $padre->producto->atributos['generacion'] ?? $padre->producto->categoria->nombre;
    }

    public function getKitItemsProperty()
    {
        $padre = $this->kitPadre;
        if (!$padre) return collect();

        $componenteIds = $padre->producto->componentes->pluck('producto_componente_id')->toArray();
        if (empty($componenteIds)) return collect();

        return ItemSerializado::where('service_order_id', $this->orden->id)
            ->where('estado', '!=', 'consumido')
            ->whereNotNull('kit_padre_id')
            ->whereIn('producto_id', $componenteIds)
            ->with('producto.categoria')
            ->get()
            ->filter(fn($item) => $item->producto->categoria->es_serializado ?? false)
            ->values();
    }

    public function getTodasPiezasKitProperty()
    {
        $padre = $this->kitPadre;
        if (!$padre) return collect();

        return KitComponente::where('producto_kit_id', $padre->producto_id)
            ->with('componente.categoria')
            ->get()
            ->map(fn($kc) => (object) [
                'producto_id' => $kc->producto_componente_id,
                'nombre' => $kc->componente->nombre,
                'categoria' => $kc->componente->categoria->nombre,
                'es_serializado' => $this->esSerializable($kc->componente->nombre),
                'cantidad_esperada' => $kc->cantidad_esperada,
            ]);
    }

    private function esSerializable($producto): bool
    {
        if (is_int($producto)) {
            $producto = \App\Models\Producto::with('categoria')->find($producto);
        }
        if (is_string($producto)) {
            $producto = \App\Models\Producto::where('nombre', $producto)->first();
        }
        if (!$producto instanceof \App\Models\Producto) {
            return false;
        }
        return $producto->categoria->es_serializado ?? false;
    }

    public function getItemsReportadosProperty(): array
    {
        return ReportePiezaNoEncajada::where('service_order_id', $this->orden->id)
            ->whereIn('estado', ['pendiente', 'buscando_pieza', 'solicitando_almacen'])
            ->pluck('item_no_encajado_id')
            ->toArray();
    }

    public function getItemsReemplazadosProperty(): array
    {
        return ReportePiezaNoEncajada::where('service_order_id', $this->orden->id)
            ->whereIn('estado', ['kit_abierto', 'resuelto'])
            ->pluck('item_no_encajado_id')
            ->toArray();
    }

    public function getDevolucionesPendientesProperty()
    {
        return ReportePiezaNoEncajada::where('service_order_id', $this->orden->id)
            ->whereIn('estado', ['pendiente', 'buscando_pieza', 'solicitando_almacen'])
            ->with('itemNoEncajado.producto')
            ->get();
    }

    public function getPuedeFinalizarProperty(): bool
    {
        if (!$this->orden->fecha_inicio_conversion) return false;

        return ReportePiezaNoEncajada::where('service_order_id', $this->orden->id)
            ->whereIn('estado', ['pendiente', 'buscando_pieza', 'solicitando_almacen'])
            ->count() === 0;
    }

    // ═══════════════════════════════════════════════
    // SOLO 2 ACCIONES: INICIAR Y FINALIZAR
    // ═══════════════════════════════════════════════

    public function iniciar()
    {
        if ($this->orden->fecha_inicio_conversion) return;

        $this->orden->update(['fecha_inicio_conversion' => now()]);
        $this->orden->refresh();

        $this->dispatch('minToast',
            titulo: 'Conversión iniciada',
            mensaje: 'Instale las piezas del kit.',
            icono: 'success'
        );
    }

    public function finalizar()
    {
        if (!$this->orden->fecha_inicio_conversion) {
            $this->dispatch('minToast', titulo: 'Error', mensaje: 'Primero debe iniciar la conversión.', icono: 'error');
            return;
        }

        $pendientes = ReportePiezaNoEncajada::where('service_order_id', $this->orden->id)
            ->whereIn('estado', ['pendiente', 'buscando_pieza', 'solicitando_almacen'])
            ->count();

        if ($pendientes > 0) {
            $this->dispatch('minToast',
                titulo: 'No puede finalizar',
                mensaje: "Tiene {$pendientes} pieza(s) pendiente(s). Espere que el almacén despache.",
                icono: 'warning'
            );
            return;
        }

        try {
            DB::transaction(function () {
                ItemSerializado::where('service_order_id', $this->orden->id)
                    ->where('estado', 'asignado')
                    ->whereNull('kit_padre_id')
                    ->whereHas('piezasEnKit')
                    ->update(['estado' => 'consumido']);

                ItemSerializado::where('service_order_id', $this->orden->id)
                    ->where('estado', 'asignado')
                    ->whereNotNull('kit_padre_id')
                    ->update(['estado' => 'instalado']);

                $itemsReemplazo = ItemSerializado::where('service_order_id', $this->orden->id)
                    ->where('estado', 'asignado')
                    ->whereNull('kit_padre_id')
                    ->whereDoesntHave('piezasEnKit')
                    ->get();

                foreach ($itemsReemplazo as $item) {
                    $item->update([
                        'estado' => 'en_stock',
                        'service_order_id' => null,
                        'atributos' => array_merge($item->atributos ?? [], [
                            'devuelto_por_no_calza' => true,
                            'devuelto_en' => now()->toDateTimeString(),
                        ]),
                    ]);

                    MovimientoStock::registrar(
                        $item->producto, 'entrada', 1,
                        $this->orden->id, Auth::id(),
                        "Devolución pieza al finalizar conversión"
                    );
                }

                $this->orden->update([
                    'fecha_fin_conversion' => now(),
                    'estado' => 'conversion_completada',
                ]);
            });

            $this->orden->refresh();
            $this->dispatch('minToast', titulo: '¡Conversión completada!', mensaje: 'La orden está lista para entrega.', icono: 'success');
            return $this->redirect(route('conversiones.mis-asignadas'));

        } catch (\Throwable $e) {
            report($e);
            $this->dispatch('minToast', titulo: 'Error', mensaje: 'Ocurrió un error.', icono: 'error');
        }
    }

    public function abrirPartesGenerales()
    {
        $this->modalPartesAbierto = true;
    }

    public function cerrarPartesGenerales()
    {
        $this->modalPartesAbierto = false;
    }

    public function render()
    {
        return view('livewire.conversiones.realizar');
    }
}
