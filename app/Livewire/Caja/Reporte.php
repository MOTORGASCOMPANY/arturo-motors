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
    public bool $soloFise = false;

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
            'soloFise' => $this->soloFise ? '1' : null,
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
            'soloFise' => $this->soloFise ? '1' : null,
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

        // Si solo FISE, filtrar sesiones que tengan movimientos FISE
        if ($this->soloFise) {
            $sesionesIds = MovimientoCaja::where('metodo_pago', 'fise')
                ->whereBetween('created_at', [$desde, $hasta])
                ->pluck('sesion_caja_id')
                ->unique();
            $sesiones = $sesiones->filter(fn ($s) => $sesionesIds->contains($s->id));
        }

        $queryIngresos = MovimientoCaja::whereBetween('created_at', [$desde, $hasta])
            ->where('tipo', 'ingreso');
        $queryEgresos = MovimientoCaja::whereBetween('created_at', [$desde, $hasta])
            ->where('tipo', 'egreso');

        if ($this->soloFise) {
            $queryIngresos->where('metodo_pago', 'fise');
            $queryEgresos->where('metodo_pago', 'fise');
        }

        $totalIngresos = (clone $queryIngresos)->sum('monto');
        $totalEgresos = (clone $queryEgresos)->sum('monto');

        $sesionesConDescuadre = $sesiones->filter(fn ($s) => $s->diferencia !== null && (float) $s->diferencia != 0);

        // Métodos de pago para gráfico
        $metodos = $this->soloFise ? ['fise'] : ['efectivo', 'tarjeta', 'transferencia', 'fise', 'otro'];
        $colores = [
            'efectivo' => '#059669',
            'tarjeta' => '#2563eb',
            'transferencia' => '#7c3aed',
            'fise' => '#d97706',
            'otro' => '#6b7280',
        ];

        $dias = min($desde->diffInDays($hasta) + 1, 90);
        $labels = [];
        $ingresosData = [];
        $egresosData = [];
        $chartData = [];
        foreach ($metodos as $metodo) {
            $chartData[$metodo] = [];
        }

        $ingresosPorDia = MovimientoCaja::where('tipo', 'ingreso')
            ->whereBetween('created_at', [$desde, $hasta])
            ->when($this->soloFise, fn ($q) => $q->where('metodo_pago', 'fise'))
            ->selectRaw('DATE(created_at) as fecha, SUM(monto) as total')
            ->groupBy('fecha')->pluck('total', 'fecha');

        $egresosPorDia = MovimientoCaja::where('tipo', 'egreso')
            ->whereBetween('created_at', [$desde, $hasta])
            ->when($this->soloFise, fn ($q) => $q->where('metodo_pago', 'fise'))
            ->selectRaw('DATE(created_at) as fecha, SUM(monto) as total')
            ->groupBy('fecha')->pluck('total', 'fecha');

        // Desglose por método de pago por día
        $ingresosPorMetodoPorDia = [];
        foreach ($metodos as $metodo) {
            $ingresosPorMetodoPorDia[$metodo] = MovimientoCaja::where('tipo', 'ingreso')
                ->where('metodo_pago', $metodo)
                ->whereBetween('created_at', [$desde, $hasta])
                ->selectRaw('DATE(created_at) as fecha, SUM(monto) as total')
                ->groupBy('fecha')->pluck('total', 'fecha');
        }

        for ($i = 0; $i < $dias; $i++) {
            $fecha = $desde->copy()->addDays($i);
            $clave = $fecha->format('Y-m-d');
            $labels[] = $fecha->format('d/m');
            $ingresosData[] = (float) ($ingresosPorDia[$clave] ?? 0);
            $egresosData[] = (float) ($egresosPorDia[$clave] ?? 0);
            foreach ($metodos as $metodo) {
                $chartData[$metodo][] = (float) ($ingresosPorMetodoPorDia[$metodo][$clave] ?? 0);
            }
        }

        $ingresosPorMetodo = MovimientoCaja::where('tipo', 'ingreso')
            ->whereBetween('created_at', [$desde, $hasta])
            ->when($this->soloFise, fn ($q) => $q->where('metodo_pago', 'fise'))
            ->whereNotNull('metodo_pago')
            ->selectRaw('metodo_pago, SUM(monto) as total')
            ->groupBy('metodo_pago')
            ->pluck('total', 'metodo_pago');

        // Egresos por concepto
        $egresosPorConcepto = MovimientoCaja::where('tipo', 'egreso')
            ->whereBetween('created_at', [$desde, $hasta])
            ->selectRaw('concepto, COUNT(*) as cantidad, SUM(monto) as total')
            ->groupBy('concepto')
            ->orderByDesc('total')
            ->get();

        // Flujo neto acumulado (saldo caja física: solo efectivo - egresos)
        $efectivoPorDia = MovimientoCaja::where('tipo', 'ingreso')
            ->where('metodo_pago', 'efectivo')
            ->whereBetween('created_at', [$desde, $hasta])
            ->selectRaw('DATE(created_at) as fecha, SUM(monto) as total')
            ->groupBy('fecha')->pluck('total', 'fecha');

        $flujoAcumulado = [];
        $saldo = (float) $this->efectivoAnterior;
        for ($i = 0; $i < $dias; $i++) {
            $fecha = $desde->copy()->addDays($i);
            $clave = $fecha->format('Y-m-d');
            $saldo += (float) ($efectivoPorDia[$clave] ?? 0) - (float) ($egresosPorDia[$clave] ?? 0);
            $flujoAcumulado[] = round($saldo, 2);
        }

        // Ingresos por hora del día (0–23)
        $ingresosPorHoraRaw = MovimientoCaja::where('tipo', 'ingreso')
            ->whereBetween('created_at', [$desde, $hasta])
            ->when($this->soloFise, fn ($q) => $q->where('metodo_pago', 'fise'))
            ->selectRaw('HOUR(created_at) as hora, SUM(monto) as total')
            ->groupBy('hora')
            ->pluck('total', 'hora');

        $labelsHora = [];
        $ingresosPorHoraData = [];
        for ($h = 0; $h < 24; $h++) {
            $labelsHora[] = sprintf('%02d', $h);
            $ingresosPorHoraData[] = (float) ($ingresosPorHoraRaw[$h] ?? 0);
        }

        // FISE vs no FISE por día
        $fisePorDia = MovimientoCaja::where('tipo', 'ingreso')
            ->where('metodo_pago', 'fise')
            ->whereBetween('created_at', [$desde, $hasta])
            ->selectRaw('DATE(created_at) as fecha, SUM(monto) as total')
            ->groupBy('fecha')->pluck('total', 'fecha');
        $noFisePorDia = MovimientoCaja::where('tipo', 'ingreso')
            ->where('metodo_pago', '!=', 'fise')
            ->whereBetween('created_at', [$desde, $hasta])
            ->selectRaw('DATE(created_at) as fecha, SUM(monto) as total')
            ->groupBy('fecha')->pluck('total', 'fecha');

        $fiseDiaData = [];
        $noFiseDiaData = [];
        for ($i = 0; $i < $dias; $i++) {
            $fecha = $desde->copy()->addDays($i);
            $clave = $fecha->format('Y-m-d');
            $fiseDiaData[] = (float) ($fisePorDia[$clave] ?? 0);
            $noFiseDiaData[] = (float) ($noFisePorDia[$clave] ?? 0);
        }

        // Top 5 ingresos del período
        $topIngresos = MovimientoCaja::where('tipo', 'ingreso')
            ->whereBetween('created_at', [$desde, $hasta])
            ->when($this->soloFise, fn ($q) => $q->where('metodo_pago', 'fise'))
            ->with('usuario')
            ->orderByDesc('monto')
            ->limit(5)
            ->get();

        // Ticket promedio por sesión
        $sesionesCerradas = $sesiones->filter(fn ($s) => $s->estado === 'cerrada');
        $ticketPromedio = $sesionesCerradas->count() > 0
            ? $sesionesCerradas->avg(fn ($s) => (float) ($s->monto_cierre ?? 0))
            : 0;

        // Payload único para todos los charts (script application/json)
        $metodosTotales = [];
        foreach ($metodos as $m) {
            $metodosTotales[$m] = (float) ($ingresosPorMetodo[$m] ?? 0);
        }
        $metodosConDatos = array_values(array_filter($metodos, fn ($m) => $metodosTotales[$m] > 0));

        $egresosLabelsChart = $egresosPorConcepto->pluck('concepto')->map(fn ($c) => $c ?: 'Sin concepto')->values()->all();
        $egresosDataChart = $egresosPorConcepto->pluck('total')->map(fn ($v) => (float) $v)->values()->all();

        $charts = [
            'labels' => $labels,
            'ingresosData' => $ingresosData,
            'egresosData' => $egresosData,
            'flujoAcumulado' => $flujoAcumulado,
            'labelsHora' => $labelsHora,
            'ingresosPorHoraData' => $ingresosPorHoraData,
            'fiseDiaData' => $fiseDiaData,
            'noFiseDiaData' => $noFiseDiaData,
            'metodos' => $metodosConDatos,
            'metodosTotales' => $metodosTotales,
            'colores' => $colores,
            'egresosLabels' => $egresosLabelsChart,
            'egresosDataCat' => $egresosDataChart,
            'metodosLabels' => [
                'efectivo' => 'Efectivo',
                'tarjeta' => 'Tarjeta',
                'transferencia' => 'Transferencia',
                'fise' => 'FISE',
                'otro' => 'Otro',
            ],
            'efectivoAnterior' => (float) $this->efectivoAnterior,
            'hasIngresos' => array_sum($ingresosData) > 0,
            'hasEgresos' => array_sum($egresosData) > 0,
            'hasMetodos' => count($metodosConDatos) > 0,
            'hasEgresosCat' => count($egresosLabelsChart) > 0,
            'hasHora' => array_sum($ingresosPorHoraData) > 0,
            'hasFiseData' => array_sum($fiseDiaData) + array_sum($noFiseDiaData) > 0,
        ];

        $this->dispatch('chart-data-updated', charts: $charts);

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
            'chartData' => $chartData,
            'colores' => $colores,
            'metodos' => $metodos,
            'egresosPorConcepto' => $egresosPorConcepto,
            'ticketPromedio' => $ticketPromedio,
            'charts' => $charts,
            'topIngresos' => $topIngresos,
            'flujoFinal' => $flujoAcumulado[$dias - 1] ?? $this->efectivoAnterior,
            'fiseTotal' => array_sum($fiseDiaData),
            'noFiseTotal' => array_sum($noFiseDiaData),
        ]);
    }
}
