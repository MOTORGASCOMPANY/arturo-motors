<?php

namespace App\Livewire\Caja;

use App\Models\SesionCaja;
use App\Models\MovimientoCaja;
use Illuminate\Support\Carbon;
use Livewire\Component;

class Reporte extends Component
{
    public string $desde;
    public string $hasta;
    public float $efectivoAnterior = 0;

    public function mount(): void
    {
        $ayer = now()->subDay();
        $this->desde = $ayer->startOfDay()->format('Y-m-d');
        $this->hasta = $ayer->endOfDay()->format('Y-m-d');

        $this->cargarEfectivoAnterior();
    }

    public function cargarEfectivoAnterior(): void
    {
        $ultimaSesion = SesionCaja::where('estado', 'cerrada')
            ->orderByDesc('cerrada_en')
            ->first();

        $this->efectivoAnterior = $ultimaSesion ? (float) $ultimaSesion->monto_cierre : 0;
    }

    protected function rangoValido(): array
    {
        $desde = Carbon::parse($this->desde)->startOfDay();
        $hasta = Carbon::parse($this->hasta)->endOfDay();

        return $desde->gt($hasta) ? [$hasta->copy()->startOfDay(), $desde->copy()->endOfDay()] : [$desde, $hasta];
    }

    protected function validarFechas(): bool
    {
        $desde = Carbon::parse($this->desde);
        $hasta = Carbon::parse($this->hasta);

        if ($desde->gt($hasta)) {
            $this->addError('fechas', 'La fecha inicial no puede ser mayor a la fecha final.');
            return false;
        }

        $dias = $desde->diffInDays($hasta) + 1;
        if ($dias > 90) {
            $this->addError('fechas', 'El rango de fechas no puede exceder 90 días.');
            return false;
        }

        return true;
    }

    public function descargarPdf(): void
    {
        if (!$this->validarFechas()) {
            $this->dispatch('minToast', titulo: 'Error', mensaje: 'Corrija las fechas antes de exportar.', icono: 'error');
            return;
        }
        $this->dispatch('descargar-pdf', url: url('/reporte-caja/pdf?' . http_build_query(array_filter([
            'desde' => $this->desde,
            'hasta' => $this->hasta,
        ]))));
    }

    public function descargarExcel(): void
    {
        if (!$this->validarFechas()) {
            $this->dispatch('minToast', titulo: 'Error', mensaje: 'Corrija las fechas antes de exportar.', icono: 'error');
            return;
        }
        $this->dispatch('descargar-excel', url: url('/reporte-caja/excel?' . http_build_query(array_filter([
            'desde' => $this->desde,
            'hasta' => $this->hasta,
        ]))));
    }

    public function render()
    {
        [$desde, $hasta] = $this->rangoValido();

        $ultimaSesion = SesionCaja::where('estado', 'cerrada')
            ->with(['movimientos'])
            ->orderByDesc('cerrada_en')
            ->first();

        $sesiones = SesionCaja::with('abiertaPor')
            ->whereBetween('abierta_en', [$desde, $hasta])
            ->orderByDesc('abierta_en')
            ->get();

        $totalIngresos = MovimientoCaja::whereBetween('created_at', [$desde, $hasta])
            ->where('tipo', 'ingreso')
            ->sum('monto');

        $totalEgresos = MovimientoCaja::whereBetween('created_at', [$desde, $hasta])
            ->where('tipo', 'egreso')->sum('monto');

        $sesionesConDescuadre = $sesiones->filter(fn ($s) => $s->diferencia !== null && (float) $s->diferencia != 0);

        $dias = min($desde->diffInDays($hasta) + 1, 90);
        $labels = [];
        $ingresosData = [];
        $egresosData = [];

        $ingresosPorDia = MovimientoCaja::where('tipo', 'ingreso')
            ->whereBetween('created_at', [$desde, $hasta])
            ->selectRaw('DATE(created_at) as fecha, SUM(monto) as total')
            ->groupBy('fecha')->pluck('total', 'fecha');

        $egresosPorDia = MovimientoCaja::where('tipo', 'egreso')
            ->whereBetween('created_at', [$desde, $hasta])
            ->selectRaw('DATE(created_at) as fecha, SUM(monto) as total')
            ->groupBy('fecha')->pluck('total', 'fecha');

        for ($i = 0; $i < $dias; $i++) {
            $fecha = $desde->copy()->addDays($i);
            $clave = $fecha->format('Y-m-d');
            $labels[] = $fecha->format('d/m');
            $ingresosData[] = (float) ($ingresosPorDia[$clave] ?? 0);
            $egresosData[] = (float) ($egresosPorDia[$clave] ?? 0);
        }

        $ingresosPorMetodo = MovimientoCaja::where('tipo', 'ingreso')
            ->whereBetween('created_at', [$desde, $hasta])
            ->whereNotNull('metodo_pago')
            ->selectRaw('metodo_pago, SUM(monto) as total')
            ->groupBy('metodo_pago')
            ->pluck('total', 'metodo_pago');

        return view('livewire.caja.reporte', [
            'sesiones' => $sesiones,
            'totalIngresos' => $totalIngresos,
            'totalEgresos' => $totalEgresos,
            'neto' => $totalIngresos - $totalEgresos,
            'sesionesConDescuadre' => $sesionesConDescuadre,
            'labels' => $labels,
            'ingresosData' => $ingresosData,
            'egresosData' => $egresosData,
            'efectivoAnterior' => $this->efectivoAnterior,
            'ingresosPorMetodo' => $ingresosPorMetodo,
        ]);
    }
}
