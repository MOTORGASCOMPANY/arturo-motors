<?php

namespace App\Livewire\RRHH;

use App\Models\DocumentoUsuario;
use Livewire\Component;
use App\Models\User;
use App\Models\TipoDocumento;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\On;

class GestionDocumentos extends Component
{
    use WithFileUploads;

    public $usuarioId; // ID del usuario a consultar
    public $documentosRequeridos;

    public function mount($id = null)
    {
        // Si no viene ID (en el perfil), tomamos el del usuario logueado
        $this->usuarioId = $id ?: Auth::id();
        $this->documentosRequeridos = TipoDocumento::all();
    }

    #[On('refresh-legajo')]
    public function refresh()
    {
        // Solo con existir este método, Livewire refrescará el render()
    }

    public function cambiarEstado($documentoId, $nuevoEstado)
    {
        // Usamos el ID directamente desde la Fachada para evitar el error en id()
        $userId = \Illuminate\Support\Facades\Auth::id();

        // Obtenemos el usuario buscando por el modelo, así Intelephense sabe que es un User
        $usuarioAutenticado = \App\Models\User::find($userId);

        // Verificamos que exista y que tenga el rol
        if (!$usuarioAutenticado || !$usuarioAutenticado->hasAnyRole(['Administrador del sistema', 'administrador'])) {
            return;
        }

        $doc = DocumentoUsuario::find($documentoId);
        if ($doc) {
            $doc->update(['estado' => $nuevoEstado]);

            $color = $nuevoEstado == 'Aprobado' ? 'success' : 'error';
            $mensaje = "Documento marcado como " . $nuevoEstado;

            $this->dispatch('minAlert', titulo: "ACTUALIZADO", mensaje: $mensaje, icono: $color);
        }
    }

    public function render()
    {
        $usuario = User::with(['documentos.tipo'])->findOrFail($this->usuarioId);

        return view('livewire.r-r-h-h.gestion-documentos', [
            'usuario' => $usuario
        ]);
    }

    public function eliminarDocumento($documentoId)
    {
        $doc = DocumentoUsuario::find($documentoId);

        if ($doc) {
            // 1. Eliminar archivo físico
            if (Storage::disk('public')->exists($doc->ruta)) {
                Storage::disk('public')->delete($doc->ruta);
            }

            // 2. Eliminar registro en BD
            $doc->delete();

            $this->dispatch('minAlert', titulo: "ELIMINADO", mensaje: "Documento borrado correctamente.", icono: "warning");
        }
    }
}
