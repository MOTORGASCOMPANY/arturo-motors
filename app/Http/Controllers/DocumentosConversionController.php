<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ServiceOrder;
use App\Support\ChecklistEvaluacion;
use Barryvdh\DomPDF\Facade\Pdf;

class DocumentosConversionController extends Controller
{
    public function evaluacion(int $ordenId)
    {
        $orden = ServiceOrder::with(['cliente', 'vehiculo', 'service', 'evaluadoPor'])->findOrFail($ordenId);

        abort_if(is_null($orden->checklist_evaluacion), 404, 'Esta orden aún no tiene evaluación registrada.');

        $pdf = Pdf::loadView('pdfs.evaluacion', [
            'orden' => $orden,
            'checklistGrupos' => ChecklistEvaluacion::grupos(),
        ]);

        return $pdf->stream('evaluacion-orden-' . $orden->id . '.pdf');
    }

    public function fichaTecnica(int $ordenId)
    {
        $orden = ServiceOrder::with([
            'cliente', 'vehiculo', 'service', 'tecnico',
            'items.producto.categoria', 'movimientosStock.producto',
        ])->findOrFail($ordenId);

        abort_if($orden->items->isEmpty(), 404, 'Esta orden aún no tiene equipos asignados.');

        $pdf = Pdf::loadView('pdfs.ficha-tecnica', ['orden' => $orden]);

        return $pdf->stream('ficha-tecnica-orden-' . $orden->id . '.pdf');
    }

    public function garantia(int $ordenId)
    {
        $orden = ServiceOrder::with(['cliente', 'vehiculo', 'service', 'items.producto.categoria'])
            ->findOrFail($ordenId);

        abort_unless(in_array($orden->estado, ['entregado', 'entregada']), 404,
            'La garantía solo puede emitirse una vez entregado el vehículo.');

        $pdf = Pdf::loadView('pdfs.garantia', ['orden' => $orden]);

        return $pdf->stream('garantia-orden-' . $orden->id . '.pdf');
    }
}
