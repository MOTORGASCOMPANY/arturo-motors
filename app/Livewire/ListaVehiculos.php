<?php

namespace App\Livewire;

use App\Models\Cliente;
use App\Models\Vehiculo;
use Livewire\Component;
use Livewire\WithPagination;

class ListaVehiculos extends Component
{
    use WithPagination;

    public $sort;

    public $order;

    public $cant;

    public $search;

    public $direction;

    // Propiedades para el modal de edición
    public $open = false;

    public $editingVehiculo;

    public $placa;

    public $marca;

    public $modelo;

    public $anio;

    public $combustible;

    public $serie;

    public $color;

    // Propiedades para el modal de creación
    public $openCreate = false;

    public $cliente_id;

    public $clientes = [];

    public $createPlaca;

    public $createMarca;

    public $createModelo;

    public $createAnio;

    public $createCombustible;

    public $createSerie;

    public $createColor;

    public function mount()
    {
        $this->direction = 'desc';
        $this->sort = 'id';
        $this->cant = 10;
        $this->open = false;
        $this->openCreate = false;
        $this->clientes = Cliente::all();
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

    // Método para cargar los datos del vehículo y abrir el modal de edición
    public function edit(Vehiculo $vehiculo)
    {
        $this->editingVehiculo = $vehiculo;
        $this->placa = $vehiculo->placa;
        $this->marca = $vehiculo->marca;
        $this->modelo = $vehiculo->modelo;
        $this->anio = $vehiculo->anio;
        $this->combustible = $vehiculo->combustible;
        $this->serie = $vehiculo->serie;
        $this->color = $vehiculo->color;
        $this->open = true;
    }

    // Método para actualizar los datos del vehículo
    public function updateVehiculo()
    {
        $this->validate([
            'placa' => 'required|max:20',
            'marca' => 'required|max:50',
            'modelo' => 'required|max:50',
            'anio' => 'required|integer|min:1900|max:2099',
            'combustible' => 'required|max:20',
            'serie' => 'required|max:50',
            'color' => 'required|max:50',
        ]);

        $this->editingVehiculo->update([
            'placa' => $this->placa,
            'marca' => $this->marca,
            'modelo' => $this->modelo,
            'anio' => $this->anio,
            'combustible' => $this->combustible,
            'serie' => $this->serie,
            'color' => $this->color,
        ]);

        $this->reset(['open', 'placa', 'marca', 'modelo', 'anio', 'combustible', 'serie', 'color']);
        $this->dispatch('minAlert', titulo: '¡BUEN TRABAJO!', mensaje: 'Vehiculo actualizado correctamente', icono: 'success');
    }

    // Método para abrir el modal de creación
    public function openCreateModal()
    {
        $this->resetCreateForm();
        $this->openCreate = true;
    }

    // Método para guardar un nuevo vehículo
    public function storeVehiculo()
    {
        $this->validate([
            'cliente_id' => 'required|exists:clientes,id',
            'createPlaca' => 'required|max:20|unique:vehiculos,placa',
            'createMarca' => 'required|max:50',
            'createModelo' => 'required|max:50',
            'createAnio' => 'required|integer|min:1900|max:2099',
            'createCombustible' => 'required|max:20',
            'createSerie' => 'required|max:50',
            'createColor' => 'required|max:50',
        ]);

        Vehiculo::create([
            'cliente_id' => $this->cliente_id,
            'placa' => $this->createPlaca,
            'marca' => $this->createMarca,
            'modelo' => $this->createModelo,
            'anio' => $this->createAnio,
            'combustible' => $this->createCombustible,
            'serie' => $this->createSerie,
            'color' => $this->createColor,
        ]);

        $this->resetCreateForm();
        $this->openCreate = false;
        $this->dispatch('minAlert', titulo: '¡BUEN TRABAJO!', mensaje: 'Vehiculo creado correctamente', icono: 'success');
    }

    // Método para limpiar el formulario de creación
    public function resetCreateForm()
    {
        $this->reset(['cliente_id', 'createPlaca', 'createMarca', 'createModelo', 'createAnio', 'createCombustible', 'createSerie', 'createColor']);
    }

    public function render()
    {
        $vehiculos = Vehiculo::with(['cliente'])
            ->buscar($this->search)
            ->ordenar($this->sort, $this->direction)
            ->paginate($this->cant);

        return view('livewire.lista-vehiculos', compact('vehiculos'));
    }
}
