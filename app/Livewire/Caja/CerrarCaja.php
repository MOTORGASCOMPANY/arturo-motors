<?php

namespace App\Livewire\Caja;

use App\Models\SesionCaja;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class CerrarCaja extends Component
{
    public $montoCierre = '0.00';
    public ?SesionCaja $sesion = null;

    public function mount()
    {
        $this->cargarSesion();
    }

    public function cargarSesion()
    {
        $this->sesion = SesionCaja::abierta()->orderByDesc('abierta_en')->first();

        // Pre-llenar con el monto esperado en efectivo (lo que debería haber en caja)
        if ($this->sesion) {
            $this->montoCierre = number_format($this->montoEsperado, 2, '.', '');
        }
    }

    public function getTotalIngresosProperty()
    {
        return $this->sesion ? $this->sesion->movimientos()->where('tipo', 'ingreso')->sum('monto') : 0;
    }

    private function ingresosPorMetodo(string $metodo): float
    {
        if (!$this->sesion) return 0;

        return $this->sesion->movimientos()
            ->where('tipo', 'ingreso')
            ->where('metodo_pago', $metodo)
            ->sum('monto');
    }

    public function getEfectivoIngresosProperty(): float  { return $this->ingresosPorMetodo('efectivo'); }
    public function getTarjetaIngresosProperty(): float   { return $this->ingresosPorMetodo('tarjeta'); }
    public function getTransferenciaIngresosProperty(): float { return $this->ingresosPorMetodo('transferencia'); }
    public function getOtroIngresosProperty(): float      { return $this->ingresosPorMetodo('otro'); }
    public function getFiseIngresosProperty(): float      { return $this->ingresosPorMetodo('fise'); }

    public function getTotalEgresosProperty()
    {
        return $this->sesion ? $this->sesion->movimientos()->where('tipo', 'egreso')->sum('monto') : 0;
    }

    public function getMontoEsperadoProperty()
    {
        if (!$this->sesion) return 0;
        return round($this->sesion->monto_apertura + $this->efectivoIngresos - $this->totalEgresos, 2);
    }

    public function getMontoCierreNumericoProperty(): float
    {
        $valor = trim((string) $this->montoCierre);
        $valor = preg_replace('/[^\d.,\-]/', '', $valor);

        if (empty($valor)) return 0;

        // Handle negative
        $negativo = str_starts_with($valor, '-');
        if ($negativo) $valor = substr($valor, 1);

        // Determine format based on last separator position
        $lastDot = strrpos($valor, '.');
        $lastComma = strrpos($valor, ',');

        if ($lastDot !== false && $lastComma !== false) {
            // Both present: last one is decimal separator
            if ($lastComma > $lastDot) {
                // European format: 1.910,00 → remove dots, replace comma with dot
                $valor = str_replace('.', '', $valor);
                $valor = str_replace(',', '.', $valor);
            } else {
                // US format: 1,910.00 → remove commas
                $valor = str_replace(',', '', $valor);
            }
        } elseif ($lastComma !== false) {
            // Only comma: check if it's a decimal separator (followed by 1-2 digits at end)
            $afterComma = substr($valor, $lastComma + 1);
            if (strlen($afterComma) <= 2) {
                // Decimal separator: 1910,50 → replace with dot
                $valor = str_replace(',', '.', $valor);
            } else {
                // Thousands separator: 1,910 or 1,910,000 → remove commas
                $valor = str_replace(',', '', $valor);
            }
        }
        // If only dot or no separator: no transformation needed (PHP handles dot as decimal)

        $resultado = (float) $valor;
        return $negativo ? -$resultado : $resultado;
    }

    public function getDiferenciaProperty()
    {
        return round($this->montoCierreNumerico - $this->montoEsperado, 2);
    }

    /**
     * Called from the form submit via Livewire.
     * Validates and closes directly. The SweetAlert confirmation
     * is handled entirely in the blade (Alpine + SweetAlert2).
     */
    public function cerrar()
    {
        if (!$this->sesion) {
            $this->addError('general', 'No hay una caja abierta.');
            return;
        }

        $this->validate(
            ['montoCierre' => 'required|regex:/^\d[\d.,]*$/'],
            [
                'montoCierre.required' => 'El monto real en caja es obligatorio.',
                'montoCierre.regex' => 'Debe ingresar un valor numérico válido.',
            ]
        );

        $montoCierreNumerico = $this->montoCierreNumerico;

        if ($montoCierreNumerico < 0) {
            $this->addError('montoCierre', 'El monto no puede ser un valor negativo.');
            return;
        }

        $diferencia = round($montoCierreNumerico - $this->montoEsperado, 2);

        $this->sesion->cerrar($montoCierreNumerico, Auth::id());

        $this->dispatch('minToast',
            titulo: '¡Caja Cerrada!',
            mensaje: 'Sesión cerrada con S/ ' . number_format($montoCierreNumerico, 2) .
                ($diferencia != 0 ? ' | Diferencia: S/ ' . number_format($diferencia, 2) : ' | Cuadrada ✓'),
            icono: 'success'
        );

        $this->sesion = null;
        $this->montoCierre = '0.00';
    }

    public function render()
    {
        return view('livewire.caja.cerrar-caja');
    }
}
