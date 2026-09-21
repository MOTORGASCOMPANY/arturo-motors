<?php

namespace App\Http\Controllers;

use App\Exports\ConversionesExport;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class ReporteConversionesExcelController extends Controller
{
    public function __invoke(Request $request)
    {
        $filtros = array_filter([
            'sede_id' => $request->input('sede_id'),
            'estado' => $request->input('estado'),
            'desde' => $request->input('desde'),
            'hasta' => $request->input('hasta'),
        ]);

        return Excel::download(
            new ConversionesExport($filtros),
            'reporte-conversiones-' . now()->format('Y-m-d-Hi') . '.xlsx'
        );
    }
}
