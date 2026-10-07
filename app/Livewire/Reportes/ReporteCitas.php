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
     * Filtro por vendedor (asesor), controlado desde el gráfico de barras.
     * Formato: 'todos' | 'ninguno' | 'interno:{id}' | 'externo:{id}'.
     */
    public string $asesorKey = 'todos';

    /**
     * Modo del rango activo: gobierna el botón resaltado, el label del período
     * y la granularidad del gráfico. Se asigna por acción del usuario, nunca
     * se deduce de la duración del rango (un rango mensual a inicios de mes
     * dura menos de 7 días y se disfrazaba de semana).
     *
     * - 'mes':    rango del mes completo (botón "Este mes" activo).
     * - 'semana': lunes a viernes navegado con los botones de semana.
     * - 'custom': fechas editadas a mano en los inputs del filtro.
     */
    public string $modo = 'mes';

    /**
     * Rango por defecto: mes completo (1 -> último día del mes en curso).
     * La navegación semanal (lunes a viernes) es una acción aparte.
     */
    public function mount(): void
    {
        $this->mesActual();
    }

    public function mesActual(): void
    {
        $this->desde = now()->startOfMonth()->format('Y-m-d');
        $this->hasta = now()->endOfMonth()->format('Y-m-d');
        $this->modo = 'mes';
    }

    public function semanaActual(): void
    {
        $lunes = \Illuminate\Support\Carbon::today()->startOfWeek(\Illuminate\Support\Carbon::MONDAY);

        $this->desde = $lunes->format('Y-m-d');
        $this->hasta = $lunes->copy()->addDays(4)->format('Y-m-d'); // viernes
        $this->modo = 'semana';
    }

    public function semanaAnterior(): void
    {
        $this->moverSemana(-7);
    }

    public function semanaSiguiente(): void
    {
        $this->moverSemana(7);
    }

    /**
     * Avanza/retrocede una semana REAL (lunes a viernes). Se ancla en el lunes
     * de la semana que contiene el inicio del rango actual: así, aunque el rango
     * activo sea el mes completo o un rango personalizado, el primer click
     * aterriza en una semana canónica en vez de desplazar el rango entero
     * conservando su duración (lo que producía "semanas" de 30 días).
     */
    protected function moverSemana(int $dias): void
    {
        $lunes = \Illuminate\Support\Carbon::parse($this->desde ?: now())
            ->startOfWeek(\Illuminate\Support\Carbon::MONDAY)
            ->addDays($dias);

        $this->desde = $lunes->format('Y-m-d');
        $this->hasta = $lunes->copy()->addDays(4)->format('Y-m-d'); // viernes
        $this->modo = 'semana';
    }

    /**
     * Editar a mano los inputs de fecha sale del preset: ni "Este mes" ni
     * "Esta semana" quedan activos porque el rango ya no corresponde a ninguno.
     */
    public function updated(string $property): void
    {
        if (in_array($property, ['desde', 'hasta'], true)) {
            $this->modo = 'custom';
        }
    }

    protected function rangoValido(): array
    {
        $desde = \Illuminate\Support\Carbon::parse($this->desde)->startOfDay();
        $hasta = \Illuminate\Support\Carbon::parse($this->hasta)->endOfDay();

        return $desde->gt($hasta) ? [$hasta->copy()->startOfDay(), $desde->copy()->endOfDay()] : [$desde, $hasta];
    }

    protected function baseQuery($desde, $hasta)
    {
        return Cita::enRango($desde, $hasta)
            ->when($this->sedeId !== 'todos', fn ($q) => $q->where('sede_id', $this->sedeId))
            ->when($this->estado !== 'todos', fn ($q) => $q->where('estado', $this->estado));
    }

    /**
     * Aplica el filtro de vendedor sobre la query base. Una cita pertenece a
     * un asesor interno (asesor_id) o externo (asesor_externo_id), nunca a los
     * dos a la vez, así que la clave codifica el tipo.
     */
    protected function aplicarFiltroAsesor($query)
    {
        if ($this->asesorKey === 'todos') {
            return $query;
        }

        if ($this->asesorKey === 'ninguno') {
            return $query->whereNull('asesor_id')->whereNull('asesor_externo_id');
        }

        [$tipo, $id] = array_pad(explode(':', $this->asesorKey, 2), 2, null);

        if ($id === null) {
            return $query;
        }

        return $tipo === 'externo'
            ? $query->where('asesor_externo_id', $id)
            : $query->where('asesor_id', $id);
    }

    public function limpiarAsesor(): void
    {
        $this->asesorKey = 'todos';
    }

    public function render()
    {
        [$desde, $hasta] = $this->rangoValido();
        $sedes = Sede::orderBy('id')->get();

        $query = $this->aplicarFiltroAsesor($this->baseQuery($desde, $hasta));
        $citas = $query->with(['cliente', 'vehiculo', 'asesor', 'asesorExterno', 'sede'])->get();

        $total = $citas->count();
        $pendientes = $citas->where('estado', 'pendiente')->count();
        $aceptadas = $citas->where('estado', 'aceptada')->count();
        $rechazadas = $citas->where('estado', 'rechazada')->count();
        $canceladas = $citas->where('estado', 'cancelada')->count();

        $porcentajeAceptacion = $total > 0 ? round(($aceptadas / $total) * 100, 1) : 0;
        $porcentajeRechazo = $total > 0 ? round(($rechazadas / $total) * 100, 1) : 0;

        // Conversión a ServiceOrder
        $conOrden = $citas->filter(fn ($c) => $c->serviceOrder !== null)->count();
        $porcentajeConversion = $total > 0 ? round(($conOrden / $total) * 100, 1) : 0;

        // Series del gráfico. La granularidad la decide el MODO, no la duración:
        //  - 'semana': 5 barras, lunes a viernes de la semana seleccionada
        //               (sábado y domingo quedan fuera, es la semana laboral).
        //  - 'mes'/'custom': un punto por DÍA de todo el rango, para que el mes
        //               completo fluctúe día a día en lugar de comprimirse en
        //               5 barras de weekday.
        $labels = [];
        $totalesPorPeriodo = [];
        $aceptadasPorPeriodo = [];
        $rechazadasPorPeriodo = [];
        $noAceptadasPorPeriodo = [];
        $conversionPorPeriodo = [];

        if ($this->modo === 'semana') {
            $granularity = 'semana';
            $chartTitle = 'Citas por semana';
            $labels = ['Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes'];

            foreach ($labels as $i => $dia) {
                $enDia = $citas->filter(fn ($c) => (int) $c->fecha_cita->isoWeekday() === $i + 1);

                $totalesPorPeriodo[] = $enDia->count();
                $aceptadasPorPeriodo[] = $enDia->where('estado', 'aceptada')->count();
                $rechazadasPorPeriodo[] = $enDia->where('estado', 'rechazada')->count();
                $noAceptadasPorPeriodo[] = $enDia
                    ->filter(fn ($c) => in_array($c->estado, ['rechazada', 'cancelada'], true))
                    ->count();
                $conversionPorPeriodo[] = $enDia->filter(fn ($c) => $c->serviceOrder !== null)->count();
            }
        } else {
            $granularity = 'dia';
            $chartTitle = 'Citas por día';

            $indicePorFecha = [];
            for ($d = $desde->copy(); $d->lte($hasta); $d->addDay()) {
                $indicePorFecha[$d->format('Y-m-d')] = count($labels);
                $labels[] = $d->format('d/m');
            }

            $n = count($labels);
            $totalesPorPeriodo = array_fill(0, $n, 0);
            $aceptadasPorPeriodo = array_fill(0, $n, 0);
            $rechazadasPorPeriodo = array_fill(0, $n, 0);
            $noAceptadasPorPeriodo = array_fill(0, $n, 0);
            $conversionPorPeriodo = array_fill(0, $n, 0);

            foreach ($citas as $c) {
                $i = $indicePorFecha[$c->fecha_cita->format('Y-m-d')] ?? null;
                if ($i === null) {
                    continue;
                }

                $totalesPorPeriodo[$i]++;
                if ($c->estado === 'aceptada') {
                    $aceptadasPorPeriodo[$i]++;
                }
                if ($c->estado === 'rechazada') {
                    $rechazadasPorPeriodo[$i]++;
                }
                if (in_array($c->estado, ['rechazada', 'cancelada'], true)) {
                    $noAceptadasPorPeriodo[$i]++;
                }
                if ($c->serviceOrder !== null) {
                    $conversionPorPeriodo[$i]++;
                }
            }
        }

        // Ratios (%) por punto del gráfico: días sin citas quedan en null para
        // que la línea se vea como hueco en vez de un 0% inventado.
        $ratioAceptadas = [];
        $ratioRechazadas = [];
        foreach ($totalesPorPeriodo as $i => $totalPeriodo) {
            $ratioAceptadas[] = $totalPeriodo > 0 ? round($aceptadasPorPeriodo[$i] * 100 / $totalPeriodo, 1) : null;
            $ratioRechazadas[] = $totalPeriodo > 0 ? round($rechazadasPorPeriodo[$i] * 100 / $totalPeriodo, 1) : null;
        }

        $sumAceptadas = array_sum($aceptadasPorPeriodo);
        $sumNoAceptadas = array_sum($noAceptadasPorPeriodo);
        $sumConversion = array_sum($conversionPorPeriodo);

        $diasRango = (int) $desde->diffInDays($hasta) + 1;
        $semanas = (int) ceil($diasRango / 7);

        if ($this->modo === 'semana') {
            $periodoLabel = 'Semana del ' . $desde->format('d/m/Y') . ' al ' . $hasta->format('d/m/Y');
        } elseif ($desde->isSameDay($hasta)) {
            $periodoLabel = $desde->copy()->locale('es')->isoFormat('dddd DD/MM/YYYY');
        } else {
            $periodoLabel = 'Del ' . $desde->format('d/m/Y') . ' al ' . $hasta->format('d/m/Y')
                . ($diasRango > 7 ? ' · ' . $semanas . ($semanas === 1 ? ' semana' : ' semanas') : '');
        }
        $sedeNombre = $this->sedeId === 'todos'
            ? null
            : optional(Sede::find($this->sedeId))->nombre;

        // Citas por asesor (tabla): respeta el filtro de vendedor.
        $porAsesor = $citas->groupBy(fn ($c) => $c->nombre_asesor ?? 'Sin asesor')
            ->map(fn ($grupo) => [
                'total' => $grupo->count(),
                'aceptadas' => $grupo->where('estado', 'aceptada')->count(),
                'rechazadas' => $grupo->where('estado', 'rechazada')->count(),
                'canceladas' => $grupo->where('estado', 'cancelada')->count(),
                'con_orden' => $grupo->filter(fn ($c) => $c->serviceOrder !== null)->count(),
            ])->sortByDesc('total');

        // Gráfico de barras por vendedor: FACETADO a propósito — ignora el
        // filtro de asesor para que siempre se vean todos los vendedores y se
        // pueda cambiar de barra sin perder las demás. Solo reconsulta cuando
        // el filtro está activo; si no, reutiliza la colección ya cargada.
        $citasGrafico = $this->asesorKey === 'todos'
            ? $citas
            : $this->baseQuery($desde, $hasta)->with(['asesor', 'asesorExterno'])->get();

        $filasAsesor = $citasGrafico
            ->groupBy(fn ($c) => $c->asesor_externo_id
                ? 'externo:' . $c->asesor_externo_id
                : ($c->asesor_id ? 'interno:' . $c->asesor_id : 'ninguno'))
            ->map(function ($grupo, $clave) {
                $primero = $grupo->first();

                return [
                    'key' => $clave,
                    'nombre' => $primero->nombre_asesor ?? 'Sin asesor',
                    'total' => $grupo->count(),
                    'aceptadas' => $grupo->where('estado', 'aceptada')->count(),
                    'pendientes' => $grupo->where('estado', 'pendiente')->count(),
                    'rechazadas' => $grupo->where('estado', 'rechazada')->count(),
                    'canceladas' => $grupo->where('estado', 'cancelada')->count(),
                ];
            })
            ->sortByDesc('total')
            ->values();

        $labelsAsesores = $filasAsesor->pluck('nombre');
        $clavesAsesores = $filasAsesor->pluck('key');
        $asesorAceptadas = $filasAsesor->pluck('aceptadas');
        $asesorPendientes = $filasAsesor->pluck('pendientes');
        $asesorRechazadas = $filasAsesor->pluck('rechazadas');
        $asesorCanceladas = $filasAsesor->pluck('canceladas');
        $asesorSeleccionado = $this->asesorKey !== 'todos'
            ? ($filasAsesor->firstWhere('key', $this->asesorKey)['nombre'] ?? null)
            : null;

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
            ratioAceptadas: $ratioAceptadas,
            ratioRechazadas: $ratioRechazadas,
        );

        $this->dispatch('chart-data-asesores',
            labels: $labelsAsesores,
            claves: $clavesAsesores,
            aceptadas: $asesorAceptadas,
            pendientes: $asesorPendientes,
            rechazadas: $asesorRechazadas,
            canceladas: $asesorCanceladas,
            seleccionado: $this->asesorKey,
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
            'porcentajeRechazo' => $porcentajeRechazo,
            'porcentajeConversion' => $porcentajeConversion,
            'porAsesor' => $porAsesor,
            'porSede' => $porSede,
            'motivos' => $motivos,
            'labels' => $labels,
            'aceptadasPorPeriodo' => $aceptadasPorPeriodo,
            'rechazadasPorPeriodo' => $rechazadasPorPeriodo,
            'noAceptadasPorPeriodo' => $noAceptadasPorPeriodo,
            'conversionPorPeriodo' => $conversionPorPeriodo,
            'ratioAceptadas' => $ratioAceptadas,
            'ratioRechazadas' => $ratioRechazadas,
            'labelsAsesores' => $labelsAsesores,
            'clavesAsesores' => $clavesAsesores,
            'asesorAceptadas' => $asesorAceptadas,
            'asesorPendientes' => $asesorPendientes,
            'asesorRechazadas' => $asesorRechazadas,
            'asesorCanceladas' => $asesorCanceladas,
            'asesorSeleccionado' => $asesorSeleccionado,
            'granularity' => $granularity,
            'chartTitle' => $chartTitle,
            'periodoLabel' => $periodoLabel,
            'modo' => $this->modo,
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
