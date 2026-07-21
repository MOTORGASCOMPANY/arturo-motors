<?php

namespace App\Livewire;

use App\Models\Vehiculo;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\On;

class ListaVehiculos extends Component
{
    use WithPagination;
    public $sort, $order, $cant, $search, $direction;
    // Propiedades para el modal y el formulario de edición
    public $open = false;
    public $editingVehiculo, $placa, $marca, $modelo, $anio, $combustible, $serie, $color;

    public function mount()
    {
        $this->direction = 'desc';
        $this->sort = 'id';
        $this->cant = 10;
        $this->open = false;
    }

    public function order($sort)
    {
        if ($this->sort === $sort) {
            $this->direction = $this->direction === 'desc' ? 'asc' : 'desc';
        } else {
            $this->sort = $sort;
            $this->direction = 'asc';
        }
        $this->resetPage(); // Resetear paginación al cambiar el orden
    }

    // Método para cargar los datos del vehículo y abrir el modal
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
        //$this->dispatch('updated-vehiculo'); // Emite un evento si es necesario
        $this->dispatch('minAlert', titulo: "¡BUEN TRABAJO!", mensaje: "Vehiculo actualizado correctamente", icono: "success");
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
