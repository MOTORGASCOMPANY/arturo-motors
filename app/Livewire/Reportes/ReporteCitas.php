<?php

namespace App\Livewire\Reportes;

use App\Models\Cita;
use App\Models\Sede;
use App\Models\ServiceOrder;
use Livewire\Component;

class ReporteCitas extends Component
{
    public string $desde = '';
    public string $hasta = '';
    public string $sedeId = 'todos';
    public string $estado = 'todos';

    public function mount(): void
    {
        $this->desde = now()->format('Y-m-d');
        $this->hasta = now()->format('Y-m-d');
    }

    public function diaAnterior(): void
    {
        $nuevo = \Illuminate\Support\Carbon::parse($this->desde ?: now())->subDay()->toDateString();
        $this->desde = $nuevo;
        $this->hasta = $nuevo;
    }

    public function diaSiguiente(): void
    {
        $nuevo = \Illuminate\Support\Carbon::parse($this->desde ?: now())->addDay()->toDateString();
        $this->desde = $nuevo;
        $this->hasta = $nuevo;
    }

    protected function rangoValido(): array
    {
        $desde = \Illuminate\Support\Carbon::parse($this->desde)->startOfDay();
        $hasta = \Illuminate\Support\Carbon::parse($this->hasta)->endOfDay();

        return $desde->gt($hasta) ? [$hasta->copy()->startOfDay(), $desde->copy()->endOfDay()] : [$desde, $hasta];
    }

    protected function baseQuery($desde, $hasta)
    {
        return Cita::whereBetween('fecha_cita', [$desde, $hasta])
            ->when($this->sedeId !== 'todos', fn ($q) => $q->where('sede_id', $this->sedeId))
            ->when($this->estado !== 'todos', fn ($q) => $q->where('estado', $this->estado));
    }

    public function render()
    {
        [$desde, $hasta] = $this->rangoValido();
        $sedes = Sede::orderBy('id')->get();

        $query = $this->baseQuery($desde, $hasta);
        $citas = $query->with(['cliente', 'vehiculo', 'asesor', 'asesorExterno', 'sede'])->get();

        $total = $citas->count();
        $pendientes = $citas->where('estado', 'pendiente')->count();
        $aceptadas = $citas->where('estado', 'aceptada')->count();
        $rechazadas = $citas->where('estado', 'rechazada')->count();
        $canceladas = $citas->where('estado', 'cancelada')->count();

        $porcentajeAceptacion = $total > 0 ? round(($aceptadas / $total) * 100, 1) : 0;

        // Conversión a ServiceOrder
        $conOrden = $citas->filter(fn ($c) => $c->serviceOrder !== null)->count();
        $porcentajeConversion = $total > 0 ? round(($conOrden / $total) * 100, 1) : 0;

        // Gráfico SIEMPRE por horas del día seleccionado (fecha "desde")
        $diaChart = $desde->copy()->startOfDay();
        $labels = [];
        $aceptadasPorPeriodo = [];
        $noAceptadasPorPeriodo = [];
        $conversionPorPeriodo = [];
        $granularity = 'hora';

        $citasDia = $citas->filter(fn ($c) => $c->fecha_cita->isSameDay($diaChart));
        for ($h = 0; $h < 24; $h++) {
            $labels[] = sprintf('%02d:00', $h);
            $enHora = $citasDia->filter(fn ($c) => (int) $c->fecha_cita->format('G') === $h);
            $aceptadasPorPeriodo[] = $enHora->where('estado', 'aceptada')->count();
            $noAceptadasPorPeriodo[] = $enHora
                ->filter(fn ($c) => in_array($c->estado, ['rechazada', 'cancelada'], true))
                ->count();
            $conversionPorPeriodo[] = $enHora->filter(fn ($c) => $c->serviceOrder !== null)->count();
        }

        $sumAceptadas = array_sum($aceptadasPorPeriodo);
        $sumNoAceptadas = array_sum($noAceptadasPorPeriodo);
        $sumConversion = array_sum($conversionPorPeriodo);
        $periodoLabel = $diaChart->locale('es')->isoFormat('dddd DD/MM/YYYY');
        $chartTitle = 'Citas por hora';
        $sedeNombre = $this->sedeId === 'todos'
            ? null
            : optional(Sede::find($this->sedeId))->nombre;

        // Citas por asesor
        $porAsesor = $citas->groupBy(fn ($c) => $c->nombre_asesor ?? 'Sin asesor')
            ->map(fn ($grupo) => [
                'total' => $grupo->count(),
                'aceptadas' => $grupo->where('estado', 'aceptada')->count(),
                'rechazadas' => $grupo->where('estado', 'rechazada')->count(),
                'canceladas' => $grupo->where('estado', 'cancelada')->count(),
                'con_orden' => $grupo->filter(fn ($c) => $c->serviceOrder !== null)->count(),
            ])->sortByDesc('total');

        // Citas por sede
        $porSede = $citas->groupBy(fn ($c) => $c->sede->nombre ?? 'Sin sede')
            ->map(fn ($grupo) => [
                'total' => $grupo->count(),
                'aceptadas' => $grupo->where('estado', 'aceptada')->count(),
                'pendientes' => $grupo->where('estado', 'pendiente')->count(),
            ])->sortByDesc('total');

        // Top motivos
        $motivos = $citas->filter(fn ($c) => !empty($c->motivo))
            ->groupBy('motivo')
            ->map(fn ($grupo) => $grupo->count())
            ->sortDesc()
            ->take(5);

        $this->dispatch('chart-data-citas',
            labels: $labels,
            aceptadas: $aceptadasPorPeriodo,
            noAceptadas: $noAceptadasPorPeriodo,
            conversion: $conversionPorPeriodo,
        );

        return view('livewire.reportes.reporte-citas', [
            'desde' => $desde,
            'hasta' => $hasta,
            'sedes' => $sedes,
            'total' => $total,
            'pendientes' => $pendientes,
            'aceptadas' => $aceptadas,
            'rechazadas' => $rechazadas,
            'canceladas' => $canceladas,
            'porcentajeAceptacion' => $porcentajeAceptacion,
            'porcentajeConversion' => $porcentajeConversion,
            'porAsesor' => $porAsesor,
            'porSede' => $porSede,
            'motivos' => $motivos,
            'labels' => $labels,
            'aceptadasPorPeriodo' => $aceptadasPorPeriodo,
            'noAceptadasPorPeriodo' => $noAceptadasPorPeriodo,
            'conversionPorPeriodo' => $conversionPorPeriodo,
            'granularity' => $granularity,
            'chartTitle' => $chartTitle,
            'periodoLabel' => $periodoLabel,
            'sumAceptadas' => $sumAceptadas,
            'sumNoAceptadas' => $sumNoAceptadas,
            'sumConversion' => $sumConversion,
            'filtroBadge' => trim(
                ($sedeNombre ?: 'Todas las sedes')
                . ' · ' . ($this->estado !== 'todos' ? ucfirst($this->estado) : 'Todos')
            ),
            'citasDelDia' => $citasDia->count(),
        ]);
    }

    public function descargarPdf(): void
    {
        $this->dispatch('descargar-pdf', url: url('/rpta-citas/export-pdf?' . http_build_query(array_filter([
            'desde' => $this->desde,
            'hasta' => $this->hasta,
            'estado' => $this->estado,
        ]))));
    }

    public function descargarExcel(): void
    {
        $this->dispatch('descargar-excel', url: url('/rpta-citas/export-excel?' . http_build_query(array_filter([
            'desde' => $this->desde,
            'hasta' => $this->hasta,
            'estado' => $this->estado,
        ]))));
    }
}
