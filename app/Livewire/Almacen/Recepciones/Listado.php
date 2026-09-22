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
                'id' => $item->id,
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
                'id' => $mov->id,
            ]);
    }

    public function render()
    {
        $items = $this->queryItemsSerializados();
        $movimientos = $this->queryMovimientos();
        $all = $items->concat($movimientos)->sortByDesc('fecha')->values();

        $perPage = 15;
        $page = $this->page ?? 1;
        $paginated = $all->slice(($page - 1) * $perPage, $perPage)->values();

        return view('livewire.almacen.recepciones.listado', [
            'recepciones' => new \Illuminate\Pagination\LengthAwarePaginator(
                $paginated,
                $all->count(),
                $perPage,
                $page,
                ['path' => route('almacen.recepciones.listado')]
            ),
            'conteos' => [
                'kits' => $items->where('tipo', 'kit')->count(),
                'serializados' => $items->where('tipo', 'serializado')->count(),
                'cantidad' => $movimientos->count(),
            ],
        ]);
    }
}
