<?php

namespace App\Livewire\RRHH;

use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\Planilla;
use App\Models\PlanillaArchivo;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\On;

class PlanillaArchivos extends Component
{
    use WithFileUploads;

    public $open = false;
    public $planilla;
    public $archivo;
    public $tipo = 'boleta';

    #[On('abrir-modal-archivos')]
    public function loadPlanilla($id)
    {
        $this->planilla = Planilla::with(['archivos', 'contrato.user'])->findOrFail($id);
        $this->open = true;
        $this->reset(['archivo']);
        $this->resetErrorBag();
    }

    public function save()
    {
        if (!$this->archivo) {
            $this->addError('archivo', 'Debes seleccionar o arrastrar un archivo.');
            return;
        }

        $this->validate([
            'archivo' => 'file|mimes:pdf,jpg,jpeg,png|max:2048',
        ]);

        // 1. Obtener datos para el nombre personalizado
        $nombreOriginal = $this->archivo->getClientOriginalName();
        $extension = $this->archivo->getClientOriginalExtension();
        $timestamp = now()->format('Ymd_His');

        // 2. Construir el nombre del archivo (Ej: P15_boleta_20260223_1605.pdf)
        $nombreCustom = "P{$this->planilla->id}_{$timestamp}.{$extension}";

        // 3. Guardar con el nuevo nombre usando storeAs
        $ruta = $this->archivo->storeAs('planillas', $nombreCustom, 'public');

        /*$nombreOriginal = $this->archivo->getClientOriginalName();
        $extension = $this->archivo->getClientOriginalExtension();
        $ruta = $this->archivo->store('planillas', 'public');*/

        PlanillaArchivo::create([
            'planilla_id' => $this->planilla->id,
            'tipo' => $this->tipo,
            'nombre' => $nombreOriginal,
            'ruta' => $ruta,
            'extension' => $extension,
        ]);

        $this->reset(['open','tipo','archivo']);
        $this->planilla->load('archivos');
        $this->dispatch('minAlert', titulo: 'Éxito', mensaje: 'Archivo guardado correctamente', icono: 'success');
    }

    public function eliminarArchivo($id)
    {
        $file = PlanillaArchivo::findOrFail($id);
        Storage::disk('public')->delete($file->ruta);
        $file->delete();
        $this->planilla->load('archivos');
    }

    public function descargarArchivo($id)
    {
        $file = PlanillaArchivo::findOrFail($id);
        return Storage::disk('public')->download($file->ruta, $file->nombre);
    }

    public function cambiarTipo($id, $nuevoTipo)
    {
        $file = PlanillaArchivo::findOrFail($id);
        $file->update(['tipo' => $nuevoTipo]);
        $this->planilla->load('archivos');
    }

    public function render()
    {
        return view('livewire.r-r-h-h.planilla-archivos');
    }
}
