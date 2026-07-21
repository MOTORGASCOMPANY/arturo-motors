<?php

namespace App\Livewire\RRHH;

use App\Models\Contrato;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\On;
use Illuminate\Support\Facades\Auth;

class Contratos extends Component
{

    use WithPagination;

    public $search = '';
    public $cant = '10';

    public $user;

    // Propiedad para resetear página en búsquedas
    public function updatingSearch()
    {
        $this->resetPage();
    }

    // Escucha el evento y refresca el componente
    #[On('refresh-tabla-contratos')]
    public function refreshTable()
    {
        // Al no hacer nada aquí, Livewire por defecto ejecuta el render()
        // y actualiza la vista, que es lo que queremos.
    }

    public function render()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        // Iniciamos la consulta base
        $query = Contrato::with('user');

        // --- FILTRO DE SEGURIDAD POR ROL ---
        if ($user->hasRole('tecnico')) {
            $query->where('user_id', $user->id);
        } else {
            // El administrador ve todo y puede usar el buscador
            $query->where(function($q) {
                $q->whereHas('user', function ($subQuery) {
                    $subQuery->where('name', 'like', '%' . $this->search . '%');
                })
                ->orWhere('cargo', 'like', '%' . $this->search . '%');
            });
        }

        $contratos = $query->orderBy('id', 'desc')->paginate($this->cant);

        return view('livewire.r-r-h-h.contratos', compact('contratos'));
    }

    public function delete($id)
    {
        $contrato = Contrato::find($id);
        if ($contrato) {
            $contrato->delete();
            $this->dispatch('minAlert', titulo: "ELIMINADO", mensaje: "Contrato borrado correctamente.", icono: "warning");
        }
    }
}
