<?php

namespace App\Livewire\Almacen;

use App\Models\Producto;
use App\Models\Sede;
use App\Models\ItemSerializado;
use App\Models\MovimientoStock;
use Livewire\Component;

class Reporte extends Component
{
    // Sin tipo int: el <select> de "Todas" envía '' y rompía la hidratación de ?int.
    public $filtroSede = null;
    public string $filtroStock = 'todos';

    public function updatedFiltroSede($value): void
    {
        $this->filtroSede = ($value === '' || $value === null) ? null : (int) $value;
    }

    public function updatedFiltroStock($value): void
    {
        $this->filtroStock = (string) $value;
    }

    public function limpiarFiltros(): void
    {
        $this->filtroSede = null;
        $this->filtroStock = 'todos';
    }

    public function stockLabel(): string
    {
        return match ($this->filtroStock) {
            'con_stock' => 'Con stock',
            'sin_stock' => 'Sin stock',
            'stock_bajo' => 'Stock bajo',
            default => 'Todos',
        };
    }

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

        // Sede efectiva: null = todas; si hay filtro, KPIs/gráficos/tablas usan SOLO esa sede.
        $sedeFiltro = $this->filtroSede;

        $conPorSede = $productos->map(function ($p) use ($sedes, $sedeFiltro) {
            $porSede = [];
            foreach ($sedes as $s) {
                $porSede[$s->id] = $p->stockSueltoEnSede($s->id);
            }
            $totalTodas = array_sum($porSede);
            $total = $sedeFiltro ? (int) ($porSede[$sedeFiltro] ?? 0) : $totalTodas;

            return [
                'producto'    => $p,
                'por_sede'    => $porSede,
                'total_todas' => $totalTodas,
                'total'       => $total,
            ];
        });

        // Stock bajo según la sede activa del filtro (o principal si es "todas")
        $sedeStock = $sedeFiltro ?? (int) ($sedes->first()?->id ?? 1);
        $stockBajo = $conPorSede->filter(
            fn ($row) => $row['producto']->stock_minimo > 0
                && $row['total'] > 0
                && $row['total'] <= $row['producto']->stock_minimo
        )->map(fn ($row) => $row['producto'])->values();

        $distribucionBase = $conPorSede->filter(fn ($row) => $row['total'] > 0);

        $distribucionFiltrada = match ($this->filtroStock) {
            'con_stock' => $distribucionBase,
            'sin_stock' => $conPorSede->filter(fn ($row) => $row['total'] === 0),
            'stock_bajo' => $conPorSede->filter(
                fn ($row) => $row['producto']->stock_minimo > 0
                    && $row['total'] <= $row['producto']->stock_minimo
            ),
            default => $distribucionBase,
        };

        // ── Gráfico 1: Stock por sede (respeta sede + stock) ──
        $stockPorSede = $sedes
            ->filter(fn ($s) => !$sedeFiltro || $s->id === $sedeFiltro)
            ->mapWithKeys(function ($s) use ($distribucionFiltrada) {
                $total = $distribucionFiltrada->sum(
                    fn ($row) => (int) ($row['por_sede'][$s->id] ?? 0)
                );
                return [$s->nombre => $total];
            });

        // ── Gráfico 2: Stock por categoría (respeta sede + stock) ──
        $stockPorCategoria = $distribucionFiltrada
            ->groupBy(fn ($row) => $row['producto']->categoria->nombre)
            ->map(fn ($grupo) => $grupo->sum('total'))
            ->sortByDesc(fn ($v) => $v);

        // ── Gráfico 3: Top productos (respeta sede + stock) ──
        $topProductos = $distribucionFiltrada
            ->filter(fn ($row) => $row['total'] > 0)
            ->sortByDesc('total')
            ->take(8)
            ->values();

        // ── Gráfico 4: Nivel de stock en el alcance de la sede (OK / bajo / sin) ──
        // Siempre sobre el universo de la sede (no el filtro de stock) para ver la salud real.
        $nivelOk = $conPorSede->filter(fn ($row) => $row['total'] > 0
            && !($row['producto']->stock_minimo > 0 && $row['total'] <= $row['producto']->stock_minimo)
        )->count();
        $nivelBajo = $conPorSede->filter(fn ($row) => $row['producto']->stock_minimo > 0
            && $row['total'] > 0
            && $row['total'] <= $row['producto']->stock_minimo
        )->count();
        $nivelSin = $conPorSede->filter(fn ($row) => $row['total'] === 0)->count();

        // ── Gráfico 5: Estados de kits (respeta sede) ──
        $kitsPorEstado = ItemSerializado::query()
            ->whereHas('producto.categoria', fn ($q) => $q->where('es_kit', true))
            ->when($sedeFiltro, fn ($q) => $q->where('sede_id', $sedeFiltro))
            ->selectRaw('estado, COUNT(*) as total')
            ->groupBy('estado')
            ->pluck('total', 'estado');

        $estadoLabels = [
            'en_stock'   => 'Sellados',
            'abierto'    => 'Incompletos',
            'completado' => 'Completados',
            'consumido'  => 'Consumidos',
            'instalado'  => 'Instalados',
            'asignado'   => 'Asignados',
        ];
        $labelsKitsEstado = [];
        $dataKitsEstado = [];
        foreach ($estadoLabels as $key => $label) {
            $n = (int) ($kitsPorEstado[$key] ?? 0);
            if ($n > 0) {
                $labelsKitsEstado[] = $label;
                $dataKitsEstado[] = $n;
            }
        }

        // ── Gráfico 6: Entradas vs salidas (últimos 30 días, respeta sede) ──
        $desdeMov = now()->subDays(29)->startOfDay();
        $movDiarios = MovimientoStock::query()
            ->where('created_at', '>=', $desdeMov)
            ->when($sedeFiltro, fn ($q) => $q->where('sede_id', $sedeFiltro))
            ->selectRaw("DATE(created_at) as dia, tipo, SUM(cantidad) as total")
            ->groupBy('dia', 'tipo')
            ->get();

        $labelsMovDias = [];
        for ($i = 29; $i >= 0; $i--) {
            $labelsMovDias[] = now()->subDays($i)->format('d/m');
        }
        $mapEntradas = [];
        $mapSalidas = [];
        foreach ($movDiarios as $row) {
            $key = \Carbon\Carbon::parse($row->dia)->format('d/m');
            if ($row->tipo === 'entrada') {
                $mapEntradas[$key] = (int) $row->total;
            } else {
                $mapSalidas[$key] = (int) $row->total;
            }
        }
        $dataEntradas = array_map(fn ($k) => $mapEntradas[$k] ?? 0, $labelsMovDias);
        $dataSalidas = array_map(fn ($k) => $mapSalidas[$k] ?? 0, $labelsMovDias);

        $totalItems = $distribucionFiltrada->sum('total');
        $productosConStock = $distribucionFiltrada->filter(fn ($row) => $row['total'] > 0)->count();
        $porcentajeStock = $productos->count() > 0
            ? round(($productosConStock / $productos->count()) * 100)
            : 0;

        // Kits instalados: respetar sede del filtro
        $kitsInstalados = ItemSerializado::whereHas('producto.categoria', fn ($q) => $q->where('es_kit', true))
            ->whereIn('estado', ['consumido', 'instalado'])
            ->when($sedeFiltro, fn ($q) => $q->where('sede_id', $sedeFiltro))
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

                $seriales = $hijos->filter(fn ($h) => ($h->atributos['tipo'] ?? '') !== 'cantidad' && !empty($h->serie))
                    ->map(fn ($h) => [
                        'nombre' => $h->producto->nombre,
                        'serie'  => $h->serie,
                    ])->values();

                return [
                    'kit'         => $kit,
                    'cliente'     => $kit->serviceOrder->cliente
                        ? trim($kit->serviceOrder->cliente->nombre . ' ' . $kit->serviceOrder->cliente->apellido)
                        : 'N/A',
                    'placa'       => $kit->serviceOrder->vehiculo->placa ?? 'N/A',
                    'vehiculo'    => trim(
                        ($kit->serviceOrder->vehiculo->marca ?? '') . ' ' .
                        ($kit->serviceOrder->vehiculo->modelo ?? '') . ' ' .
                        ($kit->serviceOrder->vehiculo->anio ?? '')
                    ),
                    'tecnico'     => $kit->serviceOrder->tecnico->name ?? 'N/A',
                    'generacion'  => $generacion,
                    'seriales'    => $seriales,
                    'total_hijos' => $hijos->count(),
                    'fecha'       => $kit->updated_at?->format('d/m/Y H:i'),
                    'orden_id'    => $kit->serviceOrder->id ?? '—',
                ];
            });

        $valorTotal = $distribucionFiltrada->sum(function ($row) {
            $precio = (float) ($row['producto']->precio_referencial ?? 0);
            return $row['total'] * $precio;
        });

        // Movimientos: respetar sede del filtro
        $movimientosRecientes = MovimientoStock::with(['producto', 'sede', 'usuario'])
            ->when($sedeFiltro, fn ($q) => $q->where('sede_id', $sedeFiltro))
            ->latest()
            ->take(15)
            ->get()
            ->map(fn ($m) => [
                'producto' => $m->producto->nombre ?? 'N/A',
                'sede'     => $m->sede->nombre ?? 'N/A',
                'tipo'     => $m->tipo,
                'cantidad' => $m->cantidad,
                'motivo'   => $m->motivo,
                'usuario'  => $m->usuario->name ?? 'N/A',
                'fecha'    => $m->created_at->format('d/m/Y H:i'),
            ]);

        $sedeLabel = $sedeFiltro
            ? ($sedes->firstWhere('id', $sedeFiltro)?->nombre ?? '')
            : 'Todas las sedes';
        $stockLabel = $this->stockLabel();
        $filtroBadge = $sedeLabel . ' · ' . $stockLabel;

        $labelsSedes = $stockPorSede->keys()->toArray();
        $dataSedes = $stockPorSede->values()->toArray();
        $labelsCategorias = $stockPorCategoria->keys()->toArray();
        $dataCategorias = $stockPorCategoria->values()->toArray();
        $labelsTop = $topProductos->map(fn ($row) => $row['producto']->nombre)->toArray();
        $dataTop = $topProductos->map(fn ($row) => (int) $row['total'])->toArray();

        // Payload único para @script → window.reporteData (los 6 charts + export)
        $charts = [
            'dataSedes'         => $dataSedes,
            'labelsSedes'       => $labelsSedes,
            'dataCategorias'    => $dataCategorias,
            'labelsCategorias'  => $labelsCategorias,
            'labelsTop'         => $labelsTop,
            'dataTop'           => $dataTop,
            'dataNivel'         => [$nivelOk, $nivelBajo, $nivelSin],
            'labelsNivel'       => ['Con stock', 'Stock bajo', 'Sin stock'],
            'labelsKitsEstado'  => $labelsKitsEstado,
            'dataKitsEstado'    => $dataKitsEstado,
            'labelsMovDias'     => $labelsMovDias,
            'dataEntradas'      => $dataEntradas,
            'dataSalidas'       => $dataSalidas,
            'exportPdfUrl'      => $this->exportPdfUrl(),
            'exportExcelUrl'    => $this->exportExcelUrl(),
            'filtroSedeLabel'   => $sedeLabel,
            'filtroStockLabel'  => $stockLabel,
            'filtroBadge'       => $filtroBadge,
        ];

        return view('livewire.almacen.reporte', [
            'sedes'             => $sedes,
            'distribucion'      => $distribucionFiltrada->values(),
            'stockBajo'         => $stockBajo,
            'totalItems'        => $totalItems,
            'productosConStock' => $productosConStock,
            'porcentajeStock'   => $porcentajeStock,
            'stockPorSede'      => $stockPorSede,
            'stockPorCategoria' => $stockPorCategoria,
            'labelsSedes'       => $labelsSedes,
            'dataSedes'         => $dataSedes,
            'labelsCategorias'  => $labelsCategorias,
            'dataCategorias'    => $dataCategorias,
            'charts'            => $charts,
            'kitsInstalados'    => $kitsInstalados,
            'valorTotal'        => $valorTotal,
            'movimientosRecientes' => $movimientosRecientes,
            'sedeLabel'         => $sedeLabel,
            'stockLabel'        => $stockLabel,
            'filtroBadge'       => $filtroBadge,
            'sedeStock'         => $sedeStock,
            'nivelOk'           => $nivelOk,
            'nivelBajo'         => $nivelBajo,
            'nivelSin'          => $nivelSin,
            'totalEntradas30'   => array_sum($dataEntradas),
            'totalSalidas30'    => array_sum($dataSalidas),
        ]);
    }
}
