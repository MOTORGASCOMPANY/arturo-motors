<?php

namespace App\Livewire\Almacen;

use App\Models\Producto;
use App\Models\Sede;
use App\Models\ItemSerializado;
use App\Models\KitComponente;
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

        // ─── Kits instalados (historial de conversiones) ───
        $kitsInstalados = ItemSerializado::whereHas('producto.categoria', fn ($q) => $q->where('es_kit', true))
            ->whereIn('estado', ['consumido', 'instalado'])
            ->with([
                'producto.categoria',
                'serviceOrder.cliente',
                'serviceOrder.vehiculo',
                'serviceOrder.tecnico',
                'piezasEnKit.producto',
            ])
            ->orderByDesc('updated_at')
            ->get()
            ->map(function ($kit) {
                $generacion = $kit->producto->atributos['generacion'] ?? '';
                $hijos = $kit->piezasEnKit;

                // Items seriales instalados (no CANT-)
                $seriales = $hijos->filter(fn ($h) => !str_starts_with($h->serie ?? '', 'CANT-') && !empty($h->serie))
                    ->map(fn ($h) => [
                        'nombre' => $h->producto->nombre,
                        'serie' => $h->serie,
                    ])->values();

                return [
                    'kit' => $kit,
                    'cliente' => $kit->serviceOrder->cliente
                        ? trim($kit->serviceOrder->cliente->nombre . ' ' . $kit->serviceOrder->cliente->apellido)
                        : 'N/A',
                    'placa' => $kit->serviceOrder->vehiculo->placa ?? 'N/A',
                    'vehiculo' => trim(
                        ($kit->serviceOrder->vehiculo->marca ?? '') . ' ' .
                        ($kit->serviceOrder->vehiculo->modelo ?? '') . ' ' .
                        ($kit->serviceOrder->vehiculo->anio ?? '')
                    ),
                    'tecnico' => $kit->serviceOrder->tecnico->name ?? 'N/A',
                    'generacion' => $generacion,
                    'seriales' => $seriales,
                    'total_hijos' => $hijos->count(),
                    'fecha' => $kit->updated_at?->format('d/m/Y H:i'),
                    'orden_id' => $kit->serviceOrder->id ?? '—',
                ];
            });

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
            'kitsInstalados' => $kitsInstalados,
        ]);
    }
}
