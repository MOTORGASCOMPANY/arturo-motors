<?php

namespace App\Http\Controllers;

use App\Models\ServiceOrder;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;

class ComprobanteController extends Controller
{
    public function pdf(int $ordenId)
    {
        $orden = ServiceOrder::with(['cliente', 'vehiculo', 'service', 'comprobante'])->findOrFail($ordenId);

        // $pdf = Pdf::loadView('pdfs.comprobante', ['orden' => $orden]);
        $pdf = Pdf::loadView('pdfs.comprobante', ['orden' => $orden])->setOption(['isRemoteEnabled' => true, 'isHtml5ParserEnabled' => true]);

        $path = 'comprobantes/' . $orden->comprobante->folio . '.pdf';

        // Storage::disk('public')->put() crea automáticamente las subcarpetas si no existen
        Storage::disk('public')->put($path, $pdf->output());

        $orden->comprobante->update(['pdf_path' => $path]);

        return $pdf->stream('comprobante-' . $orden->comprobante->folio . '.pdf');
    }
}
