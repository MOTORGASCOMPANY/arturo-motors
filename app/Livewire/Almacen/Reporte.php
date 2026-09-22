<?php

namespace App\Livewire\Almacen;

use App\Models\Producto;
use App\Models\Sede;
use App\Models\ItemSerializado;
use App\Models\KitComponente;
use App\Models\MovimientoStock;
use Livewire\Component;

class Reporte extends Component
{
    
    public ?int $filtroSede = null;
    public string $filtroStock = 'todos'; 

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

        
        $stockBajo = $productos->filter(fn ($p) => $p->stock_bajo);

        
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

        
        $distribucionFiltrada = $distribucion;

        
        if ($this->filtroSede) {
            $distribucionFiltrada = $distribucionFiltrada->filter(
                fn ($row) => $row['por_sede'][$this->filtroSede] > 0
            );
        }

        
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

        
        $stockPorSede = $sedes->mapWithKeys(function ($s) use ($productos) {
            $total = $productos->sum(fn ($p) => $p->stockEnSede($s->id));
            return [$s->nombre => $total];
        });

        
        $stockPorCategoria = $distribucionFiltrada
            ->groupBy(fn ($row) => $row['producto']->categoria->nombre)
            ->map(fn ($grupo) => $grupo->sum('total'))
            ->sortByDesc(fn ($v) => $v);

        
        $totalItems = $distribucionFiltrada->sum('total');
        $productosConStock = $distribucionFiltrada->filter(fn ($row) => $row['total'] > 0)->count();

        
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

                
                $seriales = $hijos->filter(fn ($h) => ($h->atributos['tipo'] ?? '') !== 'cantidad' && !empty($h->serie))
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

        
        $valorTotal = $distribucionFiltrada->sum(function ($row) {
            $precio = (float) ($row['producto']->precio_referencial ?? 0);
            return $row['total'] * $precio;
        });

        
        $movimientosRecientes = MovimientoStock::with(['producto', 'sede', 'usuario'])
            ->latest()
            ->take(15)
            ->get()
            ->map(fn ($m) => [
                'producto' => $m->producto->nombre ?? 'N/A',
                'sede' => $m->sede->nombre ?? 'N/A',
                'tipo' => $m->tipo,
                'cantidad' => $m->cantidad,
                'motivo' => $m->motivo,
                'usuario' => $m->usuario->name ?? 'N/A',
                'fecha' => $m->created_at->format('d/m/Y H:i'),
            ]);

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
            'valorTotal' => $valorTotal,
            'movimientosRecientes' => $movimientosRecientes,
        ]);
    }
}
