<?php

namespace App\Livewire\Almacen;

use App\Models\ReportePiezaNoEncajada;
use App\Models\ItemSerializado;
use App\Models\MovimientoStock;
use App\Models\Sede;
use App\Services\CambioPiezaService;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class ReportesPendientes extends Component
{
    public $reportes = [];
    public ?int $reporteSeleccionadoId = null;
    public ?ReportePiezaNoEncajada $reporteSeleccionado = null;
    
    public bool $modalAbrirKit = false;
    public ?int $kitSeleccionadoId = null;
    public array $kitsDisponibles = [];
    public string $busquedaKit = '';
    
    public bool $modalHistorial = false;
    public array $historialMovimientos = [];
    public array $historialAgrupado = [];
    
    public string $mensaje = '';

    protected CambioPiezaService $cambioPiezaService;

    public function boot(CambioPiezaService $cambioPiezaService): void
    {
        $this->cambioPiezaService = $cambioPiezaService;
    }

    public function mount()
    {
        $this->cargarReportes();
    }

    public function cargarReportes()
    {
        $this->reportes = ReportePiezaNoEncajada::whereIn('estado', ['pendiente', 'buscando_pieza', 'solicitando_almacen'])
            ->with(['itemNoEncajado.producto', 'tecnico', 'serviceOrder.vehiculo', 'serviceOrder.cliente'])
            ->orderBy('created_at', 'desc')
            ->get()
            ->toArray();
    }

    public function seleccionarReporte(int $reporteId)
    {
        $this->reporteSeleccionadoId = $reporteId;
        $this->reporteSeleccionado = ReportePiezaNoEncajada::with([
            'itemNoEncajado.producto',
            'tecnico',
            'serviceOrder.vehiculo',
            'serviceOrder.cliente',
            'serviceOrder.items.producto'
        ])->find($reporteId);

        $this->buscarKits();
    }

    public function buscarKits()
    {
        if (!$this->reporteSeleccionado) return;

        $sedeId = $this->reporteSeleccionado->serviceOrder->sede_id ?? 1;
        $productoNecesarioId = $this->reporteSeleccionado->itemNoEncajado->producto_id;

        $kits = $this->cambioPiezaService->buscarKitsConProducto($productoNecesarioId, $sedeId);

        // Only show 1 kit — you only need to open one to get the piece
        $kit = $kits->first();

        $this->kitsDisponibles = $kit ? [[
            'id' => $kit->id,
            'producto' => $kit->producto->nombre,
            'serie' => $kit->serie,
            'sede_id' => $kit->sede_id,
            'componentes' => $kit->producto->componentes->map(fn ($c) => [
                'nombre' => $c->componente->nombre,
                'cantidad' => $c->cantidad_esperada,
                'es_necesaria' => $c->producto_componente_id == $productoNecesarioId,
            ])->toArray(),
        ]] : [];
    }

    public function abrirModalKit()
    {
        $this->modalAbrirKit = true;
    }

    public function cerrarModalKit()
    {
        $this->modalAbrirKit = false;
        $this->kitSeleccionadoId = null;
        $this->busquedaKit = '';
    }

    public function seleccionarKit(int $kitId)
    {
        $this->kitSeleccionadoId = $kitId;
    }

    /**
     * Abrir kit y asignar pieza al reporte
     */
    public function confirmarAperturaKit()
    {
        if (!$this->reporteSeleccionado || !$this->kitSeleccionadoId) return;

        try {
            $kitItem = ItemSerializado::find($this->kitSeleccionadoId);
            $productoPiezaId = $this->reporteSeleccionado->itemNoEncajado->producto_id;

            $sedeId = $kitItem->sede_id ?? 1;

            $nuevaPieza = $this->cambioPiezaService->abrirKitYExtraerPieza(
                $kitItem,
                $productoPiezaId,
                $sedeId,
                "Apertura para reemplazo - Reporte #{$this->reporteSeleccionado->id}",
                $this->reporteSeleccionado->service_order_id
            );

            $this->cambioPiezaService->asignarPiezaDesdeAlmacen(
                $this->reporteSeleccionado,
                $nuevaPieza->id,
                "Pieza extraída del kit #{$kitItem->id}"
            );

            $this->cerrarModalKit();
            $this->cargarReportes();
            $this->reporteSeleccionado = null;
            $this->reporteSeleccionadoId = null;

            $this->dispatch('minToast', 
                titulo: 'Kit abierto y pieza asignada', 
                mensaje: 'La pieza fue extraída del kit y asignada al técnico.', 
                icono: 'success'
            );

        } catch (\Throwable $e) {
            report($e);
            $this->dispatch('minToast', titulo: 'Error', mensaje: $e->getMessage(), icono: 'error');
        }
    }

    /**
     * Asignar pieza suelta directamente (sin abrir kit)
     */
    public function asignarPiezaSuelta(int $itemId)
    {
        if (!$this->reporteSeleccionado) return;

        try {
            $this->cambioPiezaService->asignarPiezaDesdeAlmacen(
                $this->reporteSeleccionado,
                $itemId,
                'Pieza suelta asignada desde almacén'
            );

            $this->cargarReportes();
            $this->reporteSeleccionado = null;
            $this->reporteSeleccionadoId = null;

            $this->dispatch('minToast', 
                titulo: 'Pieza asignada', 
                mensaje: 'La pieza fue asignada al técnico.', 
                icono: 'success'
            );

        } catch (\Throwable $e) {
            report($e);
            $this->dispatch('minToast', titulo: 'Error', mensaje: $e->getMessage(), icono: 'error');
        }
    }

    /**
     * Abrir modal de historial de movimientos agrupado por día y orden de conversión
     */
    public function abrirHistorial()
    {
        $sedeId = Sede::activas()->orderBy('id')->first()?->id ?? 1;

        $movimientos = MovimientoStock::with([
            'producto.categoria',
            'usuario',
            'serviceOrder.cliente',
            'serviceOrder.vehiculo',
            'serviceOrder.service',
            'serviceOrder.tecnico',
        ])
            ->where('sede_id', $sedeId)
            ->orderBy('created_at', 'desc')
            ->get();

        $agrupado = [];
        foreach ($movimientos as $mov) {
            $fecha = $mov->created_at->format('Y-m-d');
            $orderId = $mov->service_order_id;

            if (!isset($agrupado[$fecha])) {
                $agrupado[$fecha] = [];
            }

            // Use a string key: "orden-{id}" or "sin-orden" for null service_order_id
            $groupKey = $orderId ? "orden-{$orderId}" : 'sin-orden';

            if (!isset($agrupado[$fecha][$groupKey])) {
                $agrupado[$fecha][$groupKey] = [
                    'orden' => $orderId ? $mov->serviceOrder : null,
                    'pieza' => $mov->producto,
                    'movimientos' => [],
                ];
            }

            $agrupado[$fecha][$groupKey]['movimientos'][] = $mov;
        }

        $this->historialAgrupado = $agrupado;
        $this->modalHistorial = true;
    }

    public function cerrarHistorial()
    {
        $this->modalHistorial = false;
        $this->historialAgrupado = [];
    }

    public function render()
    {
        return view('livewire.almacen.reportes-pendientes');
    }
}
