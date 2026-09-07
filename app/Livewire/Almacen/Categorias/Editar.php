<?php

namespace App\Livewire\Almacen\Categorias;

use App\Models\CategoriaAlmacen;
use Livewire\Attributes\On;
use Livewire\Component;

class Editar extends Component
{
    public bool $mostrarModal = false;
    public ?CategoriaAlmacen $categoria = null;

    public string $nombre = '';
    public bool $esSerializado = false;
    public bool $esKit = false;
    public string $atributosTexto = '';
    public bool $tieneProductos = false;

    #[On('abrir-modal-editar-categoria')]
    public function abrir(int $categoriaId)
    {
        $this->categoria = CategoriaAlmacen::withCount('productos')->findOrFail($categoriaId);

        $this->nombre = $this->categoria->nombre;
        $this->esSerializado = $this->categoria->es_serializado;
        $this->esKit = $this->categoria->es_kit;
        $this->atributosTexto = $this->categoria->esquema_atributos
            ? implode(', ', $this->categoria->esquema_atributos) : '';
        $this->tieneProductos = $this->categoria->productos_count > 0;

        $this->resetErrorBag();
        $this->mostrarModal = true;
    }

    public function cerrar()
    {
        $this->mostrarModal = false;
    }

    public function guardar()
    {
        $this->validate([
            'nombre' => 'required|string|max:100|unique:categorias_almacen,nombre,' . $this->categoria->id,
        ]);

        try {
            $datos = [
                'nombre' => $this->nombre,
                'esquema_atributos' => $this->atributosTexto
                    ? array_map('trim', explode(',', $this->atributosTexto)) : null,
            ];

            if (!$this->tieneProductos) {
                $datos['es_serializado'] = $this->esSerializado;
                $datos['es_kit'] = $this->esKit;
            }

            $this->categoria->update($datos);
        } catch (\Throwable $e) {
            report($e);
            $this->dispatch('minAlert', titulo: 'Error', mensaje: 'No se pudo actualizar la categoría.', icono: 'error');
            return;
        }

        $this->mostrarModal = false;
        $this->dispatch('categoria-creada');
        $this->dispatch('minToast', titulo: '¡Listo!', mensaje: 'Categoría actualizada.', icono: 'success');
    }

    public function render()
    {
        return view('livewire.almacen.categorias.editar');
    }
}