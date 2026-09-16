<?php

namespace App\Livewire\Almacen\Kits;

use App\Models\ItemSerializado;
use App\Models\Producto;
use App\Models\MovimientoStock;
use App\Models\Sede;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Completar extends Component
{
    public ?int $kitItemId = null;
    public ?ItemSerializado $kit = null;

    // Componentes faltantes que se van agregando
    public array $faltantes = [];
    // Estructura: [['producto_id' => X, 'cantidad' => 1, 'serie' => '']]

    // Búsqueda de piezas disponibles
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

        // Cargar componentes faltantes del kit
        $this->cargarFaltantes();
    }

    public function cargarFaltantes()
    {
        if (!$this->kit) return;

        // Obtener componentes que debería tener el kit
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

        // Obtener componentes que ya tiene el kit (como hijos)
        $componentesActuales = ItemSerializado::where('kit_padre_id', $this->kit->id)
            ->pluck('producto_id')
            ->toArray();

        // Los que faltan son los que no están como hijos
        $this->faltantes = [];
        foreach ($componentesEsperados as $comp) {
            if (!in_array($comp->producto_id, $componentesActuales)) {
                $this->faltantes[] = [
                    'producto_id' => $comp->producto_id,
                    'nombre' => $comp->nombre,
                    'es_serializado' => $comp->es_serializado,
                    'cantidad' => $comp->cantidad_esperada,
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

        // Buscar piezas serializadas disponibles
        $this->piezasEncontradas = ItemSerializado::with('producto.categoria')
            ->where('estado', 'en_stock')
            ->where('sede_id', $sedeId)
            ->where('id', '!=', $this->kit->id) // no a sí mismo
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
        // Verificar si ya está en faltantes
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

        // Validar series para serializados
        foreach ($agregados as $faltante) {
            if ($faltante['es_serializado'] && empty(trim($faltante['serie']))) {
                $this->addError('general', "Debes registrar la serie de: {$faltante['nombre']}");
                return;
            }
        }

        $sedeId = $this->sedePrincipalId();

        try {
            DB::transaction(function () use ($agregados, $sedeId) {
                foreach ($agregados as $faltante) {
                    if ($faltante['es_serializado']) {
                        // Crear item serializado como hijo del kit
                        ItemSerializado::create([
                            'producto_id' => $faltante['producto_id'],
                            'kit_padre_id' => $this->kit->id,
                            'serie' => trim($faltante['serie']),
                            'atributos' => [
                                'agregado_a_kit' => true,
                                'fecha' => now()->toDateString(),
                            ],
                            'estado' => 'en_stock',
                            'sede_id' => $sedeId,
                        ]);

                        // Registrar movimiento de entrada
                        $producto = Producto::find($faltante['producto_id']);
                        if ($producto) {
                            MovimientoStock::registrar(
                                $producto, 'entrada', 1, null, Auth::id(),
                                "Agregado serializado al kit {$this->kit->serie}", $sedeId
                            );
                        }
                    } else {
                        // Registrar movimiento de entrada
                        $producto = Producto::find($faltante['producto_id']);
                        if ($producto) {
                            MovimientoStock::registrar(
                                $producto,
                                'entrada',
                                $faltante['cantidad'],
                                null,
                                Auth::id(),
                                "Agregado al kit {$this->kit->serie}",
                                $sedeId
                            );
                        }
                    }
                }
            });
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
