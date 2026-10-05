<?php

namespace App\Livewire\Almacen;

use App\Models\ItemSerializado;
use App\Models\KitComponente;
use App\Models\Producto;
use App\Models\ReportePiezaNoEncajada;
use App\Models\ServiceOrder;

trait ConversionesConsulta
{
    public function getConversionesProperty()
    {
        return ServiceOrder::with(['cliente', 'vehiculo', 'service', 'tecnico'])
            ->enConversion($this->busqueda)
            ->paginate($this->perPage);
    }

    public function nombreKit(ServiceOrder $orden): string
    {
        return $orden->items->first()?->producto->nombre ?? '—';
    }

    public function resumenConversion(ServiceOrder $orden): array
    {
        $pendientes = ReportePiezaNoEncajada::deOrden($orden->id)->enSeguimiento()->count();
        $despachadas = ReportePiezaNoEncajada::deOrden($orden->id)->resuelto()->count();

        return compact('pendientes', 'despachadas');
    }

    public function getConversionSeleccionadaProperty(): ?ServiceOrder
    {
        if (!$this->conversionSeleccionadaId) return null;
        return ServiceOrder::with(['cliente', 'vehiculo', 'service', 'tecnico'])->find($this->conversionSeleccionadaId);
    }

    public function getKitPadreProperty(): ?ItemSerializado
    {
        $orden = $this->conversionSeleccionada;
        if (!$orden) return null;

        return ItemSerializado::kitPadreDe($orden->id)->with('producto.categoria')->first();
    }

    public function getGeneracionKitProperty(): string
    {
        $padre = $this->kitPadre;
        if (!$padre) return '';
        return $padre->producto->atributos['generacion'] ?? $padre->producto->categoria->nombre;
    }

    public function getKitItemsProperty()
    {
        return $this->itemsDeKit(true);
    }

    public function getItemsCantidadProperty()
    {
        return $this->itemsDeKit(false);
    }

    /** Piezas ya asignadas al kit de la conversión seleccionada. */
    private function itemsDeKit(bool $serializables)
    {
        $padre = $this->kitPadre;
        if (!$padre) return collect();

        $ids = $padre->producto->componentes->pluck('producto_componente_id')->toArray();

        return $this->conversionSeleccionada->items()
            ->componentesDeKit($ids)
            ->get()
            ->filter(fn ($i) => Producto::esSerializable($i->producto->nombre) === $serializables)
            ->values();
    }

    /** Receta completa del kit, incluidas las piezas aún no montadas. */
    public function getTodasPiezasKitProperty()
    {
        $padre = $this->kitPadre;
        if (!$padre) return collect();

        return KitComponente::deKit($padre->producto_id)->with('componente.categoria')->get()
            ->map(fn ($kc) => (object) [
                'producto_id' => $kc->producto_componente_id,
                'nombre' => $kc->componente->nombre,
                'categoria' => $kc->componente->categoria->nombre,
                'es_serializado' => Producto::esSerializable($kc->componente->nombre),
                'cantidad_esperada' => $kc->cantidad_esperada,
            ]);
    }

    public function getItemsReportadosProperty(): array
    {
        $orden = $this->conversionSeleccionada;
        if (!$orden) return [];

        return ReportePiezaNoEncajada::deOrden($orden->id)
            ->enSeguimiento()
            ->pluck('item_no_encajado_id')
            ->toArray();
    }

    public function getItemsReemplazadosProperty(): array
    {
        $orden = $this->conversionSeleccionada;
        if (!$orden) return [];

        return ReportePiezaNoEncajada::deOrden($orden->id)
            ->resuelto()
            ->pluck('item_no_encajado_id')
            ->toArray();
    }

    public function getHistorialProperty()
    {
        $orden = $this->conversionSeleccionada;
        if (!$orden) return collect();

        return ReportePiezaNoEncajada::deOrden($orden->id)
            ->resuelto()
            ->with('itemNoEncajado.producto')
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(fn ($r) => (object) [
                'id' => $r->id,
                'pieza' => $r->itemNoEncajado->producto->nombre ?? 'Pieza',
                'serie_vieja' => $r->itemNoEncajado->serie ?? '—',
                'motivo' => $r->motivo_no_encaja,
                'estado' => $r->estado,
                'fecha' => $r->created_at->format('d/m H:i'),
            ]);
    }

    public function getTimelineConversionProperty()
    {
        $orden = $this->conversionSeleccionada;
        if (!$orden) return collect();

        return $orden->historialEstados()->with('usuario')->latest()->get()
            ->map(fn ($h) => (object) [
                'estado_nuevo' => $h->estado_nuevo,
                'estado_anterior' => $h->estado_anterior,
                'fecha' => $h->created_at->format('d/m/Y H:i'),
                'usuario' => $h->usuario->name ?? 'Sistema',
                'esConversion' => in_array($h->estado_nuevo, ['en_conversion', 'conversion_completada']),
            ]);
    }
}
