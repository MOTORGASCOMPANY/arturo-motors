<?php

namespace App\Http\Controllers;

use App\Exports\CitaExport;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class ReporteCitasExcelController extends Controller
{
    public function __invoke(Request $request)
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
