<?php

namespace App\Http\Controllers;

use App\Exports\GanttExport;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class GanttController extends Controller
{
    public function __invoke(Request $request)
    {
        return Excel::download(
            new GanttExport(),
            'diagrama-gantt-arturo-motors-' . now()->format('Y-m-d') . '.xlsx'
        );
    }
}
