@props([
    'filtroBadge',
    'kitsTotal',
    'kitsSelladosChart',
    'kitsCompletadosChart',
    'kitsAsignadosChart',
    'charts' => [],
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
            emptyMessage="Sin conversiones en el rango seleccionado"
            emptyIcon="fa-chart-column"
        />

        <x-ui.chart
            id="chartConvEstado"
            title="Por estado"
            icon="fa-chart-pie"
            iconColor="text-gray-400"
            height="280px"
            :subtitle="$filtroBadge"
            emptyMessage="Sin conversiones con estados para mostrar en el período"
            emptyIcon="fa-chart-pie"
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
            emptyMessage="Sin kits en almacén con el filtro aplicado"
            emptyIcon="fa-box-open"
        >
            <p class="text-xs text-gray-500 mb-4">
                Total <span class="font-extrabold text-gray-900">{{ number_format($kitsTotal) }}</span>
                · Sellados <span class="font-bold text-brand-700">{{ number_format($kitsSelladosChart) }}</span>
                · Completados <span class="font-bold text-gray-700">{{ number_format($kitsCompletadosChart) }}</span>
                · Asignados a clientes <span class="font-bold text-brand-800">{{ number_format($kitsAsignadosChart) }}</span>
            </p>
        </x-ui.chart>

        <x-ui.chart
            id="chartComponentes"
            title="Componentes instalados"
            icon="fa-gears"
            iconColor="text-gray-400"
            height="260px"
            :subtitle="$filtroBadge"
            emptyMessage="Sin componentes instalados en el período seleccionado"
            emptyIcon="fa-gears"
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
            emptyMessage="Sin items instalados o despachados en el período seleccionado"
            emptyIcon="fa-truck"
        >
            <p class="text-xs text-gray-400">
                Items instalados/despachados a las órdenes filtradas: con número de serie vs por unidad de cantidad.
            </p>
        </x-ui.chart>
    </div>

    {{-- Chart 5: balance --}}
    <x-ui.chart
        id="chartBalanceSedes"
        title="Balance de almacén por sede"
        icon="fa-warehouse"
        iconColor="text-gray-400"
        height="280px"
        :subtitle="$filtroBadge"
        emptyMessage="Sin stock por sede con el filtro aplicado"
        emptyIcon="fa-warehouse"
    >
        <p class="text-xs text-gray-400">
            Kits = disponibles (sellados + completados) · Sueltos = mismos criterios que
            <a href="{{ route('almacen.productos.listado') }}" class="text-brand-700 font-semibold hover:underline">/almacen/productos</a>
            (stock real menos componentes dentro de kits de esa sede).
        </p>
    </x-ui.chart>
</div>
