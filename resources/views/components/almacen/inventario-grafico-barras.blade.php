{{-- Gráfico: Barras agrupadas Sellados / Completados / Consumidos por sede --}}
<div class="bg-white rounded-2xl shadow-sm border border-slate-200/80 p-6">

    <div class="flex items-center justify-between mb-6">
        <h3 class="text-slate-500 text-xs font-bold uppercase tracking-wider flex items-center gap-2">
            <i class="fas fa-chart-bar text-slate-400"></i>
            Estados de kits por sede
        </h3>
        <span class="text-sm font-semibold text-indigo-600 bg-indigo-50 px-3 py-1 rounded-full">
            {{ $grafico['labels'][0] ?? 'Todas' }}
        </span>
    </div>

    {{-- Este div lleva los datos. Livewire lo actualiza y el JS los detecta --}}
    <div x-data="inventarioGraficoBarras()"
         data-grafico="{{ json_encode($grafico) }}">

        {{-- wire:ignore evita que Livewire toque el canvas. Altura fija en el contenedor --}}
        <div wire:ignore class="relative w-full" style="height: 288px;">
            <canvas x-ref="canvas"></canvas>
        </div>
    </div>

    {{-- Leyenda --}}
    <div class="flex flex-wrap justify-center gap-4 mt-4 text-sm">
        <span class="flex items-center gap-1.5"><i class="fas fa-circle text-cyan-500 text-[10px]"></i> Sellados</span>
        <span class="flex items-center gap-1.5"><i class="fas fa-circle text-emerald-500 text-[10px]"></i> Completados</span>
        <span class="flex items-center gap-1.5"><i class="fas fa-circle text-red-500 text-[10px]"></i> Consumidos</span>
    </div>
</div>

@push('js')
<script>
function inventarioGraficoBarras() {
    // La instancia va FUERA del objeto de Alpine para que no la vuelva Proxy
    let chart = null;
    let observer = null;

    const COLORES = {
        sellados:    { base: [6, 182, 212],  hex: '#06b6d4' },
        completados: { base: [16, 185, 129], hex: '#10b981' },
        consumidos:  { base: [220, 38, 38],  hex: '#dc2626' },
    };

    const rgba = (rgb, a) => `rgba(${rgb[0]}, ${rgb[1]}, ${rgb[2]}, ${a})`;

    const construirDatasets = (g) => {
        const labels = g.labels || [];
        const armar = (label, data, color) => {
            return {
                label,
                data: data || [],
                backgroundColor: labels.map(() => rgba(color.base, 0.85)),
                borderColor: labels.map(() => color.hex),
                borderWidth: 1,
                borderRadius: 6,
                maxBarThickness: 48,
            };
        };
        return [
            armar('Sellados', g.sellados, COLORES.sellados),
            armar('Completados', g.completados, COLORES.completados),
            armar('Consumidos', g.consumidos, COLORES.consumidos),
        ];
    };

    // Plugin: muestra el valor exacto encima de cada barra
    const etiquetasValor = {
        id: 'etiquetasValor',
        afterDatasetsDraw(c) {
            const { ctx } = c;
            ctx.save();
            ctx.font = '600 11px Inter, system-ui, sans-serif';
            ctx.fillStyle = '#334155';
            ctx.textAlign = 'center';
            ctx.textBaseline = 'bottom';
            c.data.datasets.forEach((ds, i) => {
                c.getDatasetMeta(i).data.forEach((bar, idx) => {
                    ctx.fillText(ds.data[idx] ?? 0, bar.x, bar.y - 4);
                });
            });
            ctx.restore();
        }
    };

    const leerDatos = (el) => {
        try {
            return JSON.parse(el.dataset.grafico);
        } catch (e) {
            console.error('Gráfico: datos inválidos', e);
            return { labels: [], sellados: [], completados: [], consumidos: [] };
        }
    };

    return {
        init() {
            const canvas = this.$refs.canvas;
            const datos = leerDatos(this.$el);

            // Por si quedó un gráfico previo en ese canvas
            const previo = Chart.getChart(canvas);
            if (previo) previo.destroy();

            chart = new Chart(canvas.getContext('2d'), {
                type: 'bar',
                data: {
                    labels: datos.labels,
                    datasets: construirDatasets(datos),
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    layout: { padding: { top: 20 } },
                    interaction: { mode: 'index', intersect: false },
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            backgroundColor: '#1e293b',
                            titleFont: { size: 13 },
                            bodyFont: { size: 12 },
                            padding: 12,
                            callbacks: {
                                label: (ctx) => `${ctx.dataset.label}: ${ctx.parsed.y} kits`,
                            },
                        },
                    },
                    scales: {
                        x: {
                            grid: { display: false },
                            ticks: { color: '#64748b', font: { size: 12 } },
                        },
                        y: {
                            beginAtZero: true,
                            grace: '10%',
                            grid: { color: '#f1f5f9' },
                            ticks: { color: '#64748b', font: { size: 11 }, precision: 0 },
                        },
                    },
                    animation: { duration: 500, easing: 'easeOutQuart' },
                },
                plugins: [etiquetasValor],
            });

            // Cuando Livewire actualice data-grafico (cambio de sede), refrescamos el gráfico
            observer = new MutationObserver(() => {
                if (!chart) return;
                const nuevo = leerDatos(this.$el);
                chart.data.labels = nuevo.labels;
                chart.data.datasets = construirDatasets(nuevo);
                chart.update();
            });
            observer.observe(this.$el, { attributes: true, attributeFilter: ['data-grafico'] });
        },

        destroy() {
            if (observer) observer.disconnect();
            if (chart) chart.destroy();
            chart = null;
        },
    };
}
</script>
@endpush