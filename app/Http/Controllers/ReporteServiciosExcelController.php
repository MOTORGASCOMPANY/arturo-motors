<?php

namespace App\Http\Controllers;

use App\Exports\ServiciosExport;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class ReporteServiciosExcelController extends Controller
{
    public function __invoke(Request $request)
    {
        return Excel::download(
            new ServiciosExport(
                $request->desde ?? now()->startOfMonth()->format('Y-m-d'),
                $request->hasta ?? now()->format('Y-m-d'),
                $request->tipoServicio ?? 'todos'
            ),
            'reporte-servicios-' . now()->format('Y-m-d-Hi') . '.xlsx'
        );
    }
}