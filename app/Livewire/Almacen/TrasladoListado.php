<?php

namespace App\Livewire\Almacen;

use App\Models\Traslado;
use Livewire\Component;
use Livewire\WithPagination;

class TrasladoListado extends Component
{
    use WithPagination;

    public int $trasladoExpandido = 0;

    public function toggleDetalle(int $trasladoId)
    {
        $this->trasladoExpandido = $this->trasladoExpandido === $trasladoId ? 0 : $trasladoId;
    }

    public function render()
    {
        $base = Traslado::query();

        $totalTraslados = (clone $base)->count();
        $ultimos30 = (clone $base)->where('created_at', '>=', now()->subDays(30))->count();
        $kitsIncompletos = (clone $base)->where('es_kit_completo', false)->count();

        $traslados = Traslado::with(['sedeDestino', 'enviadoPor', 'detalles.producto', 'detalles.itemSerializado'])
            ->orderByDesc('created_at')
            ->paginate(10);

        // N.° correlativo global (1..N) teniendo en cuenta la página actual.
        $primerNro = $traslados->firstItem() ?? 1;
        $traslados->setCollection(
            $traslados->getCollection()->map(fn ($t, $i) => tap($t, function ($t) use ($i, $primerNro) {
                $t->nro = $primerNro + $i;
            }))
        );

        return view('livewire.almacen.traslado-listado', compact(
            'traslados',
            'totalTraslados',
            'ultimos30',
            'kitsIncompletos'
        ));
    }
}