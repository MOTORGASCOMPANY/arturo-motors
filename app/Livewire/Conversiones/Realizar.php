<?php

namespace App\Livewire\Conversiones;

use App\Models\ItemSerializado;
use App\Models\KitComponente;
use App\Models\MovimientoStock;
use App\Models\Producto;
use App\Models\ReportePiezaNoEncajada;
use App\Models\ServiceOrder;
use App\Models\Sede;
use App\Services\CambioPiezaService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class Realizar extends Component
{
    public ServiceOrder $orden;

    public bool $modalAbierto = false;
    public string $modalTipo = '';
    public ?int $kitItemSeleccionado = null;
    public string $motivoNoCalza = '';
    public ?int $productoCantidadId = null;
    public int $cantidadNoInstalada = 1;
    public bool $modalPartesAbierto = false;
    public ?int $productoSolicitadoId = null;

    // Modal confirmar instalación
    public bool $modalConfirmarInstalacion = false;
    public ?int $reporteConfirmarId = null;
    public string $nuevoSerie = '';

    // Modal asignar kit
    public bool $modalAsignarKit = false;
    public string $busquedaKit = '';
    public array $kitsDisponibles = [];
    public ?int $kitSeleccionadoId = null;

    protected CambioPiezaService $cambioPiezaService;

    public function boot(CambioPiezaService $cambioPiezaService): void
    {
        $this->cambioPiezaService = $cambioPiezaService;
    }

    protected function sedePrincipalId(): int
    {
        return Sede::activas()->orderBy('id')->first()?->id ?? 1;
    }

    public function mount(int $ordenId)
    {
        $this->orden = ServiceOrder::with([
            'cliente',
            'vehiculo',
            'service',
            'items.producto.categoria',
            'items.kitPadre.producto',
        ])->findOrFail($ordenId);

        abort_unless(
            in_array($this->orden->estado, ['en_conversion', 'conversion_completada']),
            403,
            'Esta orden no está en etapa de conversión.'
        );
    }

    /**
     * Verifica si el técnico ya registró los items del kit
     */
    public function getItemsRegistradosProperty(): bool
    {
        if ($this->orden->fecha_inicio_conversion) {
            return true;
        }

        $items = $this->orden->items()->where('estado', 'asignado')->get();

        if ($items->isEmpty()) {
            return false;
        }

        foreach ($items as $item) {
            if (isset($item->atributos['serie_registrada_por'])) {
                return true;
            }
        }

        return false;
    }

    /**
     * El item kit padre de la orden (ej: Kit Instalación 5TA)
     */
    public function getKitPadreProperty(): ?ItemSerializado
    {
        return $this->orden->items()
            ->whereNull('kit_padre_id')
            ->whereHas('piezasEnKit')
            ->with('producto.categoria')
            ->first();
    }

    /**
     * Nombre de la generación del kit (3RA, 5TA, etc.)
     */
    public function getGeneracionKitProperty(): string
    {
        $padre = $this->kitPadre;
        if (!$padre) return '';

        return $padre->producto->atributos['generacion'] ?? $padre->producto->categoria->nombre;
    }

    /**
     * Las piezas seriales del kit asignado (hijos directos del kit padre)
     * Incluye piezas que son componentes reales del kit y están asignados a esta orden
     */
    public function getKitItemsProperty()
    {
        $padre = $this->kitPadre;
        if (!$padre) return collect();

        // IDs de componentes REALES del kit
        $componenteIds = $padre->producto->componentes->pluck('producto_componente_id')->toArray();

        return $this->orden->items()
            ->where('estado', 'asignado')
            ->whereNotNull('kit_padre_id')
            ->whereIn('producto_id', $componenteIds)
            ->get()
            ->values();
    }

    /**
     * TODOS los componentes del kit desde kit_componentes (serial + cantidad)
     * Para el modal de "Partes generales"
     */
    public function getTodasPiezasKitProperty()
    {
        $padre = $this->kitPadre;
        if (!$padre) return collect();

        return KitComponente::where('producto_kit_id', $padre->producto_id)
            ->with('componente.categoria')
            ->get()
            ->map(fn($kc) => (object) [
                'producto_id' => $kc->producto_componente_id,
                'nombre' => $kc->componente->nombre,
                'categoria' => $kc->componente->categoria->nombre,
                'es_serializado' => $this->esSerializable($kc->componente->nombre),
                'cantidad_esperada' => $kc->cantidad_esperada,
            ]);
    }

    /**
     * Productos serializables (Vaporizador, Computadora, Tanque)
     */
    private const PRODUCTOS_SERIALIZABLES = ['Vaporizador', 'Computadora', 'Tanque'];

    /**
     * Verificar si un producto es serializable
     */
    private function esSerializable(string $nombre): bool
    {
        foreach (self::PRODUCTOS_SERIALIZABLES as $patron) {
            if (stripos($nombre, $patron) !== false) {
                return true;
            }
        }
        return false;
    }

    /**
     * Componentes de cantidad del kit (los que NO son serializados)
     * Para el select del modal de reporte por cantidad
     */
    public function getItemsCantidadProperty()
    {
        $padre = $this->kitPadre;
        if (!$padre) return collect();

        return KitComponente::where('producto_kit_id', $padre->producto_id)
            ->with('componente.categoria')
            ->get()
            ->filter(fn($kc) => !$this->esSerializable($kc->componente->nombre))
            ->values();
    }

    public function iniciar()
    {
        if ($this->orden->fecha_inicio_conversion) return;

        $this->orden->update(['fecha_inicio_conversion' => now()]);
        $this->orden->refresh();

        $this->dispatch('minToast',
            titulo: 'Conversión iniciada',
            mensaje: 'Ya puede instalar las piezas. Si alguna no calza, use "Reportar pieza que no se puede instalar".',
            icono: 'success'
        );
    }

    // ─── Modal para piezas serializadas ───
    public function abrirModalSerial()
    {
        $this->modalAbierto = true;
        $this->modalTipo = 'serial';
        $this->kitItemSeleccionado = null;
        $this->motivoNoCalza = '';
        $this->cantidadNoInstalada = 1;
    }

    // ─── Modal para piezas de cantidad ───
    public function abrirModalCantidad()
    {
        $this->modalAbierto = true;
        $this->modalTipo = 'cantidad';
        $this->kitItemSeleccionado = null;
        $this->motivoNoCalza = '';
        $this->cantidadNoInstalada = 1;
    }

    public function cerrarModal()
    {
        $this->modalAbierto = false;
        $this->modalTipo = '';
        $this->kitItemSeleccionado = null;
        $this->motivoNoCalza = '';
        $this->cantidadNoInstalada = 1;
    }

    public function abrirPartesGenerales(?int $productoSolicitadoId = null)
    {
        $this->productoSolicitadoId = $productoSolicitadoId;
        $this->modalPartesAbierto = true;
    }

    public function cerrarPartesGenerales()
    {
        $this->modalPartesAbierto = false;
        $this->productoSolicitadoId = null;
    }

    /**
     * Seleccionar pieza del kit (serializado)
     */
    public function seleccionarKitItem(int $itemId)
    {
        $this->kitItemSeleccionado = $itemId;
    }

    /**
     * Enviar solicitud al almacén (un solo reporte)
     */
    public function reportarPiezaNoCalza()
    {
        if ($this->modalTipo === 'serial' && !$this->kitItemSeleccionado) {
            $this->addError('general', 'Seleccione qué pieza no se puede instalar.');
            return;
        }

        if (empty($this->motivoNoCalza)) {
            $this->addError('general', 'Ingrese una observación.');
            return;
        }

        try {
            if ($this->modalTipo === 'serial') {
                $itemId = $this->kitItemSeleccionado;
            } else {
                $itemId = $this->buscarOCrearItemCantidad();
            }

            if (!$itemId) {
                $this->addError('general', 'No se pudo encontrar la pieza.');
                return;
            }

            $reporte = $this->cambioPiezaService->crearReporte(
                $this->orden,
                $itemId,
                $this->motivoNoCalza
            );

            $reporte->marcarBuscando();

            $this->cerrarModal();
            $this->orden->refresh();

            $this->dispatch('minToast',
                titulo: 'Solicitud enviada',
                mensaje: 'El almacén recibió su solicitud de reemplazo.',
                icono: 'success'
            );

        } catch (\Throwable $e) {
            report($e);
            $this->dispatch('minToast', titulo: 'Error', mensaje: 'No se pudo enviar la solicitud.', icono: 'error');
        }
    }

    /**
     * Para piezas por cantidad: crear item en la orden
     */
    private function buscarOCrearItemCantidad(): ?int
    {
        if (!$this->productoCantidadId) return null;

        $sedeId = $this->sedePrincipalId();

        $existente = ItemSerializado::where('service_order_id', $this->orden->id)
            ->where('producto_id', $this->productoCantidadId)
            ->first();

        if ($existente) {
            return $existente->id;
        }

        $nuevoItem = ItemSerializado::create([
            'producto_id' => $this->productoCantidadId,
            'serie' => 'CANT-' . strtoupper(uniqid()),
            'estado' => 'asignado',
            'service_order_id' => $this->orden->id,
            'sede_id' => $sedeId,
            'atributos' => [
                'tipo' => 'cantidad',
                'cantidad_no_instalada' => $this->cantidadNoInstalada,
                'creado_por' => 'reporte-pieza',
                'creado_en' => now()->toDateTimeString(),
            ],
        ]);

        return $nuevoItem->id;
    }

    public function aumentarCantidad()
    {
        if ($this->cantidadNoInstalada < 10) {
            $this->cantidadNoInstalada++;
        }
    }

    public function disminuirCantidad()
    {
        if ($this->cantidadNoInstalada > 1) {
            $this->cantidadNoInstalada--;
        }
    }

    /**
     * Abrir modal para confirmar instalación con serial
     */
    public function abrirConfirmarInstalacion(int $reporteId)
    {
        $this->reporteConfirmarId = $reporteId;
        $this->nuevoSerie = '';
        $this->modalConfirmarInstalacion = true;
    }

    public function cerrarConfirmarInstalacion()
    {
        $this->modalConfirmarInstalacion = false;
        $this->reporteConfirmarId = null;
        $this->nuevoSerie = '';
    }

    /**
     * Confirmar instalación con serial nuevo
     */
    public function confirmarInstalacion()
    {
        if (empty($this->nuevoSerie)) {
            $this->addError('serial', 'Ingrese el número de serie de la pieza nueva.');
            return;
        }

        try {
            $reporte = ReportePiezaNoEncajada::findOrFail($this->reporteConfirmarId);
            
            $this->cambioPiezaService->confirmarInstalacionConSerial(
                $reporte,
                $this->nuevoSerie,
                Auth::id()
            );

            $this->cerrarConfirmarInstalacion();
            $this->orden->refresh();

            $this->dispatch('minToast',
                titulo: 'Instalación confirmada',
                mensaje: 'Pieza instalada con serial: ' . $this->nuevoSerie,
                icono: 'success'
            );

        } catch (\Throwable $e) {
            report($e);
            $this->dispatch('minToast', titulo: 'Error', mensaje: $e->getMessage(), icono: 'error');
        }
    }

    public function getItemsOrdenProperty()
    {
        return $this->orden->items->where('estado', 'asignado');
    }

    /**
     * IDs de items que tienen reportes activos (no resueltos)
     * Para marcar en rojo en el kit
     */
    public function getItemsReportadosProperty()
    {
        return ReportePiezaNoEncajada::where('service_order_id', $this->orden->id)
            ->whereIn('estado', ['pendiente', 'buscando_pieza', 'solicitando_almacen'])
            ->pluck('item_no_encajado_id')
            ->toArray();
    }

    /**
     * IDs de items que ya fueron reemplazados (reporte en estado kit_abierto o resuelto)
     * Para marcar en azul en el kit
     */
    public function getItemsReemplazadosProperty()
    {
        return ReportePiezaNoEncajada::where('service_order_id', $this->orden->id)
            ->whereIn('estado', ['kit_abierto', 'resuelto'])
            ->pluck('item_no_encajado_id')
            ->toArray();
    }

    /**
     * Si se puede finalizar la conversión (no hay piezas por instalar ni reportes pendientes)
     */
    public function getPuedeFinalizarProperty(): bool
    {
        if (!$this->orden->fecha_inicio_conversion) return false;

        // Solo bloquear si hay reportes en estados que NO son kit_abierto (despachado)
        $reportesPendientes = ReportePiezaNoEncajada::where('service_order_id', $this->orden->id)
            ->whereIn('estado', ['pendiente', 'buscando_pieza', 'solicitando_almacen'])
            ->count();

        return $reportesPendientes === 0;
    }

    /**
     * Lista detallada de pendientes para finalizar
     */
    public function getPendientesProperty(): array
    {
        $pendientes = [];

        // 1. Piezas asignadas que necesitan confirmación (solo las que fueron reportadas y reemplazadas)
        $reportesKitAbierto = ReportePiezaNoEncajada::where('service_order_id', $this->orden->id)
            ->where('estado', 'kit_abierto')
            ->with('itemNuevo')
            ->get();

        foreach ($reportesKitAbierto as $reporte) {
            $itemNuevo = $reporte->itemNuevo;
            if (!$itemNuevo || $itemNuevo->estado !== 'asignado') continue;

            // Determinar si es pieza por cantidad (CANT- o tipo cantidad)
            $esCantidad = str_starts_with($itemNuevo->serie ?? '', 'CANT-')
                || ($itemNuevo->atributos['tipo'] ?? '') === 'cantidad';

            // Determinar si es pieza extraída de kit (tiene extraida_de_kit)
            $esExtraidaDeKit = isset($itemNuevo->atributos['extraida_de_kit']);

            // Solo mostrar si es serial Y NO es extraída de kit (las extraídas ya tienen serie)
            if (!$esCantidad && !$esExtraidaDeKit) {
                $pendientes[] = [
                    'tipo' => 'serial_pendiente',
                    'texto' => $itemNuevo->producto->nombre ?? 'Pieza',
                    'detalle' => 'Pieza reemplazo asignada, ingrese el serial al confirmar instalación',
                    'icono' => 'microchip',
                    'color' => 'blue',
                ];
            }
        }

        // 2. Reportes esperando almacén
        $reportesEsperando = ReportePiezaNoEncajada::where('service_order_id', $this->orden->id)
            ->whereIn('estado', ['pendiente', 'buscando_pieza', 'solicitando_almacen'])
            ->with('itemNoEncajado.producto')
            ->get();

        foreach ($reportesEsperando as $reporte) {
            $estadoTexto = match($reporte->estado) {
                'pendiente' => 'Pendiente de revisión por almacén',
                'buscando_pieza' => 'Buscando pieza en stock',
                'solicitando_almacen' => 'Esperando pieza del almacén',
                default => ucfirst(str_replace('_', ' ', $reporte->estado)),
            };

            $pendientes[] = [
                'tipo' => 'reporte_almacen',
                'texto' => $reporte->itemNoEncajado->producto->nombre ?? 'Pieza',
                'detalle' => $estadoTexto,
                'icono' => 'exclamation-triangle',
                'color' => 'red',
            ];
        }

        return $pendientes;
    }

    /**
     * Reportes que el almacén YA despachó (pieza asignada al técnico)
     */
    public function getReportesPendientesProperty()
    {
        return ReportePiezaNoEncajada::where('service_order_id', $this->orden->id)
            ->whereIn('estado', ['kit_abierto', 'resuelto'])
            ->with(['itemNoEncajado.producto', 'tecnico'])
            ->get();
    }

    /**
     * Reportes que esperan respuesta del almacén (aún no despachados)
     */
    public function getEsperandoAlmacenProperty()
    {
        return ReportePiezaNoEncajada::where('service_order_id', $this->orden->id)
            ->whereIn('estado', ['pendiente', 'buscando_pieza', 'solicitando_almacen'])
            ->with(['itemNoEncajado.producto', 'tecnico'])
            ->get();
    }

    public function finalizar()
    {
        if (!$this->orden->fecha_inicio_conversion) {
            $this->addError('general', 'Primero debe iniciar la conversión.');
            return;
        }

        // Solo bloquear si hay reportes en estados que NO son kit_abierto (despachado)
        $reportesPendientes = ReportePiezaNoEncajada::where('service_order_id', $this->orden->id)
            ->whereIn('estado', ['pendiente', 'buscando_pieza', 'solicitando_almacen'])
            ->count();

        if ($reportesPendientes > 0) {
            $this->addError('general', 'Tiene ' . $reportesPendientes . ' solicitud(es) pendiente(s). Espere la pieza del almacén.');
            return;
        }

        try {
            DB::transaction(function () {
                // 1. Kit parent item → consumido (el kit fue abierto y sus componentes instalados)
                ItemSerializado::where('service_order_id', $this->orden->id)
                    ->where('estado', 'asignado')
                    ->whereNull('kit_padre_id')
                    ->whereHas('piezasEnKit')
                    ->update(['estado' => 'consumido']);

                // 2. Items originales del kit (hijos) → instalado (se quedan en el vehículo)
                ItemSerializado::where('service_order_id', $this->orden->id)
                    ->where('estado', 'asignado')
                    ->whereNotNull('kit_padre_id')
                    ->update(['estado' => 'instalado']);

                // 3. Piezas de reemplazo sueltas (sin kit_padre_id, sin hijos) → volver al stock
                $itemsReemplazo = ItemSerializado::where('service_order_id', $this->orden->id)
                    ->where('estado', 'asignado')
                    ->whereNull('kit_padre_id')
                    ->whereDoesntHave('piezasEnKit')
                    ->get();

                foreach ($itemsReemplazo as $item) {
                    $esCantidad = str_starts_with($item->serie ?? '', 'CANT-')
                        || ($item->atributos['tipo'] ?? '') === 'cantidad';

                    $item->update([
                        'estado' => 'en_stock',
                        'service_order_id' => null,
                        'atributos' => array_merge($item->atributos ?? [], [
                            'devuelto_por_no_calza' => true,
                            'devuelto_en' => now()->toDateTimeString(),
                            'tipo_devolucion' => $esCantidad ? 'cantidad' : 'serial',
                        ]),
                    ]);

                    MovimientoStock::registrar(
                        $item->producto,
                        'entrada',
                        1,
                        $this->orden->id,
                        Auth::id(),
                        "Devolución pieza por no calzar al finalizar conversión"
                    );
                }

                $this->orden->update([
                    'fecha_fin_conversion' => now(),
                    'estado' => 'conversion_completada',
                ]);
            });

            $this->orden->refresh();
            $this->dispatch('minToast', titulo: '¡Conversión completada!', mensaje: 'La orden está lista para entrega.', icono: 'success');

            // Redirigir a mis conversiones
            return $this->redirect(route('conversiones.mis-asignadas'));

        } catch (\Throwable $e) {
            report($e);
            $this->addError('general', 'Ocurrió un error. Intente de nuevo.');
        }
    }

    public function render()
    {
        return view('livewire.conversiones.realizar');
    }
}
