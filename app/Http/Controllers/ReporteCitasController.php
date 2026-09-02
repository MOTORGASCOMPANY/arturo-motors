<?php

namespace App\Http\Controllers;

use App\Exports\CitaExport;
use App\Models\Cita;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class ReporteCitasController extends Controller
{
    public function exportPdf(Request $request)
    {
        $citas = Cita::with(['cliente', 'vehiculo', 'asesor'])
            ->buscar($request->search)
            ->estado($request->estado ?? 'todos')
            ->when($request->fechaInicio, fn ($q) => $q->whereDate('fecha_cita', '>=', $request->fechaInicio))
            ->when($request->fechaFin, fn ($q) => $q->whereDate('fecha_cita', '<=', $request->fechaFin))
            ->orderBy('fecha_cita', 'desc')
            ->get();

        $pdf = Pdf::loadView('pdfs.reporte-citas', [
            'citas' => $citas,
            'search' => $request->search,
            'estado' => $request->estado ?? 'todos',
            'fechaInicio' => $request->fechaInicio,
            'fechaFin' => $request->fechaFin,
        ])->setPaper('a4', 'landscape');

        return $pdf->download('reporte-citas-' . now()->format('Y-m-d-Hi') . '.pdf');
    }

    public function exportExcel(Request $request)
    {
        return Excel::download(
            new CitaExport(
                $request->search,
                $request->estado ?? 'todos',
                $request->fechaInicio,
                $request->fechaFin
            ),
            'reporte-citas-' . now()->format('Y-m-d-Hi') . '.xlsx'
        );
    }
}
