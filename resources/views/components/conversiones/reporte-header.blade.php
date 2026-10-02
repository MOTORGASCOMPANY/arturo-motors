@props(['filtroBadge'])

{{-- Encabezado: mismo patrón que /almacen/reporte (icono al lado del título + botones sólidos) --}}
<div class="bg-white border border-gray-200 p-6 sm:p-8 rounded-card w-full shadow-card">
    <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-6">
        <div class="flex min-w-0 items-center gap-4">
            <div class="w-14 h-14 rounded-card bg-brand-600 border border-brand-700 flex items-center justify-center shrink-0">
                <i class="fas fa-car text-white text-2xl"></i>
            </div>
            <div class="min-w-0">
                <h2 class="text-gray-800 font-bold text-2xl tracking-tight">Reporte de Conversiones GNV</h2>
                <p class="text-gray-500 text-sm mt-1">Conversiones · kits · stock ·
                    <span class="font-medium text-brand-600 break-words">{{ $filtroBadge }}</span>
                </p>
            </div>
        </div>

        <div class="flex items-center gap-3 w-full lg:w-auto justify-end">
            <button type="button" onclick="exportarPDF()"
                class="inline-flex items-center gap-2 px-4 py-2 rounded-card text-sm font-semibold text-white bg-red-600 hover:bg-red-700 border border-red-700 shadow-sm transition-colors">
                <i class="fas fa-file-pdf text-[14px]"></i>
                <span>PDF</span>
            </button>
            <button type="button" onclick="exportarExcel()"
                class="inline-flex items-center gap-2 px-4 py-2 rounded-card text-sm font-semibold text-white bg-emerald-600 hover:bg-emerald-700 border border-emerald-700 shadow-sm transition-colors">
                <i class="fas fa-file-excel text-[14px]"></i>
                <span>Excel</span>
            </button>
            <a href="{{ route('ordenes.listado') }}" wire:navigate
                class="inline-flex items-center gap-2 px-4 py-2 rounded-card text-sm font-semibold text-white bg-orange-600 hover:bg-orange-700 border border-orange-700 shadow-sm transition-colors">
                <i class="fas fa-clipboard-list text-[14px]"></i>
                <span>Órdenes</span>
            </a>
        </div>
    </div>
</div>
