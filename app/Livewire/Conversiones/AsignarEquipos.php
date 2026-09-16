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

    private const PRODUCTOS_SERIALIZABLES = ['Vaporizador', 'Computadora', 'Tanque'];

    protected function sedePrincipalId(): int
    {
        return $this->orden->sede_id ?? (Sede::activas()->orderBy('id')->first()?->id ?? 1);
    }

    public function updatedFiltroGeneracion(): void
    {
        $this->kitItemId = null;
        $this->seriesKit = [];
    }

    private function esSerializable(string $nombre): bool
    {
        foreach (self::PRODUCTOS_SERIALIZABLES as $patron) {
            if (stripos($nombre, $patron) !== false) {
                return true;
            }
        }
        return false;
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
    // ═══════════════════════════════════════════════
    public function getComponentesKitProperty()
    {
        if (!$this->kitItemId) return collect();

        $kit = ItemSerializado::with('producto.componentes.componente')->find($this->kitItemId);
        if (!$kit) return collect();

        return $kit->producto->componentes
            ->filter(fn($kc) => $this->esSerializable($kc->componente->nombre))
            ->map(fn($kc) => (object) [
                'producto_id' => $kc->producto_componente_id,
                'nombre' => $kc->componente->nombre,
            ])
            ->values();
    }

    // ═══════════════════════════════════════════════
    // REPUESTOS POR CANTIDAD
    // ═══════════════════════════════════════════════
    public function getProductosRepuestoProperty()
    {
        return Producto::whereHas('categoria', fn ($q) => $q->where('es_serializado', false))
            ->get()
            ->filter(fn($p) => $p->stockEnSede($this->sedePrincipalId()) > 0)
            ->values();
    }

    public function agregarRepuesto()
    {
        $this->validate([
            'productoRepuestoId' => 'required|exists:productos,id',
            'cantidadRepuesto' => 'required|integer|min:1',
        ]);

        $producto = Producto::find($this->productoRepuestoId);
        $disponible = $producto->stockEnSede($this->sedePrincipalId());

        if ($this->cantidadRepuesto > $disponible) {
            $this->addError('cantidadRepuesto', "Solo hay {$disponible} en stock.");
            return;
        }

        $this->repuestosSeleccionados[$this->productoRepuestoId] = $this->cantidadRepuesto;
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

        return Producto::whereIn('id', array_keys($this->repuestosSeleccionados))->get()
            ->map(function ($p) {
                $p->cantidad_solicitada = $this->repuestosSeleccionados[$p->id];
                return $p;
            });
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

        // Validar que todos los componentes seriales tengan serie
        if ($this->kitItemId) {
            $componentes = $this->componentesKit;
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

                // Validar que el serie no exista ya en la base de datos
                $existeEnDb = ItemSerializado::where('serie', $serie)->exists();
                if ($existeEnDb) {
                    $this->dispatch('minToast', titulo: 'Serie ya existe', mensaje: "El serie \"{$serie}\" ya está registrado en el sistema.", icono: 'error');
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

                    // Crear items para TODOS los componentes REALES del kit
                    $componentes = $kit->producto->componentes()->with('componente')->get();

                    foreach ($componentes as $kc) {
                        $producto = $kc->componente;
                        $esSerializable = $this->esSerializable($producto->nombre);

                        if ($esSerializable) {
                            // Usar serie capturada por almacén
                            $serieCapturada = trim($this->seriesKit[$producto->id] ?? '');

                            // Buscar item en stock de esta sede
                            $itemEnStock = ItemSerializado::where('producto_id', $producto->id)
                                ->where('estado', 'en_stock')
                                ->where('sede_id', $sedeId)
                                ->first();

                            if ($itemEnStock) {
                                $itemEnStock->update([
                                    'estado' => 'asignado',
                                    'service_order_id' => $this->orden->id,
                                    'kit_padre_id' => $kit->id,
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
                            // Componente por cantidad — crear un item por cada unidad esperada
                            for ($i = 0; $i < $kc->cantidad_esperada; $i++) {
                                ItemSerializado::create([
                                    'producto_id' => $producto->id,
                                    'serie' => 'CANT-' . strtoupper(uniqid()),
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

                // 2. Repuestos por cantidad (opcional)
                foreach ($this->repuestosSeleccionados as $productoId => $cantidad) {
                    $producto = Producto::find($productoId);
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

        // Mostrar alerta centrada (grande) de confirmación
        $this->dispatch('entrega-confirmada', [
            'redirectUrl' => route('conversiones.almacen-pendientes'),
            'mensaje' => 'Kit asignado y descontado del almacén. La orden pasó a conversión.',
        ]);
    }

    public function render()
    {
        return view('livewire.conversiones.asignar-equipos');
    }
}
