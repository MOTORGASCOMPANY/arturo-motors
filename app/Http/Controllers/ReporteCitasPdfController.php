<?php

namespace App\Http\Controllers;

use App\Models\Cita;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class ReporteCitasPdfController extends Controller
{
    public function __invoke(Request $request)
    {
        $citas = Cita::with(['cliente', 'vehiculo', 'asesor'])
            ->buscar($request->search)
            ->estado($request->estado ?? 'todos')
            ->when($request->fechaInicio, fn ($q) => $q->whereDate('fecha_cita', '>=', $request->fechaInicio))
            ->when($request->fechaFin, fn ($q) => $q->whereDate('fecha_cita', '<=', $request->fechaFin))
            ->orderBy('fecha_cita', 'desc')
            ->get();

        $pdf = Pdf::loadView('pdfs.reportes.citas', [
            'citas' => $citas,
            'search' => $request->search,
            'estado' => $request->estado ?? 'todos',
            'fechaInicio' => $request->fechaInicio,
            'fechaFin' => $request->fechaFin,
        ])->setPaper('a4', 'landscape');

        return $pdf->download('reporte-citas-' . now()->format('Y-m-d-Hi') . '.pdf');
    }
}
