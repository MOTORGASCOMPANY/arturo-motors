<?php

namespace App\Services;

use App\Models\ItemSerializado;
use App\Models\ReportePiezaNoEncajada;
use App\Models\ServiceOrder;
use App\Models\Producto;
use App\Models\ProductoStockSede;
use App\Models\KitComponente;
use App\Models\MovimientoStock;
use App\Models\Sede;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class CambioPiezaService
{
    /**
     * Buscar piezas sueltas disponibles en stock para un producto específico
     */
    public function buscarPiezaSueltas(int $productoId, int $sedeId, ?int $excludeItemId = null): \Illuminate\Database\Eloquent\Collection
    {
        $query = ItemSerializado::with('producto.categoria')
            ->where('producto_id', $productoId)
            ->where('estado', 'en_stock')
            ->where('sede_id', $sedeId)
            ->whereNull('service_order_id');

        if ($excludeItemId) {
            $query->where('id', '!=', $excludeItemId);
        }

        return $query->get();
    }

    /**
     * Buscar piezas por nombre/serie en todo el stock
     */
    public function buscarPiezas(string $termino, int $sedeId): \Illuminate\Database\Eloquent\Collection
    {
        $termino = '%' . $termino . '%';

        return ItemSerializado::with('producto.categoria')
            ->whereHas('producto', function ($q) use ($termino) {
                $q->where('nombre', 'LIKE', $termino);
            })
            ->where('estado', 'en_stock')
            ->where('sede_id', $sedeId)
            ->whereNull('service_order_id')
            ->limit(10)
            ->get();
    }

    /**
     * Crear reporte de pieza que no encaja
     */
    public function crearReporte(
        ServiceOrder $orden,
        int $itemNoEncajadoId,
        ?string $motivo = null
    ): ReportePiezaNoEncajada {
        return ReportePiezaNoEncajada::create([
            'service_order_id' => $orden->id,
            'item_no_encajado_id' => $itemNoEncajadoId,
            'tecnico_id' => Auth::id(),
            'motivo_no_encaja' => $motivo,
            'estado' => 'pendiente',
        ]);
    }

    /**
     * Reemplazar pieza: devolver vieja como defectuosa y asignar nueva
     */
    public function reemplazarPieza(ReportePiezaNoEncajada $reporte, int $itemNuevoId): bool
    {
        return DB::transaction(function () use ($reporte, $itemNuevoId) {
            // 1. Devolver pieza que no encajó al almacén como DEFECTUOSA
            $itemADevolver = ItemSerializado::where('id', $reporte->item_no_encajado_id)
                ->where('estado', 'asignado')
                ->lockForUpdate()
                ->first();

            if ($itemADevolver) {
                $kitPadreId = $itemADevolver->kit_padre_id;
                
                $itemADevolver->update([
                    'estado' => 'defectuoso',
                    'service_order_id' => null,
                    'atributos' => array_merge($itemADevolver->atributos ?? [], [
                        'defectuoso' => true,
                        'defectuoso_en' => now()->toDateTimeString(),
                        'motivo_defectuoso' => $reporte->motivo_no_encaja,
                    ]),
                ]);

                // Registrar movimiento de entrada (devolución defectuosa)
                MovimientoStock::registrar(
                    $itemADevolver->producto,
                    'entrada',
                    1,
                    $reporte->service_order_id,
                    Auth::id(),
                    "Devolución pieza DEFECTUOSA - Reporte #{$reporte->id}"
                );
            } else {
                $kitPadreId = null;
            }

            // 2. Asignar pieza nueva (manteniendo kit_padre_id)
            $nuevaPieza = ItemSerializado::where('id', $itemNuevoId)
                ->where('estado', 'en_stock')
                ->lockForUpdate()
                ->first();

            if (!$nuevaPieza) {
                throw new \Exception('La pieza seleccionada ya no está disponible.');
            }

            $nuevaPieza->update([
                'estado' => 'asignado',
                'service_order_id' => $reporte->service_order_id,
                'kit_padre_id' => $kitPadreId,
            ]);

            // Registrar movimiento de salida (asignación)
            MovimientoStock::registrar(
                $nuevaPieza->producto,
                'salida',
                1,
                $reporte->service_order_id,
                Auth::id(),
                "Asignación pieza reemplazo - Reporte #{$reporte->id}"
            );

            // 3. Actualizar reporte
            $reporte->marcarPiezaEncontrada($itemNuevoId);

            return true;
        });
    }

    /**
     * Solicitar al almacén (cuando no hay piezas sueltas)
     */
    public function solicitarAlmacen(ReportePiezaNoEncajada $reporte): bool
    {
        // Devolver pieza que no encajó al almacén como DEFECTUOSA
        $itemADevolver = ItemSerializado::where('id', $reporte->item_no_encajado_id)
            ->where('estado', 'asignado')
            ->first();

        if ($itemADevolver) {
            $itemADevolver->update([
                'estado' => 'defectuoso',
                'service_order_id' => null,
                'atributos' => array_merge($itemADevolver->atributos ?? [], [
                    'defectuoso' => true,
                    'defectuoso_en' => now()->toDateTimeString(),
                    'motivo_defectuoso' => $reporte->motivo_no_encaja,
                ]),
            ]);

            MovimientoStock::registrar(
                $itemADevolver->producto,
                'entrada',
                1,
                $reporte->service_order_id,
                Auth::id(),
                "Devolución pieza DEFECTUOSA - Solicitando almacén - Reporte #{$reporte->id}"
            );
        }

        $reporte->solicitarAlmacen();
        return true;
    }

    /**
     * Buscar kits que contengan el producto necesario
     */
    public function buscarKitsConProducto(int $productoId, int $sedeId): \Illuminate\Database\Eloquent\Collection
    {
        // Buscar productos kit que tengan el componente requerido
        $kitProductoIds = KitComponente::where('producto_componente_id', $productoId)
            ->pluck('producto_kit_id')
            ->toArray();

        if (empty($kitProductoIds)) {
            return collect();
        }

        // Buscar items_serializados que sean kits completos en stock
        return ItemSerializado::with('producto.componentes.componente')
            ->whereIn('producto_id', $kitProductoIds)
            ->where('estado', 'en_stock')
            ->where('sede_id', $sedeId)
            ->whereNull('service_order_id')
            ->get();
    }

    /**
     * Abrir kit y extraer pieza específica
     * Al abrir, TODOS los componentes se convierten en piezas sueltas.
     * La pieza solicitada se asigna a la orden, el resto queda en_stock.
     */
    public function abrirKitYExtraerPieza(
        ItemSerializado $kitItem,
        int $productoPiezaId,
        int $sedeId,
        ?string $observaciones = null,
        ?int $serviceOrderId = null
    ): ItemSerializado {
        return DB::transaction(function () use ($kitItem, $productoPiezaId, $sedeId, $observaciones, $serviceOrderId) {
            // 1. Marcar kit como abierto
            $kitItem->update([
                'estado' => 'abierto',
                'atributos' => array_merge($kitItem->atributos ?? [], [
                    'abierto_por' => Auth::id(),
                    'abierto_en' => now()->toDateTimeString(),
                    'motivo_apertura' => $observaciones ?? 'Extracción pieza para reemplazo',
                ]),
            ]);

            // 2. Buscar hijos existentes (creados por RegistrarItemsKit)
            $existentesPorProducto = ItemSerializado::where('kit_padre_id', $kitItem->id)
                ->get()
                ->groupBy('producto_id');

            // 3. Obtener todos los componentes del kit
            $kitProducto = $kitItem->producto;
            $componentes = $kitProducto->componentes;

            $nuevaPieza = null;

            // 4. Para cada componente: crear piezas sueltas
            foreach ($componentes as $kc) {
                $compId = $kc->producto_componente_id;
                $cantidad = $kc->cantidad_esperada;
                $esRequerido = ($compId == $productoPiezaId);

                // Cuántos ya existen como hijos del kit?
                $yaExistentes = isset($existentesPorProducto[$compId])
                    ? $existentesPorProducto[$compId]
                    : collect();
                $faltantes = max(0, $cantidad - $yaExistentes->count());

                // Reutilizar hijos existentes
                foreach ($yaExistentes as $existente) {
                    if ($esRequerido && !$nuevaPieza) {
                        // Esta es la pieza solicitada → asignar a la orden
                        $existente->update([
                            'estado' => 'asignado',
                            'service_order_id' => $serviceOrderId,
                            'atributos' => array_merge($existente->atributos ?? [], [
                                'extraida_de_kit' => $kitItem->id,
                                'extraida_en' => now()->toDateTimeString(),
                            ]),
                        ]);
                        $nuevaPieza = $existente;
                    }
                    // Si no es requerida, ya está en_stock (de RegistrarItemsKit)
                }

                // Crear las piezas faltantes
                for ($i = 0; $i < $faltantes; $i++) {
                    $item = ItemSerializado::create([
                        'producto_id' => $compId,
                        'kit_padre_id' => $kitItem->id,
                        'serie' => null,
                        'atributos' => [
                            'extraida_de_kit' => $kitItem->id,
                            'extraida_en' => now()->toDateTimeString(),
                        ],
                        'estado' => ($esRequerido && !$nuevaPieza) ? 'asignado' : 'en_stock',
                        'sede_id' => $sedeId,
                        'service_order_id' => ($esRequerido && !$nuevaPieza) ? $serviceOrderId : null,
                    ]);

                    if ($esRequerido && !$nuevaPieza) {
                        $nuevaPieza = $item;
                    }
                }
            }

            if (!$nuevaPieza) {
                throw new \Exception('No se encontró la pieza solicitada en los componentes del kit.');
            }

            // 5. Registrar movimiento: salida del kit sellado
            MovimientoStock::registrar(
                $kitItem->producto,
                'salida',
                1,
                null,
                Auth::id(),
                "Apertura kit #{$kitItem->id} — componentes liberados como piezas sueltas",
                $sedeId
            );

            // 6. Registrar entrada de cada componente como pieza suelta
            foreach ($componentes as $kc) {
                $compId = $kc->producto_componente_id;
                $cantidad = $kc->cantidad_esperada;
                $producto = \App\Models\Producto::find($compId);

                MovimientoStock::registrar(
                    $producto,
                    'entrada',
                    $cantidad,
                    null,
                    Auth::id(),
                    "Apertura kit #{$kitItem->id} — {$cantidad}x {$producto->nombre}",
                    $sedeId
                );
            }

            return $nuevaPieza;
        });
    }

    /**
     * Almacén asigna pieza a reporte (después de abrir kit o directamente)
     */
    public function asignarPiezaDesdeAlmacen(
        ReportePiezaNoEncajada $reporte,
        int $itemNuevoId,
        ?string $observaciones = null
    ): bool {
        return DB::transaction(function () use ($reporte, $itemNuevoId, $observaciones) {
            // 1. Marcar pieza vieja como DEFECTUOSA
            $itemViejo = ItemSerializado::where('id', $reporte->item_no_encajado_id)
                ->where('estado', 'asignado')
                ->lockForUpdate()
                ->first();

            if ($itemViejo) {
                $itemViejo->update([
                    'estado' => 'defectuoso',
                    'service_order_id' => null,
                    'atributos' => array_merge($itemViejo->atributos ?? [], [
                        'defectuoso' => true,
                        'defectuoso_en' => now()->toDateTimeString(),
                        'motivo_defectuoso' => $reporte->motivo_no_encaja,
                    ]),
                ]);

                MovimientoStock::registrar(
                    $itemViejo->producto,
                    'entrada',
                    1,
                    $reporte->service_order_id,
                    Auth::id(),
                    "Devolución pieza DEFECTUOSA - Reporte #{$reporte->id}"
                );
            }

            // 2. Asignar pieza nueva a la orden (manteniendo kit_padre_id del item viejo)
            // Accept 'en_stock' OR 'asignado' (abrirKitYExtraerPieza already sets it to 'asignado')
            $nuevaPieza = ItemSerializado::where('id', $itemNuevoId)
                ->whereIn('estado', ['en_stock', 'asignado'])
                ->lockForUpdate()
                ->first();

            if (!$nuevaPieza) {
                throw new \Exception('La pieza seleccionada ya no está disponible.');
            }

            $nuevaPieza->update([
                'estado' => 'asignado',
                'service_order_id' => $reporte->service_order_id,
                'kit_padre_id' => $itemViejo->kit_padre_id ?? null,
            ]);

            MovimientoStock::registrar(
                $nuevaPieza->producto,
                'salida',
                1,
                $reporte->service_order_id,
                Auth::id(),
                "Asignación pieza reemplazo desde almacén - Reporte #{$reporte->id}"
            );

            // 3. Actualizar reporte
            $reporte->asignarAlmacen(Auth::id());
            $reporte->kitAbierto($itemNuevoId, $observaciones);

            return true;
        });
    }

    /**
     * Confirmar que el cambio se completó (técnico confirma instalación)
     */
    public function confirmarInstalacion(ReportePiezaNoEncajada $reporte): bool
    {
        $reporte->resolver();
        return true;
    }

    /**
     * Confirmar instalación con serial nuevo (técnico ingresa serie de pieza reemplazada)
     */
    public function confirmarInstalacionConSerial(
        ReportePiezaNoEncajada $reporte,
        string $nuevoSerie,
        int $userId
    ): bool {
        return DB::transaction(function () use ($reporte, $nuevoSerie, $userId) {
            // 1. Actualizar serial de la pieza nueva
            $itemNuevo = ItemSerializado::where('id', $reporte->item_nuevo_id)
                ->lockForUpdate()
                ->first();

            if ($itemNuevo) {
                $itemNuevo->update([
                    'serie' => $nuevoSerie,
                    'atributos' => array_merge($itemNuevo->atributos ?? [], [
                        'serie_confirmada_por' => $userId,
                        'serie_confirmada_en' => now()->toDateTimeString(),
                    ]),
                ]);
            }

            // 2. Marcar reporte como resuelto
            $reporte->resolver();

            return true;
        });
    }
}
