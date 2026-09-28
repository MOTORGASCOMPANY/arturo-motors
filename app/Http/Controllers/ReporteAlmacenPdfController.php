<?php

namespace App\Http\Controllers;

use App\Models\Producto;
use App\Models\Sede;
use App\Models\ItemSerializado;
use App\Models\KitComponente;
use App\Models\CategoriaAlmacen;
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

        // ── KPIs (mismo cálculo que el dashboard) ─────────────────────
        $sedeId = $filtroSede ?? 1;

        // 1. Capacidad de armado
        $componentes = KitComponente::whereHas('componente.categoria', fn ($q) => $q->where('es_kit', false)
            ->where('es_serializado', false))
            ->with('componente.categoria')
            ->get()
            ->pluck('componente')
            ->unique('id')
            ->values();

        $disponibles = $componentes->mapWithKeys(function ($p) use ($sedeId) {
            return [$p->nombre => $p->stockSueltoEnSede($sedeId)];
        });

        if ($disponibles->isEmpty()) {
            $capacidad = ['kitsArmables' => 0, 'cuello' => 'Sin receta', 'barras' => []];
        } else {
            $kitsArmables = $disponibles->min();
            $cuello = $disponibles->search($kitsArmables);
            $barras = $disponibles->map(fn ($c, $n) => ['nombre' => $n, 'cantidad' => $c, 'esCuello' => $c === $kitsArmables])->values()->toArray();
            $capacidad = compact('kitsArmables', 'cuello', 'barras');
        }

        // 2. Tasa de consumo
        $sellados = ItemSerializado::whereHas('producto.categoria', fn ($q) => $q->where('es_kit', true))
            ->whereIn('estado', ['en_stock', 'completado'])
            ->when($sedeId, fn ($q) => $q->where('sede_id', $sedeId))
            ->count();

        $consumidos = ItemSerializado::whereHas('producto.categoria', fn ($q) => $q->where('es_kit', true))
            ->whereIn('estado', ['consumido', 'instalado'])
            ->when($sedeId, fn ($q) => $q->where('sede_id', $sedeId))
            ->count();

        $totalKits = $sellados + $consumidos;
        $tasaPct = $totalKits > 0 ? round(($consumidos / $totalKits) * 100) : 0;
        $tasa = ['porcentaje' => $tasaPct, 'sellados' => $sellados, 'consumidos' => $consumidos];

        // 3. Trazabilidad
        $conSerie = ItemSerializado::whereHas('producto.categoria', fn ($q) => $q->where('es_serializado', true))
            ->where('estado', 'en_stock')
            ->whereNull('kit_padre_id')
            ->when($sedeId, fn ($q) => $q->where('sede_id', $sedeId))
            ->get();

        $totalSerie = $conSerie->count();
        $conProduce = $conSerie->filter(fn ($i) => !empty($i->atributos['produce']))->count();
        $trazabilidadPct = $totalSerie > 0 ? round(($conProduce / $totalSerie) * 100) : 0;
        $pendientes = $conSerie->filter(fn ($i) => empty($i->atributos['produce']))->take(10)->values();
        $trazabilidad = ['porcentaje' => $trazabilidadPct, 'conProduce' => $conProduce, 'total' => $totalSerie, 'pendientes' => $pendientes];

        // 4. Alertas de stock
        $productosAlerta = Producto::where('activo', true)
            ->whereHas('categoria', fn ($q) => $q->where('es_kit', false))
            ->with('categoria')
            ->get();

        $sinStock = collect();
        $stockBajo = collect();

        foreach ($productosAlerta as $p) {
            $cant = $p->stockSueltoEnSede($sedeId);
            if ($cant === 0) {
                $sinStock->push(['nombre' => $p->nombre, 'cantidad' => 0]);
            } elseif ($p->stockBajoEnSede($sedeId)) {
                $stockBajo->push(['nombre' => $p->nombre, 'cantidad' => $cant]);
            }
        }
        $alertas = ['sinStock' => $sinStock->take(8)->values(), 'stockBajo' => $stockBajo->take(8)->values(), 'umbral' => 2];

        // ── Distribución (tabla principal) ─────────────────────────────
        $distribucion = $productos->map(function ($p) use ($sedes, $filtroSede) {
            $porSede = [];
            foreach ($sedes as $s) {
                if ($filtroSede && $s->id !== $filtroSede) {
                    $porSede[$s->id] = 0;
                } else {
                    $porSede[$s->id] = $p->stockSueltoEnSede($s->id);
                }
            }
            return [
                'producto' => $p,
                'por_sede' => $porSede,
                'total' => array_sum($porSede),
            ];
        })->filter(fn ($row) => $row['total'] > 0)->values();

        // Aplicar filtros adicionales
        if ($filtroSede) {
            $distribucion = $distribucion->filter(
                fn ($row) => $row['por_sede'][$filtroSede] > 0
            );
        }

        $distribucion = match ($filtroStock) {
            'con_stock' => $distribucion->filter(fn ($row) => $row['total'] > 0),
            'sin_stock' => $productos->map(function ($p) use ($sedes) {
                $porSede = [];
                foreach ($sedes as $s) {
                    $porSede[$s->id] = $p->stockSueltoEnSede($s->id);
                }
                return [
                    'producto' => $p,
                    'por_sede' => $porSede,
                    'total' => array_sum($porSede),
                ];
            })->filter(fn ($row) => $row['total'] === 0),
            'stock_bajo' => $distribucion->filter(fn ($row) => $row['producto']->stockBajoEnSede($filtroSede ?? 1)),
            default => $distribucion,
        };

        // Totales
        $totalItems = $distribucion->sum('total');
        $productosConStock = $distribucion->count();

        // Stock por sede (para gráfico resumen)
        $sedesParaStock = $filtroSede ? $sedes->where('id', $filtroSede) : $sedes;
        $stockPorSede = $sedesParaStock->mapWithKeys(function ($s) use ($productos) {
            $total = $productos->sum(fn ($p) => $p->stockSueltoEnSede($s->id));
            return [$s->nombre => $total];
        });

        // Stock por categoría
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
            // KPIs
            'capacidad' => $capacidad,
            'tasa' => $tasa,
            'trazabilidad' => $trazabilidad,
            'alertas' => $alertas,
            'sedeLabel' => $filtroSede ? $sedes->firstWhere('id', $filtroSede)?->nombre : 'Todas',
        ])->setPaper('a4', 'landscape');

        return $pdf->download('reporte-almacen-' . now()->format('Y-m-d-Hi') . '.pdf');
    }
}
