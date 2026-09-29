<?php

namespace App\Livewire\Almacen;

use App\Models\Producto;
use App\Models\Sede;
use App\Models\ItemSerializado;
use App\Models\KitComponente;
use App\Models\CategoriaAlmacen;
use Livewire\Component;
use Livewire\Attributes\Url;

class ReporteDashboardInventario extends Component
{
    // Sede por defecto = Callao (id=1). null = "Todas".
    #[Url(as: 'sede', except: null)]
    public ?int $filtroSede = 1;

    // Constante umbral stock bajo (configurable)
    public const STOCK_BAJO_UMBRAL = 2;

    public function mount(): void
    {
        // Si viene ?sede= en la URL, #[Url] lo hidrata automáticamente.
        // Si no viene nada, $this->filtroSede = 1 (Callao) por defecto.
    }

    public function updatedFiltroSede(?int $value): void
    {
        // null = "Todas las sedes" (lo manda el botón "Todas" del selector).
        $this->filtroSede = $value;
    }

    public function sedeFiltro(): ?int
    {
        return $this->filtroSede;
    }

    public function sedes(): \Illuminate\Database\Eloquent\Collection
    {
        return Sede::activas()->orderBy('id')->get();
    }

    // ── 1. TASA DE CONSUMO ──────────────────────────────────────────
    public function tasaConsumo(): array
    {
        $sedeId = $this->filtroSede;

        $sellados = ItemSerializado::whereHas('producto.categoria', fn ($q) => $q->where('es_kit', true))
            ->whereIn('estado', ['en_stock', 'completado'])
            ->when($sedeId, fn ($q) => $q->where('sede_id', $sedeId))
            ->count();

        $consumidos = ItemSerializado::whereHas('producto.categoria', fn ($q) => $q->where('es_kit', true))
            ->whereIn('estado', ['consumido', 'instalado'])
            ->when($sedeId, fn ($q) => $q->where('sede_id', $sedeId))
            ->count();

        $total = $sellados + $consumidos;
        $pct = $total > 0 ? round(($consumidos / $total) * 100) : 0;

        return ['porcentaje' => $pct, 'sellados' => $sellados, 'consumidos' => $consumidos];
    }

    // ── 2. ALERTAS DE STOCK ──────────────────────────────────────────
    public function alertasStock(): array
    {
        $sedeId = $this->filtroSede; // null = todas las sedes

        $productos = Producto::where('activo', true)
            ->whereHas('categoria', fn ($q) => $q->where('es_kit', false))
            ->with('categoria')
            ->get();

        $sinStock = collect();
        $stockBajo = collect();

        foreach ($productos as $p) {
            $cant = $p->stockSueltoEnSede($sedeId);
            if ($cant === 0) {
                $sinStock->push(['nombre' => $p->nombre, 'cantidad' => 0]);
            } elseif ($p->stockBajoEnSede($sedeId)) {
                $stockBajo->push(['nombre' => $p->nombre, 'cantidad' => $cant]);
            }
        }

        return [
            'sinStock' => $sinStock->take(8)->values(),
            'stockBajo' => $stockBajo->take(8)->values(),
            'umbral' => self::STOCK_BAJO_UMBRAL,
        ];
    }

    // ── 3. GRÁFICO BARRAS: SELLADOS / COMPLETADOS / CONSUMIDOS ───────
    public function grafico(): array
    {
        $sedes = $this->sedes();
        $sedeFiltro = $this->filtroSede;

        $labels = [];
        $sellados = [];
        $completados = [];
        $consumidos = [];

        foreach ($sedes as $s) {
            if ($sedeFiltro && $s->id !== $sedeFiltro) continue;

            $labels[] = $s->nombre;
            $base = ItemSerializado::whereHas('producto.categoria', fn ($q) => $q->where('es_kit', true))
                ->where('sede_id', $s->id);

            $sellados[] = (int) $base->clone()->where('estado', 'en_stock')->count();
            $completados[] = (int) $base->clone()->where('estado', 'completado')->count();
            $consumidos[] = (int) $base->clone()->whereIn('estado', ['consumido', 'instalado'])->count();
        }

        return compact('labels', 'sellados', 'completados', 'consumidos', 'sedeFiltro');
    }

    // ── 4. PRODUCTOS CON STOCK EN LA SEDE ───────────────────────────
    public function productosSede(): \Illuminate\Support\Collection
    {
        $sedeId = $this->filtroSede;

        $query = Producto::with('categoria')
            ->where('activo', true)
            ->whereHas('categoria', fn ($q) => $q->where('es_kit', false))
            ->orderBy('nombre');

        $productos = $query->get();

        return $productos->map(function ($p) use ($sedeId) {
            $cant = $p->stockSueltoEnSede($sedeId);
            $esBajo = $p->stock_minimo > 0 && $cant <= $p->stock_minimo;
            
            return [
                'id' => $p->id,
                'nombre' => $p->nombre,
                'categoria' => $p->categoria->nombre ?? 'N/A',
                'stock' => $cant,
                'stock_minimo' => $p->stock_minimo,
                'es_bajo' => $esBajo,
                'sin_stock' => $cant === 0,
            ];
        })->filter(fn ($p) => $p['stock'] > 0 || $p['es_bajo'] || $p['sin_stock'])
          ->sortBy(function ($p) {
              // Ordenar: sin stock primero, luego stock bajo, luego normal
              if ($p['sin_stock']) return 0;
              if ($p['es_bajo']) return 1;
              return 2;
          })
          ->values();
    }

    // ── 5. SELECTOR: etiqueta legible para badge ─────────────────────
    public function sedeLabel(): string
    {
        if ($this->filtroSede === null) return 'Todas';
        return $this->sedes()->firstWhere('id', $this->filtroSede)?->nombre ?? 'Todas';
    }

    public function render()
    {
        return view('livewire.almacen.reporte-dashboard-inventario', [
            'sedes' => $this->sedes(),
            'sedeActual' => $this->filtroSede,
            'sedeLabel' => $this->sedeLabel(),
            'tasa' => $this->tasaConsumo(),
            'alertas' => $this->alertasStock(),
            'grafico' => $this->grafico(),
            'productos' => $this->productosSede(),
        ]);
    }
}