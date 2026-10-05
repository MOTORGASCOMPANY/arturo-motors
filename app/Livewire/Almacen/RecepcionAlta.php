<?php

namespace App\Livewire\Almacen;

use App\Livewire\Almacen\CatalogoKits;
use App\Livewire\Almacen\ConfirmacionKit;
use App\Livewire\Almacen\ModalKit;
use App\Livewire\Almacen\ProductosSerializados;
use App\Livewire\Almacen\RecepcionPorCantidad;
use App\Models\CategoriaAlmacen;
use App\Models\ItemSerializado;
use App\Models\Producto;
use App\Models\Sede;
use App\Models\KitComponente;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class RecepcionAlta extends Component
{
    use CatalogoKits;
    use ConfirmacionKit;
    use ModalKit;
    use ProductosSerializados;
    use RecepcionPorCantidad;

    public ?int $sedeId = null;
    public string $notas = '';

    /** Contador compartido: las claves son kits y productos serializados a la vez. */
    public array $cantidades = [];

    public string $seccion = 'elegir';
    public string $subSeccionProductos = '';

    public static array $camposCompartidos = ['produce'];

    public function incrementarCantidad(int $productoId): void
    {
        $this->cantidades[$productoId] = ($this->cantidades[$productoId] ?? 0) + 1;
    }

    public function decrementarCantidad(int $productoId): void
    {
        $actual = $this->cantidades[$productoId] ?? 0;
        $this->cantidades[$productoId] = max(0, $actual - 1);
    }

    private function avisarError(\Throwable $e, string $evento = 'swal-init', string $mensajeAmigable = 'Ocurrió un error inesperado. Intentá nuevamente.'): void
    {
        report($e);
        $this->dispatch($evento, tipo: 'error', titulo: 'Error', mensaje: $mensajeAmigable);
    }

    public function elegirSeccion(string $seccion): void
    {
        $this->seccion = $seccion;
    }

    public function volverAEleccion(): void
    {
        $this->seccion = 'elegir';
    }

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

    public function registrarComponenteNuevo(): void
    {
        $this->resetValidation();

        if (!$this->modalKitId) {
            $this->dispatch('swal-kit', tipo: 'error', titulo: 'Error', mensaje: 'No hay kit seleccionado.');
            return;
        }

        try {
            $categoria = null;
            $nombre = trim($this->nuevoNombre);

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

                $nombre = $categoria->nombre;

                if (Producto::existeConNombre($nombre)) {
                    $this->dispatch('swal-kit', tipo: 'error', titulo: 'Duplicado', mensaje: 'Ya existe un producto con ese nombre.');
                    return;
                }

                if (empty(trim($this->nuevoSerie))) {
                    $this->dispatch('swal-kit', tipo: 'error', titulo: 'Error', mensaje: 'Ingresá la serie del componente.');
                    return;
                }

                $serieUpper = strtoupper(trim($this->nuevoSerie));

                if (ItemSerializado::existeSerie($serieUpper)) {
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
                if (empty($nombre)) {
                    $this->dispatch('swal-kit', tipo: 'error', titulo: 'Error', mensaje: 'El nombre es obligatorio.');
                    return;
                }

                if (Producto::existeConNombre($nombre)) {
                    $this->dispatch('swal-kit', tipo: 'error', titulo: 'Duplicado', mensaje: 'Ya existe un producto con ese nombre.');
                    return;
                }

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
                'nombre' => $nombre,
                'activo' => true,
            ]);

            KitComponente::agregarComponente(
                $this->modalKitId,
                $producto->id,
                $this->nuevoTipo === 'serializado' ? 1 : $this->nuevaCantidad
            );

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

                if (!empty($this->nuevoSerie)) {
                    $entry['unidades'][0]['serie'] = strtoupper(trim($this->nuevoSerie));
                }
            } else {
                $entry['serie'] = '';
            }

            $this->modalComponentes[] = $entry;

            $this->nuevoNombre = '';
            $this->nuevoTipo = 'serializado';
            $this->nuevoCategoriaId = null;
            $this->nuevoSerie = '';
            $this->nuevaCantidad = 1;

            $this->dispatch('swal-kit', tipo: 'success', titulo: '¡Listo!', mensaje: 'Componente registrado y agregado al kit.');
        } catch (\Throwable $e) {
            $this->avisarError($e, 'swal-kit', 'No se pudo registrar el componente.');
        }
    }

    public function mount(): void
    {
        $this->sedeId = Sede::primeraActivaId();

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
        return Producto::kitsActivos();
    }

    public function getProductosSerializadosProperty()
    {
        return Producto::serializadosActivos();
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

    public function render()
    {
        return view('livewire.almacen.recepcion-alta');
    }
}
