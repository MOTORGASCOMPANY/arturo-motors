<?php

namespace App\Livewire\Almacen;

use App\Models\ItemSerializado;
use App\Models\KitComponente;
use App\Models\MovimientoStock;
use App\Models\Producto;
use App\Models\ProductoStockSede;
use App\Models\Traslado;
use App\Models\TrasladoDetalle;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

trait TrasladoConfirmacion
{
    public function confirmarTraslado()
    {
        $this->validate(['sedeDestinoId' => 'required|exists:sedes,id']);

        if ($this->resumenVacio) {
            $this->addError('general', 'Selecciona al menos un item antes de confirmar.');
            return;
        }

        $this->buildChecklist();
        $this->mostrarChecklist = true;
    }

    protected function buildChecklist(): void
    {
        $data = [];
        $seleccionadosIds = array_keys($this->itemsSeleccionados);

        
        $kitsPadres = ItemSerializado::with('producto')
            ->whereIn('id', $seleccionadosIds)
            ->whereNull('kit_padre_id')
            ->whereHas('producto.categoria', fn ($q) => $q->where('es_kit', true))
            ->get();

        foreach ($kitsPadres as $kitItem) {
            $receta = KitComponente::with('componente')
                ->where('producto_kit_id', $kitItem->producto_id)
                ->get();

            
            $hijosSeleccionados = ItemSerializado::whereIn('id', $seleccionadosIds)
                ->where('kit_padre_id', $kitItem->id)
                ->pluck('producto_id')
                ->countBy()
                ->toArray();

            $componentes = $receta->map(fn ($r) => [
                'nombre' => $r->componente?->nombre ?? '—',
                'esperada' => $r->cantidad_esperada,
                'presente' => $hijosSeleccionados[$r->producto_componente_id] ?? 0,
                'completo' => ($hijosSeleccionados[$r->producto_componente_id] ?? 0) >= $r->cantidad_esperada,
            ]);

            $totalEsperado = $receta->sum('cantidad_esperada');
            $totalPresente = array_sum($hijosSeleccionados);

            $data[] = [
                'nombre' => $kitItem->producto->nombre,
                'tipo' => 'kit',
                'es_completo' => $totalPresente >= $totalEsperado && $totalEsperado > 0,
                'detalle' => "#{$kitItem->id}",
                'totalEsperado' => $totalEsperado,
                'totalPresente' => $totalPresente,
                'componentes' => $componentes,
            ];
        }

        
        $sueltos = ItemSerializado::with('producto')
            ->whereIn('id', $seleccionadosIds)
            ->whereNull('kit_padre_id')
            ->whereDoesntHave('producto.categoria', fn ($q) => $q->where('es_kit', true))
            ->get();

        foreach ($sueltos as $pieza) {
            $data[] = [
                'nombre' => $pieza->producto->nombre,
                'tipo' => 'pieza',
                'detalle' => $pieza->serie ?? "Item #{$pieza->id}",
            ];
        }

        
        foreach ($this->cantidadSeleccionados as $productoId => $cantidad) {
            $producto = Producto::find($productoId);
            if (! $producto) {
                continue;
            }

            $data[] = [
                'nombre' => $producto->nombre,
                'tipo' => 'cantidad',
                'detalle' => "×{$cantidad}",
            ];
        }

        $this->checklistData = $data;
    }

    
    
    

    public function confirmarEnvio()
    {
        $this->mostrarChecklist = false;
        $sedeOrigenId = $this->sedeOrigenId();

        $this->buildChecklist();
        $kitsEnChecklist = collect($this->checklistData)->where('tipo', 'kit');
        $esKitCompleto = $kitsEnChecklist->isNotEmpty()
            ? $kitsEnChecklist->every('es_completo', true)
            : null;

        try {
            DB::transaction(function () use ($sedeOrigenId, $esKitCompleto) {
                $traslado = Traslado::create([
                    'sede_destino_id' => $this->sedeDestinoId,
                    'enviado_por' => Auth::id(),
                    'observaciones' => $this->observaciones ?: null,
                    'es_kit_completo' => $esKitCompleto,
                ]);

                
                foreach (array_keys($this->itemsSeleccionados) as $itemId) {
                    $item = ItemSerializado::where('id', $itemId)
                        ->where('estado', 'en_stock')
                        ->where('sede_id', $sedeOrigenId)
                        ->lockForUpdate()
                        ->first();

                    if (! $item) {
                        throw new \RuntimeException('Uno de los items ya no está disponible.');
                    }

                    $item->update(['sede_id' => $this->sedeDestinoId]);

                    $producto = Producto::find($item->producto_id);
                    if ($producto) {
                        MovimientoStock::registrar(
                            $producto, 'salida', 1, null, Auth::id(),
                            "Traslado #{$traslado->id}", $sedeOrigenId
                        );
                        MovimientoStock::registrar(
                            $producto, 'entrada', 1, null, Auth::id(),
                            "Traslado #{$traslado->id}", $this->sedeDestinoId
                        );
                    }

                    TrasladoDetalle::create([
                        'traslado_id' => $traslado->id,
                        'producto_id' => $item->producto_id,
                        'item_serializado_id' => $item->id,
                        'cantidad' => null,
                    ]);
                }

                
                foreach ($this->cantidadSeleccionados as $productoId => $cantidad) {
                    $producto = Producto::with('categoria')->find($productoId);
                    if (! $producto) {
                        continue;
                    }

                    $stock = \App\Models\ProductoStockSede::where('producto_id', $productoId)
                        ->where('sede_id', $sedeOrigenId)
                        ->lockForUpdate()
                        ->first();

                    $enKits = ItemSerializado::where('producto_id', $productoId)
                        ->whereNotNull('kit_padre_id')
                        ->whereIn('estado', ['en_stock', 'abierto', 'completado', 'asignado'])
                        ->where('sede_id', $sedeOrigenId)
                        ->count();

                    $disponible = max(0, (int) ($stock->cantidad ?? 0) - $enKits);
                    if ($disponible < $cantidad) {
                        throw new \RuntimeException("No hay stock suelto suficiente de {$producto->nombre}. Disponible: {$disponible}.");
                    }

                    MovimientoStock::registrar(
                        $producto, 'salida', $cantidad, null, Auth::id(),
                        "Traslado #{$traslado->id}", $sedeOrigenId
                    );
                    MovimientoStock::registrar(
                        $producto, 'entrada', $cantidad, null, Auth::id(),
                        "Traslado #{$traslado->id}", $this->sedeDestinoId
                    );

                    TrasladoDetalle::create([
                        'traslado_id' => $traslado->id,
                        'producto_id' => $productoId,
                        'cantidad' => $cantidad,
                    ]);
                }
            });
        } catch (\RuntimeException $e) {
            $this->addError('general', $e->getMessage());
            return;
        } catch (\Throwable $e) {
            report($e);
            $this->addError('general', 'Ocurrió un error al registrar el traslado.');
            return;
        }

        $this->dispatch('minAlert', titulo: 'Listo!', mensaje: 'Traslado registrado correctamente.', icono: 'success');

        return $this->redirect(route('almacen.traslados.listado'), navigate: true);
    }

    public function cerrarChecklist()
    {
        $this->mostrarChecklist = false;
    }
}
