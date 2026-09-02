<?php

namespace App\Http\Controllers;

use App\Models\ServiceOrder;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;

class ComprobanteController extends Controller
{
    public function pdf(int $ordenId)
    {
        $orden = ServiceOrder::with(['cliente', 'vehiculo', 'service', 'comprobante'])->findOrFail($ordenId);

        $montoLetras = $this->convertirMontoALetras($orden->comprobante->monto);

        $pdf = Pdf::loadView('pdfs.comprobante', [
            'orden' => $orden,
            'monto_letras' => $montoLetras,
        ]);

        $path = 'comprobantes/'.$orden->comprobante->folio.'.pdf';

        Storage::disk('public')->put($path, $pdf->output());

        $orden->comprobante->update(['pdf_path' => $path]);

        return $pdf->stream('comprobante-'.$orden->comprobante->folio.'.pdf');
    }

    private function convertirMontoALetras($numero)
    {
        $enteros = floor($numero);
        $decimales = round(($numero - $enteros) * 100);

        $letras = $this->numerosALetrasLogica($enteros);

        return ucfirst($letras).' con '.str_pad($decimales, 2, '0', STR_PAD_LEFT).'/100 soles';
    }

    private function numerosALetrasLogica($num)
    {
        $unidades = ['', 'un', 'dos', 'tres', 'cuatro', 'cinco', 'seis', 'siete', 'ocho', 'nueve'];
        $decenas = ['', 'diez', 'veinte', 'treinta', 'cuarenta', 'cincuenta', 'sesenta', 'setenta', 'ochenta', 'noventa'];
        $especiales = [11 => 'once', 12 => 'doce', 13 => 'trece', 14 => 'catorce', 15 => 'quince', 16 => 'dieciséis', 17 => 'diecisiete', 18 => 'dieciocho', 19 => 'diecinueve', 21 => 'veintiuno', 22 => 'veintidós', 23 => 'veintitrés', 24 => 'veinticuatro', 25 => 'veinticinco'];

        if ($num == 0) {
            return 'cero';
        }
        if ($num == 100) {
            return 'cien';
        }
        if ($num < 10) {
            return $unidades[$num];
        }

        if ($num < 30) {
            return $especiales[$num] ?? ($num == 20 ? 'veinte' : 'veinti'.$unidades[$num % 10]);
        }

        if ($num < 100) {
            $u = $num % 10;

            return $decenas[floor($num / 10)].($u > 0 ? ' y '.$unidades[$u] : '');
        }

        if ($num < 1000) {
            $centenas = ['', 'ciento', 'doscientos', 'trescientos', 'cuatrocientos', 'quinientos', 'seiscientos', 'setecientos', 'ochocientos', 'novecientos'];
            $resto = $num % 100;

            return ($num == 100 ? 'cien' : $centenas[floor($num / 100)]).($resto > 0 ? ' '.$this->numerosALetrasLogica($resto) : '');
        }

        if ($num < 1000000) {
            $miles = floor($num / 1000);
            $resto = $num % 1000;
            $t_miles = ($miles == 1) ? 'mil' : $this->numerosALetrasLogica($miles).' mil';

            return $t_miles.($resto > 0 ? ' '.$this->numerosALetrasLogica($resto) : '');
        }

        return (string) $num;
    }
}
