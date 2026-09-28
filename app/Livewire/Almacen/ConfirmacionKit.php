<?php

namespace App\Livewire\Almacen;

use App\Models\ItemSerializado;
use App\Models\MovimientoStock;
use App\Models\Producto;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

/**
 * Validación y persistencia del kit que se está recibiendo en el paso de componentes.
 */
trait ConfirmacionKit
{
    public function confirmarModal(): void
    {
        if (empty($this->modalComponentes)) {
            $this->dispatch('swal-kit', tipo: 'warning', titulo: 'Atención', mensaje: 'No hay componentes en este kit.');
            return;
        }

        try {
            $seriesIngresadas = [];

            foreach ($this->modalComponentes as $idx => $componente) {
                if (!($componente['es_serializado'] ?? false)) continue;

                $camposEsquema = $componente['campos_esquema'] ?? ['serie'];
                $unidades = $componente['unidades'] ?? [];
                $nombreComponente = $componente['nombre'] ?? 'componente';

                foreach ($unidades as $uIdx => $unidad) {
                    foreach ($camposEsquema as $campo) {
                        if ($campo === 'produce') continue;
                        $valor = $unidad[$campo] ?? null;
                        if (empty($valor)) {
                            $this->dispatch('swal-kit', tipo: 'error', titulo: 'Campo faltante',
                                mensaje: ucfirst(str_replace('_', ' ', $campo)) . " es obligatorio para: {$nombreComponente} (unidad " . ($uIdx + 1) . ")");
                            return;
                        }
                    }

                    $serie = strtoupper(trim((string) ($unidad['serie'] ?? '')));
                    if ($serie === '') {
                        $this->dispatch('swal-kit', tipo: 'error', titulo: 'Serie faltante',
                            mensaje: "Registrá la serie de: {$nombreComponente} (unidad " . ($uIdx + 1) . ")");
                        return;
                    }

                    if (in_array($serie, $seriesIngresadas, true)) {
                        $this->dispatch('swal-kit', tipo: 'error', titulo: 'Serie duplicada', mensaje: "Duplicado: \"{$serie}\"");
                        return;
                    }

                    if (ItemSerializado::where('serie', $serie)->exists()) {
                        $this->dispatch('swal-kit', tipo: 'error', titulo: 'Serie duplicada', mensaje: "La serie \"{$serie}\" ya está registrada.");
                        return;
                    }

                    $seriesIngresadas[] = $serie;
                }
            }
        } catch (\Throwable $e) {
            $this->avisarError($e, 'swal-kit', 'No se pudieron validar los datos del kit. Revisá los campos e intentá de nuevo.');
            return;
        }

        try {
            DB::transaction(function () {
                $this->guardarRecetaKit();
                $this->crearKitsConComponentes();
            });

            $this->cerrarModal();
            $this->colaIndex++;

            if ($this->colaIndex < count($this->colaKits)) {
                $siguiente = $this->colaKits[$this->colaIndex];
                $this->abrirModal($siguiente['producto_id'], $siguiente['cantidad']);
                return;
            }

            $this->dispatch('swal-kit', tipo: 'success', titulo: '¡Recepción registrada!', mensaje: count($this->colaKits) . ' tipo(s) de kit(s) recibido(s).');
            $this->redirect(route('almacen.recepciones.listado'));
        } catch (\Throwable $e) {
            $this->avisarError($e, 'swal-kit', 'No se pudo registrar la recepción del kit: ' . $e->getMessage());
        }
    }

    private function guardarRecetaKit(): void
    {
        DB::table('kit_componentes')->where('producto_kit_id', $this->modalKitId)->delete();

        foreach ($this->modalComponentes as $componente) {
            DB::table('kit_componentes')->insert([
                'producto_kit_id' => $this->modalKitId,
                'producto_componente_id' => $componente['producto_id'],
                'cantidad_esperada' => $componente['es_serializado'] ? 1 : $componente['cantidad'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    private function crearKitsConComponentes(): void
    {
        $sedeId = $this->sedeId;
        $usuarioId = Auth::id();
        $kitProduce = trim($this->kitProduce);

        $kit = Producto::find($this->modalKitId);
        if (!$kit) throw new \RuntimeException('El kit seleccionado no existe.');

        for ($i = 0; $i < $this->modalKitCantidad; $i++) {
            $kitItem = ItemSerializado::create([
                'producto_id' => $kit->id,
                'serie' => null,
                'atributos' => ['recepcion_fecha' => now()->toDateString()],
                'estado' => 'en_stock',
                'sede_id' => $sedeId,
            ]);

            foreach ($this->modalComponentes as $componente) {
                $producto = Producto::find($componente['producto_id']);
                if (!$producto) throw new \RuntimeException("El producto {$componente['producto_id']} no existe.");

                if ($componente['es_serializado']) {
                    $unidad = $componente['unidades'][$i] ?? null;
                    if (!$unidad) throw new \RuntimeException("Faltan datos para {$componente['nombre']} (kit " . ($i + 1) . ")");

                    $atributos = [
                        'agregado_a_kit' => true,
                        'fecha' => now()->toDateString(),
                        'serie_registrada_por' => $usuarioId,
                        'serie_registrada_en' => now()->toDateTimeString(),
                        'recepcion_fecha' => now()->toDateString(),
                    ];

                    $camposEsquema = $componente['campos_esquema'] ?? [];
                    foreach ($camposEsquema as $campo) {
                        if ($campo === 'produce') continue;
                        $valor = $unidad[$campo] ?? null;
                        if ($valor !== null && $valor !== '') {
                            $atributos[$campo] = $valor;
                        }
                    }

                    if ($kitProduce !== '') {
                        $atributos['produce'] = $kitProduce;
                    }

                    ItemSerializado::create([
                        'producto_id' => $producto->id,
                        'kit_padre_id' => $kitItem->id,
                        'serie' => strtoupper(trim($unidad['serie'])),
                        'atributos' => $atributos,
                        'estado' => 'en_stock',
                        'sede_id' => $sedeId,
                    ]);

                    MovimientoStock::registrar($producto, 'entrada', 1, null, $usuarioId, "Componente serializado kit #{$kitItem->id}", $sedeId);
                } else {
                    $cantidad = (int) $componente['cantidad'];
                    for ($j = 0; $j < $cantidad; $j++) {
                        ItemSerializado::create([
                            'producto_id' => $producto->id,
                            'kit_padre_id' => $kitItem->id,
                            'serie' => null,
                            'atributos' => ['tipo' => 'cantidad', 'agregado_a_kit' => true, 'fecha' => now()->toDateString()],
                            'estado' => 'en_stock',
                            'sede_id' => $sedeId,
                        ]);
                    }
                    MovimientoStock::registrar($producto, 'entrada', $cantidad, null, $usuarioId, "Componente por cantidad kit #{$kitItem->id}", $sedeId);
                }
            }
        }

        MovimientoStock::registrar($kit, 'entrada', $this->modalKitCantidad, null, $usuarioId, 'Entrada por recepción', $sedeId);
    }
}
