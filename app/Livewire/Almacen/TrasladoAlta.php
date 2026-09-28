<?php

namespace App\Livewire\Almacen;

use App\Livewire\Almacen\TrasladoCatalogo;
use App\Livewire\Almacen\TrasladoConfirmacion;
use App\Livewire\Almacen\TrasladoSeleccion;
use App\Models\Sede;
use Livewire\Component;

class TrasladoAlta extends Component
{
    use TrasladoCatalogo;
    use TrasladoConfirmacion;
    use TrasladoSeleccion;

    
    public ?int $sedeDestinoId = null;
    public string $observaciones = '';

    
    public array $itemsSeleccionados = [];  
    public array $cantidadSeleccionados = []; 

    
    public string $buscar = '';

    
    public ?int $productoCantidadId = null;
    public int $cantidadPieza = 1;

    
    public ?int $kitInspeccionId = null;

    
    public bool $mostrarChecklist = false;
    public array $checklistData = [];

    
    public string $tabSeleccion = 'kits'; 

    protected function sedeOrigenId(): int
    {
        return Sede::activas()->orderBy('id')->first()?->id ?? 1;
    }

    public function render()
    {
        return view('livewire.almacen.traslado-alta');
    }
}
