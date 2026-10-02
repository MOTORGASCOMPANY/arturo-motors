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

    public function sedeOrigenId(): int
    {
        return Sede::activas()->orderBy('id')->first()?->id ?? 1;
    }

    public function getSedeOrigenProperty()
    {
        return Sede::find($this->sedeOrigenId());
    }

    /** Paso activo del stepper: 1 configuración, 2 selección, 3 resumen, 4 envío. */
    public function getPasoActualProperty(): int
    {
        if ($this->mostrarChecklist) {
            return 4;
        }

        if (! $this->sedeDestinoId) {
            return 1;
        }

        return $this->resumenVacio ? 2 : 3;
    }

    /** La única fuente del texto del CTA: evita repetir la lógica en desktop y móvil. */
    public function getAccionLabelProperty(): string
    {
        if (! $this->sedeDestinoId) {
            return 'Selecciona una sede destino';
        }

        if ($this->resumenVacio) {
            return 'Selecciona al menos un item';
        }

        return 'Confirmar traslado';
    }

    public function getAccionHabilitadaProperty(): bool
    {
        return (bool) $this->sedeDestinoId && ! $this->resumenVacio;
    }

    public function render()
    {
        return view('livewire.almacen.traslado-alta');
    }
}
