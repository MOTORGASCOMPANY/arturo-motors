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

    public function mount()
    {
        $this->desde = now()->startOfMonth()->format('Y-m-d');
        $this->hasta = now()->format('Y-m-d');
    }

    protected function rangoValido(): array
    {
        $desde = Carbon::parse($this->desde)->startOfDay();
        $hasta = Carbon::parse($this->hasta)->endOfDay();

        // Protección simple: si el usuario invierte las fechas, las corregimos en vez de romper la consulta
        return $desde->gt($hasta) ? [$hasta->copy()->startOfDay(), $desde->copy()->endOfDay()] : [$desde, $hasta];
    }

    public function render()
    {
        [$desde, $hasta] = $this->rangoValido();

        $sesiones = SesionCaja::with('abiertaPor')
            ->whereBetween('abierta_en', [$desde, $hasta])
            ->orderByDesc('abierta_en')
            ->get();

        $totalIngresos = MovimientoCaja::whereBetween('created_at', [$desde, $hasta])
            ->where('tipo', 'ingreso')->sum('monto');

        $totalEgresos = MovimientoCaja::whereBetween('created_at', [$desde, $hasta])
            ->where('tipo', 'egreso')->sum('monto');

        $sesionesConDescuadre = $sesiones->filter(fn ($s) => $s->diferencia !== null && (float) $s->diferencia != 0);

        // Serie diaria para el gráfico, acotada a un máximo razonable de días
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

        return view('livewire.caja.reporte', [
            'sesiones' => $sesiones,
            'totalIngresos' => $totalIngresos,
            'totalEgresos' => $totalEgresos,
            'neto' => $totalIngresos - $totalEgresos,
            'sesionesConDescuadre' => $sesionesConDescuadre,
            'labels' => $labels,
            'ingresosData' => $ingresosData,
            'egresosData' => $egresosData,
        ]);
    }
}
