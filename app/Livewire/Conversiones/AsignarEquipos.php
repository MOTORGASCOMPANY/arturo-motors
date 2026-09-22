<?php

namespace App\Livewire\Conversiones;

use App\Models\ServiceOrder;
use App\Models\ItemSerializado;
use App\Models\Producto;
use App\Models\MovimientoStock;
use App\Models\Sede;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
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
        return $this->orden->sede_id ?? (Sede::activas()->orderBy('id')->first()?->id ?? 1);
    }

    public function updatedFiltroGeneracion(): void
    {
        $this->kitItemId = null;
        $this->seriesKit = [];
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
            ->where('estado', 'en_stock')
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
            ->where('estado', 'en_stock')
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
            ->filter(fn($kc) => $this->esSerializable($kc->componente->nombre))
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

            if ($this->esSerializable($producto->nombre)) {
                foreach ($itemsComp as $item) {
                    $serie = $item->serie ?? ($this->seriesKit[$producto->id] ?? null);
                    if ($serie) {
                        $piezasSerializadas[] = [
                            'nombre' => $producto->nombre,
                            'serie' => $serie,
                        ];
                    }
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
    // EXCLUYE los que son componentes de CUALQUIER kit (consistente con almacén)
    // ═══════════════════════════════════════════════

    public function getProductosRepuestoProperty()
    {
        $sedeId = $this->sedePrincipalId();

        // IDs de productos que son componentes de CUALQUIER kit (para excluirlos)
        // Consistente con Almacen\Productos\Listado::getListadoInventarioProperty()
        $componentesDeCualquierKit = DB::table('kit_componentes')
            ->join('productos', 'producto_componente_id', '=', 'productos.id')
            ->join('categorias_almacen', 'productos.categoria_id', '=', 'categorias_almacen.id')
            ->where('categorias_almacen.es_serializado', false) // solo los de cantidad
            ->pluck('producto_componente_id')
            ->toArray();

        // SOLO items POR CANTIDAD (es_serializado=false, no kits)
        // Y que NO sean componentes de NINGÚN kit
        $query = Producto::whereHas('categoria', fn ($q) => $q->where('es_serializado', false)->where('es_kit', false))
            ->whereHas('stockPorSede', fn ($q) => $q->where('sede_id', $sedeId)->where('cantidad', '>', 0))
            ->with(['stockPorSede' => fn ($q) => $q->where('sede_id', $sedeId)->where('cantidad', '>', 0)]);

        if (!empty($componentesDeCualquierKit)) {
            $query->whereNotIn('id', $componentesDeCualquierKit);
        }

        return $query->get()
            ->map(function ($p) use ($sedeId) {
                $stock = $p->stockPorSede->where('sede_id', $sedeId)->sum('cantidad');
                return (object) [
                    'producto_id' => $p->id,
                    'producto' => $p,
                    'tipo' => 'cantidad',
                    'cantidad_disponible' => $stock,
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
        $disponible = $producto->stockEnSede($sedeId);

        if ($this->cantidadRepuesto > $disponible) {
            $this->addError('cantidadRepuesto', "Solo hay {$disponible} en stock.");
            return;
        }

        $key = $producto->id;
        if (isset($this->repuestosSeleccionados[$key])) {
            $this->addError('cantidadRepuesto', 'Este producto ya está en la lista. Quitalo y volvé a agregarlo con la cantidad total.');
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
    public function confirmarEntrega()
    {
        if (empty($this->kitItemId) && empty($this->repuestosSeleccionados)) {
            $this->dispatch('minToast', titulo: 'Faltan datos', mensaje: 'Seleccioná un kit para confirmar.', icono: 'warning');
            return;
        }

        // Validar series SOLO para componentes que necesitan serie (no la tienen aún)
        if ($this->kitItemId) {
            $componentes = $this->componentesKit; // Solo retorna los que necesitan serie
            $seriesIngresadas = [];

            foreach ($componentes as $comp) {
                $serie = trim($this->seriesKit[$comp->producto_id] ?? '');

                if (empty($serie)) {
                    $this->dispatch('minToast', titulo: 'Serie requerida', mensaje: "Ingrese el número de serie de: {$comp->nombre}", icono: 'warning');
                    return;
                }

                // Validar duplicados entre los que se están ingresando
                $serieUpper = strtoupper($serie);
                if (in_array($serieUpper, $seriesIngresadas)) {
                    $this->dispatch('minToast', titulo: 'Serie duplicada', mensaje: "El serie \"{$serie}\" está repetido. Ingrese series diferentes.", icono: 'error');
                    return;
                }
                $seriesIngresadas[] = $serieUpper;

                // Validar que el serie no exista en OTRO item (no en el de este kit)
                $existeEnOtro = ItemSerializado::where('serie', $serie)
                    ->where('kit_padre_id', '!=', $this->kitItemId)
                    ->exists();
                if ($existeEnOtro) {
                    $this->dispatch('minToast', titulo: 'Serie ya existe', mensaje: "El serie \"{$serie}\" ya está registrado en otro kit/item.", icono: 'error');
                    return;
                }
            }
        }

        $sedeId = $this->orden->sede_id ?? $this->sedePrincipalId();

        // Guard: no duplicate assignment
        $yaTieneItems = $this->orden->items()->count() > 0;
        if ($yaTieneItems) {
            $this->dispatch('minToast', titulo: 'Error', mensaje: 'Esta orden ya tiene items asignados. No se puede asignar un kit dos veces.', icono: 'error');
            return;
        }

        // Preparar datos para la alerta
        $resumenKit = $this->resumenKit;

        try {
            DB::transaction(function () use ($sedeId) {
                // 1. Abrir y asignar kit si se seleccionó uno
                if ($this->kitItemId) {
                    $kit = ItemSerializado::where('id', $this->kitItemId)
                        ->where('estado', 'en_stock')
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
                        $esSerializable = $this->esSerializable($producto->nombre);

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

                // 2. Repuestos varios (solo cantidad)
                foreach ($this->repuestosSeleccionados as $key => $repuesto) {
                    $producto = Producto::find($repuesto->producto_id);
                    if (!$producto) continue;

                    $sedeId = $this->orden->sede_id ?? $this->sedePrincipalId();

                    // Cantidad
                    $cantidad = $repuesto->cantidad_solicitada;
                    $disponible = $producto->stockEnSede($sedeId);

                    if ($disponible < $cantidad) {
                        throw new \RuntimeException("No hay stock suficiente de {$producto->nombre}.");
                    }

                    MovimientoStock::registrar(
                        $producto, 'salida', $cantidad, $this->orden->id, Auth::id(),
                        'Entrega para conversión #' . $this->orden->id, $sedeId
                    );
                }

                $this->orden->update(['estado' => 'en_conversion']);
            });
        } catch (\RuntimeException $e) {
            $this->dispatch('minToast', titulo: 'Error', mensaje: $e->getMessage(), icono: 'error');
            return;
        } catch (\Throwable $e) {
            report($e);
            $this->dispatch('minToast', titulo: 'Error', mensaje: 'Ocurrió un error al confirmar. Intenta de nuevo.', icono: 'error');
            return;
        }

        // Mostrar alerta con resumen del kit asignado
        $this->dispatch('entrega-confirmada', [
            'redirectUrl' => route('conversiones.almacen-pendientes'),
            'resumen' => $resumenKit,
        ]);
    }

    public function render()
    {
        return view('livewire.conversiones.asignar-equipos');
    }
}
