<?php

namespace App\Services;

use App\Models\ItemSerializado;
use App\Models\KitPiezaExtraida;
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
            ->whereNull('service_order_id')
            ->whereNull('kit_padre_id'); // solo piezas sueltas, no las que están dentro de un kit

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
            ->whereNull('kit_padre_id') // solo piezas sueltas
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
            ->whereNull('kit_padre_id')
            ->get();
    }

    /**
     * Abrir kit y extraer SOLO la pieza necesaria.
     * El kit queda incompleto — el resto de los componentes siguen conceptualmente
     * dentro del kit, NO se crean piezas sueltas para todo.
     */
    public function abrirKitYExtraerPieza(
        ItemSerializado $kitItem,
        int $productoPiezaId,
        int $sedeId,
        ?string $observaciones = null,
        ?int $serviceOrderId = null,
        ?string $serie = null
    ): ItemSerializado {
        return DB::transaction(function () use ($kitItem, $productoPiezaId, $sedeId, $observaciones, $serviceOrderId, $serie) {
            // 1. Verificar que el producto extraído pertenece al kit
            $kitProducto = $kitItem->producto;
            $componente = $kitProducto->componentes
                ->firstWhere('producto_componente_id', $productoPiezaId);

            if (!$componente) {
                throw new \Exception(
                    "El producto #{$productoPiezaId} no es componente del kit '{$kitProducto->nombre}'."
                );
            }

            // 2. Verificar que esta pieza no fue ya extraída
            $yaExtraida = KitPiezaExtraida::where('item_serializado_id', $kitItem->id)
                ->where('producto_componente_id', $productoPiezaId)
                ->exists();

            if ($yaExtraida) {
                throw new \Exception(
                    "El componente ya fue extraído de este kit anteriormente."
                );
            }

            // 3. Marcar kit como abierto (si no lo está)
            if ($kitItem->estado !== 'abierto') {
                $kitItem->update([
                    'estado' => 'abierto',
                    'atributos' => array_merge($kitItem->atributos ?? [], [
                        'abierto_por' => Auth::id(),
                        'abierto_en' => now()->toDateTimeString(),
                        'motivo_apertura' => $observaciones ?? 'Extracción pieza',
                    ]),
                ]);
            }

            // 4. Crear la pieza extraída como item_serializado independiente
            $piezaExtraida = ItemSerializado::create([
                'producto_id' => $productoPiezaId,
                'kit_padre_id' => $kitItem->id,
                'serie' => $serie,
                'atributos' => [
                    'extraida_de_kit' => $kitItem->id,
                    'extraida_en' => now()->toDateTimeString(),
                ],
                'estado' => $serviceOrderId ? 'asignado' : 'en_stock',
                'sede_id' => $sedeId,
                'service_order_id' => $serviceOrderId,
            ]);

            // 4b. Desvincular el hijo original del kit: sin esto el kit queda
            // "fantasma" (extracción registrada pero el hijo sigue contando
            // como presente en piezasEnKit / receta).
            ItemSerializado::where('kit_padre_id', $kitItem->id)
                ->where('producto_id', $productoPiezaId)
                ->where('id', '!=', $piezaExtraida->id)
                ->whereNotIn('estado', ['defectuoso', 'devuelta_por_no_calzar'])
                ->update(['kit_padre_id' => null]);

            // 5. Registrar extracción en kit_piezas_extraidas
            KitPiezaExtraida::create([
                'item_serializado_id' => $kitItem->id,
                'producto_componente_id' => $productoPiezaId,
                'cantidad_extraida' => 1,
                'service_order_id' => $serviceOrderId,
                'extraida_por' => Auth::id(),
                'extraida_en' => now(),
            ]);

            // 6. Movimiento: salida del componente extraído (nombre del COMPONENTE, no del kit)
            $nombreComponente = $componente->componente->nombre ?? $kitProducto->nombre;
            MovimientoStock::registrar(
                $componente->componente ?? $kitItem->producto,
                'salida',
                1,
                $serviceOrderId,
                Auth::id(),
                "Apertura kit #{$kitItem->id} — extracción {$nombreComponente}",
                $sedeId
            );

            return $piezaExtraida;
        });
    }

    /**
     * Rearmar un kit abierto: recibir pieza de repuesto y devolverla al kit.
     * El kit vuelve a estar completo.
     */
    public function rearmarKit(
        ItemSerializado $kitItem,
        int $piezaRepuestoId,
        int $sedeId,
        ?string $observaciones = null
    ): bool {
        return DB::transaction(function () use ($kitItem, $piezaRepuestoId, $sedeId, $observaciones) {
            if ($kitItem->estado !== 'abierto') {
                throw new \Exception('Solo se pueden rearmar kits en estado abierto.');
            }

            // 1. Verificar que la pieza de repuesto es del tipo correcto
            $piezaRepuesto = ItemSerializado::where('id', $piezaRepuestoId)
                ->where('estado', 'en_stock')
                ->where('sede_id', $sedeId)
                ->lockForUpdate()
                ->first();

            if (!$piezaRepuesto) {
                throw new \Exception('La pieza de repuesto seleccionada no está disponible.');
            }

            // 2. Verificar que es un componente del kit
            $kitProducto = $kitItem->producto;
            $esComponente = $kitProducto->componentes
                ->contains('producto_componente_id', $piezaRepuesto->producto_id);

            if (!$esComponente) {
                throw new \Exception(
                    "La pieza '{$piezaRepuesto->producto->nombre}' no es componente del kit '{$kitProducto->nombre}'."
                );
            }

            // 3. Verificar que esta pieza fue extraída previamente
            $extraccion = KitPiezaExtraida::where('item_serializado_id', $kitItem->id)
                ->where('producto_componente_id', $piezaRepuesto->producto_id)
                ->exists();

            if (!$extraccion) {
                throw new \Exception(
                    "Esta pieza no fue extraída de este kit, no se puede rearmar."
                );
            }

            // 4. Eliminar la extracción
            KitPiezaExtraida::where('item_serializado_id', $kitItem->id)
                ->where('producto_componente_id', $piezaRepuesto->producto_id)
                ->delete();

            // 5. Eliminar la pieza de repuesto (vuelve a ser parte del kit)
            $piezaRepuesto->delete();

            // 6. Verificar si el kit está completo
            $extraccionesRestantes = KitPiezaExtraida::where('item_serializado_id', $kitItem->id)
                ->count();

            if ($extraccionesRestantes === 0) {
                // Kit completo — vende a en_stock
                $kitItem->update([
                    'estado' => 'en_stock',
                    'atributos' => array_merge($kitItem->atributos ?? [], [
                        'rearmado_por' => Auth::id(),
                        'rearmado_en' => now()->toDateTimeString(),
                    ]),
                ]);
            }

            // 7. Movimiento: entrada del componente al kit
            MovimientoStock::registrar(
                $kitItem->producto,
                'entrada',
                1,
                null,
                Auth::id(),
                "Rearme kit #{$kitItem->id} — pieza devuelta",
                $sedeId
            );

            return true;
        });
    }

    /**
     * Obtener las piezas que faltan en un kit (las que fueron extraídas).
     */
    public function piezasFaltantesEnKit(ItemSerializado $kitItem): \Illuminate\Support\Collection
    {
        $extracciones = KitPiezaExtraida::where('item_serializado_id', $kitItem->id)
            ->get();

        $extraidas = $extracciones->pluck('producto_componente_id')->toArray();

        return $kitItem->producto->componentes
            ->filter(fn ($kc) => in_array($kc->producto_componente_id, $extraidas))
            ->map(fn ($kc) => [
                'producto_id' => $kc->producto_componente_id,
                'nombre' => $kc->componente->nombre ?? 'Desconocido',
                'cantidad_extraida' => $extracciones
                    ->where('producto_componente_id', $kc->producto_componente_id)
                    ->sum('cantidad_extraida'),
            ]);
    }

    /**
     * Verificar si un kit está completo (sin piezas extraídas).
     */
    public function kitEstaCompleto(ItemSerializado $kitItem): bool
    {
        return KitPiezaExtraida::where('item_serializado_id', $kitItem->id)->count() === 0;
    }

    /**
     * Almacén asigna pieza a reporte (después de abrir kit o directamente)
     */
    public function asignarPiezaDesdeAlmacen(
        ReportePiezaNoEncajada $reporte,
        int $itemNuevoId,
        ?string $observaciones = null,
        ?string $serie = null
    ): bool {
        return DB::transaction(function () use ($reporte, $itemNuevoId, $observaciones, $serie) {
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

            $updateData = [
                'estado' => 'asignado',
                'service_order_id' => $reporte->service_order_id,
                'kit_padre_id' => $itemViejo->kit_padre_id ?? null,
            ];

            // Guardar serie si se proporcionó
            if ($serie) {
                $updateData['serie'] = $serie;
                $updateData['atributos'] = array_merge($nuevaPieza->atributos ?? [], [
                    'serie_capturada_por_almacen' => Auth::id(),
                    'serie_capturada_en' => now()->toDateTimeString(),
                ]);
            }

            $nuevaPieza->update($updateData);

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
