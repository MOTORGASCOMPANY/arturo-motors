<?php

namespace App\Livewire\Almacen;

use App\Models\CategoriaAlmacen;
use App\Models\Producto;
use App\Models\KitComponente;
use Illuminate\Support\Facades\DB;

/**
 * Estado y operaciones del paso "componentes": abrir/cerrar el kit en recepción,
 * alta rápida de componentes y carga de los existentes.
 */
trait ModalKit
{
    public bool $modalKitAbierto = false;
    public int $modalKitId = 0;
    public string $modalKitNombre = '';
    public int $modalKitCantidad = 0;
    public array $modalComponentes = [];
    public string $kitProduce = '';
    public bool $mostrandoFormNuevo = false;

    public string $nuevoNombre = '';
    public string $nuevoTipo = 'serializado';
    public ?int $nuevoCategoriaId = null;
    public string $nuevoSerie = '';
    public int $nuevaCantidad = 1;

    public ?int $productoExistenteId = null;

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

            $cantidadesGuardadas = KitComponente::cantidadesDe($kitId);

            if (!empty($cantidadesGuardadas)) {
                $productos = Producto::with('categoria')
                    ->whereIn('id', array_keys($cantidadesGuardadas))
                    ->get();

                foreach ($productos as $producto) {
                    $esSerializado = $producto->categoria->es_serializado ?? false;
                    $esquema = $producto->categoria->esquema_atributos ?? ['serie'];
                    $camposEsquema = is_string($esquema) ? (json_decode($esquema, true) ?? ['serie']) : $esquema;

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
        return CategoriaAlmacen::serializadasConEsquema();
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

            KitComponente::agregarComponente($this->modalKitId, $producto->id, 1);

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
            $this->avisarError($e, 'swal-kit', 'No se pudo agregar el componente existente.');
        }
    }

    public function quitarComponenteModal(int $index): void
    {
        if (!isset($this->modalComponentes[$index])) return;

        $componente = $this->modalComponentes[$index];

        try {
            KitComponente::quitarComponente($this->modalKitId, $componente['producto_id']);

            array_splice($this->modalComponentes, $index, 1);
        } catch (\Throwable $e) {
            $this->avisarError($e, 'swal-kit', 'No se pudo quitar el componente.');
        }
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
}
