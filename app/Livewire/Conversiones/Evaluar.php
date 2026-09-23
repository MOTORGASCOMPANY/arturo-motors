<?php

namespace App\Livewire\Conversiones;

use App\Models\ServiceOrder;
use App\Support\ChecklistEvaluacion;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithFileUploads;

class Evaluar extends Component
{
    use WithFileUploads;

    public ServiceOrder $orden;
    public array $checklist = [];
    public string $observaciones = '';
    public string $fichaDanoUrl = '';

    public array $gruposChecklist = [];

    public function mount(int $ordenId)
    {
        $this->orden = ServiceOrder::with(['cliente', 'vehiculo', 'service'])->findOrFail($ordenId);

        $esAdminOJefe = Auth::user()->hasAnyRole(['Administrador del sistema', 'Jefe de Taller']);
        abort_unless($esAdminOJefe || $this->orden->tecnico_id === Auth::id(), 403, 'Esta orden no está asignada a ti.');
        abort_unless($this->orden->estado === 'en_evaluacion', 403, 'Esta orden no está en etapa de evaluación.');

        $this->gruposChecklist = ChecklistEvaluacion::grupos();

        $guardado = $this->orden->checklist_evaluacion ?? [];
        foreach ($this->gruposChecklist as $items) {
            foreach ($items as $clave => $label) {
                $this->checklist[$clave] = $guardado[$clave] ?? false;
            }
        }

        $ficha = $this->orden->ficha_dano ?? '';
        if ($ficha && !str_starts_with($ficha, '/') && !str_starts_with($ficha, 'data:')) {
            $ficha = '/' . $ficha;
        }
        $this->fichaDanoUrl = $ficha;
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

    public function setFichaUrl(string $url): void
    {
        $this->fichaDanoUrl = $url;
    }

    public function limpiarFicha(): void
    {
        $this->fichaDanoUrl = '';
    }

    public function guardarFicha(string $base64Image): bool
    {
        // Decode base64
        $data = $base64Image;
        if (str_contains($data, ',')) {
            $data = explode(',', $data, 2)[1];
        }
        $binary = base64_decode($data);

        // Validate it's a valid image
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime = finfo_buffer($finfo, $binary);
        finfo_close($finfo);

        if (!in_array($mime, ['image/webp', 'image/png', 'image/jpeg'])) {
            $this->addError('fichaDano', 'Formato de imagen no válido.');
            return false;
        }

        // Delete old ficha if exists (acepta '/storage/...' o 'storage/...')
        if ($this->orden->ficha_dano) {
            $oldRel = ltrim($this->orden->ficha_dano, '/');
            if (str_starts_with($oldRel, 'storage/')) {
                $oldRel = substr($oldRel, strlen('storage/'));
            }
            Storage::disk('public')->delete($oldRel);
        }

        // Save new file
        $filename = "fichas-dano/{$this->orden->id}-" . now()->timestamp . '.webp';
        Storage::disk('public')->put($filename, $binary);

        $this->fichaDanoUrl = '/storage/' . $filename;

        // Persistir en la orden de inmediato (no esperar a Apto/No apto)
        try {
            $this->orden->update(['ficha_dano' => $this->fichaDanoUrl]);
        } catch (\Throwable $e) {
            report($e);
            $this->addError(
                'fichaDano',
                'La imagen se guardó en disco, pero no se pudo registrar en la orden. Se reintentará al guardar la evaluación.'
            );
        }

        return true;
    }

    public function guardarEvaluacion(bool $aprobado, bool $sinFichaConfirmado = false)
    {
        $fichaVacia = empty($this->fichaDanoUrl);

        // Sin ficha y sin confirmación → error visible, nunca return silencioso
        if ($fichaVacia && !$sinFichaConfirmado) {
            $this->addError(
                'fichaDano',
                'Falta la ficha de daño. Dibuja y guarda una, o confirma continuar sin ficha.'
            );
            $this->dispatch(
                'minAlert',
                titulo: 'Sin ficha de daño',
                mensaje: 'No hay ficha guardada. Confirma continuar sin ficha o dibuja una antes de evaluar.',
                icono: 'warning'
            );
            return;
        }

        if (!$aprobado) {
            $this->validate([
                'observaciones' => 'required|string|min:5',
            ], [
                'observaciones.required' => 'Indica el motivo del rechazo.',
                'observaciones.min' => 'El motivo debe tener al menos 5 caracteres.',
            ]);
        }

        // Solo se limpia ficha_dano de la orden en el path "continuar sin ficha" confirmado
        $fichaParaGuardar = ($sinFichaConfirmado && $fichaVacia)
            ? null
            : ($this->fichaDanoUrl ?: null);

        try {
            $this->orden->update([
                'checklist_evaluacion' => $this->checklist,
                'evaluacion_aprobada' => $aprobado,
                'evaluacion_observaciones' => $this->observaciones ?: null,
                'ficha_dano' => $fichaParaGuardar,
                'evaluado_por' => Auth::id(),
                'evaluado_en' => now(),
                'estado' => $aprobado ? 'aprobado_conversion' : 'evaluacion_rechazada',
            ]);
        } catch (\Throwable $e) {
            report($e);
            $this->addError('general', 'Ocurrió un error al guardar la evaluación. Intenta de nuevo.');
            return;
        }

        session()->flash('swal', [
            'icono' => $aprobado ? 'success' : 'warning',
            'titulo' => $aprobado ? '¡APROBADO!' : 'EVALUACIÓN RECHAZADA',
            'mensaje' => $aprobado ? 'Vehículo aprobado para conversión.' : 'Evaluación registrada como no apto.',
        ]);

        $this->redirect(route('conversiones.mis-asignadas'));
    }

    public function render()
    {
        return view('livewire.conversiones.evaluar');
    }
}
