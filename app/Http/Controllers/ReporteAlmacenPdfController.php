<?php

namespace App\Http\Controllers;

use App\Models\Producto;
use App\Models\Sede;
use App\Models\ItemSerializado;
use App\Models\KitComponente;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class ReporteAlmacenPdfController extends Controller
{
    public function __invoke(Request $request)
    {
        $sedes = Sede::activas()->orderBy('id')->get();
        $productos = Producto::with('categoria')->where('activo', true)->get();

        // Filtros
        $filtroSede = $request->input('sede_id') ? (int) $request->input('sede_id') : null;
        $filtroStock = $request->input('stock', 'todos');
        $sedeId = $filtroSede; // null = todas las sedes (mismo criterio que el dashboard)

        // ── 1. Capacidad de armado ────────────────────────────────
        $componentes = KitComponente::whereHas('componente.categoria', fn ($q) => $q->where('es_kit', false)
            ->where('es_serializado', false))
            ->with('componente.categoria')
            ->get()
            ->pluck('componente')
            ->unique('id')
            ->values();

        $disponibles = $componentes->mapWithKeys(fn ($p) => [$p->nombre => $p->stockSueltoEnSede($sedeId)]);

        if ($disponibles->isEmpty()) {
            $capacidad = ['kitsArmables' => 0, 'cuello' => 'Sin receta', 'barras' => []];
        } else {
            $kitsArmables = $disponibles->min();
            $cuello = $disponibles->search($kitsArmables);
            $barras = $disponibles
                ->map(fn ($c, $n) => ['nombre' => $n, 'cantidad' => $c, 'esCuello' => $c === $kitsArmables])
                ->values()->toArray();
            $capacidad = compact('kitsArmables', 'cuello', 'barras');
        }

        // ── 2. Tasa de consumo ────────────────────────────────────
        $sellados = ItemSerializado::whereHas('producto.categoria', fn ($q) => $q->where('es_kit', true))
            ->whereIn('estado', ['en_stock', 'completado'])
            ->when($sedeId, fn ($q) => $q->where('sede_id', $sedeId))
            ->count();

        $consumidos = ItemSerializado::whereHas('producto.categoria', fn ($q) => $q->where('es_kit', true))
            ->whereIn('estado', ['consumido', 'instalado'])
            ->when($sedeId, fn ($q) => $q->where('sede_id', $sedeId))
            ->count();

        $totalKits = $sellados + $consumidos;
        $tasa = [
            'porcentaje' => $totalKits > 0 ? round(($consumidos / $totalKits) * 100) : 0,
            'sellados' => $sellados,
            'consumidos' => $consumidos,
        ];

        // ── 3. Trazabilidad ───────────────────────────────────────
        $conSerie = ItemSerializado::whereHas('producto.categoria', fn ($q) => $q->where('es_serializado', true))
            ->where('estado', 'en_stock')
            ->whereNull('kit_padre_id')
            ->when($sedeId, fn ($q) => $q->where('sede_id', $sedeId))
            ->get();

        $totalSerie = $conSerie->count();
        $conProduce = $conSerie->filter(fn ($i) => !empty($i->atributos['produce']))->count();
        $trazabilidad = [
            'porcentaje' => $totalSerie > 0 ? round(($conProduce / $totalSerie) * 100) : 0,
            'conProduce' => $conProduce,
            'total' => $totalSerie,
            'pendientes' => $conSerie->filter(fn ($i) => empty($i->atributos['produce']))->take(10)->values(),
        ];

        // ── 4. Alertas de stock ───────────────────────────────────
        $productosAlerta = Producto::where('activo', true)
            ->whereHas('categoria', fn ($q) => $q->where('es_kit', false))
            ->with('categoria')
            ->get();

        $sinStock = collect();
        $stockBajo = collect();

        foreach ($productosAlerta as $p) {
            if ($p->stockSueltoEnSede($sedeId) === 0) {
                $sinStock->push(['nombre' => $p->nombre, 'cantidad' => 0]);
            } elseif ($p->stockBajoEnSede($sedeId)) {
                $stockBajo->push($p); // la vista lo usa como modelo
            }
        }

        // Los totales se calculan ANTES de limitar, para que el KPI no quede capado en 8.
        $alertas = [
            'sinStock' => $sinStock->take(8)->values(),
            'stockBajo' => $stockBajo->take(8)->values(),
            'totalSinStock' => $sinStock->count(),
            'totalStockBajo' => $stockBajo->count(),
            'umbral' => 2,
        ];

        // ── Distribución (tabla principal) ────────────────────────
        $filas = $productos->map(function ($p) use ($sedes, $filtroSede) {
            $porSede = [];
            foreach ($sedes as $s) {
                $porSede[$s->id] = (!$filtroSede || $s->id === $filtroSede)
                    ? $p->stockSueltoEnSede($s->id)
                    : 0;
            }
            return ['producto' => $p, 'por_sede' => $porSede, 'total' => array_sum($porSede)];
        });

        $distribucion = match ($filtroStock) {
            'sin_stock' => $filas->filter(fn ($r) => $r['total'] === 0),
            'stock_bajo' => $filas->filter(fn ($r) => $r['total'] > 0 && $r['producto']->stockBajoEnSede($filtroSede)),
            default => $filas->filter(fn ($r) => $r['total'] > 0), // 'todos' y 'con_stock'
        };
        $distribucion = $distribucion->values();

        // ── Totales y resúmenes ───────────────────────────────────
        $totalItems = $distribucion->sum('total');
        $productosConStock = $distribucion->where('total', '>', 0)->count();

        $sedesParaStock = $filtroSede ? $sedes->where('id', $filtroSede) : $sedes;
        $stockPorSede = $sedesParaStock->mapWithKeys(fn ($s) => [
            $s->nombre => $productos->sum(fn ($p) => $p->stockSueltoEnSede($s->id)),
        ]);

        $stockPorCategoria = $distribucion
            ->groupBy(fn ($row) => $row['producto']->categoria->nombre)
            ->map(fn ($grupo) => $grupo->sum('total'))
            ->sortByDesc(fn ($v) => $v);

        $pdf = Pdf::loadView('pdfs.reporte-almacen', [
            'sedes' => $sedes,
            'filtroSede' => $filtroSede,
            'filtroStock' => $filtroStock,
            'distribucion' => $distribucion,
            'stockBajo' => $stockBajo,
            'totalItems' => $totalItems,
            'productosConStock' => $productosConStock,
            'stockPorSede' => $stockPorSede,
            'stockPorCategoria' => $stockPorCategoria,
            'capacidad' => $capacidad,
            'tasa' => $tasa,
            'trazabilidad' => $trazabilidad,
            'alertas' => $alertas,
            'sedeLabel' => $filtroSede ? ($sedes->firstWhere('id', $filtroSede)?->nombre ?? 'Todas') : 'Todas',
        ])->setPaper('a4', 'landscape');

        return $pdf->download('reporte-almacen-' . now()->format('Y-m-d-Hi') . '.pdf');
    }
}