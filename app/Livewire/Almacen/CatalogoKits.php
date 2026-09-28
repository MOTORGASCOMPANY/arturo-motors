<?php

namespace App\Livewire\Almacen;

use App\Models\CategoriaAlmacen;
use App\Models\Producto;
use Illuminate\Support\Facades\DB;

trait CatalogoKits
{
    public array $colaKits = [];
    public int $colaIndex = 0;

    public bool $mostrandoFormKit = false;
    public string $nuevoKitNombre = '';
    public string $nuevoKitGeneracion = '';

    public bool $modalEditarKit = false;
    public int $editarKitId = 0;
    public string $editarKitNombre = '';
    public string $editarKitGeneracion = '';

    public function guardar(): void
    {
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

            $this->modalEditarKit = false;
            $this->dispatch('swal-kit', tipo: 'success', titulo: '¡Listo!', mensaje: 'Kit actualizado.');
        } catch (\Throwable $e) {
            $this->avisarError($e, 'swal-kit', 'No se pudo actualizar el kit.');
        }
    }
}
