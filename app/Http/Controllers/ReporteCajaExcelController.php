<?php

namespace App\Http\Controllers;

use App\Exports\CajaExport;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class ReporteCajaExcelController extends Controller
{
    public function __invoke(Request $request)
    {
        return Excel::download(
            new CajaExport(
                $request->desde ?? now()->startOfMonth()->format('Y-m-d'),
                $request->hasta ?? now()->format('Y-m-d')
            ),
            'reporte-caja-' . now()->format('Y-m-d-Hi') . '.xlsx'
        );
    }
}