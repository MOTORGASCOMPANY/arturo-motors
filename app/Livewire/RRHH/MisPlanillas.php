<?php

namespace App\Livewire\RRHH;

use Livewire\Component;
use App\Models\Planilla;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\On;

class MisPlanillas extends Component
{
    public function descargar($ruta, $nombre)
    {
        return Storage::disk('public')->download($ruta, $nombre);
    }

    // Al escuchar el evento, Livewire ejecutará esta función y por ende el render() automáticamente
    #[On('refresh-planilla')]
    public function refresh()
    {
        // No necesita contenido, solo existir para que Livewire refresque el componente
    }

    public function render()
    {
        $planillas = Planilla::whereHas('contrato', function ($query) {
            $query->where('user_id', Auth::id());
        })
            ->with(['archivos'])
            ->orderBy('periodo', 'desc')
            ->get()
            ->groupBy(function ($item) {
                return $item->periodo->format('Y'); // Agrupar por Año
            })
            ->map(function ($yearGroup) {
                return $yearGroup->groupBy(function ($item) {
                    return $item->periodo->translatedFormat('F'); // Agrupar por Mes
                });
            });

        return view('livewire.r-r-h-h.mis-planillas', compact('planillas'));
    }
}
