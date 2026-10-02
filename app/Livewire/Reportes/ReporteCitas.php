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

    /**
     * Rango por defecto de todos los reportes: 1 del mes en curso -> hoy.
     * La navegación semanal (lunes a viernes) sigue disponible como acción aparte.
     */
    public function mount(): void
    {
        $this->mesActual();
    }

    public function mesActual(): void
    {
        $this->desde = now()->startOfMonth()->format('Y-m-d');
        $this->hasta = now()->format('Y-m-d');
    }

    public function semanaActual(): void
    {
        $lunes = \Illuminate\Support\Carbon::today()->startOfWeek(\Illuminate\Support\Carbon::MONDAY);

        $this->desde = $lunes->format('Y-m-d');
        $this->hasta = $lunes->copy()->addDays(4)->format('Y-m-d'); // viernes
    }

    public function semanaAnterior(): void
    {
        $this->moverSemana(-7);
    }

    public function semanaSiguiente(): void
    {
        $this->moverSemana(7);
    }

    protected function moverSemana(int $dias): void
    {
        $this->desde = \Illuminate\Support\Carbon::parse($this->desde ?: now())->addDays($dias)->format('Y-m-d');
        $this->hasta = \Illuminate\Support\Carbon::parse($this->hasta ?: now())->addDays($dias)->format('Y-m-d');
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

        // Gráfico semanal: X = lunes a viernes de la semana seleccionada.
        // Se cuentan por día ISO (1 = lunes ... 5 = viernes), de modo que el
        // sábado y domingo quedan fuera del gráfico (la semana laboral no los incluye).
        $labels = ['Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes'];
        $aceptadasPorPeriodo = [];
        $noAceptadasPorPeriodo = [];
        $conversionPorPeriodo = [];
        $granularity = 'semana';

        foreach ($labels as $indice => $dia) {
            $iso = $indice + 1;
            $enDia = $citas->filter(fn ($c) => (int) $c->fecha_cita->isoWeekday() === $iso);

            $aceptadasPorPeriodo[] = $enDia->where('estado', 'aceptada')->count();
            $noAceptadasPorPeriodo[] = $enDia
                ->filter(fn ($c) => in_array($c->estado, ['rechazada', 'cancelada'], true))
                ->count();
            $conversionPorPeriodo[] = $enDia->filter(fn ($c) => $c->serviceOrder !== null)->count();
        }

        $sumAceptadas = array_sum($aceptadasPorPeriodo);
        $sumNoAceptadas = array_sum($noAceptadasPorPeriodo);
        $sumConversion = array_sum($conversionPorPeriodo);
        $diasRango = (int) $desde->diffInDays($hasta) + 1;
        $esRangoSemanal = $diasRango <= 7;
        $semanas = (int) ceil($diasRango / 7);
        $periodoLabel = $esRangoSemanal
            ? 'Semana del ' . $desde->format('d/m/Y') . ' al ' . $hasta->format('d/m/Y')
            : 'Del ' . $desde->format('d/m/Y') . ' al ' . $hasta->format('d/m/Y') . ' · ' . $semanas . ($semanas === 1 ? ' semana' : ' semanas');
        $chartTitle = 'Citas por semana';
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
            'esRangoSemanal' => $esRangoSemanal,
            'sumAceptadas' => $sumAceptadas,
            'sumNoAceptadas' => $sumNoAceptadas,
            'sumConversion' => $sumConversion,
            'filtroBadge' => trim(
                ($sedeNombre ?: 'Todas las sedes')
                . ' · ' . ($this->estado !== 'todos' ? ucfirst($this->estado) : 'Todos')
            ),
        ]);
    }

    /**
     * Los controladores de export leen `fechaInicio`/`fechaFin`/`sede_id`, no
     * `desde`/`hasta`. Sin este mapeo el rango se perdía y el PDF/Excel exportaba
     * todas las citas de la historia sin aplicar ningún filtro de fecha.
     */
    protected function filtrosExport(): array
    {
        [$desde, $hasta] = $this->rangoValido();

        return array_filter([
            'fechaInicio' => $desde->format('Y-m-d'),
            'fechaFin' => $hasta->format('Y-m-d'),
            'estado' => $this->estado,
            'sede_id' => $this->sedeId !== 'todos' ? $this->sedeId : null,
        ]);
    }

    public function descargarPdf(): void
    {
        $this->dispatch('descargar-pdf', url: url('/rpta-citas/export-pdf?' . http_build_query($this->filtrosExport())));
    }

    public function descargarExcel(): void
    {
        $this->dispatch('descargar-excel', url: url('/rpta-citas/export-excel?' . http_build_query($this->filtrosExport())));
    }
}
