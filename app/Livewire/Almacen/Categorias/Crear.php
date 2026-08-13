<?php

namespace App\Livewire\Almacen\Categorias;

use App\Models\CategoriaAlmacen;
use Livewire\Attributes\On;
use Livewire\Component;

class Crear extends Component
{
    public bool $mostrarModal = false;

    public string $nombre = '';
    public bool $esSerializado = false;
    public string $atributosTexto = '';

    #[On('abrir-modal-categoria')]
    public function abrir()
    {
        $this->reset(['nombre', 'esSerializado', 'atributosTexto']);
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
            'nombre' => 'required|string|max:100|unique:categorias_almacen,nombre',
        ]);

        try {
            CategoriaAlmacen::create([
                'nombre' => $this->nombre,
                'es_serializado' => $this->esSerializado,
                'esquema_atributos' => $this->atributosTexto
                    ? array_map('trim', explode(',', $this->atributosTexto))
                    : null,
            ]);
        } catch (\Throwable $e) {
            report($e);
            $this->dispatch('minAlert', titulo: 'Error', mensaje: 'No se pudo crear la categoría. Intenta de nuevo.', icono: 'error');
            return;
        }

        $this->mostrarModal = false;
        $this->reset(['nombre', 'esSerializado', 'atributosTexto']);

        $this->dispatch('categoria-creada');
        $this->dispatch('minToast', titulo: '¡Listo!', mensaje: 'Categoría creada correctamente.', icono: 'success');
    }
    
    public function render()
    {
        return view('livewire.almacen.categorias.crear');
    }
}
