<?php

namespace App\Livewire\Servicios;

use App\Models\Comprobante;
use App\Models\ServiceOrder;
use Illuminate\Support\Carbon;
use Livewire\Component;

class Reporte extends Component
{
    public string $desde;
    public string $hasta;
    public string $tipoServicio = 'todos';

    public function mount()
    {
        $this->desde = now()->startOfMonth()->format('Y-m-d');
        $this->hasta = now()->format('Y-m-d');
    }

    /**
     * Rango efectivo [00:00 del desde, 23:59:59 del hasta].
     * Si el usuario invierte las fechas se intercambian, para que pantalla, PDF y Excel
     * midan siempre el mismo período.
     */
    protected function rangoValido(): array
    {
        $desde = Carbon::parse($this->desde)->startOfDay();
        $hasta = Carbon::parse($this->hasta)->endOfDay();

        return $desde->gt($hasta) ? [$hasta->copy()->startOfDay(), $desde->copy()->endOfDay()] : [$desde, $hasta];
    }

    /**
     * Filtros que viajan al PDF/Excel, ya con el rango corregido.
     * Antes se enviaban `$this->desde/$this->hasta` crudos: con las fechas invertidas
     * la pantalla mostraba datos y el export salía vacío.
     */
    private function filtrosExport(): array
    {
        [$desde, $hasta] = $this->rangoValido();

        return [
            'desde' => $desde->format('Y-m-d'),
            'hasta' => $hasta->format('Y-m-d'),
            'tipoServicio' => $this->tipoServicio,
        ];
    }

    public function descargarPdf(): void
    {
        $this->dispatch('descargar-pdf', url: url('/reporte-servicios/pdf?' . http_build_query($this->filtrosExport())));
    }

    public function descargarExcel(): void
    {
        $this->dispatch('descargar-excel', url: url('/reporte-servicios/excel?' . http_build_query($this->filtrosExport())));
    }

    /** Comprobantes cobrados en el período, con los filtros de fecha y tipo aplicados. */
    protected function baseQuery($desde, $hasta)
    {
        return Comprobante::whereBetween('created_at', [$desde, $hasta])
            ->when($this->tipoServicio !== 'todos', function ($q) {
                $q->whereHas('serviceOrder.service', fn ($s) => $s->where('tipo', $this->tipoServicio));
            });
    }

    /**
     * Órdenes creadas en el período, con los MISMOS filtros que baseQuery().
     * Es la única fuente del gráfico y de los KPIs: así leyenda, series y tarjetas
     * siempre cuadran entre sí.
     */
    protected function ordenesQuery($desde, $hasta)
    {
        return ServiceOrder::whereBetween('created_at', [$desde, $hasta])
            ->when($this->tipoServicio !== 'todos', function ($q) {
                $q->whereHas('service', fn ($s) => $s->where('tipo', $this->tipoServicio));
            });
    }

    public function render()
    {
        [$desde, $hasta] = $this->rangoValido();

        $comprobantes = $this->baseQuery($desde, $hasta)
            ->with(['serviceOrder.service', 'serviceOrder.tecnico'])
            ->get();

        $totalVentas = $comprobantes->sum('monto');

        // Órdenes cobradas = órdenes distintas con comprobante en el período.
        // Contar comprobantes directamente inflaría el dato si una orden tiene
        // varios comprobantes (pagos parciales) y desinflaría el ticket promedio.
        $idsOrdenesCobradas = $comprobantes->pluck('service_order_id')->filter()->unique();
        $totalOrdenes = $idsOrdenesCobradas->count();

        $ventasPorServicio = $comprobantes
            ->groupBy(fn ($c) => $c->serviceOrder?->service?->nombre ?? 'Sin servicio')
            ->map(fn ($grupo) => [
                'cantidad' => $grupo->count(),
                'total' => $grupo->sum('monto'),
            ])
            ->sortByDesc('total');

        $ventasPorTecnico = $comprobantes
            ->filter(fn ($c) => $c->serviceOrder?->tecnico_id !== null)
            ->groupBy(fn ($c) => $c->serviceOrder?->tecnico?->name ?? 'Sin técnico')
            ->map(fn ($grupo) => [
                'cantidad' => $grupo->count(),
                'total' => $grupo->sum('monto'),
            ])
            ->sortByDesc('total');

        // ─── Órdenes del período: base única del gráfico y de los KPIs ───────
        $ordenesDelPeriodo = $this->ordenesQuery($desde, $hasta)
            ->with('service:id,nombre,tipo')
            ->get();

        // Índice por fecha: evita recorrer toda la colección en cada día del bucle.
        $ordenesPorFecha = $ordenesDelPeriodo->groupBy(
            fn ($o) => $o->created_at->format('Y-m-d')
        );

        // Carbon 3 devuelve diffInDays() como FLOAT (0.9999… para un solo día):
        // el cast a int evita redondear hacia arriba y sumar un día fantasma.
        $dias = (int) $desde->diffInDays($hasta) + 1;
        $semanas = (int) ceil($dias / 7);

        $labels = [];
        $conversionPendientes = [];
        $conversionCompletadas = [];
        $simpleCompletadas = [];

        // Gráfico semanal: bloques de 7 días contados desde `desde`, para que el
        // último bloque nunca se salga del rango seleccionado.
        for ($s = 0; $s < $semanas; $s++) {
            $inicio = $desde->copy()->addDays($s * 7);
            $fin = $inicio->copy()->addDays(6);

            if ($fin->gt($hasta)) {
                $fin = $hasta->copy();
            }

            $labels[] = $inicio->isSameDay($fin)
                ? $inicio->format('d/m')
                : $inicio->format('d/m') . '–' . $fin->format('d/m');

            $pendientes = 0;
            $completadas = 0;
            $simples = 0;

            for ($f = $inicio->copy(); $f->lte($fin); $f->addDay()) {
                $diaOrdenes = $ordenesPorFecha->get($f->format('Y-m-d'), collect());

                $pendientes += $diaOrdenes
                    ->filter(fn ($o) => $o->service?->tipo === 'conversion' && ! $this->esFinal($o))
                    ->count();

                $completadas += $diaOrdenes
                    ->filter(fn ($o) => $o->service?->tipo === 'conversion' && $o->estado === ServiceOrder::ESTADO_ENTREGADO)
                    ->count();

                $simples += $diaOrdenes
                    ->filter(fn ($o) => $o->service?->tipo === 'simple' && $o->estado === ServiceOrder::ESTADO_ENTREGADO)
                    ->count();
            }

            $conversionPendientes[] = $pendientes;
            $conversionCompletadas[] = $completadas;
            $simpleCompletadas[] = $simples;
        }

        // ─── KPIs: derivados del mismo conjunto que el gráfico ──────────────
        $conversionesDelPeriodo = $ordenesDelPeriodo
            ->filter(fn ($o) => $o->service?->tipo === 'conversion');

        $totalConversionesPendientes = $conversionesDelPeriodo
            ->filter(fn ($o) => ! $this->esFinal($o))
            ->count();

        $totalConversionesCompletadas = $conversionesDelPeriodo
            ->filter(fn ($o) => $o->estado === ServiceOrder::ESTADO_ENTREGADO)
            ->count();

        $totalSimplesCompletadas = $ordenesDelPeriodo
            ->filter(fn ($o) => $o->service?->tipo === 'simple' && $o->estado === ServiceOrder::ESTADO_ENTREGADO)
            ->count();

        $hayDatos = $ordenesDelPeriodo->isNotEmpty() || $comprobantes->isNotEmpty();

        // Descuentos: precio_lista - precio_final, por orden única (no por comprobante).
        // Sin recorte: si precio_final > precio_lista el número tiene que verse,
        // no enmascararse en 0.
        $ordenesCobradas = ServiceOrder::whereIn('id', $idsOrdenesCobradas)
            ->get(['id', 'precio_lista', 'precio_final']);

        $totalDescuentos = $ordenesCobradas->sum(
            fn ($o) => (float) $o->precio_lista - (float) $o->precio_final
        );

        // Tiempo promedio de conversión: se promedia en MINUTOS enteros y se
        // expresa como "X horas Y minutos" (o "Y minutos" si no llega a la hora),
        // en vez del viejo float de horas con decimales ("2.5h").
        $ordenesConDuracion = ServiceOrder::whereIn('id', $idsOrdenesCobradas)
            ->whereNotNull('fecha_inicio_conversion')
            ->whereNotNull('fecha_fin_conversion')
            ->get();

        $minutosPromedio = $ordenesConDuracion->count() > 0
            ? (int) round($ordenesConDuracion->avg(
                fn ($o) => $o->fecha_inicio_conversion->diffInMinutes($o->fecha_fin_conversion)
            ))
            : 0;

        $horas = intdiv($minutosPromedio, 60);
        $minutos = $minutosPromedio % 60;

        $tiempoPromedioTexto = match (true) {
            $minutosPromedio <= 0 => '0 minutos',
            $horas > 0 && $minutos > 0 => $horas . ($horas === 1 ? ' hora ' : ' horas ')
                . $minutos . ($minutos === 1 ? ' minuto' : ' minutos'),
            $horas > 0 => $horas . ($horas === 1 ? ' hora' : ' horas'),
            default => $minutos . ($minutos === 1 ? ' minuto' : ' minutos'),
        };

        // Dispatch chart data to JS (wire:ignore prevents morph from updating data-* attrs)
        $this->dispatch('chart-data-updated',
            labels: $labels,
            conversionPendientes: $conversionPendientes,
            conversionCompletadas: $conversionCompletadas,
            simpleCompletadas: $simpleCompletadas
        );

        return view('livewire.servicios.reporte', [
            'totalVentas' => $totalVentas,
            'totalOrdenes' => $totalOrdenes,
            'ticketPromedio' => $totalOrdenes > 0 ? $totalVentas / $totalOrdenes : 0,
            'ventasPorServicio' => $ventasPorServicio,
            'ventasPorTecnico' => $ventasPorTecnico,
            'labels' => $labels,
            'conversionPendientes' => $conversionPendientes,
            'conversionCompletadas' => $conversionCompletadas,
            'simpleCompletadas' => $simpleCompletadas,
            'desde' => $desde,
            'hasta' => $hasta,
            'tipoServicio' => $this->tipoServicio,
            'totalConversionesPendientes' => $totalConversionesPendientes,
            'totalConversionesCompletadas' => $totalConversionesCompletadas,
            'totalSimplesCompletadas' => $totalSimplesCompletadas,
            'hayDatos' => $hayDatos,
            'totalDescuentos' => $totalDescuentos,
            'tiempoPromedioTexto' => $tiempoPromedioTexto,
        ]);
    }

    /**
     * Una orden dejó de ser pendiente cuando entra en un estado final
     * (entregada, cancelada o evaluación rechazada).
     */
    private function esFinal(ServiceOrder $orden): bool
    {
        return in_array($orden->estado, ServiceOrder::ESTADOS_FINALES, true);
    }
}
