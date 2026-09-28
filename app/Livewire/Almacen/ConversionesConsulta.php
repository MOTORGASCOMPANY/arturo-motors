<?php

namespace App\Livewire\Almacen;

use App\Models\ItemSerializado;
use App\Models\KitComponente;
use App\Models\ReportePiezaNoEncajada;
use App\Models\ServiceOrder;

trait ConversionesConsulta
{
    public function getConversionesProperty()
    {
        $query = ServiceOrder::with(['cliente', 'vehiculo', 'service', 'tecnico'])
            ->where('estado', 'en_conversion')
            ->whereHas('items', fn($q) => $q->whereNull('kit_padre_id')->whereHas('piezasEnKit'));

        if (!empty($this->busqueda)) {
            $query->where(function ($q) {
                $q->where('id', 'like', "%{$this->busqueda}%")
                  ->orWhereHas('cliente', fn($cq) => $cq->where('nombre', 'like', "%{$this->busqueda}%"))
                  ->orWhereHas('vehiculo', fn($vq) => $vq->where('placa', 'like', "%{$this->busqueda}%"))
                  ->orWhereHas('tecnico', fn($tq) => $tq->where('name', 'like', "%{$this->busqueda}%"));
            });
        }

        return $query->orderBy('fecha_inicio_conversion', 'asc')->paginate($this->perPage);
    }

    public function nombreKit(ServiceOrder $orden): string
    {
        return $orden->items->first()?->producto->nombre ?? '—';
    }

    public function resumenConversion(ServiceOrder $orden): array
    {
        $pendientes = ReportePiezaNoEncajada::where('service_order_id', $orden->id)
            ->whereIn('estado', ['pendiente', 'buscando_pieza', 'solicitando_almacen'])->count();
        $despachadas = ReportePiezaNoEncajada::where('service_order_id', $orden->id)
            ->whereIn('estado', ['kit_abierto', 'resuelto'])->count();

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
        return $orden->items()->whereNull('kit_padre_id')->whereHas('piezasEnKit')->with('producto.categoria')->first();
    }

    public function getGeneracionKitProperty(): string
    {
        $padre = $this->kitPadre;
        if (!$padre) return '';
        return $padre->producto->atributos['generacion'] ?? $padre->producto->categoria->nombre;
    }

    public function getKitItemsProperty()
    {
        $padre = $this->kitPadre;
        if (!$padre) return collect();
        $ids = $padre->producto->componentes->pluck('producto_componente_id')->toArray();
        return $this->conversionSeleccionada->items()->where('estado', 'asignado')->whereNotNull('kit_padre_id')
            ->whereIn('producto_id', $ids)->get()
            ->filter(fn($i) => $this->esSerializable($i->producto->nombre))->values();
    }

    public function getItemsCantidadProperty()
    {
        $padre = $this->kitPadre;
        if (!$padre) return collect();
        $ids = $padre->producto->componentes->pluck('producto_componente_id')->toArray();
        return $this->conversionSeleccionada->items()->where('estado', 'asignado')->whereNotNull('kit_padre_id')
            ->whereIn('producto_id', $ids)->get()
            ->filter(fn($i) => !$this->esSerializable($i->producto->nombre))->values();
    }

    public function getTodasPiezasKitProperty()
    {
        $padre = $this->kitPadre;
        if (!$padre) return collect();
        return KitComponente::where('producto_kit_id', $padre->producto_id)->with('componente.categoria')->get()
            ->map(fn($kc) => (object) [
                'producto_id' => $kc->producto_componente_id,
                'nombre' => $kc->componente->nombre,
                'categoria' => $kc->componente->categoria->nombre,
                'es_serializado' => $this->esSerializable($kc->componente->nombre),
                'cantidad_esperada' => $kc->cantidad_esperada,
            ]);
    }

    public function getItemsReportadosProperty(): array
    {
        $orden = $this->conversionSeleccionada;
        if (!$orden) return [];
        return ReportePiezaNoEncajada::where('service_order_id', $orden->id)
            ->whereIn('estado', ['pendiente', 'buscando_pieza', 'solicitando_almacen'])->pluck('item_no_encajado_id')->toArray();
    }

    public function getItemsReemplazadosProperty(): array
    {
        $orden = $this->conversionSeleccionada;
        if (!$orden) return [];
        return ReportePiezaNoEncajada::where('service_order_id', $orden->id)
            ->whereIn('estado', ['kit_abierto', 'resuelto'])->pluck('item_no_encajado_id')->toArray();
    }

    public function getHistorialProperty()
    {
        $orden = $this->conversionSeleccionada;
        if (!$orden) return collect();
        return ReportePiezaNoEncajada::where('service_order_id', $orden->id)
            ->whereIn('estado', ['kit_abierto', 'resuelto'])
            ->with('itemNoEncajado.producto')
            ->orderBy('created_at', 'desc')->get()
            ->map(fn($r) => (object) [
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
            ->map(fn($h) => (object) [
                'estado_nuevo' => $h->estado_nuevo,
                'estado_anterior' => $h->estado_anterior,
                'fecha' => $h->created_at->format('d/m/Y H:i'),
                'usuario' => $h->usuario->name ?? 'Sistema',
                'esConversion' => in_array($h->estado_nuevo, ['en_conversion', 'conversion_completada']),
            ]);
    }
}
