<?php

namespace App\Livewire\Almacen\Kits;

use App\Models\ItemSerializado;
use App\Models\Producto;
use App\Models\MovimientoStock;
use App\Models\ProductoStockSede;
use App\Models\Sede;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Completar extends Component
{
    public ?int $kitItemId = null;
    public ?ItemSerializado $kit = null;

    
    public array $faltantes = [];
    

    
    public string $buscarPieza = '';
    public array $piezasEncontradas = [];

    protected function sedePrincipalId(): int
    {
        return Sede::activas()->orderBy('id')->first()?->id ?? 1;
    }

    public function mount(int $kitItemId)
    {
        $this->kitItemId = $kitItemId;
        $this->kit = ItemSerializado::with('producto.categoria')->find($kitItemId);

        if (!$this->kit || !$this->kit->producto->categoria->es_kit) {
            abort(404, 'Kit no encontrado.');
        }

        
        $this->cargarFaltantes();
    }

    public function cargarFaltantes()
    {
        if (!$this->kit) return;

        
        $componentesEsperados = DB::table('kit_componentes')
            ->join('productos', 'producto_componente_id', '=', 'productos.id')
            ->join('categorias_almacen', 'productos.categoria_id', '=', 'categorias_almacen.id')
            ->where('producto_kit_id', $this->kit->producto_id)
            ->select(
                'productos.id as producto_id',
                'productos.nombre',
                'categorias_almacen.es_serializado',
                'kit_componentes.cantidad_esperada'
            )
            ->get();

        
        $componentesActuales = ItemSerializado::where('kit_padre_id', $this->kit->id)
            ->pluck('producto_id')
            ->countBy()
            ->toArray();

        $this->faltantes = [];
        foreach ($componentesEsperados as $comp) {
            $presentes = $componentesActuales[$comp->producto_id] ?? 0;
            $faltan = max(0, (int) $comp->cantidad_esperada - $presentes);

            if ($faltan > 0) {
                $this->faltantes[] = [
                    'producto_id' => $comp->producto_id,
                    'nombre' => $comp->nombre,
                    'es_serializado' => $comp->es_serializado,
                    'cantidad' => $faltan,
                    'serie' => '',
                    'agregado' => false,
                ];
            }
        }
    }

    public function updatedBuscarPieza()
    {
        $termino = trim($this->buscarPieza);
        if (strlen($termino) < 2) {
            $this->piezasEncontradas = [];
            return;
        }

        $sedeId = $this->sedePrincipalId();

        
        $this->piezasEncontradas = ItemSerializado::with('producto.categoria')
            ->where('estado', 'en_stock')
            ->where('sede_id', $sedeId)
            ->whereNull('kit_padre_id')
            ->where('id', '!=', $this->kit->id) 
            ->where(function ($q) use ($termino) {
                $q->where('serie', 'like', "%{$termino}%")
                  ->orWhereHas('producto', fn ($p) => $p->where('nombre', 'like', "%{$termino}%"));
            })
            ->limit(10)
            ->get()
            ->toArray();
    }

    public function agregarPiezaDisponible(int $productoId, ?string $serie = null)
    {
        
        foreach ($this->faltantes as &$faltante) {
            if ($faltante['producto_id'] == $productoId && !$faltante['agregado']) {
                $faltante['agregado'] = true;
                if ($serie) {
                    $faltante['serie'] = $serie;
                }
                break;
            }
        }
        unset($faltante);

        $this->piezasEncontradas = [];
        $this->buscarPieza = '';
    }

    public function quitarFaltante(int $index)
    {
        $this->faltantes[$index]['agregado'] = false;
        $this->faltantes[$index]['serie'] = '';
    }

    public function guardar()
    {
        if (!$this->kit) return;

        $agregados = array_filter($this->faltantes, fn ($f) => $f['agregado']);
        if (empty($agregados)) {
            $this->dispatch('minToast', titulo: 'Atención', mensaje: 'No se agregó ninguna pieza.', icono: 'warning');
            return;
        }

        
        foreach ($agregados as $faltante) {
            if ($faltante['es_serializado'] && empty(trim($faltante['serie']))) {
                $this->addError('general', "Debes registrar la serie de: {$faltante['nombre']}");
                return;
            }
        }

        $sedeId = $this->sedePrincipalId();

        try {
            DB::transaction(function () use ($agregados, $sedeId) {
                $kit = ItemSerializado::where('id', $this->kit->id)
                    ->whereIn('estado', ['en_stock', 'abierto'])
                    ->lockForUpdate()
                    ->first();

                if (!$kit) {
                    throw new \RuntimeException('El kit ya no está disponible.');
                }

                foreach ($agregados as $faltante) {
                    $productoId = $faltante['producto_id'];

                    if ($faltante['es_serializado']) {
                        $serie = trim($faltante['serie']);
                        $existente = ItemSerializado::where('serie', $serie)
                            ->lockForUpdate()
                            ->first();

                        if ($existente) {
                            // Pieza ya registrada: solo vincularla al kit (sin entrada:
                            // el stock ya fue contabilizado al recibirla).
                            $puedeVincular = (int) $existente->producto_id === (int) $productoId
                                && (int) $existente->sede_id === (int) $sedeId
                                && $existente->estado === 'en_stock'
                                && $existente->kit_padre_id === null;

                            if (!$puedeVincular) {
                                throw new \RuntimeException("La serie {$serie} ya está registrada y no está disponible para vincular.");
                            }

                            $existente->update(['kit_padre_id' => $kit->id]);
                        } else {
                            // Pieza NUEVA que llega con el kit: entrada + hijo.
                            ItemSerializado::create([
                                'producto_id' => $productoId,
                                'kit_padre_id' => $kit->id,
                                'serie' => $serie,
                                'atributos' => [
                                    'agregado_a_kit' => true,
                                    'fecha' => now()->toDateString(),
                                ],
                                'estado' => 'en_stock',
                                'sede_id' => $sedeId,
                            ]);

                            $producto = Producto::find($productoId);
                            if ($producto) {
                                MovimientoStock::registrar(
                                    $producto, 'entrada', 1, null, Auth::id(),
                                    "Agregado serializado al kit {$kit->serie}", $sedeId
                                );
                            }
                        }
                    } else {
                        $cantidad = (int) $faltante['cantidad'];

                        // Cantidad: validar stock suelto, crear hijos SIN entrada ni
                        // decremento (la salida recién ocurre en finalizar()).
                        $stock = ProductoStockSede::where('producto_id', $productoId)
                            ->where('sede_id', $sedeId)
                            ->lockForUpdate()
                            ->first();

                        $total = $stock ? (int) $stock->cantidad : 0;
                        $enKits = ItemSerializado::where('producto_id', $productoId)
                            ->whereNotNull('kit_padre_id')
                            ->whereIn('estado', ['en_stock', 'abierto', 'completado', 'asignado'])
                            ->where('sede_id', $sedeId)
                            ->count();
                        $suelto = max(0, $total - $enKits);

                        if ($suelto < $cantidad) {
                            throw new \RuntimeException(
                                "Stock insuficiente para {$faltante['nombre']}: necesitás {$cantidad}, hay {$suelto}."
                            );
                        }

                        for ($j = 0; $j < $cantidad; $j++) {
                            ItemSerializado::create([
                                'producto_id' => $productoId,
                                'kit_padre_id' => $kit->id,
                                'serie' => null,
                                'atributos' => [
                                    'tipo' => 'cantidad',
                                    'agregado_a_kit' => true,
                                    'fecha' => now()->toDateString(),
                                ],
                                'estado' => 'en_stock',
                                'sede_id' => $sedeId,
                            ]);
                        }
                    }
                }
            });
        } catch (\RuntimeException $e) {
            $this->dispatch('minToast', titulo: 'Atención', mensaje: $e->getMessage(), icono: 'warning');
            return;
        } catch (\Throwable $e) {
            report($e);
            $this->dispatch('minToast', titulo: 'Error', mensaje: 'Ocurrió un error al guardar.', icono: 'error');
            return;
        }

        $this->dispatch('minToast', titulo: '¡Kit completado!', mensaje: 'Se agregaron ' . count($agregados) . ' pieza(s) al kit.', icono: 'success');
        $this->redirect(route('almacen.stock'));
    }

    public function render()
    {
        return view('livewire.almacen.kits.completar');
    }
}
