<?php

namespace App\Livewire;

use App\Models\Service;
use Livewire\Component;
use Livewire\WithPagination;

class ListaServicios extends Component
{
    use WithPagination;

    public $sort = 'id';
    public $direction = 'desc';
    public $cant = 10;
    public $search = '';

    // Modal
    public $open = false;
    public $editingService = null;
    public $nombre = '';
    public $tipo = 'simple';
    public $precio_base = 0;
    public $activo = true;

    // Crear
    public $openCreate = false;
    public $createNombre = '';
    public $createTipo = 'simple';
    public $createPrecio_base = 0;
    public $createActivo = true;

    protected $rules = [
        'nombre' => 'required|string|max:100',
        'tipo' => 'required|in:simple,conversion',
        'precio_base' => 'required|numeric|min:0',
        'activo' => 'boolean',
        'createNombre' => 'required|string|max:100',
        'createTipo' => 'required|in:simple,conversion',
        'createPrecio_base' => 'required|numeric|min:0',
        'createActivo' => 'boolean',
    ];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function order($sort)
    {
        if ($this->sort === $sort) {
            $this->direction = $this->direction === 'desc' ? 'asc' : 'desc';
        } else {
            $this->sort = $sort;
            $this->direction = 'asc';
        }
        $this->resetPage();
    }

    public function edit(Service $service)
    {
        $this->editingService = $service;
        $this->nombre = $service->nombre;
        $this->tipo = $service->tipo;
        $this->precio_base = $service->precio_base;
        $this->activo = $service->activo;
        $this->open = true;
    }

    public function updateService()
    {
        $this->validate([
            'nombre' => 'required|string|max:100',
            'tipo' => 'required|in:simple,conversion',
            'precio_base' => 'required|numeric|min:0',
        ]);

        $this->editingService->update([
            'nombre' => $this->nombre,
            'tipo' => $this->tipo,
            'precio_base' => $this->precio_base,
            'activo' => $this->activo,
        ]);

        $this->reset(['open', 'editingService', 'nombre', 'tipo', 'precio_base', 'activo']);
        $this->dispatch('minAlert', titulo: "¡BUEN TRABAJO!", mensaje: "Servicio actualizado correctamente", icono: "success");
    }

    public function openCreateModal()
    {
        $this->resetCreateForm();
        $this->openCreate = true;
    }

    public function storeService()
    {
        $this->validate([
            'createNombre' => 'required|string|max:100',
            'createTipo' => 'required|in:simple,conversion',
            'createPrecio_base' => 'required|numeric|min:0',
        ]);

        Service::create([
            'nombre' => $this->createNombre,
            'tipo' => $this->createTipo,
            'precio_base' => $this->createPrecio_base,
            'activo' => $this->createActivo,
        ]);

        $this->resetCreateForm();
        $this->openCreate = false;
        $this->dispatch('minAlert', titulo: "¡BUEN TRABAJO!", mensaje: "Servicio creado correctamente", icono: "success");
    }

    public function resetCreateForm()
    {
        $this->reset(['createNombre', 'createTipo', 'createPrecio_base', 'createActivo']);
    }

    public function render()
    {
        $servicios = Service::query()
            ->when($this->search, function ($q) {
                $q->where('nombre', 'like', "%{$this->search}%")
                  ->orWhere('tipo', 'like', "%{$this->search}%");
            })
            ->orderBy($this->sort, $this->direction)
            ->paginate($this->cant);

        return view('livewire.lista-servicios', compact('servicios'));
    }
}
