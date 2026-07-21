<?php

namespace App\Livewire;

use App\Models\Conversion;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\On;

class ListaConversiones extends Component
{
    use WithPagination;
    public $sort, $order, $cant, $search, $direction, $es;
    // Propiedades para el modal de edición
    public $open = false;
    public $conversionAEditar;
    // Propiedades para los campos del formulario del modal
    public $tecnico_id;
    public $fecha_inicio;
    public $fecha_fin;
    public $estado;

    // Carga el usuario autenticado
    public $user;


    // Método para escuchar el evento 'refreshList' de otros componentes
    #[On('refreshList')]
    public function refresh()
    {
        $this->render();
        // Se actualiza la vista, que es lo que se quiere lograr.
        // No es necesario llamar a render() aquí, Livewire se encarga automáticamente.
    }

    public function mount()
    {
        $this->direction = 'desc';
        $this->sort = 'id';
        $this->cant = 10;
        $this->user = Auth::user();
    }

    public function order($sort)
    {
        if ($this->sort === $sort) {
            $this->direction = $this->direction === 'desc' ? 'asc' : 'desc';
        } else {
            $this->sort = $sort;
            $this->direction = 'asc';
        }
    }

    // Método para abrir el modal y cargar los datos de la conversión a editar
    public function editConversion(Conversion $conversionId)
    {
        $this->conversionAEditar = $conversionId;
        $this->tecnico_id = $conversionId->tecnico_id;
        $this->fecha_inicio = $conversionId->fecha_inicio;
        $this->fecha_fin = $conversionId->fecha_fin;
        $this->estado = $conversionId->estado;
        $this->open = true;
    }

    // Método para guardar los cambios de la conversión con la validación dentro
    public function updateConversion()
    {
        $this->validate([
            'tecnico_id' => 'required|exists:users,id',
            'fecha_inicio' => 'nullable|date',
            'fecha_fin' => 'nullable|date',
            'estado' => 'required|in:en_proceso,completado,certificado',
        ]);

        // Verifique el estado y actualiza el estado del Expediente
        if (in_array($this->estado, ['completado', 'certificado'])) {
            $expediente = $this->conversionAEditar->expediente;
            if ($expediente) {
                $expediente->update(['estado' => 'conversion_completada']);
            }
        }

        $this->conversionAEditar->update([
            'tecnico_id' => $this->tecnico_id,
            'fecha_inicio' => $this->fecha_inicio,
            'fecha_fin' => $this->fecha_fin,
            'estado' => $this->estado,
        ]);

        $this->open = false;

        $this->dispatch('minAlert', titulo: '¡ACTUALIZADO!', mensaje: 'La conversión se actualizó correctamente', icono: 'success');
    }

    //Redirecciona a la página de SolicitudRepuestos con el ID de la conversión.
    public function redirectToSolicitudRepuestos($conversionId)
    {
        return redirect()->route('SolicitudRepuestos', ['conversionId' => $conversionId]);
    }

    public function render()
    {
        $conversiones = Conversion::with(['expediente.vehiculo', 'tecnico'])
            ->buscar($this->search)
            ->estado($this->es)
            ->when($this->user->hasRole('Tecnico'), function ($q) {
                $q->where('tecnico_id', $this->user->id);
            })
            ->ordenar($this->sort, $this->direction)
            ->paginate($this->cant);
        
        // Obtener la lista de usuarios con el rol de 'tecnico' para el select del modal
        $tecnicos = User::whereHas('roles', function ($query) {
            $query->where('name', 'Tecnico');
        })->get();

        return view('livewire.lista-conversiones', compact('conversiones', 'tecnicos'));
    }
}
