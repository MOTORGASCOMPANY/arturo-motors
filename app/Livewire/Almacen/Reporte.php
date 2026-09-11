<?php

namespace App\Livewire\Almacen;

use App\Models\Producto;
use App\Models\Sede;
use Livewire\Component;

class Reporte extends Component
{
    // Filtros
    public ?int $filtroSede = null;
    public string $filtroStock = 'todos'; // todos, con_stock, sin_stock, stock_bajo

    public function exportPdfUrl(): string
    {
        return route('ReporteAlmacen.Pdf', [
            'sede_id' => $this->filtroSede,
            'stock' => $this->filtroStock,
        ]);
    }

    public function exportExcelUrl(): string
    {
        return route('ReporteAlmacen.Excel', [
            'sede_id' => $this->filtroSede,
            'stock' => $this->filtroStock,
        ]);
    }

    public function render()
    {
        $sedes = Sede::activas()->orderBy('id')->get();
        $productos = Producto::with('categoria')->where('activo', true)->get();

        // Stock bajo (Callao)
        $stockBajo = $productos->filter(fn ($p) => $p->stock_bajo);

        // Matriz producto × sede
        $distribucion = $productos->map(function ($p) use ($sedes) {
            $porSede = [];
            foreach ($sedes as $s) {
                $porSede[$s->id] = $p->stockEnSede($s->id);
            }
            return [
                'producto' => $p,
                'por_sede' => $porSede,
                'total' => array_sum($porSede),
            ];
        })->filter(fn ($row) => $row['total'] > 0)->values();

        // Aplicar filtros
        $distribucionFiltrada = $distribucion;

        // Filtro por sede
        if ($this->filtroSede) {
            $distribucionFiltrada = $distribucionFiltrada->filter(
                fn ($row) => $row['por_sede'][$this->filtroSede] > 0
            );
        }

        // Filtro por nivel de stock
        $distribucionFiltrada = match ($this->filtroStock) {
            'con_stock' => $distribucionFiltrada->filter(fn ($row) => $row['total'] > 0),
            'sin_stock' => $productos->map(function ($p) use ($sedes) {
                $porSede = [];
                foreach ($sedes as $s) {
                    $porSede[$s->id] = $p->stockEnSede($s->id);
                }
                return [
                    'producto' => $p,
                    'por_sede' => $porSede,
                    'total' => array_sum($porSede),
                ];
            })->filter(fn ($row) => $row['total'] === 0),
            'stock_bajo' => $distribucion->filter(fn ($row) => $row['producto']->stock_bajo),
            default => $distribucionFiltrada,
        };

        // Datos para gráfico de barras (stock por sede)
        $stockPorSede = $sedes->mapWithKeys(function ($s) use ($productos) {
            $total = $productos->sum(fn ($p) => $p->stockEnSede($s->id));
            return [$s->nombre => $total];
        });

        // Datos para gráfico de pastel (stock por categoría)
        $stockPorCategoria = $distribucionFiltrada
            ->groupBy(fn ($row) => $row['producto']->categoria->nombre)
            ->map(fn ($grupo) => $grupo->sum('total'))
            ->sortByDesc(fn ($v) => $v);

        // KPIs
        $totalItems = $distribucionFiltrada->sum('total');
        $productosConStock = $distribucionFiltrada->filter(fn ($row) => $row['total'] > 0)->count();

        return view('livewire.almacen.reporte', [
            'sedes' => $sedes,
            'distribucion' => $distribucionFiltrada->values(),
            'stockBajo' => $stockBajo,
            'totalItems' => $totalItems,
            'productosConStock' => $productosConStock,
            'stockPorSede' => $stockPorSede,
            'stockPorCategoria' => $stockPorCategoria,
            'labelsSedes' => $stockPorSede->keys()->toArray(),
            'dataSedes' => $stockPorSede->values()->toArray(),
            'labelsCategorias' => $stockPorCategoria->keys()->toArray(),
            'dataCategorias' => $stockPorCategoria->values()->toArray(),
        ]);
    }
}
