<?php

namespace App\Livewire\Almacen\Recepciones;

use App\Models\CategoriaAlmacen;
use App\Models\Producto;
use App\Models\ItemSerializado;
use App\Models\MovimientoStock;
use App\Models\Sede;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class Crear extends Component
{
    public ?string $proveedorId = null;
    public ?int $sedeId = null;
    public string $notas = '';
    public array $cantidades = [];

    public array $series = []; // [producto_id => [{serie, marca, ...}, ...]]
    public array $produces = []; // [producto_id => produce] — shared across all units of same product

    public array $proveedores = [
        'MOCAVIN',
        'AUTO TOP',
        'UNIGAS',
        "D'WILLIAMS",
    ];

    public string $seccion = 'elegir';

    // Modal kit
    public bool $modalKitAbierto = false;
    public int $modalKitId = 0;
    public string $modalKitNombre = '';
    public int $modalKitCantidad = 0;
    public array $modalComponentes = [];
    public string $kitProduce = ''; // Produce compartido para todos los componentes del kit
    public bool $mostrandoFormNuevo = false;

    public string $nuevoNombre = '';
    public string $nuevoTipo = 'serializado';
    public ?int $nuevoCategoriaId = null;
    public string $nuevoSerie = '';
    public int $nuevaCantidad = 1;

    public ?int $productoExistenteId = null;

    public array $colaKits = [];
    public int $colaIndex = 0;

    public bool $mostrandoFormKit = false;
    public string $nuevoKitNombre = '';
    public string $nuevoKitGeneracion = '';

    public bool $modalEditarKit = false;
    public int $editarKitId = 0;
    public string $editarKitNombre = '';
    public string $editarKitGeneracion = '';

    // Modal nuevo proveedor
    public bool $modalProveedorAbierto = false;
    public string $nuevoProveedorNombre = '';

    // Productos por cantidad
    public string $subSeccionProductos = '';
    public array $cantidadesCantidad = [];
    public string $buscarCantidad = '';
    public bool $mostrandoFormNuevoCantidad = false;
    public string $nuevoCantidadNombre = '';
    public ?int $nuevoCantidadCategoriaId = null;
    public int $nuevoCantidadStockInicial = 1;

    /**
     * Campos que se comparten entre todas las unidades del mismo producto (ej: produce).
     * El resto de campos del esquema son por unidad.
     */
    public static array $camposCompartidos = ['produce'];

    // ── Helper centralizado de errores ──────────────────────────
    // Cualquier excepción inesperada pasa por acá: se loguea, se avisa
    // al usuario y NUNCA se toca el estado de modales/forms, para que
    // el usuario pueda corregir y reintentar sin perder lo que cargó.
    private function avisarError(\Throwable $e, string $evento = 'swal-init', string $mensajeAmigable = 'Ocurrió un error inesperado. Intentá nuevamente.'): void
    {
        report($e);
        $this->dispatch($evento, tipo: 'error', titulo: 'Error', mensaje: $mensajeAmigable);
    }

    public function elegirSeccion(string $seccion): void
    {
        if (empty($this->proveedorId)) {
            $this->dispatch('swal-init', tipo: 'warning', titulo: 'Atención', mensaje: 'Seleccioná un proveedor antes de continuar.');
            return;
        }
        $this->seccion = $seccion;
    }

    public function volverAEleccion(): void
    {
        $this->seccion = 'elegir';
    }

    // ── Modal nuevo proveedor ──────────────────────────────────

    public function abrirModalProveedor(): void
    {
        $this->modalProveedorAbierto = true;
        $this->nuevoProveedorNombre = '';
    }

    public function cerrarModalProveedor(): void
    {
        $this->modalProveedorAbierto = false;
        $this->nuevoProveedorNombre = '';
    }

    public function agregarProveedor(string $nombre): void
    {
        $nombre = strtoupper(trim($nombre));

        if ($nombre === '') {
            $this->dispatch('swal-init', tipo: 'error', titulo: 'Error', mensaje: 'El nombre es obligatorio.');
            return;
        }

        if (in_array($nombre, $this->proveedores, true)) {
            $this->dispatch('swal-init', tipo: 'warning', titulo: 'Duplicado', mensaje: 'Ese proveedor ya existe.');
            return;
        }

        $this->proveedores[] = $nombre;
        $this->proveedorId = $nombre;

        $this->dispatch('swal-init', tipo: 'success', titulo: '¡Listo!', mensaje: "Proveedor \"{$nombre}\" agregado.");
    }

    public function guardarProveedor(): void
    {
        $nombre = strtoupper(trim($this->nuevoProveedorNombre));

        if ($nombre === '') {
            $this->dispatch('swal-init', tipo: 'error', titulo: 'Error', mensaje: 'El nombre es obligatorio.');
            return; // modal se mantiene abierto
        }

        if (in_array($nombre, $this->proveedores, true)) {
            $this->dispatch('swal-init', tipo: 'warning', titulo: 'Duplicado', mensaje: 'Ese proveedor ya existe.');
            return; // modal se mantiene abierto
        }

        try {
            $this->proveedores[] = $nombre;
            $this->proveedorId = $nombre;

            // Solo cerramos el modal cuando TODO salió bien
            $this->modalProveedorAbierto = false;
            $this->nuevoProveedorNombre = '';

            $this->dispatch('swal-init', tipo: 'success', titulo: '¡Listo!', mensaje: "Proveedor \"{$nombre}\" agregado.");
        } catch (\Throwable $e) {
            // Si algo falla, el modal NO se cierra (no tocamos modalProveedorAbierto)
            $this->avisarError($e, 'swal-init', 'No se pudo agregar el proveedor.');
        }
    }

    // ── Productos por cantidad ─────────────────────────────────

    public function abrirSubSeccion(string $tipo): void
    {
        $this->subSeccionProductos = $tipo;

        if ($tipo === 'cantidad') {
            foreach ($this->productosCantidadDisponibles as $prod) {
                if (!isset($this->cantidadesCantidad[$prod->id])) {
                    $this->cantidadesCantidad[$prod->id] = 0;
                }
            }
        }
    }

    public function volverAProductos(): void
    {
        $this->subSeccionProductos = '';
    }

    public function toggleFormNuevoCantidad(): void
    {
        $this->mostrandoFormNuevoCantidad = !$this->mostrandoFormNuevoCantidad;
        $this->nuevoCantidadNombre = '';
        $this->nuevoCantidadCategoriaId = null;
        $this->nuevoCantidadStockInicial = 1;
        $this->resetValidation();
    }

    public function getCategoriasCantidadProperty()
    {
        return CategoriaAlmacen::where('es_serializado', false)
            ->where('es_kit', false)
            ->orderBy('nombre')
            ->get();
    }

    public function getProductosCantidadDisponiblesProperty()
    {
        return Producto::with('categoria')
            ->whereHas('categoria', fn ($q) => $q->where('es_serializado', false)->where('es_kit', false))
            ->where('activo', true)
            ->orderBy('categoria_id')
            ->orderBy('nombre')
            ->get();
    }

    public function getFiltradosCantidadProperty()
    {
        $q = trim($this->buscarCantidad);
        return $this->productosCantidadDisponibles->when($q, fn ($col) => $col->filter(fn ($p) => stripos($p->nombre, $q) !== false));
    }

    public function crearProductoCantidad(): void
    {
        $this->resetValidation();

        // ── Validaciones: si fallan, el form permanece abierto ──
        if (empty(trim($this->nuevoCantidadNombre))) {
            $this->dispatch('swal-init', tipo: 'error', titulo: 'Error', mensaje: 'El nombre es obligatorio.');
            return;
        }

        if (!$this->nuevoCantidadCategoriaId) {
            $this->dispatch('swal-init', tipo: 'error', titulo: 'Error', mensaje: 'Seleccioná una categoría.');
            return;
        }

        if ($this->nuevoCantidadStockInicial < 1) {
            $this->dispatch('swal-init', tipo: 'error', titulo: 'Error', mensaje: 'El stock debe ser al menos 1.');
            return;
        }

        try {
            if (Producto::where('nombre', 'LIKE', trim($this->nuevoCantidadNombre))->exists()) {
                $this->dispatch('swal-init', tipo: 'error', titulo: 'Duplicado', mensaje: 'Ya existe un producto con ese nombre.');
                return;
            }

            $producto = Producto::create([
                'categoria_id' => $this->nuevoCantidadCategoriaId,
                'nombre' => trim($this->nuevoCantidadNombre),
                'activo' => true,
            ]);

            $this->cantidadesCantidad[$producto->id] = $this->nuevoCantidadStockInicial;
            // NO cerrar el form — solo limpiar campos para que el usuario pueda crear otro
            $this->nuevoCantidadNombre = '';
            $this->nuevoCantidadStockInicial = 1;
            // Mantener la categoría seleccionada por si crea varios en la misma

            $this->dispatch('swal-init', tipo: 'success', titulo: '¡Listo!', mensaje: "{$producto->nombre} registrado con stock inicial.");
        } catch (\Throwable $e) {
            // Ante un error de BD (ej. constraint), el form NO se cierra
            $this->avisarError($e, 'swal-init', 'No se pudo registrar el producto.');
        }
    }

    public function getTotalCantidadProperty(): int
    {
        return collect($this->cantidadesCantidad)->filter(fn ($c) => $c > 0)->sum();
    }

    public function guardarCantidad(): void
    {
        if (empty($this->proveedorId)) {
            $this->dispatch('swal-init', tipo: 'warning', titulo: 'Atención', mensaje: 'Seleccioná un proveedor.');
            return;
        }

        $conCantidad = collect($this->cantidadesCantidad)->filter(fn ($c) => $c > 0)->toArray();

        if (empty($conCantidad)) {
            $this->dispatch('swal-init', tipo: 'warning', titulo: 'Atención', mensaje: 'Poné cantidad en al menos un producto.');
            return;
        }

        $usuarioId = Auth::id();
        $sedeId = $this->sedeId;
        $proveedor = $this->proveedorId;
        $totalRegistrado = 0;

        try {
            DB::transaction(function () use ($conCantidad, $usuarioId, $sedeId, $proveedor, &$totalRegistrado) {
                foreach ($conCantidad as $productoId => $cantidad) {
                    $producto = Producto::find($productoId);
                    if (!$producto) continue;

                    MovimientoStock::registrar($producto, 'entrada', $cantidad, null, $usuarioId, "Recepción de proveedor: {$proveedor}", $sedeId);
                    $totalRegistrado += $cantidad;
                }
            });

            $this->dispatch('swal-init', tipo: 'success', titulo: '¡Recepción registrada!', mensaje: "{$totalRegistrado} unidad(es) recibida(s).");
            $this->redirect(route('almacen.recepciones.listado'));

        } catch (\Throwable $e) {
            // La transacción falló completa: no se pierde nada en pantalla,
            // el usuario sigue en la misma sección con sus cantidades cargadas.
            $this->avisarError($e, 'swal-init', 'No se pudo registrar la recepción.');
        }
    }

    // ── Modal kit ──────────────────────────────────────────────

    public function abrirModal(int $kitId, int $cantidad): void
    {
        try {
            $kit = Producto::find($kitId);
            if (!$kit) {
                $this->dispatch('swal-kit', tipo: 'error', titulo: 'Error', mensaje: 'El kit seleccionado no existe.');
                return;
            }

            $this->modalKitId = $kitId;
            $this->modalKitNombre = $kit->nombre;
            $this->modalKitCantidad = $cantidad;
            $this->modalComponentes = [];
            $this->kitProduce = '';
            $this->mostrandoFormNuevo = false;

            $cantidadesGuardadas = DB::table('kit_componentes')
                ->where('producto_kit_id', $kitId)
                ->pluck('cantidad_esperada', 'producto_componente_id')
                ->toArray();

            if (!empty($cantidadesGuardadas)) {
                $productos = Producto::with('categoria')
                    ->whereIn('id', array_keys($cantidadesGuardadas))
                    ->get();

                foreach ($productos as $producto) {
                    $esSerializado = $producto->categoria->es_serializado ?? false;
                    $esquema = $producto->categoria->esquema_atributos ?? ['serie'];
                    $camposEsquema = is_string($esquema) ? (json_decode($esquema, true) ?? ['serie']) : $esquema;
                    // Campos por unidad (excluyendo 'produce' que es compartido)
                    $camposPorUnidad = $esSerializado
                        ? array_values(array_diff($camposEsquema, self::$camposCompartidos))
                        : [];

                    $entry = [
                        'producto_id' => $producto->id,
                        'nombre' => $producto->nombre,
                        'es_serializado' => $esSerializado,
                        'cantidad' => $esSerializado ? 1 : ($cantidadesGuardadas[$producto->id] ?? 1),
                        'campos_esquema' => $camposPorUnidad,
                    ];

                    // Para serializados: crear N unidades (una por kit)
                    if ($esSerializado) {
                        $entry['unidades'] = [];
                        for ($u = 0; $u < $cantidad; $u++) {
                            $unit = ['serie' => ''];
                            foreach ($camposPorUnidad as $campo) {
                                if ($campo !== 'serie') $unit[$campo] = '';
                            }
                            $entry['unidades'][] = $unit;
                        }
                    } else {
                        $entry['serie'] = '';
                    }

                    $this->modalComponentes[] = $entry;
                }
            }

            $this->modalKitAbierto = true;
        } catch (\Throwable $e) {
            // Si algo falla al abrir el modal, avisamos y no dejamos el modal
            // a medio abrir con datos incompletos.
            $this->modalKitAbierto = false;
            $this->avisarError($e, 'swal-kit', 'No se pudo abrir el detalle del kit.');
        }
    }

    public function cerrarModal(): void
    {
        $this->modalKitAbierto = false;
        $this->modalComponentes = [];
        $this->modalKitId = 0;
        $this->modalKitNombre = '';
        $this->modalKitCantidad = 0;
        $this->kitProduce = '';
        $this->mostrandoFormNuevo = false;
    }

    public function toggleFormNuevo(): void
    {
        $this->mostrandoFormNuevo = !$this->mostrandoFormNuevo;
        $this->nuevoNombre = '';
        $this->nuevoTipo = 'serializado';
        $this->nuevoCategoriaId = null;
        $this->nuevoSerie = '';
        $this->nuevaCantidad = 1;
        $this->resetValidation();
    }

    public function getCategoriasSerializadasProperty()
    {
        return CategoriaAlmacen::where('es_serializado', true)
            ->whereNotNull('esquema_atributos')
            ->where('es_kit', false)
            ->orderBy('nombre')
            ->get();
    }

    public function registrarComponenteNuevo(): void
    {
        $this->resetValidation();

        // ── Validaciones: si fallan, el modal Y el form permanecen abiertos ──
        if (!$this->modalKitId) {
            $this->dispatch('swal-kit', tipo: 'error', titulo: 'Error', mensaje: 'No hay kit seleccionado.');
            return;
        }

        if (empty(trim($this->nuevoNombre))) {
            $this->dispatch('swal-kit', tipo: 'error', titulo: 'Error', mensaje: 'El nombre es obligatorio.');
            return;
        }

        try {
            if (Producto::where('nombre', 'LIKE', trim($this->nuevoNombre))->exists()) {
                $this->dispatch('swal-kit', tipo: 'error', titulo: 'Duplicado', mensaje: 'Ya existe un producto con ese nombre.');
                return;
            }

            $categoria = null;

            if ($this->nuevoTipo === 'serializado') {
                if (!$this->nuevoCategoriaId) {
                    $this->dispatch('swal-kit', tipo: 'error', titulo: 'Error', mensaje: 'Seleccioná la categoría del componente.');
                    return;
                }

                $categoria = CategoriaAlmacen::find($this->nuevoCategoriaId);
                if (!$categoria || !$categoria->es_serializado) {
                    $this->dispatch('swal-kit', tipo: 'error', titulo: 'Error', mensaje: 'Categoría inválida.');
                    return;
                }

                if (empty(trim($this->nuevoSerie))) {
                    $this->dispatch('swal-kit', tipo: 'error', titulo: 'Error', mensaje: 'Ingresá la serie del componente.');
                    return;
                }

                $serieUpper = strtoupper(trim($this->nuevoSerie));

                if (ItemSerializado::where('serie', $serieUpper)->exists()) {
                    $this->dispatch('swal-kit', tipo: 'error', titulo: 'Duplicado', mensaje: "La serie \"{$serieUpper}\" ya está registrada.");
                    return;
                }

                foreach ($this->modalComponentes as $componente) {
                    if ($componente['es_serializado'] ?? false) {
                        foreach (($componente['unidades'] ?? []) as $unidad) {
                            if (strtoupper(trim($unidad['serie'] ?? '')) === $serieUpper) {
                                $this->dispatch('swal-kit', tipo: 'error', titulo: 'Duplicado', mensaje: "La serie \"{$serieUpper}\" ya está en este kit.");
                                return;
                            }
                        }
                    }
                }
            } else {
                if ($this->nuevaCantidad < 1) {
                    $this->dispatch('swal-kit', tipo: 'error', titulo: 'Error', mensaje: 'La cantidad debe ser al menos 1.');
                    return;
                }

                $categoria = CategoriaAlmacen::firstOrCreate(
                    ['nombre' => 'Componentes Kit'],
                    ['es_serializado' => false, 'es_kit' => false]
                );
            }

            if (!$categoria) {
                $this->dispatch('swal-kit', tipo: 'error', titulo: 'Error', mensaje: 'No se pudo determinar la categoría del componente.');
                return;
            }

            $producto = Producto::create([
                'categoria_id' => $categoria->id,
                'nombre' => trim($this->nuevoNombre),
                'activo' => true,
            ]);

            DB::table('kit_componentes')->insert([
                'producto_kit_id' => $this->modalKitId,
                'producto_componente_id' => $producto->id,
                'cantidad_esperada' => $this->nuevoTipo === 'serializado' ? 1 : $this->nuevaCantidad,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $esquema = $categoria->esquema_atributos ?? ['serie'];
            $camposEsquema = is_string($esquema) ? (json_decode($esquema, true) ?? ['serie']) : $esquema;
            $esSerializado = $this->nuevoTipo === 'serializado';
            $camposPorUnidad = $esSerializado
                ? array_values(array_diff($camposEsquema, self::$camposCompartidos))
                : [];

            $entry = [
                'producto_id' => $producto->id,
                'nombre' => $producto->nombre,
                'es_serializado' => $esSerializado,
                'cantidad' => $esSerializado ? 1 : $this->nuevaCantidad,
                'campos_esquema' => $camposPorUnidad,
            ];

            if ($esSerializado) {
                $entry['unidades'] = [];
                for ($u = 0; $u < $this->modalKitCantidad; $u++) {
                    $unit = ['serie' => ''];
                    foreach ($camposPorUnidad as $campo) {
                        if ($campo !== 'serie') $unit[$campo] = '';
                    }
                    $entry['unidades'][] = $unit;
                }
                // Si el usuario ya puso una serie en el formulario, meterla en la primera unidad
                if (!empty($this->nuevoSerie)) {
                    $entry['unidades'][0]['serie'] = strtoupper(trim($this->nuevoSerie));
                }
            } else {
                $entry['serie'] = '';
            }

            $this->modalComponentes[] = $entry;

            // NO cerrar el form ni el modal — solo limpiar campos para que el usuario pueda crear otro
            $this->nuevoNombre = '';
            $this->nuevoTipo = 'serializado';
            $this->nuevoCategoriaId = null;
            $this->nuevoSerie = '';
            $this->nuevaCantidad = 1;

            $this->dispatch('swal-kit', tipo: 'success', titulo: '¡Listo!', mensaje: 'Componente registrado y agregado al kit.');
        } catch (\Throwable $e) {
            // Ante cualquier error de BD, ni el modal ni el form se cierran
            $this->avisarError($e, 'swal-kit', 'No se pudo registrar el componente.');
        }
    }

    public function agregarComponenteExistente(): void
    {
        if (!$this->productoExistenteId) {
            $this->dispatch('swal-kit', tipo: 'warning', titulo: 'Atención', mensaje: 'Seleccioná un producto.');
            return;
        }

        if (!$this->modalKitId) {
            $this->dispatch('swal-kit', tipo: 'error', titulo: 'Error', mensaje: 'No hay kit seleccionado.');
            return;
        }

        try {
            $producto = Producto::with('categoria')->find($this->productoExistenteId);
            if (!$producto) {
                $this->dispatch('swal-kit', tipo: 'error', titulo: 'Error', mensaje: 'El producto seleccionado no existe.');
                return;
            }

            foreach ($this->modalComponentes as $componente) {
                if (($componente['producto_id'] ?? null) === $producto->id) {
                    $this->dispatch('swal-kit', tipo: 'warning', titulo: 'Atención', mensaje: "{$producto->nombre} ya está en este kit.");
                    return;
                }
            }

            $esSerializado = $producto->categoria->es_serializado ?? false;
            $esquema = $producto->categoria->esquema_atributos ?? ['serie'];
            $camposEsquema = is_string($esquema) ? (json_decode($esquema, true) ?? ['serie']) : $esquema;
            $camposPorUnidad = $esSerializado
                ? array_values(array_diff($camposEsquema, self::$camposCompartidos))
                : [];

            DB::table('kit_componentes')->insert([
                'producto_kit_id' => $this->modalKitId,
                'producto_componente_id' => $producto->id,
                'cantidad_esperada' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $entry = [
                'producto_id' => $producto->id,
                'nombre' => $producto->nombre,
                'es_serializado' => $esSerializado,
                'cantidad' => 1,
                'campos_esquema' => $camposPorUnidad,
            ];

            if ($esSerializado) {
                $entry['unidades'] = [];
                for ($u = 0; $u < $this->modalKitCantidad; $u++) {
                    $unit = ['serie' => ''];
                    foreach ($camposPorUnidad as $campo) {
                        if ($campo !== 'serie') $unit[$campo] = '';
                    }
                    $entry['unidades'][] = $unit;
                }
            } else {
                $entry['serie'] = '';
            }

            $this->modalComponentes[] = $entry;

            $this->productoExistenteId = null;
        } catch (\Throwable $e) {
            // El modal permanece abierto para que el usuario reintente
            $this->avisarError($e, 'swal-kit', 'No se pudo agregar el componente existente.');
        }
    }

    public function quitarComponenteModal(int $index): void
    {
        if (!isset($this->modalComponentes[$index])) return;

        $componente = $this->modalComponentes[$index];

        try {
            DB::table('kit_componentes')
                ->where('producto_kit_id', $this->modalKitId)
                ->where('producto_componente_id', $componente['producto_id'])
                ->delete();

            // Solo quitamos del arreglo en memoria si el delete en BD no falló
            array_splice($this->modalComponentes, $index, 1);
        } catch (\Throwable $e) {
            // Si falla el delete en BD, NO tocamos el arreglo en memoria
            // (así lo que ve el usuario sigue siendo consistente con la BD)
            $this->avisarError($e, 'swal-kit', 'No se pudo quitar el componente.');
        }
    }

    public function eliminarKit(int $kitId): void
    {
        try {
            $kit = Producto::find($kitId);
            if (!$kit) {
                $this->dispatch('swal-kit', tipo: 'error', titulo: 'Error', mensaje: 'El kit no existe.');
                return;
            }

            DB::table('kit_componentes')->where('producto_kit_id', $kitId)->delete();
            $kit->update(['activo' => false]);

            unset($this->cantidades[$kitId]);

            $this->dispatch('swal-kit', tipo: 'success', titulo: 'Eliminado', mensaje: "{$kit->nombre} desactivado.");
        } catch (\Throwable $e) {
            $this->avisarError($e, 'swal-kit', 'No se pudo eliminar el kit.');
        }
    }

    public function toggleFormKit(): void
    {
        $this->mostrandoFormKit = !$this->mostrandoFormKit;
        $this->nuevoKitNombre = '';
        $this->nuevoKitGeneracion = '';
        $this->resetValidation();
    }

    public function registrarKitNuevo(): void
    {
        $this->resetValidation();

        // ── Validaciones: si fallan, el form permanece abierto ──
        if (empty(trim($this->nuevoKitNombre))) {
            $this->dispatch('swal-kit', tipo: 'error', titulo: 'Error', mensaje: 'El nombre del kit es obligatorio.');
            return;
        }

        if (empty(trim($this->nuevoKitGeneracion))) {
            $this->dispatch('swal-kit', tipo: 'error', titulo: 'Error', mensaje: 'La generación es obligatoria.');
            return;
        }

        try {
            if (Producto::where('nombre', 'LIKE', trim($this->nuevoKitNombre))->exists()) {
                $this->dispatch('swal-kit', tipo: 'error', titulo: 'Duplicado', mensaje: 'Ya existe un producto con ese nombre.');
                return;
            }

            $categoria = CategoriaAlmacen::firstOrCreate(
                ['nombre' => 'Kits'],
                ['es_serializado' => false, 'es_kit' => true]
            );

            $producto = Producto::create([
                'categoria_id' => $categoria->id,
                'nombre' => trim($this->nuevoKitNombre),
                'atributos' => ['generacion' => trim($this->nuevoKitGeneracion)],
                'activo' => true,
            ]);

            $this->cantidades[$producto->id] = 0;
            // NO cerrar el form — solo limpiar campos para crear otro kit
            $this->nuevoKitNombre = '';
            $this->nuevoKitGeneracion = '';

            $this->dispatch('swal-kit', tipo: 'success', titulo: '¡Listo!', mensaje: "{$producto->nombre} registrado.");
        } catch (\Throwable $e) {
            $this->avisarError($e, 'swal-kit', 'No se pudo registrar el kit.');
        }
    }

    public function abrirEditarKit(int $kitId): void
    {
        try {
            $kit = Producto::find($kitId);
            if (!$kit) {
                $this->dispatch('swal-kit', tipo: 'error', titulo: 'Error', mensaje: 'El kit no existe.');
                return;
            }

            $this->editarKitId = $kitId;
            $this->editarKitNombre = $kit->nombre;
            $this->editarKitGeneracion = $kit->atributos['generacion'] ?? '';
            $this->modalEditarKit = true;
        } catch (\Throwable $e) {
            $this->modalEditarKit = false;
            $this->avisarError($e, 'swal-kit', 'No se pudo abrir la edición del kit.');
        }
    }

    public function cerrarEditarKit(): void
    {
        $this->modalEditarKit = false;
        $this->editarKitId = 0;
        $this->editarKitNombre = '';
        $this->editarKitGeneracion = '';
    }

    public function actualizarKit(int $id, string $nombre, string $generacion): void
    {
        $this->resetValidation();

        if (empty(trim($nombre))) {
            $this->dispatch('swal-kit', tipo: 'error', titulo: 'Error', mensaje: 'El nombre es obligatorio.');
            return;
        }

        try {
            $kit = Producto::find($id);
            if (!$kit) {
                $this->dispatch('swal-kit', tipo: 'error', titulo: 'Error', mensaje: 'El kit no existe.');
                return;
            }

            if (Producto::where('nombre', 'LIKE', trim($nombre))->where('id', '!=', $kit->id)->exists()) {
                $this->dispatch('swal-kit', tipo: 'error', titulo: 'Duplicado', mensaje: 'Ya existe un producto con ese nombre.');
                return;
            }

            $kit->update([
                'nombre' => trim($nombre),
                'atributos' => array_merge($kit->atributos ?? [], ['generacion' => trim($generacion)]),
            ]);

            $this->dispatch('swal-kit', tipo: 'success', titulo: '¡Listo!', mensaje: 'Kit actualizado.');
        } catch (\Throwable $e) {
            $this->avisarError($e, 'swal-kit', 'No se pudo actualizar el kit.');
        }
    }

    public function guardarEditarKit(): void
    {
        $this->resetValidation();

        // ── Validación: si falla, el modal permanece abierto ──
        if (empty(trim($this->editarKitNombre))) {
            $this->dispatch('swal-kit', tipo: 'error', titulo: 'Error', mensaje: 'El nombre es obligatorio.');
            return;
        }

        try {
            $kit = Producto::find($this->editarKitId);
            if (!$kit) {
                $this->dispatch('swal-kit', tipo: 'error', titulo: 'Error', mensaje: 'El kit no existe.');
                return;
            }

            if (Producto::where('nombre', 'LIKE', trim($this->editarKitNombre))->where('id', '!=', $kit->id)->exists()) {
                $this->dispatch('swal-kit', tipo: 'error', titulo: 'Duplicado', mensaje: 'Ya existe un producto con ese nombre.');
                return;
            }

            $kit->update([
                'nombre' => trim($this->editarKitNombre),
                'atributos' => array_merge($kit->atributos ?? [], ['generacion' => trim($this->editarKitGeneracion)]),
            ]);

            // Solo cerramos el modal cuando el update salió bien
            $this->modalEditarKit = false;
            $this->dispatch('swal-kit', tipo: 'success', titulo: '¡Listo!', mensaje: 'Kit actualizado.');
        } catch (\Throwable $e) {
            // El modal NO se cierra ante un error de BD
            $this->avisarError($e, 'swal-kit', 'No se pudo actualizar el kit.');
        }
    }

    // ── Guardar kits ───────────────────────────────────────────

    public function guardar(): void
    {
        if (empty($this->proveedorId)) {
            $this->dispatch('swal-kit', tipo: 'warning', titulo: 'Atención', mensaje: 'Seleccioná un proveedor.');
            return;
        }

        $kitsARecibir = collect($this->cantidades)->filter(fn ($c) => $c > 0)->toArray();

        if (empty($kitsARecibir)) {
            $this->dispatch('swal-kit', tipo: 'warning', titulo: 'Atención', mensaje: 'Seleccioná al menos un kit para recibir.');
            return;
        }

        $this->colaKits = [];
        foreach ($kitsARecibir as $productoId => $cantidad) {
            $this->colaKits[] = ['producto_id' => $productoId, 'cantidad' => $cantidad];
        }

        $this->colaIndex = 0;
        $primero = $this->colaKits[0];
        $this->abrirModal($primero['producto_id'], $primero['cantidad']);
    }

    public function confirmarModal(): void
    {
        if (empty($this->modalComponentes)) {
            $this->dispatch('swal-kit', tipo: 'warning', titulo: 'Atención', mensaje: 'No hay componentes en este kit.');
            return; // modal permanece abierto
        }

        // ── Validación de campos y series ──
        // Envuelta en try/catch: cualquier índice inesperado faltante
        // (undefined array key, etc.) se transforma en un aviso amigable
        // en lugar de romper la request y "cerrar" el modal.
        try {
            $seriesIngresadas = [];

            foreach ($this->modalComponentes as $idx => $componente) {
                if (!($componente['es_serializado'] ?? false)) continue;

                $camposEsquema = $componente['campos_esquema'] ?? ['serie'];
                $unidades = $componente['unidades'] ?? [];
                $nombreComponente = $componente['nombre'] ?? 'componente';

                foreach ($unidades as $uIdx => $unidad) {
                    // Validar campos requeridos del esquema (excepto produce que es opcional y compartido)
                    foreach ($camposEsquema as $campo) {
                        if ($campo === 'produce') continue;
                        $valor = $unidad[$campo] ?? null;
                        if (empty($valor)) {
                            $this->dispatch('swal-kit', tipo: 'error', titulo: 'Campo faltante',
                                mensaje: ucfirst(str_replace('_', ' ', $campo)) . " es obligatorio para: {$nombreComponente} (unidad " . ($uIdx + 1) . ")");
                            return; // modal permanece abierto
                        }
                    }

                    // Validar serie (siempre requerida para serializados)
                    $serie = strtoupper(trim((string) ($unidad['serie'] ?? '')));
                    if ($serie === '') {
                        $this->dispatch('swal-kit', tipo: 'error', titulo: 'Serie faltante',
                            mensaje: "Registrá la serie de: {$nombreComponente} (unidad " . ($uIdx + 1) . ")");
                        return; // modal permanece abierto
                    }

                    if (in_array($serie, $seriesIngresadas, true)) {
                        $this->dispatch('swal-kit', tipo: 'error', titulo: 'Serie duplicada', mensaje: "Duplicado: \"{$serie}\"");
                        return; // modal permanece abierto
                    }

                    if (ItemSerializado::where('serie', $serie)->exists()) {
                        $this->dispatch('swal-kit', tipo: 'error', titulo: 'Serie duplicada', mensaje: "La serie \"{$serie}\" ya está registrada.");
                        return; // modal permanece abierto
                    }

                    $seriesIngresadas[] = $serie;
                }
            }
        } catch (\Throwable $e) {
            // Error inesperado durante la validación: el modal NO se cierra
            $this->avisarError($e, 'swal-kit', 'No se pudieron validar los datos del kit. Revisá los campos e intentá de nuevo.');
            return;
        }

        // ── Guardado real ──
        try {
            DB::transaction(function () {
                $this->guardarRecetaKit();
                $this->crearKitsConComponentes();
            });

            // Solo cerramos el modal (o avanzamos de kit) cuando el guardado fue exitoso
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
            // La transacción falló: el modal NO se cierra, el usuario conserva
            // todo lo que cargó y puede corregir/reintentar.
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
        $proveedorNombre = $this->proveedorId;
        $usuarioId = Auth::id();
        $kitProduce = trim($this->kitProduce);

        $kit = Producto::find($this->modalKitId);
        if (!$kit) throw new \RuntimeException('El kit seleccionado no existe.');

        for ($i = 0; $i < $this->modalKitCantidad; $i++) {
            $kitItem = ItemSerializado::create([
                'producto_id' => $kit->id,
                'serie' => null,
                'atributos' => ['proveedor' => $proveedorNombre, 'recepcion_fecha' => now()->toDateString()],
                'estado' => 'en_stock',
                'sede_id' => $sedeId,
            ]);

            foreach ($this->modalComponentes as $componente) {
                $producto = Producto::find($componente['producto_id']);
                if (!$producto) throw new \RuntimeException("El producto {$componente['producto_id']} no existe.");

                if ($componente['es_serializado']) {
                    // Obtener la unidad correspondiente a este kit (índice $i)
                    $unidad = $componente['unidades'][$i] ?? null;
                    if (!$unidad) throw new \RuntimeException("Faltan datos para {$componente['nombre']} (kit " . ($i + 1) . ")");

                    // Atributos base del componente
                    $atributos = [
                        'agregado_a_kit' => true,
                        'fecha' => now()->toDateString(),
                        'serie_registrada_por' => $usuarioId,
                        'serie_registrada_en' => now()->toDateTimeString(),
                        'proveedor' => $proveedorNombre,
                        'recepcion_fecha' => now()->toDateString(),
                    ];

                    // Agregar campos del esquema desde la unidad (marca, generacion, capacidad, etc.)
                    $camposEsquema = $componente['campos_esquema'] ?? [];
                    foreach ($camposEsquema as $campo) {
                        if ($campo === 'produce') continue;
                        $valor = $unidad[$campo] ?? null;
                        if ($valor !== null && $valor !== '') {
                            $atributos[$campo] = $valor;
                        }
                    }

                    // Produce compartido (opcional)
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

        MovimientoStock::registrar($kit, 'entrada', $this->modalKitCantidad, null, $usuarioId, "Recepción de proveedor: {$proveedorNombre}", $sedeId);
    }

    // ── Guardar productos individuales (serializados) ──────────

    public function guardarProductos(): void
    {
        if (empty($this->proveedorId)) {
            $this->dispatch('swal-init', tipo: 'warning', titulo: 'Atención', mensaje: 'Seleccioná un proveedor.');
            return;
        }

        $conCantidad = collect($this->cantidades)->filter(fn ($c) => $c > 0)->toArray();

        if (empty($conCantidad)) {
            $this->dispatch('swal-init', tipo: 'warning', titulo: 'Atención', mensaje: 'Poné cantidad en al menos un producto.');
            return;
        }

        // ── Validar campos requeridos según esquema de cada producto ──
        // Envuelta en try/catch: si algo inesperado ocurre acá (esquema mal
        // formado, índice faltante), avisamos sin perder lo cargado.
        try {
            foreach ($conCantidad as $productoId => $cantidad) {
                $producto = Producto::find($productoId);
                if (!$producto || !$producto->categoria?->es_serializado) continue;

                $esquema = $producto->categoria->esquema_atributos ?? ['serie'];
                $campos = is_string($esquema) ? (json_decode($esquema, true) ?? ['serie']) : $esquema;
                $camposUnidad = self::camposPorUnidad($campos);
                $seriesProducto = $this->series[$productoId] ?? [];

                // Validar campos por unidad
                for ($i = 0; $i < $cantidad; $i++) {
                    $data = $seriesProducto[$i] ?? [];
                    foreach ($camposUnidad as $campo) {
                        if (empty($data[$campo] ?? null)) {
                            $this->addError('general', "Falta {$campo} para {$producto->nombre} (unidad " . ($i + 1) . ").");
                            $this->dispatch('swal-init', tipo: 'error', titulo: 'Campo faltante',
                                mensaje: "Falta {$campo} para {$producto->nombre} (unidad " . ($i + 1) . ").");
                            return; // formulario permanece con lo cargado, sin navegar
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
        $proveedor = $this->proveedorId;
        $totalRegistrado = 0;

        try {
            DB::transaction(function () use ($conCantidad, $usuarioId, $sedeId, $proveedor, &$totalRegistrado) {
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
                            'proveedor' => $proveedor,
                            'recepcion_fecha' => now()->toDateString(),
                            'registrado_por' => $usuarioId,
                        ];

                        // Campo compartido (produce) — mismo valor para todas las unidades
                        if ($produceCompartido !== null && $produceCompartido !== '') {
                            $atributos['produce'] = $produceCompartido;
                        }

                        // Campos por unidad (serie, marca, generacion, capacidad, etc.)
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

                    MovimientoStock::registrar($producto, 'entrada', $cantidad, null, $usuarioId, "Recepción de proveedor: {$proveedor}", $sedeId);
                    $totalRegistrado += $cantidad;
                }
            });

            $this->dispatch('swal-init', tipo: 'success', titulo: '¡Recepción registrada!', mensaje: "{$totalRegistrado} producto(s) recibido(s).");
            $this->redirect(route('almacen.recepciones.listado'));

        } catch (\Throwable $e) {
            // La transacción falló: el usuario permanece en la misma pantalla,
            // con todas las series y cantidades tal como las había cargado.
            $this->avisarError($e, 'swal-init', 'No se pudo registrar la recepción.');
        }
    }

    // ── Getters ────────────────────────────────────────────────

    public function mount(): void
    {
        $this->sedeId = Sede::activas()->orderBy('id')->first()?->id ?? 1;

        foreach ($this->kitsDisponibles as $kit) {
            $this->cantidades[$kit->id] = 0;
        }

        foreach ($this->productosSerializados as $prod) {
            $this->cantidades[$prod->id] = 0;
            $this->produces[$prod->id] = '';
        }
    }

    public function getKitsDisponiblesProperty()
    {
        return Producto::whereHas('categoria', fn ($q) => $q->where('es_kit', true))
            ->where('activo', true)
            ->get();
    }

    public function getProductosSerializadosProperty()
    {
        return Producto::with('categoria')
            ->whereHas('categoria', fn ($q) => $q->where('es_serializado', true)->where('es_kit', false))
            ->where('activo', true)
            ->orderBy('categoria_id')
            ->orderBy('nombre')
            ->get();
    }

    public function getProductosPorCategoriaProperty(): \Illuminate\Support\Collection
    {
        return $this->productosSerializados->groupBy(fn ($p) => $p->categoria->nombre ?? 'Sin categoría');
    }

    public static function camposPorUnidad(array $esquema): array
    {
        return array_values(array_diff($esquema, self::$camposCompartidos));
    }

    public function getTotalKitsProperty(): int
    {
        return collect($this->cantidades)
            ->filter(fn ($c, $id) => $c > 0 && in_array($id, $this->kitsDisponibles->pluck('id')->toArray()))
            ->sum();
    }

    public function getTotalProductosProperty(): int
    {
        return collect($this->cantidades)
            ->filter(fn ($c, $id) => $c > 0 && in_array($id, $this->productosSerializados->pluck('id')->toArray()))
            ->sum();
    }

    public function getProductosDisponiblesModalProperty()
    {
        $idsEnModal = collect($this->modalComponentes)->pluck('producto_id')->filter()->values()->toArray();

        return Producto::with('categoria')
            ->whereHas('categoria', fn ($q) => $q->where('es_kit', false))
            ->where('activo', true)
            ->whereNotIn('id', $idsEnModal)
            ->orderBy('categoria_id')
            ->orderBy('nombre')
            ->get();
    }

    public function render()
    {
        return view('livewire.almacen.recepciones.crear');
    }
}