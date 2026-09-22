<?php

namespace App\Livewire\Almacen\Kits;

use App\Models\ItemSerializado;
use App\Models\KitPiezaExtraida;
use App\Models\MovimientoStock;
use App\Models\Sede;
use App\Services\CambioPiezaService;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Rearmar extends Component
{
    public ?int $kitItemId = null;
    public ?ItemSerializado $kit = null;
    public array $piezasFaltantes = [];

    
    public string $buscarPieza = '';
    public array $piezasEncontradas = [];
    public ?int $piezaSeleccionadaId = null;

    protected CambioPiezaService $cambioService;

    public function boot(CambioPiezaService $cambioService)
    {
        $this->cambioService = $cambioService;
    }

    protected function sedePrincipalId(): int
    {
        return Sede::activas()->orderBy('id')->first()?->id ?? 1;
    }

    public function mount(int $kitItemId)
    {
        $this->kitItemId = $kitItemId;
        $this->kit = ItemSerializado::with('producto.categoria')->find($kitItemId);

        if (!$this->kit || $this->kit->estado !== 'abierto') {
            abort(404, 'Kit no encontrado o no está en estado abierto.');
        }

        $this->cargarFaltantes();
    }

    public function cargarFaltantes()
    {
        if (!$this->kit) return;

        $faltantes = $this->cambioService->piezasFaltantesEnKit($this->kit);

        $this->piezasFaltantes = $faltantes->map(fn ($f) => [
            'producto_id' => $f['producto_id'],
            'nombre' => $f['nombre'],
            'cantidad_extraida' => $f['cantidad_extraida'],
        ])->toArray();
    }

    public function updatedBuscarPieza()
    {
        $termino = trim($this->buscarPieza);
        if (strlen($termino) < 2) {
            $this->piezasEncontradas = [];
            return;
        }

        $sedeId = $this->sedePrincipalId();

        $this->piezasEncontradas = ItemSerializado::with('producto.categoria')
            ->where('estado', 'en_stock')
            ->where('sede_id', $sedeId)
            ->where('id', '!=', $this->kit->id)
            ->where(function ($q) use ($termino) {
                $q->where('serie', 'like', "%{$termino}%")
                  ->orWhereHas('producto', fn ($p) => $p->where('nombre', 'like', "%{$termino}%"));
            })
            ->limit(10)
            ->get()
            ->toArray();
    }

    public function seleccionarPieza(int $piezaId)
    {
        $this->piezaSeleccionadaId = $piezaId;
        $this->piezasEncontradas = [];
        $this->buscarPieza = '';
    }

    public function deseleccionarPieza()
    {
        $this->piezaSeleccionadaId = null;
    }

    public function confirmarRearme()
    {
        if (!$this->kit) return;

        if (!$this->piezaSeleccionadaId) {
            $this->dispatch('swal', tipo: 'warning', titulo: 'Atención', mensaje: 'Seleccioná una pieza de repuesto.');
            return;
        }

        try {
            $resultado = $this->cambioService->rearmarKit(
                $this->kit,
                $this->piezaSeleccionadaId,
                $this->sedePrincipalId(),
                'Rearme de kit abierto'
            );

            if ($resultado) {
                $this->dispatch('swal', tipo: 'success', titulo: '¡Kit rearmando!',
                    mensaje: 'La pieza fue devuelta al kit. Verificando si está completo...');

                
                $this->kit->refresh();
                $this->cargarFaltantes();
                $this->piezaSeleccionadaId = null;

                
                if ($this->kit->estado === 'en_stock') {
                    $this->dispatch('swal', tipo: 'success', titulo: '¡Kit completo!',
                        mensaje: 'El kit fue rearmando exitosamente y está listo para usar.');
                    $this->redirect(route('almacen.stock'));
                }
            }
        } catch (\Exception $e) {
            $this->dispatch('swal', tipo: 'error', titulo: 'Error', mensaje: $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.almacen.kits.rearmar');
    }
}
