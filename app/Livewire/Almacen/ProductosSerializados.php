<?php

namespace App\Livewire\Almacen;

use App\Models\ItemSerializado;
use App\Models\MovimientoStock;
use App\Models\Producto;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

trait ProductosSerializados
{
    public array $series = [];
    public array $produces = [];

    public function guardarProductos(): void
    {
        $conCantidad = collect($this->cantidades)->filter(fn ($c) => $c > 0)->toArray();

        if (empty($conCantidad)) {
            $this->dispatch('swal-init', tipo: 'warning', titulo: 'Atención', mensaje: 'Poné cantidad en al menos un producto.');
            return;
        }

        try {
            foreach ($conCantidad as $productoId => $cantidad) {
                $producto = Producto::find($productoId);
                if (!$producto || !$producto->categoria?->es_serializado) continue;

                $esquema = $producto->categoria->esquema_atributos ?? ['serie'];
                $campos = is_string($esquema) ? (json_decode($esquema, true) ?? ['serie']) : $esquema;
                $camposUnidad = self::camposPorUnidad($campos);
                $seriesProducto = $this->series[$productoId] ?? [];

                for ($i = 0; $i < $cantidad; $i++) {
                    $data = $seriesProducto[$i] ?? [];
                    foreach ($camposUnidad as $campo) {
                        if (empty($data[$campo] ?? null)) {
                            $this->addError('general', "Falta {$campo} para {$producto->nombre} (unidad " . ($i + 1) . ").");
                            $this->dispatch('swal-init', tipo: 'error', titulo: 'Campo faltante',
                                mensaje: "Falta {$campo} para {$producto->nombre} (unidad " . ($i + 1) . ").");
                            return;
                        }
                    }
                }
            }
        } catch (\Throwable $e) {
            $this->avisarError($e, 'swal-init', 'No se pudieron validar los productos. Revisá los datos e intentá de nuevo.');
            return;
        }

        $usuarioId = Auth::id();
        $sedeId = $this->sedeId;
        $totalRegistrado = 0;

        try {
            DB::transaction(function () use ($conCantidad, $usuarioId, $sedeId, &$totalRegistrado) {
                foreach ($conCantidad as $productoId => $cantidad) {
                    $producto = Producto::find($productoId);
                    if (!$producto) continue;

                    $esquema = $producto->categoria->esquema_atributos ?? ['serie'];
                    $campos = is_string($esquema) ? (json_decode($esquema, true) ?? ['serie']) : $esquema;
                    $camposUnidad = self::camposPorUnidad($campos);
                    $seriesProducto = $this->series[$productoId] ?? [];
                    $esSerializado = $producto->categoria?->es_serializado ?? false;
                    $produceCompartido = $this->produces[$productoId] ?? null;

                    for ($i = 0; $i < $cantidad; $i++) {
                        $data = $seriesProducto[$i] ?? [];

                        $atributos = [
                            'recepcion_fecha' => now()->toDateString(),
                            'registrado_por' => $usuarioId,
                        ];

                        if ($produceCompartido !== null && $produceCompartido !== '') {
                            $atributos['produce'] = $produceCompartido;
                        }

                        foreach ($camposUnidad as $campo) {
                            $valor = $data[$campo] ?? null;
                            if ($valor !== null && $valor !== '') {
                                $atributos[$campo] = $valor;
                            }
                        }

                        $serie = $esSerializado ? ($data['serie'] ?? null) : null;

                        ItemSerializado::create([
                            'producto_id' => $producto->id,
                            'serie' => $serie,
                            'atributos' => $atributos,
                            'estado' => 'en_stock',
                            'sede_id' => $sedeId,
                        ]);
                    }

                    MovimientoStock::registrar($producto, 'entrada', $cantidad, null, $usuarioId, 'Entrada por recepción', $sedeId);
                    $totalRegistrado += $cantidad;
                }
            });

            $this->dispatch('swal-init', tipo: 'success', titulo: '¡Recepción registrada!', mensaje: "{$totalRegistrado} producto(s) recibido(s).");
            $this->redirect(route('almacen.recepciones.listado'));
        } catch (\Throwable $e) {
            $this->avisarError($e, 'swal-init', 'No se pudo registrar la recepción.');
        }
    }
}
