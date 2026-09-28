<?php

namespace App\Http\Controllers;

use App\Exports\AlmacenExport;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class ReporteAlmacenExcelController extends Controller
{
    public function __invoke(Request $request)
    {
        $filtroSede = $request->input('sede_id') ? (int) $request->input('sede_id') : null;

        return Excel::download(
            new AlmacenExport($filtroSede),
            'reporte-almacen-' . now()->format('Y-m-d-Hi') . '.xlsx'
        );
    }
}