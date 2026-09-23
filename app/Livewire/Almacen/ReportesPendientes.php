<?php

namespace App\Livewire\Almacen;

use App\Models\ItemSerializado;
use App\Models\KitComponente;
use App\Models\MovimientoStock;
use App\Models\ReportePiezaNoEncajada;
use App\Models\ServiceOrder;
use App\Models\Sede;
use App\Services\CambioPiezaService;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class ReportesPendientes extends Component
{
    use WithPagination;

    public string $busqueda = '';
    public int $perPage = 15;

    public bool $modalAbierto = false;
    public ?int $conversionSeleccionadaId = null;

    public ?int $piezaReemplazarId = null;
    public string $nuevaSerie = '';
    public ?string $metodoReemplazo = null;
    public ?int $kitSeleccionadoId = null;
    public string $busquedaKit = '';
    public array $kitsDisponibles = [];
    public string $cantidadAdicional = '1';
    public string $observacion = '';

    public bool $modalPartesAbierto = false;

    
    public array $piezasSueltas = [];
    public ?int $piezaSueltaSeleccionadaId = null;

    protected CambioPiezaService $cambioPiezaService;

    public function boot(CambioPiezaService $cambioPiezaService): void
    {
        $this->cambioPiezaService = $cambioPiezaService;
    }

    
    
    

    public function getConversionesProperty()
    {
        $query = ServiceOrder::with(['cliente', 'vehiculo', 'service', 'tecnico'])
            ->where('estado', 'en_conversion')
            ->whereHas('items', fn($q) => $q->whereNull('kit_padre_id')->whereHas('piezasEnKit'));

        if (!empty($this->busqueda)) {
            $query->where(function ($q) {
                $q->where('id', 'like', "%{$this->busqueda}%")
                  ->orWhereHas('cliente', fn($cq) => $cq->where('nombre', 'like', "%{$this->busqueda}%"))
                  ->orWhereHas('vehiculo', fn($vq) => $vq->where('placa', 'like', "%{$this->busqueda}%"))
                  ->orWhereHas('tecnico', fn($tq) => $tq->where('name', 'like', "%{$this->busqueda}%"));
            });
        }

        return $query->orderBy('fecha_inicio_conversion', 'asc')->paginate($this->perPage);
    }

    public function nombreKit(ServiceOrder $orden): string
    {
        return $orden->items->first()?->producto->nombre ?? '—';
    }

    public function resumenConversion(ServiceOrder $orden): array
    {
        $pendientes = ReportePiezaNoEncajada::where('service_order_id', $orden->id)
            ->whereIn('estado', ['pendiente', 'buscando_pieza', 'solicitando_almacen'])->count();
        $despachadas = ReportePiezaNoEncajada::where('service_order_id', $orden->id)
            ->whereIn('estado', ['kit_abierto', 'resuelto'])->count();

        return compact('pendientes', 'despachadas');
    }

    
    
    

    public function abrirModal(int $ordenId)
    {
        $orden = ServiceOrder::find($ordenId);
        if (!$orden) return;

        if (!$orden->fecha_inicio_conversion) {
            $this->dispatch('minToast', titulo: 'Conversión no iniciada', mensaje: 'El técnico debe iniciar la conversión primero.', icono: 'warning');
            return;
        }

        $this->conversionSeleccionadaId = $ordenId;
        $this->modalAbierto = true;
        $this->resetReemplazo();
    }

    public function cerrarModal()
    {
        $this->modalAbierto = false;
        $this->conversionSeleccionadaId = null;
        $this->resetReemplazo();
    }

    private function resetReemplazo()
    {
        $this->piezaReemplazarId = null;
        $this->nuevaSerie = '';
        $this->metodoReemplazo = null;
        $this->kitSeleccionadoId = null;
        $this->busquedaKit = '';
        $this->kitsDisponibles = [];
        $this->cantidadAdicional = '1';
        $this->observacion = '';
        $this->modalPartesAbierto = false;
        $this->piezasSueltas = [];
        $this->piezaSueltaSeleccionadaId = null;
    }

    
    
    

    public function getConversionSeleccionadaProperty(): ?ServiceOrder
    {
        if (!$this->conversionSeleccionadaId) return null;
        return ServiceOrder::with(['cliente', 'vehiculo', 'service', 'tecnico'])->find($this->conversionSeleccionadaId);
    }

    public function getKitPadreProperty(): ?ItemSerializado
    {
        $orden = $this->conversionSeleccionada;
        if (!$orden) return null;
        return $orden->items()->whereNull('kit_padre_id')->whereHas('piezasEnKit')->with('producto.categoria')->first();
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
        $ids = $padre->producto->componentes->pluck('producto_componente_id')->toArray();
        return $this->conversionSeleccionada->items()->where('estado', 'asignado')->whereNotNull('kit_padre_id')
            ->whereIn('producto_id', $ids)->get()
            ->filter(fn($i) => $this->esSerializable($i->producto->nombre))->values();
    }

    public function getItemsCantidadProperty()
    {
        $padre = $this->kitPadre;
        if (!$padre) return collect();
        $ids = $padre->producto->componentes->pluck('producto_componente_id')->toArray();
        return $this->conversionSeleccionada->items()->where('estado', 'asignado')->whereNotNull('kit_padre_id')
            ->whereIn('producto_id', $ids)->get()
            ->filter(fn($i) => !$this->esSerializable($i->producto->nombre))->values();
    }

    public function getTodasPiezasKitProperty()
    {
        $padre = $this->kitPadre;
        if (!$padre) return collect();
        return KitComponente::where('producto_kit_id', $padre->producto_id)->with('componente.categoria')->get()
            ->map(fn($kc) => (object) [
                'producto_id' => $kc->producto_componente_id,
                'nombre' => $kc->componente->nombre,
                'categoria' => $kc->componente->categoria->nombre,
                'es_serializado' => $this->esSerializable($kc->componente->nombre),
                'cantidad_esperada' => $kc->cantidad_esperada,
            ]);
    }

    public function getItemsReportadosProperty(): array
    {
        $orden = $this->conversionSeleccionada;
        if (!$orden) return [];
        return ReportePiezaNoEncajada::where('service_order_id', $orden->id)
            ->whereIn('estado', ['pendiente', 'buscando_pieza', 'solicitando_almacen'])->pluck('item_no_encajado_id')->toArray();
    }

    public function getItemsReemplazadosProperty(): array
    {
        $orden = $this->conversionSeleccionada;
        if (!$orden) return [];
        return ReportePiezaNoEncajada::where('service_order_id', $orden->id)
            ->whereIn('estado', ['kit_abierto', 'resuelto'])->pluck('item_no_encajado_id')->toArray();
    }

    public function getHistorialProperty()
    {
        $orden = $this->conversionSeleccionada;
        if (!$orden) return collect();
        return ReportePiezaNoEncajada::where('service_order_id', $orden->id)
            ->whereIn('estado', ['kit_abierto', 'resuelto'])
            ->with('itemNoEncajado.producto')
            ->orderBy('created_at', 'desc')->get()
            ->map(fn($r) => (object) [
                'id' => $r->id,
                'pieza' => $r->itemNoEncajado->producto->nombre ?? 'Pieza',
                'serie_vieja' => $r->itemNoEncajado->serie ?? '—',
                'motivo' => $r->motivo_no_encaja,
                'estado' => $r->estado,
                'fecha' => $r->created_at->format('d/m H:i'),
            ]);
    }

    public function getTimelineConversionProperty()
    {
        $orden = $this->conversionSeleccionada;
        if (!$orden) return collect();
        return $orden->historialEstados()->with('usuario')->latest()->get()
            ->map(fn($h) => (object) [
                'estado_nuevo' => $h->estado_nuevo,
                'estado_anterior' => $h->estado_anterior,
                'fecha' => $h->created_at->format('d/m/Y H:i'),
                'usuario' => $h->usuario->name ?? 'Sistema',
                'esConversion' => in_array($h->estado_nuevo, ['en_conversion', 'conversion_completada']),
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

    public function abrirPartesGenerales() { $this->modalPartesAbierto = true; }
    public function cerrarPartesGenerales() { $this->modalPartesAbierto = false; }

    
    private function crearReporte(int $ordenId, int $itemId, string $observacion = ''): ?ReportePiezaNoEncajada
    {
        $orden = ServiceOrder::find($ordenId);
        if (!$orden) return null;

        $existe = ReportePiezaNoEncajada::where('service_order_id', $ordenId)
            ->where('item_no_encajado_id', $itemId)
            ->whereIn('estado', ['pendiente', 'buscando_pieza', 'solicitando_almacen'])->first();

        if ($existe) return $existe;

        $motivo = !empty($observacion) ? $observacion : 'Reemplazo directo por almacén';

        return ReportePiezaNoEncajada::create([
            'service_order_id' => $ordenId,
            'item_no_encajado_id' => $itemId,
            'tecnico_id' => $orden->tecnico_id,
            'motivo_no_encaja' => $motivo,
            'estado' => 'pendiente',
        ]);
    }

    
    
    

    public function seleccionarPieza(int $itemId)
    {
        $item = ItemSerializado::with('producto')->find($itemId);
        if (!$item) return;

        $esSerial = $this->esSerializable($item->producto->nombre);
        $sedeId = Sede::activas()->orderBy('id')->first()?->id ?? 1;

        if ($esSerial) {
            
            $sueltas = $this->cambioPiezaService->buscarPiezaSueltas($item->producto_id, $sedeId, $itemId);

            if ($sueltas->isNotEmpty()) {
                
                $sueltas = $sueltas->filter(function ($s) {
                    $esSerial = $s->producto->categoria->es_serializado ?? false;
                    return !$esSerial || !empty($s->serie);
                });

                if ($sueltas->isNotEmpty()) {
                    
                    $this->piezaReemplazarId = $itemId;
                    $this->metodoReemplazo = 'buscando_suelta';
                    $this->piezasSueltas = $sueltas->map(fn($s) => [
                        'id' => $s->id,
                        'serie' => $s->serie,
                        'producto' => $s->producto->nombre,
                        'atributos' => $s->atributos ?? [],
                    ])->toArray();
                    return;
                }
            }

            
            $kits = $this->cambioPiezaService->buscarKitsConProducto($item->producto_id, $sedeId);
            if ($kits->isEmpty()) {
                $this->dispatch('minToast', titulo: 'Sin stock', mensaje: 'No hay pieza suelta ni kit disponible.', icono: 'warning');
                $this->resetReemplazo();
                return;
            }

            
            $this->piezaReemplazarId = $itemId;
            $this->metodoReemplazo = 'buscando_kit';
            $this->kitsDisponibles = $kits->map(function($k) use ($item) {
                return [
                    'id' => $k->id,
                    'producto' => $k->producto->nombre,
                    'serie' => $k->serie,
                    'componentes' => $k->producto->componentes->map(function($c) use ($item) {
                        $comp = $c->componente;
                        $attrs = $comp->atributos ?? [];
                        return [
                            'nombre' => $comp->nombre,
                            'es_necesaria' => $c->producto_componente_id == $item->producto_id,
                            'marca' => $attrs['marca'] ?? null,
                            'generacion' => $attrs['generacion'] ?? null,
                            'capacidad' => $attrs['capacidad'] ?? null,
                            'produce' => $attrs['produce'] ?? null,
                        ];
                    })->toArray(),
                ];
            })->toArray();
        } else {
            
            $this->piezaReemplazarId = $itemId;
            $this->metodoReemplazo = 'cantidad';
            $this->cantidadAdicional = '1';
        }
    }

    
    
    

    public function seleccionarPiezaSuelta(int $piezaSueltaId)
    {
        $this->piezaSueltaSeleccionadaId = $piezaSueltaId;
    }

    public function confirmarPiezaSuelta()
    {
        if (!$this->piezaSueltaSeleccionadaId || !$this->piezaReemplazarId) {
            $this->dispatch('minToast', titulo: 'Error', mensaje: 'Seleccioná una pieza.', icono: 'error');
            return;
        }

        try {
            $reporte = $this->crearReporte($this->conversionSeleccionadaId, $this->piezaReemplazarId, $this->observacion);
            $this->cambioPiezaService->asignarPiezaDesdeAlmacen(
                $reporte,
                $this->piezaSueltaSeleccionadaId,
                'Pieza suelta elegida por almacén'
            );

            $suelta = ItemSerializado::find($this->piezaSueltaSeleccionadaId);
            $this->dispatch('minToast', titulo: 'Reemplazado', mensaje: "Serie {$suelta->serie} asignada.", icono: 'success');
            $this->resetReemplazo();
        } catch (\Throwable $e) {
            report($e);
            $this->dispatch('minToast', titulo: 'Error', mensaje: $e->getMessage(), icono: 'error');
        }
    }

    
    
    

    public function seleccionarKit(int $kitId)
    {
        $this->kitSeleccionadoId = $kitId;
        $this->metodoReemplazo = 'kit_seleccionado';
        $this->nuevaSerie = '';
    }

    public function confirmarReemplazoKit()
    {
        $serie = trim($this->nuevaSerie);
        if (empty($serie)) {
            $this->dispatch('minToast', titulo: 'Error', mensaje: 'Ingrese la serie.', icono: 'error');
            return;
        }
        if (ItemSerializado::where('serie', $serie)->exists()) {
            $this->dispatch('minToast', titulo: 'Error', mensaje: "La serie \"{$serie}\" ya existe.", icono: 'error');
            return;
        }

        try {
            
            $reporte = $this->crearReporte($this->conversionSeleccionadaId, $this->piezaReemplazarId, $this->observacion);
            $kitItem = ItemSerializado::find($this->kitSeleccionadoId);

            $nueva = $this->cambioPiezaService->abrirKitYExtraerPieza(
                $kitItem, $reporte->itemNoEncajado->producto_id,
                $kitItem->sede_id ?? 1, "Apertura para reemplazo", $reporte->service_order_id, $serie
            );
            $this->cambioPiezaService->asignarPiezaDesdeAlmacen($reporte, $nueva->id, "Extraído del kit #{$kitItem->id}", $serie);

            $this->dispatch('minToast', titulo: 'Reemplazado', mensaje: "Serie {$serie} asignada.", icono: 'success');
            $this->resetReemplazo();
        } catch (\Throwable $e) {
            report($e);
            $this->dispatch('minToast', titulo: 'Error', mensaje: $e->getMessage(), icono: 'error');
        }
    }

    
    
    

    public function confirmarCantidadAdicional()
    {
        $cantidad = (int) $this->cantidadAdicional;
        if ($cantidad <= 0) {
            $this->dispatch('minToast', titulo: 'Error', mensaje: 'Cantidad inválida.', icono: 'error');
            return;
        }

        try {
            $orden = $this->conversionSeleccionada;
            $item = ItemSerializado::find($this->piezaReemplazarId);
            if (!$item) return;

            $sedeId = Sede::activas()->orderBy('id')->first()?->id ?? 1;

            
            // Despacho almacén → orden: RESTA stock (salida), no suma
            MovimientoStock::registrar(
                $item->producto, 'salida', $cantidad, $orden->id, Auth::id(),
                "Cantidad adicional para orden #{$orden->id}", $sedeId
            );

            $nuevo = ItemSerializado::create([
                'producto_id' => $item->producto_id,
                'serie' => null,
                'estado' => 'asignado',
                'service_order_id' => $orden->id,
                'sede_id' => $sedeId,
                'atributos' => ['tipo' => 'cantidad', 'cantidad_solicitada' => $cantidad, 'creado_por' => 'almacen', 'creado_en' => now()->toDateTimeString()],
            ]);

            $motivo = !empty($this->observacion) ? $this->observacion : "Cantidad adicional x{$cantidad}";

            ReportePiezaNoEncajada::create([
                'service_order_id' => $orden->id,
                'item_no_encajado_id' => $nuevo->id,
                'tecnico_id' => $orden->tecnico_id,
                'motivo_no_encaja' => $motivo,
                'estado' => 'resuelto',
            ]);

            $this->dispatch('minToast', titulo: 'Agregado', mensaje: "x{$cantidad} piezas asignadas.", icono: 'success');
            $this->resetReemplazo();
        } catch (\Throwable $e) {
            report($e);
            $this->dispatch('minToast', titulo: 'Error', mensaje: $e->getMessage(), icono: 'error');
        }
    }

    public function cancelarReemplazo() { $this->resetReemplazo(); }

    public function render() { return view('livewire.almacen.reportes-pendientes'); }
}
