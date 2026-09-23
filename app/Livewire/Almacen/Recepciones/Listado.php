<?php

namespace App\Livewire\Almacen\Recepciones;

use App\Models\ItemSerializado;
use App\Models\MovimientoStock;
use Livewire\Component;
use Livewire\WithPagination;

class Listado extends Component
{
    use WithPagination;

    public string $search = '';
    public string $filtroTipo = 'todos';

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatedFiltroTipo(): void
    {
        $this->resetPage();
    }

    private function queryItemsSerializados()
    {
        return ItemSerializado::with('producto.categoria', 'sede')
            ->whereNull('kit_padre_id')
            ->when($this->filtroTipo === 'kits', fn ($q) => $q->whereHas('producto.categoria', fn ($c) => $c->where('es_kit', true)))
            ->when($this->filtroTipo === 'serializados', fn ($q) => $q->whereHas('producto.categoria', fn ($c) => $c->where('es_kit', false)->where('es_serializado', true)))
            ->when($this->search, fn ($q) => $q->whereHas('producto', fn ($p) => $p->where('nombre', 'like', "%{$this->search}%")))
            ->orderByDesc('created_at')
            ->get()
            ->map(fn ($item) => [
                'tipo' => $item->producto->categoria->es_kit ? 'kit' : 'serializado',
                'tipo_label' => $item->producto->categoria->es_kit ? 'Kit' : 'Serializado',
                'tipo_color' => $item->producto->categoria->es_kit ? 'bg-indigo-100 text-indigo-700' : 'bg-green-100 text-green-700',
                'nombre' => $item->producto->nombre,
                'sede' => $item->sede?->nombre ?? '—',
                'estado' => $item->estado,
                'fecha' => $item->created_at,
                // ID real de inventario (mismo #1033 de Productos)
                'id' => $item->id,
                'origen' => 'items_serializados',
            ]);
    }

    private function queryMovimientos()
    {
        return MovimientoStock::with('producto', 'sede')
            ->where('tipo', 'entrada')
            ->when(in_array($this->filtroTipo, ['kits', 'serializados']), fn ($q) => $q->whereRaw('0=1'))
            ->when($this->search, fn ($q) => $q->whereHas('producto', fn ($p) => $p->where('nombre', 'like', "%{$this->search}%")))
            ->orderByDesc('created_at')
            ->get()
            ->map(fn ($mov) => [
                'tipo' => 'cantidad',
                'tipo_label' => 'Por cantidad',
                'tipo_color' => 'bg-amber-100 text-amber-700',
                'nombre' => $mov->producto->nombre,
                'sede' => $mov->sede?->nombre ?? '—',
                'estado' => null,
                'cantidad' => $mov->cantidad,
                'fecha' => $mov->created_at,
                // ID real del ledger de movimientos
                'id' => $mov->id,
                'origen' => 'movimientos_stock',
            ]);
    }

    public function render()
    {
        $items = $this->queryItemsSerializados();
        $movimientos = $this->queryMovimientos();
        $all = $items->concat($movimientos)->sortByDesc('fecha')->values();

        // Correlativo 1..N solo para orden visual (N.° de historial).
        // El ID de columna SIEMPRE es el real de BD para casar con Inventario:
        // kit/serializado → items_serializados.id (#1033), cantidad → movimientos_stock.id.
        $all = $all->map(fn ($row, $i) => array_merge($row, ['nro' => $i + 1]))->values();

        $perPage = 15;
        $total = $all->count();
        $lastPage = max(1, (int) ceil($total / $perPage));

        // Clamp: evita página vacía cuando ?page=N queda fuera de rango
        // (filtro redujo resultados, datos borrados, o URL tipeada a mano).
        // Livewire 3 guarda el estado en paginators['page'], NO en $this->page.
        $page = (int) $this->getPage();
        if ($page < 1) {
            $page = 1;
        } elseif ($page > $lastPage) {
            $page = $lastPage;
        }
        if ((int) ($this->paginators['page'] ?? 0) !== $page) {
            $this->paginators['page'] = $page;
        }

        $paginated = $all->slice(($page - 1) * $perPage, $perPage)->values();

        $paginator = new \Illuminate\Pagination\LengthAwarePaginator(
            $paginated,
            $total,
            $perPage,
            $page,
            [
                'path' => route('almacen.recepciones.listado'),
                'pageName' => 'page',
                'query' => array_filter(
                    request()->query(),
                    fn ($k) => $k !== 'page',
                    ARRAY_FILTER_USE_KEY
                ),
            ]
        );

        return view('livewire.almacen.recepciones.listado', [
            'recepciones' => $paginator,
            'conteos' => [
                'kits' => $items->where('tipo', 'kit')->count(),
                'serializados' => $items->where('tipo', 'serializado')->count(),
                'cantidad' => $movimientos->count(),
            ],
        ]);
    }
}
