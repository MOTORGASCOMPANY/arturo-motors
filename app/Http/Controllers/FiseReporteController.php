<?php

namespace App\Http\Controllers;

use App\Exports\FiseReporteExport;
use App\Services\FiseReporteService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class FiseReporteController extends Controller
{
    public function pdf(Request $request, FiseReporteService $servicio)
    {
        $datos = $servicio->datos($request->query('desde'), $request->query('hasta'));

        $pdf = Pdf::loadView('pdfs.fise-reporte', $datos);

        return $pdf->download('reporte-fise-' . now()->format('Y-m-d-His') . '.pdf');
    }

    public function excel(Request $request, FiseReporteService $servicio)
    {
        $datos = $servicio->datos($request->query('desde'), $request->query('hasta'));

        $spreadsheet = (new FiseReporteExport($datos))->spreadsheet();

        return response()->streamDownload(
            function () use ($spreadsheet) {
                (new Xlsx($spreadsheet))->save('php://output');
            },
            'reporte-fise-' . now()->format('Y-m-d-His') . '.xlsx',
            ['Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet']
        );
    }
}
