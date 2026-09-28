<?php

namespace App\Livewire\Almacen;

use App\Models\CategoriaAlmacen;
use App\Models\MovimientoStock;
use App\Models\Producto;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

trait RecepcionPorCantidad
{
    public array $cantidadesCantidad = [];
    public string $buscarCantidad = '';
    public bool $mostrandoFormNuevoCantidad = false;
    public string $nuevoCantidadNombre = '';
    public ?int $nuevoCantidadCategoriaId = null;
    public int $nuevoCantidadStockInicial = 1;

    public function incrementarCantidadCantidad(int $productoId): void
    {
        $this->cantidadesCantidad[$productoId] = ($this->cantidadesCantidad[$productoId] ?? 0) + 1;
    }

    public function decrementarCantidadCantidad(int $productoId): void
    {
        $actual = $this->cantidadesCantidad[$productoId] ?? 0;
        $this->cantidadesCantidad[$productoId] = max(0, $actual - 1);
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

            $this->nuevoCantidadNombre = '';
            $this->nuevoCantidadStockInicial = 1;

            $this->dispatch('swal-init', tipo: 'success', titulo: '¡Listo!', mensaje: "{$producto->nombre} registrado con stock inicial.");
        } catch (\Throwable $e) {
            $this->avisarError($e, 'swal-init', 'No se pudo registrar el producto.');
        }
    }

    public function getTotalCantidadProperty(): int
    {
        return collect($this->cantidadesCantidad)->filter(fn ($c) => $c > 0)->sum();
    }

    public function guardarCantidad(): void
    {
        $conCantidad = collect($this->cantidadesCantidad)->filter(fn ($c) => $c > 0)->toArray();

        if (empty($conCantidad)) {
            $this->dispatch('swal-init', tipo: 'warning', titulo: 'Atención', mensaje: 'Poné cantidad en al menos un producto.');
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

                    MovimientoStock::registrar($producto, 'entrada', $cantidad, null, $usuarioId, 'Entrada por recepción', $sedeId);
                    $totalRegistrado += $cantidad;
                }
            });

            $this->dispatch('swal-init', tipo: 'success', titulo: '¡Recepción registrada!', mensaje: "{$totalRegistrado} unidad(es) recibida(s).");
            $this->redirect(route('almacen.recepciones.listado'));
        } catch (\Throwable $e) {
            $this->avisarError($e, 'swal-init', 'No se pudo registrar la recepción.');
        }
    }
}
