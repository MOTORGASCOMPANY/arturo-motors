<?php

namespace App\Livewire\Conversiones;

use App\Models\ServiceOrder;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Evaluar extends Component
{
    public ServiceOrder $orden;
    public array $checklist = [];
    public string $observaciones = '';

    // Catálogo de ítems a revisar, agrupados para la vista
    public array $gruposChecklist = [
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
            'barita_capot' => 'Barita de capot',
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
    ];

    public function mount(int $ordenId)
    {
        $this->orden = ServiceOrder::with(['cliente', 'vehiculo', 'service'])->findOrFail($ordenId);

        // Seguridad: solo el técnico asignado puede evaluar, y solo en el estado correcto
        abort_unless($this->orden->tecnico_id === Auth::id(), 403, 'Esta orden no está asignada a ti.');
        abort_unless($this->orden->estado === 'en_evaluacion', 403, 'Esta orden no está en etapa de evaluación.');

        // Inicializa el checklist (si ya había datos guardados de un intento previo, los recupera)
        $guardado = $this->orden->checklist_evaluacion ?? [];
        foreach ($this->gruposChecklist as $items) {
            foreach ($items as $clave => $label) {
                $this->checklist[$clave] = $guardado[$clave] ?? false;
            }
        }
    }

    public function guardarEvaluacion(bool $aprobado)
    {
        if (!$aprobado) {
            $this->validate([
                'observaciones' => 'required|string|min:5',
            ], [
                'observaciones.required' => 'Indica el motivo del rechazo.',
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

        session()->flash('mensaje', $aprobado
            ? 'Vehículo aprobado para conversión.'
            : 'Evaluación registrada como no apto.');

        $this->redirect(route('conversiones.mis-asignadas'), navigate: true);
    }

    public function render()
    {
        return view('livewire.conversiones.evaluar');
    }
}
