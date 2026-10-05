<?php

namespace App\Livewire\Almacen;

use App\Models\ItemSerializado;
use App\Models\Producto;
use App\Models\ReportePiezaNoEncajada;
use App\Models\ServiceOrder;

trait ConversionesSeleccionPieza
{
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

        $esSerial = Producto::esSerializable($item->producto);
        $sedeId = $this->sedeOperativa();

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

            $piezasConSerie = ItemSerializado::whereIn('kit_padre_id', $kits->pluck('id'))
                ->where('producto_id', $item->producto_id)
                ->whereNotNull('serie')
                ->whereNotIn('estado', ['defectuoso', 'devuelta_por_no_calzar'])
                ->get()
                ->keyBy('kit_padre_id');

            $this->kitsDisponibles = $kits->map(function($k) use ($item, $piezasConSerie) {
                $piezaConSerie = $piezasConSerie->get($k->id);
                return [
                    'id' => $k->id,
                    'producto' => $k->producto->nombre,
                    'serie' => $k->serie,
                    'pieza_con_serie_id' => $piezaConSerie?->id,
                    'serie_disponible' => $piezaConSerie?->serie,
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
            // No serializado: se despacha por cantidad. Antes de ofrecerlo hay
            // que comprobar stock suelto real en la sede operativa; si es 0,
            // no se muestra la opción (mismo criterio que la rama serializable).
            $disponible = (int) $item->producto->stockSueltoEnSede($sedeId);

            if ($disponible <= 0) {
                $this->dispatch('minToast', titulo: 'Sin stock', mensaje: "No hay stock suelto de {$item->producto->nombre} en esta sede.", icono: 'warning');
                $this->resetReemplazo();
                return;
            }

            $this->piezaReemplazarId = $itemId;
            $this->metodoReemplazo = 'cantidad';
            $this->stockDisponible = $disponible;
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
        $this->nuevaSerie = '';
        $this->piezaKitConSerie = null;

        $item = ItemSerializado::find($this->piezaReemplazarId);
        if ($item) {
            $hijo = ItemSerializado::where('kit_padre_id', $kitId)
                ->where('producto_id', $item->producto_id)
                ->whereNotNull('serie')
                ->whereNotIn('estado', ['defectuoso', 'devuelta_por_no_calzar'])
                ->first();

            if ($hijo) {
                $this->piezaKitConSerie = ['id' => $hijo->id, 'serie' => $hijo->serie];
                $this->metodoReemplazo = 'kit_con_serie';
                return;
            }
        }

        $this->metodoReemplazo = 'kit_seleccionado';
    }

    public function confirmarReemplazoKit()
    {
        // ── Caso A: el kit ya tiene el hijo con serie → reutilizar ese item ──
        if ($this->piezaKitConSerie) {
            try {
                $reporte = $this->crearReporte($this->conversionSeleccionadaId, $this->piezaReemplazarId, $this->observacion);
                $kitItem = ItemSerializado::find($this->kitSeleccionadoId);
                $pieza = ItemSerializado::find($this->piezaKitConSerie['id']);

                $this->cambioPiezaService->reemplazarUsandoHijoDeKit(
                    $reporte, $kitItem, $pieza, $this->observacion
                );

                $this->dispatch('minToast', titulo: 'Reemplazado', mensaje: "Serie {$pieza->serie} asignada.", icono: 'success');
                $this->resetReemplazo();
            } catch (\Throwable $e) {
                report($e);
                $this->dispatch('minToast', titulo: 'Error', mensaje: $e->getMessage(), icono: 'error');
            }
            return;
        }

        // ── Caso B: el kit no tiene serie → flujo actual (crear item con serie nueva) ──
        $serie = trim($this->nuevaSerie);
        if (empty($serie)) {
            $this->dispatch('minToast', titulo: 'Error', mensaje: 'Ingrese la serie.', icono: 'error');
            return;
        }
        if (ItemSerializado::existeSerie($serie)) {
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
}
