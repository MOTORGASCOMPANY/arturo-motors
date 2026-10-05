<?php

namespace App\Http\Controllers;

use App\Models\Producto;
use App\Models\ItemSerializado;
use Illuminate\Support\Facades\DB;

class KitComponentesController extends Controller
{
    public function index(int $productoId)
    {
        $producto = Producto::find($productoId);

        if (!$producto || !$producto->categoria->es_kit) {
            return response()->json(['producto' => null, 'receta' => [], 'kits' => []]);
        }

        $receta = DB::table('kit_componentes')
            ->join('productos', 'producto_componente_id', '=', 'productos.id')
            ->join('categorias_almacen', 'productos.categoria_id', '=', 'categorias_almacen.id')
            ->where('producto_kit_id', $productoId)
            ->select(
                'productos.id as producto_id',
                'productos.nombre',
                'categorias_almacen.es_serializado',
                'kit_componentes.cantidad_esperada as cantidad'
            )
            ->orderBy('productos.nombre')
            ->get();

        $kitsItems = ItemSerializado::with(['sede', 'serviceOrder.tecnico'])
            ->where('producto_id', $productoId)
            ->whereNull('kit_padre_id')
            ->orderByDesc('created_at')
            ->get();

        $kitsData = $kitsItems->map(function ($kit) {
            $piezas = ItemSerializado::with(['producto', 'producto.categoria'])
                ->where('kit_padre_id', $kit->id)
                ->get();

            $serializados = $piezas->filter(fn($p) => !str_starts_with($p->serie ?? '', 'CANT-'))
                ->map(fn($p) => [
                    'id' => $p->id,
                    'nombre' => $p->producto?->nombre ?? '—',
                    'serie' => $p->serie ?? '—',
                    'estado' => $p->estado,
                    'atributos' => $p->atributos ?? [],
                ])->values();

            $cantidad = $piezas->filter(fn($p) => str_starts_with($p->serie ?? '', 'CANT-'))
                ->map(fn($p) => [
                    'id' => $p->id,
                    'nombre' => $p->producto?->nombre ?? '—',
                    'serie' => $p->serie ?? '—',
                    'cantidad' => $p->atributos['cantidad'] ?? 1,
                ])->values();

            return [
                'id' => $kit->id,
                'estado' => $kit->estado,
                'sede' => $kit->sede?->nombre ?? '—',
                'created_at' => $kit->created_at?->format('d/m/Y H:i'),
                'service_order_id' => $kit->service_order_id,
                'tecnico' => $kit->serviceOrder?->tecnico?->name ?? null,
                'serializados' => $serializados,
                'cantidad' => $cantidad,
            ];
        });

        return response()->json([
            'producto' => ['id' => $producto->id, 'nombre' => $producto->nombre],
            'receta' => $receta,
            'kits' => $kitsData,
        ]);
    }
}