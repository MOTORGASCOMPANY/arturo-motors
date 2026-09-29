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
            <div class="flex items-center justify-between gap-2 mb-5">
                <h3 class="text-sm font-bold text-gray-600 uppercase tracking-wider flex items-center gap-2">
                    <i class="fas fa-chart-column text-gray-400"></i>
                    Conversiones por mes
                </h3>
                <span class="text-[11px] font-bold text-brand-700 bg-brand-50 border border-brand-200 rounded-full px-2.5 py-1">{{ $filtroBadge }}</span>
            </div>
            <div class="relative" style="height: 280px;" wire:ignore>
                <canvas id="chartConvMes"></canvas>
            </div>
        </div>

        <div class="bg-white rounded-card shadow-card border border-gray-200 p-6">
            <div class="flex items-center justify-between gap-2 mb-5">
                <h3 class="text-sm font-bold text-gray-600 uppercase tracking-wider flex items-center gap-2">
                    <i class="fas fa-chart-pie text-gray-400"></i>
                    Por estado
                </h3>
                <span class="text-[11px] font-bold text-brand-700 bg-brand-50 border border-brand-200 rounded-full px-2.5 py-1 shrink-0">{{ $filtroBadge }}</span>
            </div>
            <div class="relative" style="height: 280px;" wire:ignore>
                <canvas id="chartConvEstado"></canvas>
            </div>
        </div>
    </div>

    {{-- Charts row 2 --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="bg-white rounded-card shadow-card border border-gray-200 p-6">
            <div class="flex items-center justify-between gap-2 mb-2 flex-wrap">
                <h3 class="text-sm font-bold text-gray-600 uppercase tracking-wider flex items-center gap-2">
                    <i class="fas fa-box-open text-gray-400"></i>
                    Kits en almacén
                </h3>
                <span class="text-[11px] font-bold text-brand-700 bg-brand-50 border border-brand-200 rounded-full px-2.5 py-1 shrink-0">{{ $filtroBadge }}</span>
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
            <div class="flex items-center justify-between gap-2 mb-5">
                <h3 class="text-sm font-bold text-gray-600 uppercase tracking-wider flex items-center gap-2">
                    <i class="fas fa-gears text-gray-400"></i>
                    Componentes instalados
                </h3>
                <span class="text-[11px] font-bold text-brand-700 bg-brand-50 border border-brand-200 rounded-full px-2.5 py-1 shrink-0">{{ $filtroBadge }}</span>
            </div>
            <div class="relative" style="height: 260px;" wire:ignore>
                <canvas id="chartComponentes"></canvas>
            </div>
        </div>

        <div class="bg-white rounded-card shadow-card border border-gray-200 p-6">
            <div class="flex items-center justify-between gap-2 mb-5">
                <h3 class="text-sm font-bold text-gray-600 uppercase tracking-wider flex items-center gap-2">
                    <i class="fas fa-truck text-gray-400"></i>
                    Despachados: serie vs cantidad
                </h3>
                <span class="text-[11px] font-bold text-brand-700 bg-brand-50 border border-brand-200 rounded-full px-2.5 py-1 shrink-0">{{ $filtroBadge }}</span>
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
        <div class="flex items-center justify-between gap-2 mb-5">
            <h3 class="text-sm font-bold text-gray-600 uppercase tracking-wider flex items-center gap-2">
                <i class="fas fa-warehouse text-gray-400"></i>
                Balance de almacén por sede
            </h3>
            <span class="text-[11px] font-bold text-brand-700 bg-brand-50 border border-brand-200 rounded-full px-2.5 py-1">{{ $filtroBadge }}</span>
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
