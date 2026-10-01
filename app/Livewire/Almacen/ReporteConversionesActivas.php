<?php

namespace App\Livewire\Almacen;

use App\Models\ItemSerializado;
use App\Models\KitComponente;
use App\Models\MovimientoStock;
use App\Models\ReportePiezaNoEncajada;
use App\Models\ServiceOrder;
use App\Models\Sede;
use App\Services\CambioPiezaService;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class ReporteConversionesActivas extends Component
{
    use WithPagination;
    use ConversionesConsulta;
    use ConversionesDespachoPorCantidad;
    use ConversionesSeleccionPieza;

    public string $busqueda = '';
    public int $perPage = 15;

    public bool $modalAbierto = false;
    public ?int $conversionSeleccionadaId = null;

    public ?int $piezaReemplazarId = null;
    public string $nuevaSerie = '';
    public ?string $metodoReemplazo = null;
    public ?int $kitSeleccionadoId = null;
    public ?array $piezaKitConSerie = null;
    public string $busquedaKit = '';
    public array $kitsDisponibles = [];
    public string $cantidadAdicional = '1';
    public int $stockDisponible = 0;
    public string $observacion = '';

    public bool $modalPartesAbierto = false;

    
    public array $piezasSueltas = [];
    public ?int $piezaSueltaSeleccionadaId = null;

    protected CambioPiezaService $cambioPiezaService;

    public function boot(CambioPiezaService $cambioPiezaService): void
    {
        $this->cambioPiezaService = $cambioPiezaService;
    }

    
    
    

    public function abrirModal(int $ordenId)
    {
        $orden = ServiceOrder::find($ordenId);
        if (!$orden) return;

        if (!$orden->fecha_inicio_conversion) {
            $this->dispatch('minToast', titulo: 'Conversión no iniciada', mensaje: 'El técnico debe iniciar la conversión primero.', icono: 'warning');
            return;
        }

        $this->conversionSeleccionadaId = $ordenId;
        $this->modalAbierto = true;
        $this->resetReemplazo();
    }

    public function cerrarModal()
    {
        $this->modalAbierto = false;
        $this->conversionSeleccionadaId = null;
        $this->resetReemplazo();
    }

    private function resetReemplazo()
    {
        $this->piezaReemplazarId = null;
        $this->nuevaSerie = '';
        $this->metodoReemplazo = null;
        $this->kitSeleccionadoId = null;
        $this->piezaKitConSerie = null;
        $this->busquedaKit = '';
        $this->kitsDisponibles = [];
        $this->cantidadAdicional = '1';
        $this->stockDisponible = 0;
        $this->observacion = '';
        $this->modalPartesAbierto = false;
        $this->piezasSueltas = [];
        $this->piezaSueltaSeleccionadaId = null;
    }

    private function esSerializable($producto): bool
    {
        if (is_int($producto)) {
            $producto = \App\Models\Producto::with('categoria')->find($producto);
        }
        if (is_string($producto)) {
            $producto = \App\Models\Producto::where('nombre', $producto)->first();
        }
        if (!$producto instanceof \App\Models\Producto) {
            return false;
        }
        return $producto->categoria->es_serializado ?? false;
    }

    private function sedeOperativa(): int
    {
        return Sede::activas()->orderBy('id')->first()?->id ?? 1;
    }

    public function abrirPartesGenerales() { $this->modalPartesAbierto = true; }
    public function cerrarPartesGenerales() { $this->modalPartesAbierto = false; }


    public function render() { return view('livewire.almacen.reporte-conversiones-activas'); }
}
