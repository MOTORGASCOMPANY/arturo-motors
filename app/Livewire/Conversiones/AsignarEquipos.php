<?php

namespace App\Livewire\Conversiones;

use App\Models\ServiceOrder;
use App\Models\ItemSerializado;
use App\Models\Producto;
use App\Models\ProductoStockSede;
use App\Models\MovimientoStock;
use App\Models\Sede;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\On;
use Livewire\Component;

class AsignarEquipos extends Component
{
    public ServiceOrder $orden;

    // Kit seleccionado para asignar
    public ?int $kitItemId = null;

    // Filtro de generación (3RA, 5TA)
    public string $filtroGeneracion = '';

    // Series de los componentes del kit (capturados por almacén)
    public array $seriesKit = []; // [producto_id => 'numero_serie']

    // Repuestos por cantidad
    public ?int $productoRepuestoId = null;
    public int $cantidadRepuesto = 1;
    public array $repuestosSeleccionados = []; // [productoId => cantidad]

    // Se determina por categoría (es_serializado), NO por nombre hardcodeado

    protected function sedePrincipalId(): int
    {
        return $this->orden->sede_id ?? (Sede::primeraActivaId());
    }

    public function updatedFiltroGeneracion(): void
    {
        $this->kitItemId = null;
        $this->seriesKit = [];
    }

    public function mount(int $ordenId)
    {
        $this->orden = ServiceOrder::with(['cliente', 'vehiculo', 'service'])->findOrFail($ordenId);
        
        // Permitir si está en aprobado_conversion O en_conversion sin items
        $puedeAcceder = $this->orden->estado === 'aprobado_conversion' 
            || ($this->orden->estado === 'en_conversion' && $this->orden->items()->count() === 0);
        
        abort_unless($puedeAcceder, 403, 'Esta orden no está en etapa de asignación de equipos.');
    }

    // ═══════════════════════════════════════════════
    // KITS DISPONIBLES (solo los que NO están reservados)
    // ═══════════════════════════════════════════════
    public function getKitsDisponiblesProperty()
    {
        $query = ItemSerializado::with('producto.categoria')
            ->kitDisponible()
            ->where('sede_id', $this->sedePrincipalId())
            ->whereHas('producto.categoria', fn ($q) => $q->where('es_kit', true));

        // Filtrar por generación si se seleccionó
        if ($this->filtroGeneracion) {
            $query->whereHas('producto', fn ($q) => $q->where('atributos->generacion', $this->filtroGeneracion));
        }

        return $query->orderByDesc('created_at')->get();
    }

    /**
     * Generaciones disponibles en stock
     */
    public function getGeneracionesDisponiblesProperty()
    {
        return ItemSerializado::with('producto')
            ->kitDisponible()
            ->where('sede_id', $this->sedePrincipalId())
            ->whereHas('producto.categoria', fn ($q) => $q->where('es_kit', true))
            ->get()
            ->pluck('producto.atributos.generacion')
            ->filter()
            ->unique()
            ->sort()
            ->values()
            ->toArray();
    }

    // ═══════════════════════════════════════════════
    // COMPONENTES DEL KIT SELECCIONADO (para inputs de serie)
    // Solo muestra componentes que NECESITAN serie (no la tienen aún)
    // ═══════════════════════════════════════════════
    public function getComponentesKitProperty()
    {
        if (!$this->kitItemId) return collect();

        $kit = ItemSerializado::with('producto.componentes.componente')->find($this->kitItemId);
        if (!$kit) return collect();

        // Obtener items ya existentes dentro de este kit
        $itemsExistentes = ItemSerializado::where('kit_padre_id', $kit->id)->get();

        return $kit->producto->componentes
            ->filter(fn($kc) => Producto::esSerializable($kc->componente->nombre))
            ->filter(function ($kc) use ($itemsExistentes) {
                // Solo mostrar si NO tiene serie ya registrada
                $itemDelKit = $itemsExistentes->firstWhere('producto_id', $kc->producto_componente_id);
                return !$itemDelKit || empty($itemDelKit->serie);
            })
            ->map(fn($kc) => (object) [
                'producto_id' => $kc->producto_componente_id,
                'nombre' => $kc->componente->nombre,
            ])
            ->values();
    }

    // ═══════════════════════════════════════════════
    // RESUMEN DEL KIT (para la alerta de confirmación)
    // ═══════════════════════════════════════════════
    public function getResumenKitProperty(): ?array
    {
        if (!$this->kitItemId) return null;

        $kit = ItemSerializado::with('producto')->find($this->kitItemId);
        if (!$kit) return null;

        $itemsExistentes = ItemSerializado::where('kit_padre_id', $kit->id)->get();
        $componentes = $kit->producto->componentes()->with('componente')->get();

        $piezasCantidad = [];
        $piezasSerializadas = [];

        foreach ($componentes as $kc) {
            $producto = $kc->componente;
            $itemsComp = $itemsExistentes->where('producto_id', $kc->producto_componente_id);

            if (Producto::esSerializable($producto->nombre)) {
                $listado = false;

                foreach ($itemsComp as $item) {
                    $serie = $item->serie ?? ($this->seriesKit[$producto->id] ?? null);
                    if ($serie) {
                        $piezasSerializadas[] = [
                            'nombre' => $producto->nombre,
                            'serie' => $serie,
                        ];
                        $listado = true;
                    }
                }

                // Componente que todavía no tiene item propio: se crea al confirmar,
                // así que se lista con la serie que se está ingresando.
                if (!$listado && !empty($this->seriesKit[$producto->id])) {
                    $piezasSerializadas[] = [
                        'nombre' => $producto->nombre,
                        'serie' => $this->seriesKit[$producto->id],
                    ];
                }
            } else {
                $cantidad = $itemsComp->count();
                if ($cantidad > 0) {
                    $piezasCantidad[] = [
                        'nombre' => $producto->nombre,
                        'cantidad' => $cantidad,
                    ];
                }
            }
        }

        return [
            'nombre' => $kit->producto->nombre,
            'piezas_cantidad' => $piezasCantidad,
            'piezas_serializadas' => $piezasSerializadas,
        ];
    }

    // ═══════════════════════════════════════════════
    // REPUESTOS VARIOS (solo items POR CANTIDAD - es_serializado=false)
    // MUESTRA stock suelto real de ProductoStockSede (incluye componentes de kits sueltos)
    // ═══════════════════════════════════════════════

    public function getProductosRepuestoProperty()
    {
        $sedeId = $this->sedePrincipalId();

        // SOLO items POR CANTIDAD (es_serializado=false, no kits)
        // Stock suelto REAL = ProductoStockSede.cantidad - items asignados a kits
        return Producto::whereHas('categoria', fn ($q) => $q->where('es_serializado', false)->where('es_kit', false))
            ->whereHas('stockPorSede', fn ($q) => $q->where('sede_id', $sedeId)->where('cantidad', '>', 0))
            ->with(['stockPorSede' => fn ($q) => $q->where('sede_id', $sedeId)->where('cantidad', '>', 0)])
            ->get()
            ->map(function ($p) use ($sedeId) {
                $stockTotal = $p->stockPorSede->where('sede_id', $sedeId)->sum('cantidad');

                // Restar componentes de cantidad reservados dentro de kits
                $enKits = ItemSerializado::montadasEnKit($p->id, $sedeId);

                return (object) [
                    'producto_id' => $p->id,
                    'producto' => $p,
                    'tipo' => 'cantidad',
                    'cantidad_disponible' => max(0, $stockTotal - $enKits),
                ];
            })
            ->filter(fn ($p) => $p->cantidad_disponible > 0)
            ->values();
    }

    public function agregarRepuesto()
    {
        $this->validate([
            'productoRepuestoId' => 'required|exists:productos,id',
            'cantidadRepuesto' => 'required|integer|min:1',
        ]);

        $producto = Producto::find($this->productoRepuestoId);
        if (!$producto) {
            $this->addError('productoRepuestoId', 'Producto no encontrado.');
            return;
        }

        $sedeId = $this->sedePrincipalId();

        // Para productos por CANTIDAD (es_serializado=false), validar contra stock suelto REAL
        $stockSede = $producto->stockPorSede()->where('sede_id', $sedeId)->first();
        $enKits = ItemSerializado::montadasEnKit($producto->id, $sedeId);
        $disponible = ($stockSede->cantidad ?? 0) - $enKits;

        if ($this->cantidadRepuesto > $disponible) {
            $this->addError('cantidadRepuesto', "Solo hay {$disponible} sueltos en stock.");
            $this->bloquear([
                'titulo' => 'Stock insuficiente',
                'mensaje' => "Solo hay {$disponible} unidades disponibles en stock de {$producto->nombre}. Bajá la cantidad e intentá de nuevo.",
            ]);
            return;
        }

        $key = $producto->id;
        if (isset($this->repuestosSeleccionados[$key])) {
            $mensajeDuplicado = 'Este producto ya está en la lista. Quitalo y volvé a agregarlo con la cantidad total.';
            $this->addError('cantidadRepuesto', $mensajeDuplicado);
            $this->bloquear([
                'titulo' => 'Repuesto ya agregado',
                'mensaje' => $mensajeDuplicado,
            ]);
            return;
        }

        $this->repuestosSeleccionados[$key] = (object) [
            'key' => $key,
            'producto_id' => $producto->id,
            'tipo' => 'cantidad',
            'producto' => $producto,
            'cantidad_solicitada' => $this->cantidadRepuesto,
        ];

        $this->reset(['productoRepuestoId', 'cantidadRepuesto']);
        $this->cantidadRepuesto = 1;
    }

    public function quitarRepuesto(int $productoId)
    {
        unset($this->repuestosSeleccionados[$productoId]);
    }

    public function getRepuestosCarritoProperty()
    {
        if (empty($this->repuestosSeleccionados)) return collect();

        return collect($this->repuestosSeleccionados)->map(function ($p) {
            return (object) [
                'key' => $p->key,
                'producto_id' => $p->producto_id,
                'producto' => $p->producto,
                'tipo' => 'cantidad',
                'cantidad_solicitada' => $p->cantidad_solicitada,
            ];
        })->values();
    }

    // ═══════════════════════════════════════════════
    // CONFIRMAR ASIGNACIÓN
    // ═══════════════════════════════════════════════
    /**
     * Validaciones previas a la confirmación.
     *
     * @return array{titulo: string, mensaje: string}|null null si todo está OK
     */
    private function validarAsignacion(): ?array
    {
        // 1. El kit es obligatorio
        if (empty($this->kitItemId)) {
            return ['titulo' => 'Kit no seleccionado', 'mensaje' => 'Seleccione el kit para continuar.'];
        }

        // 2. Series SOLO para componentes que necesitan serie (no la tienen aún)
        $seriesIngresadas = [];
        foreach ($this->componentesKit as $comp) {
            $serie = trim($this->seriesKit[$comp->producto_id] ?? '');

            if (empty($serie)) {
                return ['titulo' => 'Serie requerida', 'mensaje' => "Ingrese el número de serie de: {$comp->nombre}"];
            }

            // Duplicados entre los que se están ingresando
            $serieUpper = strtoupper($serie);
            if (in_array($serieUpper, $seriesIngresadas)) {
                return ['titulo' => 'Serie duplicada', 'mensaje' => "El serie \"{$serie}\" está repetido. Ingrese series diferentes."];
            }
            $seriesIngresadas[] = $serieUpper;

            // El serie no puede existir en OTRO item (no en el de este kit)
            $existeEnOtro = ItemSerializado::porSerie($serie)
                ->where('kit_padre_id', '!=', $this->kitItemId)
                ->exists();
            if ($existeEnOtro) {
                return ['titulo' => 'Serie ya existe', 'mensaje' => "El serie \"{$serie}\" ya está registrado en otro kit/item."];
            }
        }

        // 3. Guard: no duplicate assignment
        if ($this->orden->items()->count() > 0) {
            return ['titulo' => 'Orden ya asignada', 'mensaje' => 'Esta orden ya tiene items asignados. No se puede asignar un kit dos veces.'];
        }

        return null;
    }

    /**
     * Modal de SweetAlert2 que NO se cierra con click afuera ni con ESC:
     * hay que presionar OK para volver a la pantalla.
     */
    private function bloquear(array $error): void
    {
        $this->dispatch('asignacion-bloqueada', titulo: $error['titulo'], mensaje: $error['mensaje']);
    }

    /**
     * Botón "Confirmar asignación": valida y, si todo está OK, pide la confirmación
     * al usuario (modal con Confirmar / Cancelar). La asignación real ocurre recién
     * cuando el usuario confirma y se ejecuta confirmarEntrega().
     */
    public function iniciarConfirmacion(): void
    {
        if ($error = $this->validarAsignacion()) {
            $this->bloquear($error);
            return;
        }

        $this->dispatch(
            'asignacion-confirmar',
            titulo: '¿Confirmar asignación?',
            mensaje: 'Esta acción no se puede deshacer'
        );
    }

    #[On('confirmar-entrega')]
    public function confirmarEntrega()
    {
        // Se re-valida por si se invoca sin pasar por iniciarConfirmacion()
        if ($error = $this->validarAsignacion()) {
            $this->bloquear($error);
            return;
        }

        $sedeId = $this->orden->sede_id ?? $this->sedePrincipalId();

        // Preparar datos para la alerta
        $resumenKit = $this->resumenKit;

        try {
            DB::transaction(function () use ($sedeId) {
                // 1. Abrir y asignar kit si se seleccionó uno
                if ($this->kitItemId) {
                    $kit = ItemSerializado::where('id', $this->kitItemId)
                        ->kitDisponible()
                        ->where('sede_id', $sedeId)
                        ->lockForUpdate()
                        ->first();

                    if (!$kit) {
                        throw new \RuntimeException('El kit ya no está disponible.');
                    }

                    // Cambiar estado del kit a asignado
                    $kit->update([
                        'estado' => 'asignado',
                        'service_order_id' => $this->orden->id,
                    ]);

                    // Obtener items existentes dentro del kit
                    $itemsExistentes = ItemSerializado::where('kit_padre_id', $kit->id)->get();

                    // Procesar TODOS los componentes REALES del kit
                    $componentes = $kit->producto->componentes()->with('componente')->get();

                    foreach ($componentes as $kc) {
                        $producto = $kc->componente;
                        $esSerializable = Producto::esSerializable($producto->nombre);

                        if ($esSerializable) {
                            // Buscar item existente de este kit
                            $itemEnStock = $itemsExistentes->where('producto_id', $producto->id)->first();

                            // Usar serie: si ya tiene, reutilizar; si no, usar la ingresada
                            $serieCapturada = $itemEnStock && $itemEnStock->serie
                                ? $itemEnStock->serie
                                : trim($this->seriesKit[$producto->id] ?? '');

                            if ($itemEnStock) {
                                $itemEnStock->update([
                                    'estado' => 'asignado',
                                    'service_order_id' => $this->orden->id,
                                    'serie' => $serieCapturada,
                                    'atributos' => array_merge($itemEnStock->atributos ?? [], [
                                        'serie_capturada_por' => Auth::id(),
                                        'serie_capturada_en' => now()->toDateTimeString(),
                                    ]),
                                ]);
                            } else {
                                ItemSerializado::create([
                                    'producto_id' => $producto->id,
                                    'serie' => $serieCapturada,
                                    'estado' => 'asignado',
                                    'service_order_id' => $this->orden->id,
                                    'kit_padre_id' => $kit->id,
                                    'sede_id' => $sedeId,
                                    'atributos' => [
                                        'tipo' => 'serial',
                                        'creado_automaticamente' => true,
                                        'creado_por' => 'asignar-equipos',
                                        'creado_en' => now()->toDateTimeString(),
                                        'serie_capturada_por' => Auth::id(),
                                        'serie_capturada_en' => now()->toDateTimeString(),
                                    ],
                                ]);
                            }
                        } else {
                            // Componente por cantidad — usar items existentes del kit
                            $itemsComp = $itemsExistentes->where('producto_id', $producto->id);
                            $itemsAAsignar = $itemsComp->take($kc->cantidad_esperada);

                            foreach ($itemsAAsignar as $item) {
                                $item->update([
                                    'estado' => 'asignado',
                                    'service_order_id' => $this->orden->id,
                                ]);
                            }

                            // Si faltan items, crear los que falten
                            $faltantes = $kc->cantidad_esperada - $itemsAAsignar->count();
                            for ($i = 0; $i < $faltantes; $i++) {
                                ItemSerializado::create([
                                    'producto_id' => $producto->id,
                                    'serie' => null,
                                    'estado' => 'asignado',
                                    'service_order_id' => $this->orden->id,
                                    'kit_padre_id' => $kit->id,
                                    'sede_id' => $sedeId,
                                    'atributos' => [
                                        'tipo' => 'cantidad',
                                        'creado_automaticamente' => true,
                                        'creado_por' => 'asignar-equipos',
                                        'creado_en' => now()->toDateTimeString(),
                                    ],
                                ]);
                            }
                        }
                    }
                }

                // 2. Repuestos varios (solo cantidad) - descuenta de ProductoStockSede
                foreach ($this->repuestosSeleccionados as $key => $repuesto) {
                    $producto = Producto::find($repuesto->producto_id);
                    if (!$producto) continue;

                    $sedeId = $this->orden->sede_id ?? $this->sedePrincipalId();

                    // Cantidad
                    $cantidad = $repuesto->cantidad_solicitada;

                    // Stock suelto REAL (ProductoStockSede - items en kits)
                    $stockSede = ProductoStockSede::where('producto_id', $producto->id)
                        ->where('sede_id', $sedeId)
                        ->first();

                    $enKits = ItemSerializado::montadasEnKit($producto->id, $sedeId);

                    $disponible = ($stockSede->cantidad ?? 0) - $enKits;

                    if ($disponible < $cantidad) {
                        throw new \RuntimeException("No hay stock suelto suficiente de {$producto->nombre}. Disponible: {$disponible}.");
                    }

                    MovimientoStock::registrar(
                        $producto, 'salida', $cantidad, $this->orden->id, Auth::id(),
                        'Entrega para conversión #' . $this->orden->id, $sedeId
                    );

                    // Enlazar como item de la orden (igual que reportes-piezas)
                    // kit_padre_id = null a propósito: el descuento de "suelto" ya lo hace MovimientoStock;
                    // si además se marcara dentro de un kit, el stock restaría dos veces.
                    ItemSerializado::create([
                        'producto_id' => $producto->id,
                        'serie' => null,
                        'estado' => 'asignado',
                        'service_order_id' => $this->orden->id,
                        'sede_id' => $sedeId,
                        'atributos' => [
                            'tipo' => 'cantidad',
                            'cantidad_solicitada' => $cantidad,
                            'repuesto_vario' => true,
                            'creado_por' => 'asignar-equipos',
                            'creado_en' => now()->toDateTimeString(),
                        ],
                    ]);
                }

                $this->orden->update(['estado' => 'en_conversion']);
            });
        } catch (\RuntimeException $e) {
            $this->bloquear(['titulo' => 'No se pudo confirmar', 'mensaje' => $e->getMessage()]);
            return;
        } catch (\Throwable $e) {
            report($e);
            $this->bloquear(['titulo' => 'Error', 'mensaje' => 'Ocurrió un error al confirmar. Intenta de nuevo.']);
            return;
        }

        // Mostrar alerta con resumen del kit asignado
        $this->dispatch('entrega-confirmada', [
            'titulo' => '¡Componentes asignados!',
            'mensaje' => 'La asignación se confirmó correctamente.',
            'redirectUrl' => route('conversiones.almacen-pendientes'),
            'resumen' => $resumenKit,
        ]);
    }

    public function render()
    {
        return view('livewire.conversiones.asignar-equipos');
    }
}
