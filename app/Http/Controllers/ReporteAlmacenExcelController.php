<?php

namespace App\Http\Controllers;

use App\Exports\Inventario2026Export;
use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class ReporteAlmacenExcelController extends Controller
{
    public function __invoke(Request $request)
    {
        $filtroSede = $request->input('sede_id') ? (int) $request->input('sede_id') : null;

        $spreadsheet = (new Inventario2026Export($filtroSede))->spreadsheet();

        return response()->streamDownload(
            function () use ($spreadsheet) {
                (new Xlsx($spreadsheet))->save('php://output');
            },
            'inventario-2026-' . now()->format('Y-m-d-Hi') . '.xlsx',
            ['Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet']
        );
    }
}