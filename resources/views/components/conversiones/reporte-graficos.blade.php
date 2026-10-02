@props([
    'filtroBadge',
    'kitsTotal',
    'kitsSelladosChart',
    'kitsCompletadosChart',
    'kitsAsignadosChart',
])

{{-- Bloque de gráficos (Chart.js) — los canvas viven en wire:ignore --}}
<div class="space-y-6 font-inter">
    {{-- Charts row 1 --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 bg-white rounded-card shadow-card border border-gray-200 p-6">
            <div class="flex flex-wrap items-start justify-between gap-x-3 gap-y-2 mb-5">
                <h3 class="min-w-0 flex items-start gap-2 text-sm font-bold text-gray-600 uppercase tracking-wider">
                    <i class="fas fa-chart-column text-gray-400 shrink-0 mt-0.5"></i>
                    <span>Conversiones por mes</span>
                </h3>
                <span class="max-w-full text-[11px] font-bold text-brand-700 bg-brand-50 border border-brand-200 rounded-full px-2.5 py-1 break-words">{{ $filtroBadge }}</span>
            </div>
            <div class="relative" style="height: 280px;" wire:ignore>
                <canvas id="chartConvMes"></canvas>
            </div>
        </div>

        <div class="bg-white rounded-card shadow-card border border-gray-200 p-6">
            <div class="flex flex-wrap items-start justify-between gap-x-3 gap-y-2 mb-5">
                <h3 class="min-w-0 flex items-start gap-2 text-sm font-bold text-gray-600 uppercase tracking-wider">
                    <i class="fas fa-chart-pie text-gray-400 shrink-0 mt-0.5"></i>
                    <span>Por estado</span>
                </h3>
                <span class="max-w-full text-[11px] font-bold text-brand-700 bg-brand-50 border border-brand-200 rounded-full px-2.5 py-1 break-words">{{ $filtroBadge }}</span>
            </div>
            <div class="relative" style="height: 280px;" wire:ignore>
                <canvas id="chartConvEstado"></canvas>
            </div>
        </div>
    </div>

    {{-- Charts row 2 --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="bg-white rounded-card shadow-card border border-gray-200 p-6">
            <div class="flex flex-wrap items-start justify-between gap-x-3 gap-y-2 mb-2">
                <h3 class="min-w-0 flex items-start gap-2 text-sm font-bold text-gray-600 uppercase tracking-wider">
                    <i class="fas fa-box-open text-gray-400 shrink-0 mt-0.5"></i>
                    <span>Kits en almacén</span>
                </h3>
                <span class="max-w-full text-[11px] font-bold text-brand-700 bg-brand-50 border border-brand-200 rounded-full px-2.5 py-1 break-words">{{ $filtroBadge }}</span>
            </div>
            <p class="text-xs text-gray-500 mb-4">
                Total <span class="font-extrabold text-gray-900">{{ number_format($kitsTotal) }}</span>
                · Sellados <span class="font-bold text-brand-700">{{ number_format($kitsSelladosChart) }}</span>
                · Completados <span class="font-bold text-gray-700">{{ number_format($kitsCompletadosChart) }}</span>
                · Asignados a clientes <span class="font-bold text-brand-800">{{ number_format($kitsAsignadosChart) }}</span>
            </p>
            <div class="relative" style="height: 240px;" wire:ignore>
                <canvas id="chartKitsUsados"></canvas>
            </div>
        </div>

        <div class="bg-white rounded-card shadow-card border border-gray-200 p-6">
            <div class="flex flex-wrap items-start justify-between gap-x-3 gap-y-2 mb-5">
                <h3 class="min-w-0 flex items-start gap-2 text-sm font-bold text-gray-600 uppercase tracking-wider">
                    <i class="fas fa-gears text-gray-400 shrink-0 mt-0.5"></i>
                    <span>Componentes instalados</span>
                </h3>
                <span class="max-w-full text-[11px] font-bold text-brand-700 bg-brand-50 border border-brand-200 rounded-full px-2.5 py-1 break-words">{{ $filtroBadge }}</span>
            </div>
            <div class="relative" style="height: 260px;" wire:ignore>
                <canvas id="chartComponentes"></canvas>
            </div>
        </div>

        <div class="bg-white rounded-card shadow-card border border-gray-200 p-6">
            <div class="flex flex-wrap items-start justify-between gap-x-3 gap-y-2 mb-5">
                <h3 class="min-w-0 flex items-start gap-2 text-sm font-bold text-gray-600 uppercase tracking-wider">
                    <i class="fas fa-truck text-gray-400 shrink-0 mt-0.5"></i>
                    <span>Despachados: serie vs cantidad</span>
                </h3>
                <span class="max-w-full text-[11px] font-bold text-brand-700 bg-brand-50 border border-brand-200 rounded-full px-2.5 py-1 break-words">{{ $filtroBadge }}</span>
            </div>
            <div class="relative" style="height: 260px;" wire:ignore>
                <canvas id="chartDespachados"></canvas>
            </div>
            <p class="text-xs text-gray-400 mt-3">
                Items instalados/despachados a las órdenes filtradas: con número de serie vs por unidad de cantidad.
            </p>
        </div>
    </div>

    {{-- Chart 5: balance --}}
    <div class="bg-white rounded-card shadow-card border border-gray-200 p-6">
        <div class="flex flex-wrap items-start justify-between gap-x-3 gap-y-2 mb-5">
            <h3 class="min-w-0 flex items-start gap-2 text-sm font-bold text-gray-600 uppercase tracking-wider">
                <i class="fas fa-warehouse text-gray-400 shrink-0 mt-0.5"></i>
                <span>Balance de almacén por sede</span>
            </h3>
            <span class="max-w-full text-[11px] font-bold text-brand-700 bg-brand-50 border border-brand-200 rounded-full px-2.5 py-1 break-words">{{ $filtroBadge }}</span>
        </div>
        <div class="relative" style="height: 280px;" wire:ignore>
            <canvas id="chartBalanceSedes"></canvas>
        </div>
        <p class="text-xs text-gray-400 mt-3">
            Kits = disponibles (sellados + completados) · Sueltos = mismos criterios que
            <a href="{{ route('almacen.productos.listado') }}" class="text-brand-700 font-semibold hover:underline">/almacen/productos</a>
            (stock real menos componentes dentro de kits de esa sede).
        </p>
    </div>
</div>
