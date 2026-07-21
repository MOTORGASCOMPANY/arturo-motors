<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Repuesto;
use App\Models\ConversionDetalle;

class GestorRepuestos extends Component
{
    use WithPagination;

    // Propiedades del formulario
    public $nombre, $descripcion, $precio, $stock, $repuesto_id;

    // Propiedades para la búsqueda y paginación
    public $search = '';
    public $cant = 10;
    public $sort = 'nombre';
    public $direction = 'asc';

    // Propiedades de estado de la interfaz de usuario
    public $open_form = false;
    public $confirming_delete = false;
    public $repuesto_to_delete = null;

    protected $rules = [
        'nombre' => 'required|string|max:255',
        'descripcion' => 'nullable|string|max:255',
        'precio' => 'required|numeric|min:0',
        'stock' => 'required|integer|min:0',
    ];

    //Resetea las propiedades del formulario.
    public function resetForm()
    {
        $this->reset(['nombre', 'descripcion', 'precio', 'stock', 'repuesto_id', 'open_form']);
    }

    //Guarda o actualiza un repuesto.
    public function save()
    {
        $this->validate();

        $is_new = is_null($this->repuesto_id);

        Repuesto::updateOrCreate(
            ['id' => $this->repuesto_id],
            [
                'nombre' => $this->nombre,
                'descripcion' => $this->descripcion,
                'precio' => $this->precio,
                'stock' => $this->stock,
            ]
        );

        $this->resetForm();
        //$this->dispatch('saved');
        // Determina el mensaje de éxito basándose en si se ha creado o actualizado.
        $message = $is_new ? "¡El repuesto se ha creado con éxito!" : "¡El repuesto se ha actualizado con éxito!";        
        $this->dispatch('minAlert', titulo: "¡BUEN TRABAJO!", mensaje: $message, icono: "success");
    }

    //Edita un repuesto existente.
    public function edit(Repuesto $repuesto)
    {
        $this->repuesto_id = $repuesto->id;
        $this->nombre = $repuesto->nombre;
        $this->descripcion = $repuesto->descripcion;
        $this->precio = $repuesto->precio;
        $this->stock = $repuesto->stock;

        $this->open_form = true;
    }

    //Confirma la eliminación de un repuesto.
    public function confirmDelete(Repuesto $repuesto)
    {
        $this->confirming_delete = true;
        $this->repuesto_to_delete = $repuesto;
    }
    //Elimina el repuesto seleccionado.
    public function delete()
    {
        if ($this->repuesto_to_delete) {
            $this->repuesto_to_delete->delete();
            $this->confirming_delete = false;
            $this->repuesto_to_delete = null;
            //$this->dispatch('deleted');
            $this->dispatch('minAlert', titulo: "¡ELIMINADO!", mensaje: "Se ha eliminado con éxito.", icono: "success");
        }
    }

    //Renderiza la vista del componente.
    public function render()
    {
        // Consulta los repuestos con filtros de búsqueda y paginación
        $repuestos = Repuesto::where('nombre', 'like', '%' . $this->search . '%')
            ->orWhere('descripcion', 'like', '%' . $this->search . '%')
            ->orderBy($this->sort, $this->direction)
            ->paginate($this->cant);

        // Retorna la vista con los datos
        return view('livewire.gestor-repuestos', [
            'repuestos' => $repuestos,
        ]);
    }
}
