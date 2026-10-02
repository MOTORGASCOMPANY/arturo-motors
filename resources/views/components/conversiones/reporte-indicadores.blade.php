@props([
    'filtroBadge',
    'totalConversiones',
    'completadas',
    'enProceso',
    'tasaCompletado',
    'duracionPromedio',
    'kitsInstalados',
    'instaladosPorCombustible',
    'kitsDisponibles',
    'kitsSellados',
    'kitsCompletados',
    'piezasSueltas',
    'sueltosSerializados',
    'sueltosCantidadTotal',
])

{{-- 4 tarjetas: número grande + franja inferior de celdas (valor + etiqueta) --}}
<div class="font-inter">
    <h3 class="flex flex-wrap items-center gap-x-2 gap-y-2 mb-6 px-1 text-sm font-bold text-gray-500 uppercase tracking-wider">
        <i class="fas fa-gauge-high text-gray-400 shrink-0"></i>
        <span class="min-w-0">Indicadores de conversión</span>
        <span class="ml-auto max-w-full inline-flex items-center gap-1.5 rounded-full bg-brand-50 border border-brand-200 text-brand-700 px-3 py-1 text-xs font-bold normal-case tracking-normal break-words">
            {{ $filtroBadge }}
        </span>
    </h3>

    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-6">
        {{-- 1. Conversiones --}}
        <div class="bg-white rounded-card shadow-card border border-gray-200 border-l-4 border-l-brand-600 p-5">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-card bg-brand-50 flex items-center justify-center shrink-0">
                    <i class="fas fa-car text-brand-600"></i>
                </div>
                <div>
                    <p class="text-3xl font-extrabold text-gray-900 leading-none">{{ number_format($totalConversiones) }}</p>
                    <span class="text-xs font-bold text-gray-500 uppercase tracking-wider">Conversiones</span>
                </div>
            </div>

            <div class="mt-4 pt-4 border-t border-gray-200 grid grid-cols-2 gap-x-4 gap-y-4">
                <div>
                    <p class="text-sm font-bold text-emerald-600 leading-none">{{ number_format($completadas) }}</p>
                    <p class="text-[10px] font-semibold uppercase tracking-wider text-gray-500 mt-1">Completadas</p>
                </div>
                <div class="border-l border-gray-100 pl-4">
                    <p class="text-sm font-bold text-amber-600 leading-none">{{ number_format($enProceso) }}</p>
                    <p class="text-[10px] font-semibold uppercase tracking-wider text-gray-500 mt-1">En proceso</p>
                </div>
                <div>
                    <p class="text-sm font-bold text-brand-700 leading-none">{{ $tasaCompletado }}%</p>
                    <p class="text-[10px] font-semibold uppercase tracking-wider text-gray-500 mt-1">Tasa completado</p>
                </div>
                <div class="border-l border-gray-100 pl-4">
                    <p class="text-sm font-bold text-gray-900 leading-none">{{ $duracionPromedio }}h</p>
                    <p class="text-[10px] font-semibold uppercase tracking-wider text-gray-500 mt-1">Duración prom.</p>
                </div>
            </div>
        </div>

        {{-- 2. Kits instalados --}}
        <div class="bg-white rounded-card shadow-card border border-gray-200 border-l-4 border-l-brand-500 p-5">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-card bg-brand-50 flex items-center justify-center shrink-0">
                    <i class="fas fa-gears text-brand-600"></i>
                </div>
                <div>
                    <p class="text-3xl font-extrabold text-gray-900 leading-none">{{ number_format($kitsInstalados) }}</p>
                    <span class="text-xs font-bold text-gray-500 uppercase tracking-wider">Kits instalados</span>
                </div>
            </div>

            {{-- Sub-datos: sólo los tipos de conversión presentes (GNV solo, o GNV + GLP distribuidos) --}}
            <div class="mt-4 pt-4 border-t border-gray-200 {{ count($instaladosPorCombustible) > 1 ? 'grid grid-cols-2 gap-x-4' : '' }}">
                @forelse ($instaladosPorCombustible as $tipo => $valor)
                    <div class="{{ !$loop->first && $loop->index % 2 === 1 ? 'border-l border-gray-100 pl-4' : '' }}">
                        <p class="text-sm font-bold text-gray-900 leading-none">{{ number_format($valor) }}</p>
                        <p class="text-[10px] font-semibold uppercase tracking-wider text-gray-500 mt-1">{{ $tipo }}</p>
                    </div>
                @empty
                    <div>
                        <p class="text-sm font-bold text-gray-400 leading-none">0</p>
                        <p class="text-[10px] font-semibold uppercase tracking-wider text-gray-500 mt-1">Sin kits instalados</p>
                    </div>
                @endforelse
            </div>
        </div>

        {{-- 3. Kits --}}
        <div class="bg-white rounded-card shadow-card border border-gray-200 border-l-4 border-l-brand-700 p-5">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-card bg-gray-100 flex items-center justify-center shrink-0">
                    <i class="fas fa-box text-gray-600"></i>
                </div>
                <div>
                    <p class="text-3xl font-extrabold text-gray-900 leading-none">{{ number_format($kitsDisponibles) }}</p>
                    <span class="text-xs font-bold text-gray-500 uppercase tracking-wider">Kits disponibles</span>
                </div>
            </div>

            <div class="mt-4 pt-4 border-t border-gray-200 grid grid-cols-2 gap-x-4">
                <div>
                    <p class="text-sm font-bold text-gray-900 leading-none">{{ number_format($kitsSellados) }}</p>
                    <p class="text-[10px] font-semibold uppercase tracking-wider text-gray-500 mt-1">Sellados</p>
                </div>
                <div class="border-l border-gray-100 pl-4">
                    <p class="text-sm font-bold text-gray-900 leading-none">{{ number_format($kitsCompletados) }}</p>
                    <p class="text-[10px] font-semibold uppercase tracking-wider text-gray-500 mt-1">Completados</p>
                </div>
            </div>
        </div>

        {{-- 4. Sueltos --}}
        <div class="bg-white rounded-card shadow-card border border-gray-200 border-l-4 border-l-gray-400 p-5">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-card bg-gray-100 flex items-center justify-center shrink-0">
                    <i class="fas fa-puzzle-piece text-gray-600"></i>
                </div>
                <div>
                    <p class="text-3xl font-extrabold text-gray-900 leading-none">{{ number_format($piezasSueltas) }}</p>
                    <span class="text-xs font-bold text-gray-500 uppercase tracking-wider">Piezas sueltas</span>
                </div>
            </div>

            <div class="mt-4 pt-4 border-t border-gray-200 grid grid-cols-2 gap-x-4">
                <div>
                    <p class="text-sm font-bold text-gray-900 leading-none">{{ number_format($sueltosSerializados) }}</p>
                    <p class="text-[10px] font-semibold uppercase tracking-wider text-gray-500 mt-1">Con serie</p>
                </div>
                <div class="border-l border-gray-100 pl-4">
                    <p class="text-sm font-bold text-gray-900 leading-none">{{ number_format($sueltosCantidadTotal) }}</p>
                    <p class="text-[10px] font-semibold uppercase tracking-wider text-gray-500 mt-1">Por cantidad</p>
                </div>
            </div>
        </div>
    </div>
</div>
