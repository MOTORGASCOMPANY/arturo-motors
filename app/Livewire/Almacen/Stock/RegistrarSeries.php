<?php

namespace App\Livewire\Almacen\Stock;

use App\Models\ItemSerializado;
use App\Models\Sede;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class RegistrarSeries extends Component
{
    public ?int $filtroSedeId = null;
    public string $busqueda = '';

    
    public bool $modalAbierto = false;
    public int $kitItemId = 0;
    public string $kitNombre = '';
    public array $itemsPendientes = [];
    
    
    
    
    
    
    
    

    public function mount()
    {
        $this->filtroSedeId = Sede::activas()->orderBy('id')->first()?->id;
    }

    
    public function getKitsPendientesProperty()
    {
        $sedeId = $this->filtroSedeId;

        
        return ItemSerializado::with('producto.categoria', 'sede')
            ->whereHas('producto.categoria', fn ($q) => $q->where('es_kit', true))
            ->whereHas('piezasEnKit', fn ($q) => $q->whereNull('serie'))
            ->when($sedeId, fn ($q) => $q->where('sede_id', $sedeId))
            ->when($this->busqueda, function ($q) {
                $q->whereHas('producto', fn ($p) => $p->where('nombre', 'like', "%{$this->busqueda}%"));
            })
            ->withCount(['piezasEnKit as total_piezas' => fn ($q) => $q])
            ->withCount(['piezasEnKit as piezas_con_serie' => fn ($q) => $q->whereNotNull('serie')])
            ->orderByDesc('created_at')
            ->get();
    }

    
    public function abrirModal(int $kitItemId)
    {
        $kit = ItemSerializado::with('producto.categoria')->find($kitItemId);
        if (!$kit) return;

        $this->kitItemId = $kitItemId;
        $this->kitNombre = $kit->producto->nombre . ' #' . $kit->id;

        
        $hijos = ItemSerializado::with('producto.categoria')
            ->where('kit_padre_id', $kitItemId)
            ->orderBy('producto_id')
            ->get();

        $this->itemsPendientes = $hijos->map(function ($hijo) {
            $esSerializado = $hijo->producto->categoria->es_serializado ?? false;

            return [
                'item_id'         => $hijo->id,
                'producto_nombre' => $hijo->producto->nombre,
                'es_serializado'  => $esSerializado,
                'serie_actual'    => $hijo->serie,
                'serie_nueva'     => $hijo->serie ?? '',
                'cantidad'        => 1,
                'estado'          => $hijo->estado,
            ];
        })->toArray();

        $this->modalAbierto = true;
    }

    
    public function cerrarModal()
    {
        $this->modalAbierto = false;
        $this->itemsPendientes = [];
        $this->kitItemId = 0;
        $this->kitNombre = '';
    }

    
    public function guardarSeries()
    {
        $itemsARegistrar = array_filter($this->itemsPendientes, function ($item) {
            return $item['es_serializado'] && !empty(trim($item['serie_nueva']));
        });

        if (empty($itemsARegistrar)) {
            $this->dispatch('minToast', titulo: 'Atención', mensaje: 'No ingresaste ninguna serie.', icono: 'warning');
            return;
        }

        
        $seriesIngresadas = [];
        foreach ($itemsARegistrar as $item) {
            $serie = strtoupper(trim($item['serie_nueva']));
            if (in_array($serie, $seriesIngresadas)) {
                $this->dispatch('minToast', titulo: 'Serie duplicada', mensaje: "El serie \"{$serie}\" está repetido.", icono: 'error');
                return;
            }
            $seriesIngresadas[] = $serie;
        }

        
        foreach ($itemsARegistrar as $item) {
            $existe = ItemSerializado::where('id', '!=', $item['item_id'])
                ->where('serie', trim($item['serie_nueva']))
                ->exists();
            if ($existe) {
                $this->dispatch('minToast', titulo: 'Serie ya existe', mensaje: "El serie \"{$item['serie_nueva']}\" ya está registrado.", icono: 'error');
                return;
            }
        }

        try {
            DB::transaction(function () use ($itemsARegistrar) {
                foreach ($itemsARegistrar as $itemData) {
                    $item = ItemSerializado::find($itemData['item_id']);
                    if ($item) {
                        $item->update([
                            'serie' => trim($itemData['serie_nueva']),
                            'atributos' => array_merge($item->atributos ?? [], [
                                'serie_registrada_por'  => Auth()->id(),
                                'serie_registrada_en'   => now()->toDateTimeString(),
                            ]),
                        ]);
                    }
                }
            });
        } catch (\Throwable $e) {
            report($e);
            $this->dispatch('minToast', titulo: 'Error', mensaje: 'Ocurrió un error al guardar.', icono: 'error');
            return;
        }

        $this->dispatch('minToast',
            titulo: '¡Series registradas!',
            mensaje: count($itemsARegistrar) . ' serie(s) registrada(s) correctamente.',
            icono: 'success'
        );

        $this->cerrarModal();
    }

    public function render()
    {
        return view('livewire.almacen.stock.registrar-series');
    }
}
