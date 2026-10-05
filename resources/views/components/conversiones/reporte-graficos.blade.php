@props([
    'filtroBadge',
    'kitsTotal',
    'kitsSelladosChart',
    'kitsCompletadosChart',
    'kitsAsignadosChart',
])

{{-- Bloque de gráficos (Chart.js) — usando x-ui.chart reutilizable --}}
<div class="space-y-6 font-inter">
    {{-- Charts row 1 --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <x-ui.chart
            id="chartConvMes"
            title="Conversiones por mes"
            icon="fa-chart-column"
            iconColor="text-gray-400"
            height="280px"
            :subtitle="$filtroBadge"
            class="lg:col-span-2"
        />

        <x-ui.chart
            id="chartConvEstado"
            title="Por estado"
            icon="fa-chart-pie"
            iconColor="text-gray-400"
            height="280px"
            :subtitle="$filtroBadge"
        />
    </div>

    {{-- Charts row 2 --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <x-ui.chart
            id="chartKitsUsados"
            title="Kits en almacén"
            icon="fa-box-open"
            iconColor="text-gray-400"
            height="240px"
            :subtitle="$filtroBadge"
            :legend="[
                'Sellados' => '#2563eb',
                'Completados' => '#10b981',
                'Asignados a clientes' => '#f59e0b',
                'En armado' => '#64748b',
            ]"
        />

        <x-ui.chart
            id="chartComponentes"
            title="Componentes instalados"
            icon="fa-gears"
            iconColor="text-gray-400"
            height="260px"
            :subtitle="$filtroBadge"
        />

        <x-ui.chart
            id="chartDespachados"
            title="Despachados: serie vs cantidad"
            icon="fa-truck"
            iconColor="text-gray-400"
            height="260px"
            :subtitle="$filtroBadge"
            :legend="[
                'Con serie' => '#8b5cf6',
                'Por cantidad' => '#10b981',
            ]"
        />
    </div>

    {{-- Chart 5: balance --}}
    <x-ui.chart
        id="chartBalanceSedes"
        title="Balance de almacén por sede"
        icon="fa-warehouse"
        iconColor="text-gray-400"
        height="280px"
        :subtitle="$filtroBadge"
    />
</div>