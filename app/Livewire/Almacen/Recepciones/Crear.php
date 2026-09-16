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

    public array $proveedores = [
        'MOCAVIN',
        'AUTO TOP',
        'UNIGAS',
        "D'WILLIAMS",
    ];

    // ─── Modal recepción ────────────────────────────────────────
    public bool $modalAbierto = false;
    public int $modalKitId = 0;
    public string $modalKitNombre = '';
    public int $modalKitCantidad = 0;
    public array $modalComponentes = [];
    public array $colaKits = [];
    public int $colaIndex = 0;

    // ─── Modal editar kit ──────────────────────────────────────
    public bool $modalEditarKit = false;
    public int $editarKitId = 0;
    public string $editarKitNombre = '';
    public string $editarKitGeneracion = '';

    // ─── Formulario nuevo componente (en modal recepción) ──────
    public bool $mostrandoFormNuevo = false;
    public string $nuevoNombre = '';
    public string $nuevoTipo = 'serializado';
    public string $nuevoSerie = '';
    public int $nuevaCantidad = 1;

    // ─── Seleccionar componente existente ─────────────────────
    public ?int $productoExistenteId = null;

    // ─── Formulario nuevo kit (en pantalla principal) ──────────
    public bool $mostrandoFormKit = false;
    public string $nuevoKitNombre = '';
    public string $nuevoKitGeneracion = '';

    public function mount()
    {
        $this->sedeId = Sede::activas()->orderBy('id')->first()?->id ?? 1;
        foreach ($this->kitsDisponibles as $kit) {
            $this->cantidades[$kit->id] = 0;
        }
    }

    public function getKitsDisponiblesProperty()
    {
        return Producto::whereHas('categoria', fn ($q) => $q->where('es_kit', true))
            ->where('activo', true)
            ->get();
    }

    public function getTotalProperty(): int
    {
        return array_sum($this->cantidades);
    }

    // ═══════════════════════════════════════════════════════════
    // MODAL RECEPCIÓN
    // ═══════════════════════════════════════════════════════════

    public function getProductosDisponiblesProperty()
    {
        $idsEnModal = collect($this->modalComponentes)->pluck('producto_id')->filter()->values()->toArray();

        return Producto::with('categoria')
            ->whereHas('categoria', fn ($q) => $q->where('es_kit', false))
            ->where('activo', true)
            ->whereNotIn('id', $idsEnModal)
            ->orderBy('nombre')
            ->get();
    }

    public function abrirModal(int $kitId, int $cantidad)
    {
        $kit = Producto::find($kitId);
        if (!$kit) return;

        $this->modalKitId = $kitId;
        $this->modalKitNombre = $kit->nombre;
        $this->modalKitCantidad = $cantidad;
        $this->modalComponentes = [];
        $this->mostrandoFormNuevo = false;

        // Cargar componentes de kit_componentes
        $cantidadesGuardadas = DB::table('kit_componentes')
            ->where('producto_kit_id', $kitId)
            ->pluck('cantidad_esperada', 'producto_componente_id')
            ->toArray();

        if (!empty($cantidadesGuardadas)) {
            $productos = Producto::with('categoria')
                ->whereIn('id', array_keys($cantidadesGuardadas))
                ->get();

            foreach ($productos as $p) {
                $esSerializado = $p->categoria->es_serializado ?? false;
                $this->modalComponentes[] = [
                    'producto_id'    => $p->id,
                    'nombre'         => $p->nombre,
                    'es_serializado' => $esSerializado,
                    'cantidad'       => $esSerializado ? 1 : ($cantidadesGuardadas[$p->id] ?? 1),
                    'serie'          => '',
                ];
            }
        }

        $this->modalAbierto = true;
    }

    public function cerrarModal()
    {
        $this->modalAbierto = false;
        $this->modalComponentes = [];
        $this->modalKitId = 0;
        $this->modalKitNombre = '';
        $this->modalKitCantidad = 0;
        $this->mostrandoFormNuevo = false;
    }

    // ═══════════════════════════════════════════════════════════
    // FORMULARIO NUEVO COMPONENTE (en modal)
    // ═══════════════════════════════════════════════════════════

    public function toggleFormNuevo()
    {
        $this->mostrandoFormNuevo = !$this->mostrandoFormNuevo;
        $this->nuevoNombre = '';
        $this->nuevoTipo = 'serializado';
        $this->nuevoSerie = '';
        $this->nuevaCantidad = 1;
        $this->resetValidation();
    }

    public function registrarComponenteNuevo()
    {
        $this->resetValidation();

        if (!$this->modalKitId) {
            $this->dispatch('swal', tipo: 'error', titulo: 'Error', mensaje: 'No hay kit seleccionado.');
            return;
        }

        if (empty(trim($this->nuevoNombre))) {
            $this->dispatch('swal', tipo: 'error', titulo: 'Error', mensaje: 'El nombre es obligatorio.');
            return;
        }

        // Verificar que no exista
        if (Producto::where('nombre', 'LIKE', trim($this->nuevoNombre))->exists()) {
            $this->dispatch('swal', tipo: 'error', titulo: 'Duplicado', mensaje: 'Ya existe un producto con ese nombre.');
            return;
        }

        // Validar serie si es serializado
        if ($this->nuevoTipo === 'serializado') {
            if (empty(trim($this->nuevoSerie))) {
                $this->dispatch('swal', tipo: 'error', titulo: 'Error', mensaje: 'Ingresá la serie del componente.');
                return;
            }
            $serieUpper = strtoupper(trim($this->nuevoSerie));
            if (ItemSerializado::where('serie', $serieUpper)->exists()) {
                $this->dispatch('swal', tipo: 'error', titulo: 'Duplicado', mensaje: "El serie \"{$serieUpper}\" ya está registrado.");
                return;
            }
            foreach ($this->modalComponentes as $c) {
                if ($c['es_serializado'] && strtoupper(trim($c['serie'])) === $serieUpper) {
                    $this->dispatch('swal', tipo: 'error', titulo: 'Duplicado', mensaje: "El serie \"{$serieUpper}\" ya está en este kit.");
                    return;
                }
            }
        } else {
            if ($this->nuevaCantidad < 1) {
                $this->dispatch('swal', tipo: 'error', titulo: 'Error', mensaje: 'La cantidad debe ser al menos 1.');
                return;
            }
        }

        // Buscar categoría correcta según tipo
        $catNombre = $this->nuevoTipo === 'serializado' ? 'Componente serializado' : 'Componente cantidad';
        $categoria = CategoriaAlmacen::firstOrCreate(
            ['nombre' => $catNombre],
            ['es_serializado' => $this->nuevoTipo === 'serializado', 'es_kit' => false]
        );

        $producto = Producto::create([
            'categoria_id' => $categoria->id,
            'nombre'       => trim($this->nuevoNombre),
            'activo'       => true,
        ]);

        DB::table('kit_componentes')->insert([
            'producto_kit_id'        => $this->modalKitId,
            'producto_componente_id' => $producto->id,
            'cantidad_esperada'      => $this->nuevoTipo === 'serializado' ? 1 : $this->nuevaCantidad,
            'created_at'             => now(),
            'updated_at'             => now(),
        ]);

        $this->modalComponentes[] = [
            'producto_id'    => $producto->id,
            'nombre'         => $producto->nombre,
            'es_serializado' => $this->nuevoTipo === 'serializado',
            'cantidad'       => $this->nuevoTipo === 'serializado' ? 1 : $this->nuevaCantidad,
            'serie'          => $this->nuevoTipo === 'serializado' ? strtoupper(trim($this->nuevoSerie)) : '',
        ];

        $this->mostrandoFormNuevo = false;
        $this->nuevoNombre = '';
        $this->nuevoTipo = 'serializado';
        $this->nuevoSerie = '';
        $this->nuevaCantidad = 1;
        $this->dispatch('swal', tipo: 'success', titulo: '¡Listo!', mensaje: 'Componente registrado y agregado al kit.');
    }

    // ─── Seleccionar componente existente ───────────────────────
    public function agregarComponenteExistente()
    {
        if (!$this->productoExistenteId) {
            $this->dispatch('swal', tipo: 'warning', titulo: 'Atención', mensaje: 'Seleccioná un producto.');
            return;
        }

        if (!$this->modalKitId) return;

        $producto = Producto::with('categoria')->find($this->productoExistenteId);
        if (!$producto) return;

        // Verificar que no esté ya en el kit
        foreach ($this->modalComponentes as $c) {
            if ($c['producto_id'] === $producto->id) {
                $this->dispatch('swal', tipo: 'warning', titulo: 'Atención', mensaje: "{$producto->nombre} ya está en este kit.");
                return;
            }
        }

        $esSerializado = $producto->categoria->es_serializado ?? false;

        DB::table('kit_componentes')->insert([
            'producto_kit_id'        => $this->modalKitId,
            'producto_componente_id' => $producto->id,
            'cantidad_esperada'      => $esSerializado ? 1 : 1,
            'created_at'             => now(),
            'updated_at'             => now(),
        ]);

        $this->modalComponentes[] = [
            'producto_id'    => $producto->id,
            'nombre'         => $producto->nombre,
            'es_serializado' => $esSerializado,
            'cantidad'       => 1,
            'serie'          => '',
        ];

        $this->productoExistenteId = null;
    }

    // ─── Quitar componente del modal ───────────────────────────
    public function quitarComponenteModal(int $index)
    {
        if (!isset($this->modalComponentes[$index])) return;

        $comp = $this->modalComponentes[$index];

        // Quitar de kit_componentes
        DB::table('kit_componentes')
            ->where('producto_kit_id', $this->modalKitId)
            ->where('producto_componente_id', $comp['producto_id'])
            ->delete();

        // Quitar del array
        array_splice($this->modalComponentes, $index, 1);
    }

    // ─── Eliminar kit ──────────────────────────────────────────
    public function eliminarKit(int $kitId)
    {
        $kit = Producto::find($kitId);
        if (!$kit) return;

        // Quitar de cantidades
        unset($this->cantidades[$kitId]);

        // Eliminar componentes del kit
        DB::table('kit_componentes')->where('producto_kit_id', $kitId)->delete();

        // Desactivar (no eliminar físicamente)
        $kit->update(['activo' => false]);

        $this->dispatch('swal', tipo: 'success', titulo: 'Eliminado', mensaje: "{$kit->nombre} desactivado.");
    }

    // ═══════════════════════════════════════════════════════════
    // FORMULARIO NUEVO KIT (en pantalla principal)
    // ═══════════════════════════════════════════════════════════

    public function toggleFormKit()
    {
        $this->mostrandoFormKit = !$this->mostrandoFormKit;
        $this->nuevoKitNombre = '';
        $this->nuevoKitGeneracion = '';
        $this->resetValidation();
    }

    public function registrarKitNuevo()
    {
        $this->resetValidation();

        if (empty(trim($this->nuevoKitNombre))) {
            $this->dispatch('swal', tipo: 'error', titulo: 'Error', mensaje: 'El nombre del kit es obligatorio.');
            return;
        }

        if (empty(trim($this->nuevoKitGeneracion))) {
            $this->dispatch('swal', tipo: 'error', titulo: 'Error', mensaje: 'La generación es obligatoria.');
            return;
        }

        if (Producto::where('nombre', 'LIKE', trim($this->nuevoKitNombre))->exists()) {
            $this->dispatch('swal', tipo: 'error', titulo: 'Duplicado', mensaje: 'Ya existe un producto con ese nombre.');
            return;
        }

        $categoria = CategoriaAlmacen::firstOrCreate(
            ['nombre' => 'Kits'],
            ['es_serializado' => false, 'es_kit' => true]
        );

        $producto = Producto::create([
            'categoria_id' => $categoria->id,
            'nombre'       => trim($this->nuevoKitNombre),
            'atributos'    => ['generacion' => trim($this->nuevoKitGeneracion)],
            'activo'       => true,
        ]);

        // Agregar a cantidades para que aparezca en la lista
        $this->cantidades[$producto->id] = 0;

        $this->mostrandoFormKit = false;
        $this->nuevoKitNombre = '';
        $this->nuevoKitGeneracion = '';
        $this->dispatch('swal', tipo: 'success', titulo: '¡Listo!', mensaje: "{$producto->nombre} registrado.");
    }

    // ═══════════════════════════════════════════════════════════
    // EDITAR KIT
    // ═══════════════════════════════════════════════════════════

    public function abrirEditarKit(int $kitId)
    {
        $kit = Producto::find($kitId);
        if (!$kit) return;

        $this->editarKitId = $kitId;
        $this->editarKitNombre = $kit->nombre;
        $this->editarKitGeneracion = $kit->atributos['generacion'] ?? '';
        $this->modalEditarKit = true;
    }

    public function cerrarEditarKit()
    {
        $this->modalEditarKit = false;
        $this->editarKitId = 0;
        $this->editarKitNombre = '';
        $this->editarKitGeneracion = '';
    }

    public function guardarEditarKit()
    {
        $this->resetValidation();

        if (empty(trim($this->editarKitNombre))) {
            $this->dispatch('swal', tipo: 'error', titulo: 'Error', mensaje: 'El nombre es obligatorio.');
            return;
        }

        $kit = Producto::find($this->editarKitId);
        if (!$kit) return;

        // Verificar nombre duplicado (excluyendo el actual)
        if (Producto::where('nombre', 'LIKE', trim($this->editarKitNombre))->where('id', '!=', $kit->id)->exists()) {
            $this->dispatch('swal', tipo: 'error', titulo: 'Duplicado', mensaje: 'Ya existe un producto con ese nombre.');
            return;
        }

        $kit->update([
            'nombre'    => trim($this->editarKitNombre),
            'atributos' => array_merge($kit->atributos ?? [], ['generacion' => trim($this->editarKitGeneracion)]),
        ]);

        $this->modalEditarKit = false;
        $this->dispatch('swal', tipo: 'success', titulo: '¡Listo!', mensaje: 'Kit actualizado.');
    }

    // ═══════════════════════════════════════════════════════════
    // GUARDAR RECEPCIÓN
    // ═══════════════════════════════════════════════════════════

    public function guardar()
    {
        if (empty($this->proveedorId)) {
            $this->dispatch('swal', tipo: 'warning', titulo: 'Atención', mensaje: 'Seleccioná un proveedor.');
            return;
        }

        $kitsARecibir = collect($this->cantidades)
            ->filter(fn ($cant) => $cant > 0)
            ->toArray();

        if (empty($kitsARecibir)) {
            $this->dispatch('swal', tipo: 'warning', titulo: 'Atención', mensaje: 'Seleccioná al menos un kit.');
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

    // ═══════════════════════════════════════════════════════════
    // CONFIRMAR MODAL
    // ═══════════════════════════════════════════════════════════

    public function confirmarModal()
    {
        if (empty($this->modalComponentes)) {
            $this->dispatch('swal', tipo: 'warning', titulo: 'Atención', mensaje: 'No hay componentes en este kit.');
            return;
        }

        $seriesIngresadas = [];
        foreach ($this->modalComponentes as $c) {
            if (!$c['es_serializado']) continue;

            $serie = trim($c['serie']);
            if (empty($serie)) {
                $this->dispatch('swal', tipo: 'error', titulo: 'Serie faltante', mensaje: "Registrá la serie de: {$c['nombre']}");
                return;
            }
            $serieUpper = strtoupper($serie);
            if (in_array($serieUpper, $seriesIngresadas)) {
                $this->dispatch('swal', tipo: 'error', titulo: 'Serie duplicado', mensaje: "Duplicado: \"{$serie}\"");
                return;
            }
            $seriesIngresadas[] = $serieUpper;
        }

        $this->guardarRecetaKit();
        $this->crearKitsConComponentes();

        $this->cerrarModal();
        $this->colaIndex++;

        if ($this->colaIndex < count($this->colaKits)) {
            $siguiente = $this->colaKits[$this->colaIndex];
            $this->abrirModal($siguiente['producto_id'], $siguiente['cantidad']);
        } else {
            $this->dispatch('swal', tipo: 'success', titulo: '¡Recepción registrada!', mensaje: count($this->colaKits) . ' tipo(s) de kit(s) recibido(s).');
            $this->redirect(route('almacen.recepciones.listado'));
        }
    }

    private function guardarRecetaKit(): void
    {
        DB::table('kit_componentes')->where('producto_kit_id', $this->modalKitId)->delete();
        foreach ($this->modalComponentes as $c) {
            DB::table('kit_componentes')->insert([
                'producto_kit_id'        => $this->modalKitId,
                'producto_componente_id' => $c['producto_id'],
                'cantidad_esperada'      => $c['es_serializado'] ? 1 : $c['cantidad'],
                'created_at'             => now(),
                'updated_at'             => now(),
            ]);
        }
    }

    private function crearKitsConComponentes(): void
    {
        $sedeId = $this->sedeId;
        $proveedorNombre = $this->proveedorId;

        DB::transaction(function () use ($sedeId, $proveedorNombre) {
            $kit = Producto::find($this->modalKitId);
            if (!$kit) return;

            for ($i = 0; $i < $this->modalKitCantidad; $i++) {
                $kitItem = ItemSerializado::create([
                    'producto_id' => $kit->id,
                    'serie'       => null,
                    'atributos'   => ['proveedor' => $proveedorNombre, 'recepcion_fecha' => now()->toDateString()],
                    'estado'      => 'en_stock',
                    'sede_id'     => $sedeId,
                ]);

                foreach ($this->modalComponentes as $c) {
                    $producto = Producto::find($c['producto_id']);
                    if (!$producto) continue;

                    if ($c['es_serializado']) {
                        ItemSerializado::create([
                            'producto_id'  => $producto->id,
                            'kit_padre_id' => $kitItem->id,
                            'serie'        => strtoupper(trim($c['serie'])),
                            'atributos'    => [
                                'agregado_a_kit'       => true,
                                'fecha'                => now()->toDateString(),
                                'serie_registrada_por' => Auth::id(),
                                'serie_registrada_en'  => now()->toDateTimeString(),
                            ],
                            'estado'  => 'en_stock',
                            'sede_id' => $sedeId,
                        ]);
                        MovimientoStock::registrar($producto, 'entrada', 1, null, Auth::id(), "Componente serializado kit #{$kitItem->id}", $sedeId);
                    } else {
                        $cant = $c['cantidad'];
                        for ($j = 0; $j < $cant; $j++) {
                            ItemSerializado::create([
                                'producto_id'  => $producto->id,
                                'kit_padre_id' => $kitItem->id,
                                'serie'        => 'CANT-' . strtoupper(uniqid()),
                                'atributos'    => ['tipo' => 'cantidad', 'agregado_a_kit' => true, 'fecha' => now()->toDateString()],
                                'estado'  => 'en_stock',
                                'sede_id' => $sedeId,
                            ]);
                        }
                        MovimientoStock::registrar($producto, 'entrada', $cant, null, Auth::id(), "Componente por cantidad kit #{$kitItem->id}", $sedeId);
                    }
                }
            }

            MovimientoStock::registrar($kit, 'entrada', $this->modalKitCantidad, null, Auth::id(), "Recepción de proveedor: {$proveedorNombre}", $sedeId);
        });
    }

    public function render()
    {
        return view('livewire.almacen.recepciones.crear');
    }
}
