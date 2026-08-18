<?php

namespace App\Livewire\Conversiones;

use App\Models\ServiceOrder;
use App\Support\ChecklistEvaluacion;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Evaluar extends Component
{
    public ServiceOrder $orden;
    public array $checklist = [];
    public string $observaciones = '';

    /**
     * cambia la propiedad $gruposChecklist para que se llene con ChecklistEvaluacion::grupos() en el mount(), en vez de tener el array repetido ahí 
     **/
    // Catálogo de ítems a revisar, se poblará dinámicamente
    public array $gruposChecklist = [];

    // Catálogo de ítems a revisar, agrupados para la vista
    /*public array $gruposChecklist = [
        'Documentos' => [
            'tarjeta_propiedad' => 'Tarjeta de propiedad',
            'soat' => 'SOAT',
        ],
        'Exterior' => [
            'espejos' => 'Espejos',
            'antena' => 'Antena',
            'plumillas' => 'Plumillas',
            'emblemas' => 'Emblemas',
            'tapa_combustible' => 'Tapa de combustible',
            'tapa_aceite' => 'Tapa de aceite',
            'tapa_radiador' => 'Tapa de radiador',
            //'barita_capot' => 'Barita de capot',
            'varilla_capot' => 'Varilla de capot',
            'espejo_anterior' => 'Espejo interior',
        ],
        'Interior' => [
            'llave_contacto' => 'Llave de contacto',
            'vasos' => 'Vasos / portavasos',
            'tapasoles' => 'Tapasoles',
            'radio' => 'Radio',
            'reproductor_cd' => 'Reproductor CD',
            'parlantes' => 'Parlantes',
            'cenicero' => 'Cenicero',
            'encendedor' => 'Encendedor',
            'pisos' => 'Pisos',
            'fundas_forros' => 'Fundas / forros',
            'cinturones' => 'Cinturones de seguridad',
            'claxon' => 'Claxon',
            'bateria' => 'Batería',
        ],
        'Seguridad y herramientas' => [
            'llanta_repuesto' => 'Llanta de repuesto',
            'gata_palanca' => 'Gata y palanca',
            'llave_ruedas' => 'Llave de ruedas',
            'triangulo' => 'Triángulo de seguridad',
            'extintor' => 'Extintor',
            'linterna' => 'Linterna',
            'herramientas' => 'Kit de herramientas',
        ],
    ];*/    

    public function mount(int $ordenId)
    {
        $this->orden = ServiceOrder::with(['cliente', 'vehiculo', 'service'])->findOrFail($ordenId);

        // Seguridad: solo el técnico asignado puede evaluar, y solo en el estado correcto
        abort_unless($this->orden->tecnico_id === Auth::id(), 403, 'Esta orden no está asignada a ti.');
        abort_unless($this->orden->estado === 'en_evaluacion', 403, 'Esta orden no está en etapa de evaluación.');

        // Se obtiene la lista de grupos desde la clase Helper/Support
        $this->gruposChecklist = ChecklistEvaluacion::grupos();

        // Inicializa el checklist (si ya había datos guardados de un intento previo, los recupera)
        $guardado = $this->orden->checklist_evaluacion ?? [];
        foreach ($this->gruposChecklist as $items) {
            foreach ($items as $clave => $label) {
                $this->checklist[$clave] = $guardado[$clave] ?? false;
            }
        }
    }

    public function marcarTodo()
    {
        foreach ($this->checklist as $clave => $val) {
            $this->checklist[$clave] = true;
        }
    }

    public function desmarcarTodo()
    {
        foreach ($this->checklist as $clave => $val) {
            $this->checklist[$clave] = false;
        }
    }

    public function guardarEvaluacion(bool $aprobado)
    {
        if (!$aprobado) {
            $this->validate([
                'observaciones' => 'required|string|min:5',
            ], [
                'observaciones.required' => 'Indica el motivo del rechazo.',
                'observaciones.min' => 'El motivo debe tener al menos 5 caracteres.',
            ]);
        }

        try {
            $this->orden->update([
                'checklist_evaluacion' => $this->checklist,
                'evaluacion_aprobada' => $aprobado,
                'evaluacion_observaciones' => $this->observaciones ?: null,
                'evaluado_por' => Auth::id(),
                'evaluado_en' => now(),
                'estado' => $aprobado ? 'aprobado_conversion' : 'evaluacion_rechazada',
            ]);
        } catch (\Throwable $e) {
            report($e);
            $this->addError('general', 'Ocurrió un error al guardar la evaluación. Intenta de nuevo.');
            return;
        }

        // Usamos session()->flash para que el mensaje sobreviva a la redirección estándar HTTP
        session()->flash('swal', [
            'icono' => $aprobado ? 'success' : 'warning',
            'titulo' => $aprobado ? '¡APROBADO!' : 'EVALUACIÓN RECHAZADA',
            'mensaje' => $aprobado ? 'Vehículo aprobado para conversión.' : 'Evaluación registrada como no apto.',
        ]);

        //$this->redirect(route('conversiones.mis-asignadas'), navigate: true);
        $this->redirect(route('conversiones.mis-asignadas'));
    }

    public function render()
    {
        return view('livewire.conversiones.evaluar');
    }
}
