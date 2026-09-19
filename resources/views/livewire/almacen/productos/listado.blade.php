<div wire:loading.class="opacity-50 pointer-events-none">
    <div class="max-w-7xl mx-auto px-4 py-4 space-y-4">
    @pushOnce('scripts')
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    @endPushOnce

        <div class="sticky top-0 z-20 bg-gray-50/95 backdrop-blur-sm -mx-4 px-4 pt-2 pb-2 space-y-2">

            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-2xl font-bold text-gray-800 flex items-center gap-2">
                        <i class="fas fa-store text-indigo-600"></i>
                        Almacén
                    </h2>
                    <p class="text-sm text-gray-500 mt-1">Inventario y productos del almacén</p>
                </div>

                <div class="flex gap-2">
                    <a href="{{ route('almacen.recepciones.crear') }}"
                        class="bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold px-4 py-2 rounded-lg transition-colors flex items-center gap-2">
                        <i class="fas fa-plus text-xs"></i>
                        Agregar producto
                    </a>
                </div>
            </div>

            <div class="border-b border-gray-200 bg-white rounded-t-xl px-1">
                <nav class="flex gap-0" x-data>

                    <button wire:click="$set('vistaActual', 'inventario')"
                        class="px-5 py-3 text-sm font-semibold border-b-2 transition-colors"
                        :class="$wire.vistaActual === 'inventario'
                            ? 'border-indigo-600 text-indigo-600'
                            : 'border-transparent text-gray-500 hover:text-gray-700'">
                        <i class="fas fa-boxes-stacked mr-1.5"></i>
                        Inventario
                    </button>

                    <button wire:click="$set('vistaActual', 'kits')"
                        class="px-5 py-3 text-sm font-semibold border-b-2 transition-colors"
                        :class="$wire.vistaActual === 'kits'
                            ? 'border-indigo-600 text-indigo-600'
                            : 'border-transparent text-gray-500 hover:text-gray-700'">
                        <i class="fas fa-box mr-1.5"></i>
                        Kits
                    </button>

                    <button wire:click="$set('vistaActual', 'catalogo')"
                        class="px-5 py-3 text-sm font-semibold border-b-2 transition-colors"
                        :class="$wire.vistaActual === 'catalogo'
                            ? 'border-indigo-600 text-indigo-600'
                            : 'border-transparent text-gray-500 hover:text-gray-700'">
                        <i class="fas fa-list mr-1.5"></i>
                        Catálogo
                    </button>

                </nav>
            </div>

            @if ($vistaActual === 'inventario' || $vistaActual === 'kits')
                <div class="bg-white rounded-xl border border-gray-200 p-3">
                    <div class="flex flex-wrap items-center gap-3">

                        <div class="min-w-[180px]">
                            <select wire:model.live="filtroSedeId"
                                class="w-full text-sm border border-gray-200 rounded-lg px-3 py-2 bg-white focus:ring-2 focus:ring-indigo-500">
                                <option value="">Todas las sedes</option>
                                @foreach ($sedes as $s)
                                    <option value="{{ $s->id }}">{{ $s->nombre }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="flex items-center bg-gray-50 rounded-lg px-3 py-2 flex-1 min-w-[200px]">
                            <i class="fas fa-search text-gray-400 text-sm mr-2"></i>
                            <input
                                class="bg-transparent outline-none text-sm w-full border-none focus:ring-0"
                                type="text"
                                wire:model.live.debounce.400ms="busquedaInventario"
                                placeholder="Buscar por nombre...">
                        </div>

                    </div>
                </div>
            @endif

        </div>

        @if ($vistaActual === 'inventario')

            @php
                $inv = $resumenInventario ?? [];
                $c = $inv['conteos'] ?? [];
            @endphp

            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-2">

                <button wire:click="verDetalle(0, 'sellado')" type="button"
                    class="bg-white rounded-xl border border-gray-200 p-3 text-left hover:shadow-md hover:border-amber-300 transition-all">
                    <span class="text-[10px] font-bold text-gray-400 uppercase tracking-wider block">Sellados</span>
                    <p class="text-xl font-black text-amber-600 mt-0.5">{{ $c['sellados'] ?? 0 }}</p>
                    <span class="text-[10px] text-gray-400 font-medium">En stock</span>
                </button>

                <button wire:click="verDetalle(0, 'incompleto')" type="button"
                    class="bg-white rounded-xl border border-gray-200 p-3 text-left hover:shadow-md hover:border-orange-300 transition-all">
                    <span class="text-[10px] font-bold text-gray-400 uppercase tracking-wider block">Incompletos</span>
                    <p class="text-xl font-black text-orange-600 mt-0.5">{{ $c['incompletos'] ?? 0 }}</p>
                    <span class="text-[10px] text-gray-400 font-medium">Abiertos</span>
                </button>

                <button wire:click="verDetalle(0, 'completado')" type="button"
                    class="bg-white rounded-xl border border-gray-200 p-3 text-left hover:shadow-md hover:border-purple-300 transition-all">
                    <span class="text-[10px] font-bold text-gray-400 uppercase tracking-wider block">Kits completados</span>
                    <p class="text-xl font-black text-purple-600 mt-0.5">{{ $c['completados'] ?? 0 }}</p>
                    <span class="text-[10px] text-gray-400 font-medium">Completados</span>
                </button>

                <button wire:click="verDetalle(0, 'consumido')" type="button"
                    class="bg-white rounded-xl border border-gray-200 p-3 text-left hover:shadow-md hover:border-red-300 transition-all">
                    <span class="text-[10px] font-bold text-gray-400 uppercase tracking-wider block">Consumidos</span>
                    <p class="text-xl font-black text-red-600 mt-0.5">{{ $c['consumidos'] ?? 0 }}</p>
                    <span class="text-[10px] text-gray-400 font-medium">En órdenes</span>
                </button>

                <button wire:click="verDetalle(0, 'sueltosSerializados')" type="button"
                    class="bg-white rounded-xl border border-gray-200 p-3 text-left hover:shadow-md hover:border-green-300 transition-all">
                    <span class="text-[10px] font-bold text-gray-400 uppercase tracking-wider block">Sueltos serial.</span>
                    <p class="text-xl font-black text-green-600 mt-0.5">{{ $c['sueltosSerializados'] ?? 0 }}</p>
                    <span class="text-[10px] text-gray-400 font-medium">Items con serie</span>
                </button>

                <button wire:click="verDetalle(0, 'sueltosCantidad')" type="button"
                    class="bg-white rounded-xl border border-gray-200 p-3 text-left hover:shadow-md hover:border-indigo-300 transition-all">
                    <span class="text-[10px] font-bold text-gray-400 uppercase tracking-wider block">Sueltos cantidad</span>
                    <p class="text-xl font-black text-indigo-600 mt-0.5">{{ $c['sueltosCantidadTipos'] ?? 0 }}</p>
                    <span class="text-[10px] text-gray-400 font-medium">Tipos de producto</span>
                </button>

            </div>

            @php
                $sellados = $inv['kitsSellados'] ?? collect();
            @endphp

            @if ($sellados->isNotEmpty())

                <div class="bg-gray-50 rounded-xl border border-gray-200 p-4">

                    <h3 class="text-xs font-bold text-gray-500 uppercase tracking-wider flex items-center gap-2 mb-3">
                        <i class="fas fa-box text-amber-500"></i>
                        Kits sellados
                        <span class="text-xs font-normal text-gray-400">({{ $sellados->flatten()->count() }})</span>
                    </h3>

                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3 max-h-72 overflow-y-auto pr-1" x-data>

                        @foreach ($sellados as $productoId => $items)

                            @php
                                $prod = $items->first()?->producto;
                            @endphp

                            <div
                                class="text-left bg-white border border-gray-200 rounded-xl p-3 hover:shadow-md hover:border-amber-300 transition-all flex items-center gap-3 cursor-pointer"
                                @click="$dispatch('ver-componentes-kit', { productoId: {{ $productoId }} })">

                                <div class="w-10 h-10 rounded-lg bg-amber-50 flex items-center justify-center shrink-0">
                                    <i class="fas fa-box text-amber-500 text-sm"></i>
                                </div>

                                <div class="min-w-0 flex-1">
                                    <p class="text-sm font-bold text-gray-800 truncate">{{ $prod?->nombre ?? 'Producto' }}</p>
                                    <p class="text-[10px] text-gray-400">{{ $items->first()?->sede?->nombre ?? '—' }}</p>
                                </div>

                                <span class="px-2 py-0.5 bg-amber-100 text-amber-700 text-[10px] font-bold rounded-full">
                                    {{ $items->count() }}
                                </span>

                            </div>

                        @endforeach

                    </div>
                </div>

            @endif

            @php
                $incompletos = $inv['kitsIncompletos'] ?? collect();
            @endphp

            @if ($incompletos->isNotEmpty())

                <div class="bg-gray-50 rounded-xl border border-gray-200 p-4">

                    <h3 class="text-xs font-bold text-gray-500 uppercase tracking-wider flex items-center gap-2 mb-3">
                        <i class="fas fa-box-open text-orange-500"></i>
                        Kits incompletos
                        <span class="text-xs font-normal text-gray-400">({{ $incompletos->flatten()->count() }})</span>
                    </h3>

                    <div class="space-y-2 max-h-80 overflow-y-auto pr-1">

                        @foreach ($incompletos as $productoId => $items)

                            @php
                                $prod = $items->first()?->producto;
                            @endphp

                            @foreach ($items as $kitItem)

                                <div class="bg-white border border-gray-200 rounded-xl p-3 flex items-center gap-3">

                                    <div class="w-10 h-10 rounded-lg bg-orange-50 flex items-center justify-center shrink-0">
                                        <i class="fas fa-box-open text-orange-500 text-sm"></i>
                                    </div>

                                    <div class="min-w-0 flex-1">
                                        <p class="text-sm font-bold text-gray-800 truncate">{{ $prod?->nombre ?? 'Producto' }}</p>
                                        <p class="text-[10px] text-gray-400">{{ $kitItem->sede?->nombre ?? '—' }} · #{{ $kitItem->id }}</p>
                                    </div>

                                    <button
                                        wire:click="abrirCompletarKit({{ $kitItem->id }})"
                                        class="px-3 py-1.5 bg-indigo-600 text-white text-[10px] font-bold rounded-lg hover:bg-indigo-700 transition whitespace-nowrap">
                                        <i class="fas fa-plus mr-1"></i> Completar
                                    </button>

                                </div>

                            @endforeach

                        @endforeach

                    </div>
                </div>

            @endif

            @php
                $completados = $inv['kitsCompletados'] ?? collect();
                $consumidos = $inv['kitsConsumidos'] ?? collect();
            @endphp

            @if ($completados->isNotEmpty())

                <div class="bg-gray-50 rounded-xl border border-gray-200 p-4">

                    <h3 class="text-xs font-bold text-gray-500 uppercase tracking-wider flex items-center gap-2 mb-3">
                        <i class="fas fa-check-circle text-purple-500"></i>
                        Kits completados
                        <span class="text-xs font-normal text-gray-400">({{ $completados->flatten()->count() }})</span>
                    </h3>

                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3 max-h-72 overflow-y-auto pr-1" x-data>

                        @foreach ($completados as $productoId => $items)

                            @php
                                $prod = $items->first()?->producto;
                            @endphp

                            <div
                                class="text-left bg-white border border-gray-200 rounded-xl p-3 hover:shadow-md hover:border-purple-300 transition-all flex items-center gap-3 cursor-pointer"
                                @click="$dispatch('ver-componentes-kit', { productoId: {{ $productoId }} })">

                                <div class="w-10 h-10 rounded-lg bg-purple-50 flex items-center justify-center shrink-0">
                                    <i class="fas fa-check-circle text-purple-500 text-sm"></i>
                                </div>

                                <div class="min-w-0 flex-1">
                                    <p class="text-sm font-bold text-gray-800 truncate">
                                        {{ $prod?->nombre ?? 'Producto' }}
                                    </p>

                                    <p class="text-[10px] text-gray-400">
                                        {{ $items->first()?->sede?->nombre ?? '—' }}
                                    </p>
                                </div>

                                <span class="px-2 py-0.5 bg-purple-100 text-purple-700 text-[10px] font-bold rounded-full">
                                    {{ $items->count() }}
                                </span>

                            </div>

                        @endforeach

                    </div>
                </div>

            @endif

            @if ($consumidos->isNotEmpty())

                <div class="bg-gray-50 rounded-xl border border-gray-200 p-4">

                    <h3 class="text-xs font-bold text-gray-500 uppercase tracking-wider flex items-center gap-2 mb-3">
                        <i class="fas fa-fire text-red-500"></i>
                        Consumidos
                        <span class="text-xs font-normal text-gray-400">({{ $consumidos->flatten()->count() }})</span>
                    </h3>

                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3 max-h-72 overflow-y-auto pr-1" x-data>

                        @foreach ($consumidos as $productoId => $items)

                            @php
                                $prod = $items->first()?->producto;
                            @endphp

                            <div
                                class="text-left bg-white border border-gray-200 rounded-xl p-3 hover:shadow-md hover:border-red-300 transition-all flex items-center gap-3 cursor-pointer"
                                @click="$dispatch('ver-componentes-kit', { productoId: {{ $productoId }} })">

                                <div class="w-10 h-10 rounded-lg bg-red-50 flex items-center justify-center shrink-0">
                                    <i class="fas fa-fire text-red-500 text-sm"></i>
                                </div>

                                <div class="min-w-0 flex-1">
                                    <p class="text-sm font-bold text-gray-800 truncate">{{ $prod?->nombre ?? 'Producto' }}</p>
                                    <p class="text-[10px] text-gray-400">{{ $items->first()?->sede?->nombre ?? '—' }}</p>
                                </div>

                                <span class="px-2 py-0.5 bg-red-100 text-red-700 text-[10px] font-bold rounded-full">
                                    {{ $items->count() }}
                                </span>

                            </div>

                        @endforeach

                    </div>
                </div>

            @endif

            @php
                $sueltosS = $inv['sueltosSerializados'] ?? collect();
            @endphp

            @if ($sueltosS->isNotEmpty())

                <div class="bg-gray-50 rounded-xl border border-gray-200 p-4">

                    <h3 class="text-xs font-bold text-gray-500 uppercase tracking-wider flex items-center gap-2 mb-3">
                        <i class="fas fa-barcode text-green-500"></i>
                        Piezas sueltas — Serializados
                        <span class="text-xs font-normal text-gray-400">({{ $sueltosS->flatten()->count() }})</span>
                    </h3>

                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3 max-h-72 overflow-y-auto pr-1">

                        @foreach ($sueltosS as $productoId => $items)

                            @php
                                $prod = $items->first()?->producto;
                            @endphp

                            <button
                                type="button"
                                wire:click="verDetalle({{ $productoId }}, 'sueltosSerializados')"
                                class="text-left bg-white border border-gray-200 rounded-xl p-3 hover:shadow-md hover:border-green-300 transition-all flex items-center gap-3">

                                <div class="w-10 h-10 rounded-lg bg-green-50 flex items-center justify-center shrink-0">
                                    <i class="fas fa-barcode text-green-500 text-sm"></i>
                                </div>

                                <div class="min-w-0 flex-1">
                                    <p class="text-sm font-bold text-gray-800 truncate">{{ $prod?->nombre ?? 'Producto' }}</p>
                                    <p class="text-[10px] text-gray-400">{{ $items->first()?->sede?->nombre ?? '—' }}</p>
                                </div>

                                <span class="px-2 py-0.5 bg-green-100 text-green-700 text-[10px] font-bold rounded-full">
                                    {{ $items->count() }}
                                </span>

                            </button>

                        @endforeach

                    </div>
                </div>

            @endif

            @php
                $sueltosC = $inv['sueltosCantidad'] ?? collect();
            @endphp

            @if ($sueltosC->isNotEmpty())

                <div class="bg-gray-50 rounded-xl border border-gray-200 p-4">

                    <h3 class="text-xs font-bold text-gray-500 uppercase tracking-wider flex items-center gap-2 mb-3">
                        <i class="fas fa-cubes text-indigo-500"></i>
                        Piezas sueltas — Por cantidad
                        <span class="text-xs font-normal text-gray-400">({{ $sueltosC->count() }})</span>
                    </h3>

                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3 max-h-72 overflow-y-auto pr-1">

                        @foreach ($sueltosC as $stock)

                            <div class="bg-white border border-gray-200 rounded-xl p-3 flex items-center gap-3">

                                <div class="w-10 h-10 rounded-lg bg-indigo-50 flex items-center justify-center shrink-0">
                                    <i class="fas fa-cubes text-indigo-500 text-sm"></i>
                                </div>

                                <div class="min-w-0 flex-1">
                                    <p class="text-sm font-bold text-gray-800 truncate">{{ $stock->producto?->nombre ?? 'Producto' }}</p>
                                    <p class="text-[10px] text-gray-400">{{ $stock->sede?->nombre ?? '—' }}</p>
                                </div>

                                <span class="px-2 py-0.5 bg-indigo-100 text-indigo-700 text-[10px] font-bold rounded-full">
                                    {{ $stock->cantidad }}
                                </span>

                            </div>

                        @endforeach

                    </div>
                </div>

            @endif

            @if ($detalleProductoId)

                @php

                    $itemsDetalle = match($tipoDetalle) {

                        'sellado' =>
                            ($inv['kitsSellados'] ?? collect())->get($detalleProductoId),

                        'incompleto' =>
                            ($inv['kitsIncompletos'] ?? collect())->get($detalleProductoId),

                        'consumido' =>
                            ($inv['kitsConsumidos'] ?? collect())->get($detalleProductoId),

                        'sueltosSerializados' =>
                            ($inv['sueltosSerializados'] ?? collect())->get($detalleProductoId),

                        default => null,
                    };

                    $prodDetalle = $itemsDetalle?->first()?->producto;

                    $agrupados = [];
                    $sinGrupo = [];

                    if ($tipoDetalle === 'sueltosSerializados' && $itemsDetalle) {

                        foreach ($itemsDetalle as $item) {

                            $produce = $item->atributos['produce'] ?? null;

                            if ($produce) {
                                $agrupados[$produce][] = $item;
                            } else {
                                $sinGrupo[] = $item;
                            }
                        }

                        ksort($agrupados);
                    }

                @endphp

                <div class="fixed inset-0 z-50 overflow-hidden">

                    <div
                        class="fixed inset-0 bg-black/50 transition-opacity z-40"
                        wire:click="$set('detalleProductoId', null)">
                    </div>

                    <div class="relative z-50 flex min-h-full items-center justify-center p-4">

                        <div
                            class="relative bg-white rounded-xl shadow-2xl w-full max-w-3xl flex flex-col border border-gray-200"
                            style="max-height: 85vh;">

                            <div class="px-6 py-4 border-b border-gray-200 bg-gray-50 rounded-t-xl flex items-center justify-between">

                                <div class="flex items-center gap-3 min-w-0">

                                    <div class="w-10 h-10 rounded-lg bg-white border border-gray-200 flex items-center justify-center shrink-0">

                                        @if ($tipoDetalle === 'sueltosCantidad')
                                            <i class="fas fa-cubes text-indigo-500"></i>
                                        @else
                                            <i class="fas fa-box text-gray-500"></i>
                                        @endif

                                    </div>

                                    <div class="min-w-0">

                                        <h3 class="text-base font-bold text-gray-800 truncate">
                                            {{ $prodDetalle?->nombre ?? 'Detalle' }}
                                        </h3>

                                        <div class="flex items-center gap-2 mt-0.5">

                                            @if ($tipoDetalle === 'sueltosCantidad')

                                                <span class="text-xs text-gray-500">
                                                    {{ ($sueltosCantidadDetalle ?? collect())->count() }} productos
                                                </span>

                                            @else

                                                <span class="text-xs text-gray-500">
                                                    {{ $detallesInventario->count() }} unidades
                                                </span>

                                            @endif

                                            <span class="text-xs text-gray-400">·</span>

                                            <span class="text-xs text-gray-500 capitalize">
                                                {{ str_replace('sueltos', 'Sueltos ', $tipoDetalle) }}
                                            </span>

                                        </div>

                                    </div>

                                </div>

                                <button
                                    wire:click="$set('detalleProductoId', null)"
                                    class="text-gray-400 hover:text-gray-600 transition-colors w-8 h-8 rounded-lg hover:bg-gray-100 flex items-center justify-center">
                                    <i class="fas fa-times"></i>
                                </button>

                            </div>

                            <div class="px-6 py-4 overflow-y-auto" style="max-height: 60vh;">

                                @if ($tipoDetalle === 'sellado')

                                    @forelse ($detallesInventario as $item)

                                        <div class="bg-white border border-gray-200 rounded-xl p-4 mb-2">

                                            <div class="flex items-center justify-between">

                                                <div class="flex items-center gap-2">

                                                    <span class="px-2 py-0.5 bg-amber-100 text-amber-700 text-[10px] font-bold rounded-full">
                                                        SELLADO
                                                    </span>

                                                    @if ($item->serie)
                                                        <span class="font-mono text-sm font-bold text-gray-800">
                                                            {{ $item->serie }}
                                                        </span>
                                                    @endif

                                                </div>

                                                <span class="text-xs text-gray-400">
                                                    {{ $item->created_at->format('d/m/Y') }}
                                                </span>

                                            </div>

                                            <div class="mt-2 text-xs text-gray-500">
                                                <i class="fas fa-map-marker-alt mr-1"></i>
                                                {{ $item->sede?->nombre ?? '—' }}
                                            </div>

                                        </div>

                                    @empty

                                        <p class="text-gray-400 text-sm text-center py-6">
                                            Sin unidades.
                                        </p>

                                    @endforelse

                                @elseif ($tipoDetalle === 'incompleto')

                                    @forelse ($detallesInventario as $item)

                                        @php

                                             $receta = \App\Models\KitComponente::where(
                                                 'producto_kit_id',
                                                 $item->producto_id
                                             )->get();

                                             $piezasActuales = $item->piezasEnKit ?? collect();

                                             $countActuales = $piezasActuales
                                                 ->pluck('producto_id')
                                                 ->countBy()
                                                 ->toArray();

                                             $faltanItems = $receta->filter(function ($r) use ($countActuales) {
                                                 return ($countActuales[$r->producto_componente_id] ?? 0) < $r->cantidad_esperada;
                                             });

                                        @endphp

                                        <div class="bg-white border border-gray-200 rounded-xl p-4 mb-2">

                                            <div class="flex items-center justify-between mb-2">

                                                <div class="flex items-center gap-2">

                                                    <span class="px-2 py-0.5 bg-orange-100 text-orange-700 text-[10px] font-bold rounded-full">
                                                        KIT INCOMPLETO
                                                    </span>

                                                    @if ($item->serie)
                                                        <span class="font-mono text-sm font-bold text-gray-800">
                                                            {{ $item->serie }}
                                                        </span>
                                                    @endif

                                                </div>

                                                <span class="text-xs text-gray-400">
                                                    {{ $item->created_at->format('d/m/Y') }}
                                                </span>

                                            </div>

                                            <div class="text-xs text-gray-500 mb-2">
                                                <i class="fas fa-map-marker-alt mr-1"></i>
                                                {{ $item->sede?->nombre ?? '—' }}
                                            </div>

                                            @if ($faltanItems->isNotEmpty())

                                                <div x-data="{ ver: false }" class="mt-2">

                                                    <button
                                                        x-on:click="ver = !ver"
                                                        type="button"
                                                        class="inline-flex items-center gap-1.5 text-[11px] font-semibold text-orange-600 hover:text-orange-700 transition">

                                                        <i
                                                            class="fas"
                                                            :class="ver ? 'fa-chevron-up' : 'fa-chevron-down'">
                                                        </i>

                                                        Faltan {{ $faltanItems->count() }} componente(s)

                                                    </button>

                                                    <div
                                                        x-show="ver"
                                                        x-collapse
                                                        class="mt-2 space-y-1">

                                                        @foreach ($faltanItems as $faltante)

                                                             @php
                                                                $tiene = $countActuales[$faltante->producto_componente_id] ?? 0;
                                                                $necesita = $faltante->cantidad_esperada;
                                                            @endphp

                                                            <div class="flex items-center gap-2 p-2 bg-orange-50 border border-orange-200 rounded-lg text-xs">

                                                                <i class="fas fa-exclamation-circle text-orange-500"></i>

                                                                <span class="font-semibold text-gray-700">
                                                                    {{ $faltante->componente->nombre ?? '—' }}
                                                                </span>

                                                                <span class="text-gray-500">
                                                                    · Tiene {{ $tiene }},
                                                                    necesita {{ $necesita }}
                                                                </span>

                                                            </div>

                                                        @endforeach

                                                    </div>

                                                </div>

                                            @else

                                                <div class="flex items-center gap-2 text-xs text-green-600 mt-2">
                                                    <i class="fas fa-check-circle"></i>
                                                    Todos los componentes presentes
                                                </div>

                                            @endif

                                            <div class="mt-3">

                                                <button
                                                    wire:click="abrirCompletarKit({{ $item->id }})"
                                                    class="px-3 py-1.5 bg-indigo-600 text-white text-xs font-bold rounded-lg hover:bg-indigo-700 transition">

                                                    <i class="fas fa-plus mr-1"></i>
                                                    Completar kit

                                                </button>

                                            </div>

                                        </div>

                                    @empty

                                        <p class="text-gray-400 text-sm text-center py-6">
                                            Sin kits incompletos.
                                        </p>

                                    @endforelse

                                @elseif ($tipoDetalle === 'consumido')

                                    @forelse ($detallesInventario as $kitItem)

                                        <div class="bg-white border border-gray-200 rounded-xl mb-2 overflow-hidden">

                                            <div class="px-4 py-3 bg-purple-50 border-b border-gray-100">
                                                <div class="flex items-center justify-between">

                                                    <div class="flex items-center gap-3">
                                                        <span class="px-2 py-0.5 bg-purple-100 text-purple-700 text-[10px] font-bold rounded-full">
                                                            KIT CONSUMIDO
                                                        </span>

                                                        <span class="font-semibold text-gray-800">
                                                            {{ $kitItem->producto->nombre }}
                                                        </span>

                                                        <span class="text-xs text-gray-400">#{{ $kitItem->id }}</span>
                                                    </div>

                                                    <span class="text-xs text-gray-400">
                                                        {{ $kitItem->created_at->format('d/m/Y') }}
                                                    </span>

                                                </div>
                                            </div>

                                            @if ($kitItem->serviceOrder)
                                                <div class="px-4 py-3 grid grid-cols-3 gap-2 text-xs border-b border-gray-100 bg-gray-50">

                                                    <div class="bg-white rounded-lg p-2.5 border border-gray-200">
                                                        <span class="text-[10px] text-gray-400 uppercase font-bold">
                                                            Técnico
                                                        </span>
                                                        <p class="font-semibold text-gray-800 mt-0.5">
                                                            {{ $kitItem->serviceOrder->tecnico?->name ?? '—' }}
                                                        </p>
                                                    </div>

                                                    <div class="bg-white rounded-lg p-2.5 border border-gray-200">
                                                        <span class="text-[10px] text-gray-400 uppercase font-bold">
                                                            Cliente
                                                        </span>
                                                        <p class="font-semibold text-gray-800 mt-0.5">
                                                            {{ $kitItem->serviceOrder->cliente?->nombre ?? '—' }}
                                                        </p>
                                                    </div>

                                                    <div class="bg-white rounded-lg p-2.5 border border-gray-200">
                                                        <span class="text-[10px] text-gray-400 uppercase font-bold">
                                                            Vehículo
                                                        </span>
                                                        <p class="font-semibold text-gray-800 mt-0.5">
                                                            @if ($kitItem->serviceOrder->vehiculo)
                                                                {{ $kitItem->serviceOrder->vehiculo->marca ?? '' }}
                                                                {{ $kitItem->serviceOrder->vehiculo->modelo ?? '' }}
                                                                <span class="text-gray-400">·</span>
                                                                {{ $kitItem->serviceOrder->vehiculo->placa ?? '' }}
                                                            @else
                                                                —
                                                            @endif
                                                        </p>
                                                    </div>

                                                </div>

                                                <div class="px-4 py-2 text-[10px] text-gray-400 border-b border-gray-100">
                                                    <i class="fas fa-file-alt mr-1"></i>
                                                    Orden #{{ $kitItem->serviceOrder->id }}
                                                    <span class="mx-1">·</span>
                                                    <i class="fas fa-map-marker-alt mr-1"></i>
                                                    {{ $kitItem->sede?->nombre ?? '—' }}
                                                </div>
                                            @else
                                                <div class="px-4 py-2 text-xs text-gray-400 border-b border-gray-100">
                                                    <i class="fas fa-map-marker-alt mr-1"></i>
                                                    {{ $kitItem->sede?->nombre ?? '—' }}
                                                </div>
                                            @endif

                                            @if ($kitItem->piezasEnKit->isNotEmpty())
                                                <div class="px-4 py-3" x-data="{ open: false }">
                                                    <button
                                                        type="button"
                                                        @click="open = !open"
                                                        class="w-full inline-flex items-center justify-between p-2 bg-gray-50 rounded-lg border border-gray-200 hover:bg-gray-100 transition">
                                                        <span class="text-sm font-semibold text-purple-700 flex items-center gap-2">
                                                            <i class="fas fa-box text-purple-500"></i>
                                                            Ver piezas
                                                        </span>
                                                        <i class="fas" :class="open ? 'fa-chevron-up' : 'fa-chevron-down'" class="text-gray-400 text-sm"></i>
                                                    </button>

                                                    <div x-show="open" x-collapse class="mt-2 space-y-3">

                                                        @php
                                                            $serializadas = $kitItem->piezasEnKit->filter(fn ($p) => $p->serie && !str_starts_with($p->serie, 'CANT-'));
                                                            $cantidad = $kitItem->piezasEnKit->filter(fn ($p) => !$p->serie || str_starts_with($p->serie, 'CANT-'));
                                                            $cantidadAgrupada = $cantidad->groupBy('producto_id')->map(fn ($g) => ['producto' => $g->first()->producto, 'count' => $g->count()]);
                                                        @endphp

                                                        @if ($serializadas->isNotEmpty())
                                                            <div class="mb-3">
                                                                <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-1.5">
                                                                    Serializadas ({{ $serializadas->count() }})
                                                                </p>
                                                                <div class="space-y-1">
                                                                    @foreach ($serializadas as $pieza)
                                                                        <div class="flex items-center gap-3 p-2.5 bg-white border border-gray-200 rounded-lg">
                                                                            <div class="w-8 h-8 rounded-full bg-purple-100 flex items-center justify-center shrink-0">
                                                                                <i class="fas fa-barcode text-purple-500 text-xs"></i>
                                                                            </div>
                                                                            <div class="flex-1 min-w-0">
                                                                                <p class="text-sm font-medium text-gray-800 truncate">
                                                                                    {{ $pieza->producto->nombre }}
                                                                                </p>
                                                                                <p class="text-[10px] text-gray-500 font-mono">
                                                                                    {{ $pieza->serie }}
                                                                                </p>
                                                                                @if (!empty($pieza->atributos['produce'] ?? ''))
                                                                                    <p class="text-[10px] text-indigo-600 font-medium">
                                                                                        {{ $pieza->atributos['produce'] }}
                                                                                    </p>
                                                                                @endif
                                                                            </div>
                                                                            <span class="px-2 py-0.5 bg-purple-100 text-purple-700 text-[10px] font-bold rounded-full">
                                                                                INSTALADO
                                                                            </span>
                                                                        </div>
                                                                    @endforeach
                                                                </div>
                                                            </div>
                                                        @endif

                                                        @if ($cantidadAgrupada->isNotEmpty())
                                                            <div>
                                                                <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-1.5">
                                                                    Por cantidad
                                                                </p>
                                                                <div class="space-y-1">
                                                                    @foreach ($cantidadAgrupada as $prod => $g)
                                                                        <div class="flex items-center gap-3 p-2.5 bg-white border border-gray-200 rounded-lg">
                                                                            <div class="w-8 h-8 rounded-full bg-amber-100 flex items-center justify-center shrink-0">
                                                                                <i class="fas fa-cubes text-amber-500 text-xs"></i>
                                                                            </div>
                                                                            <div class="flex-1 min-w-0">
                                                                                <p class="text-sm font-medium text-gray-800 truncate">
                                                                                    {{ $g['producto']->nombre }}
                                                                                </p>
                                                                            </div>
                                                                            <span class="px-2 py-0.5 bg-amber-100 text-amber-700 text-[10px] font-bold rounded-full">
                                                                                x{{ $g['count'] }}
                                                                            </span>
                                                                        </div>
                                                                    @endforeach
                                                                </div>
                                                            </div>
                                                        @endif

                                                    </div>
                                                </div>
                                            @endif

                                        </div>

                                    @empty

                                        <p class="text-gray-400 text-sm text-center py-6">
                                            Sin consumidos.
                                        </p>

                                    @endforelse

                                @elseif ($tipoDetalle === 'sueltosSerializados')

                                    @foreach ($agrupados as $produce => $itemsGrupo)

                                        @php
                                            $primerItem = is_array($itemsGrupo) ? reset($itemsGrupo) : $itemsGrupo->first();
                                            $attrsPrimer = $primerItem->atributos ?? [];
                                            $ingreso = $attrsPrimer['fecha_recepcion'] ?? $attrsPrimer['recepcion_fecha'] ?? null;
                                        @endphp

                                        <div class="mb-3 border border-green-200 bg-green-50 rounded-xl p-3">

                                            <div class="flex items-center gap-2 mb-2 pb-2 border-b border-green-200">

                                                <i class="fas fa-industry text-green-600 text-xs"></i>

                                                <span class="text-xs font-bold text-green-700 uppercase tracking-wider">
                                                    Produce: {{ $produce }}
                                                </span>

                                                <span class="text-[10px] font-normal text-green-600">
                                                    ({{ count($itemsGrupo) }})
                                                </span>

                                                @if ($ingreso)
                                                    <span class="ml-2 px-1.5 py-0.5 bg-indigo-100 text-indigo-700 text-[10px] font-medium rounded">
                                                        Ingreso: {{ $ingreso }}
                                                    </span>
                                                @endif

                                            </div>

                                            <div class="space-y-1.5">

                                                @foreach ($itemsGrupo as $item)

                                                    @php
                                                        $esquema = $item->producto->categoria->esquema_atributos ?? ['serie'];
                                                        $campos = is_string($esquema) ? json_decode($esquema, true) : $esquema;
                                                        $attrs = $item->atributos ?? [];
                                                        $sinSerie = empty($item->serie);
                                                    @endphp

                                                    <div class="flex items-center justify-between bg-white border border-green-200 rounded-lg p-2.5 text-xs">

                                                        <div class="flex items-center gap-2 flex-wrap">

                                                            <span class="px-1.5 py-0.5 {{ $item->serie ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }} text-[10px] font-bold rounded">
                                                                {{ $item->serie ? 'SERIE' : 'SIN SERIE' }}
                                                            </span>

                                                            <span class="font-mono font-bold text-gray-800">
                                                                {{ $item->serie ?? 'Sin serie' }}
                                                            </span>

                                                            <span class="text-gray-500">
                                                                {{ $item->producto?->nombre ?? 'Producto' }}
                                                            </span>

                                                            @foreach ($campos as $campo)
                                                                @if ($campo !== 'serie' && !empty($attrs[$campo] ?? ''))
                                                                    <span class="px-1.5 py-0.5 bg-gray-100 text-gray-700 text-[10px] font-medium rounded">
                                                                        {{ $campo === 'capacidad' ? 'Capac.' : ucfirst($campo) }}: {{ $attrs[$campo] }}
                                                                    </span>
                                                                @endif
                                                            @endforeach

                                                        </div>

                                                        <div class="flex items-center gap-1">
                                                            <span class="text-gray-400">{{ $item->sede?->nombre ?? '—' }}</span>

                                                            @if ($sinSerie)
                                                                <button type="button"
                                                                    wire:click="abrirEditarItem({{ $item->id }})"
                                                                    class="px-2 py-1 text-[10px] font-bold text-amber-700 bg-amber-100 rounded hover:bg-amber-200 transition whitespace-nowrap">
                                                                    <i class="fas fa-edit mr-0.5"></i> Completar
                                                                </button>
                                                            @endif
                                                        </div>

                                                    </div>
                                                @endforeach

                                            </div>

                                        </div>

                                    @endforeach

                                    @if (count($sinGrupo) > 0)

                                        <div class="mb-3 border border-gray-200 bg-gray-50 rounded-xl p-3">

                                            <div class="flex items-center gap-2 mb-2 pb-2 border-b border-gray-200">

                                                <i class="fas fa-question-circle text-gray-400 text-xs"></i>

                                                <span class="text-xs font-bold text-gray-500 uppercase tracking-wider">
                                                    Sin produce / Sin serie
                                                </span>

                                                <span class="text-[10px] font-normal text-gray-400">
                                                    ({{ count($sinGrupo) }})
                                                </span>

                                            </div>

                                            <div class="space-y-1.5">

                                                @foreach ($sinGrupo as $item)

                                                    @php
                                                        $esquema = $item->producto->categoria->esquema_atributos ?? ['serie'];
                                                        $campos = is_string($esquema) ? json_decode($esquema, true) : $esquema;
                                                        $attrs = $item->atributos ?? [];
                                                        $faltan = collect($campos)->filter(fn ($c) => empty($attrs[$c] ?? ''))->values();
                                                        $sinSerie = empty($item->serie);
                                                    @endphp

                                                    <div class="flex items-center justify-between bg-white border border-gray-200 rounded-lg p-2.5 text-xs">

                                                        <div class="flex items-center gap-2">

                                                            <span class="px-1.5 py-0.5 {{ $item->serie ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }} text-[10px] font-bold rounded">

                                                                {{ $item->serie ? 'SERIE' : 'SIN SERIE' }}

                                                            </span>

                                                            <span class="font-mono font-bold text-gray-800">

                                                                {{ $item->serie ?? '#' . $item->id }}

                                                            </span>

                                                            <span class="text-gray-500">

                                                                {{ $item->producto?->nombre ?? 'Producto' }}

                                                            </span>

                                                            @foreach ($campos as $campo)
                                                                @if ($campo !== 'serie' && !empty($attrs[$campo] ?? ''))
                                                                    <span class="px-1.5 py-0.5 bg-gray-100 text-gray-700 text-[10px] font-medium rounded">
                                                                        {{ $campo === 'capacidad' ? 'Capac.' : ucfirst($campo) }}: {{ $attrs[$campo] }}
                                                                    </span>
                                                                @endif
                                                            @endforeach

                                                            @if ($faltan->isNotEmpty())
                                                                <span class="px-1.5 py-0.5 bg-amber-100 text-amber-700 text-[10px] font-medium rounded">
                                                                    Faltan: {{ $faltan->implode(', ') }}
                                                                </span>
                                                            @endif

                                                        </div>

                                                        <div class="flex items-center gap-1">
                                                            <span class="text-gray-400">{{ $item->sede?->nombre ?? '—' }}</span>

                                                            @if ($sinSerie)
                                                                <button type="button"
                                                                    wire:click="abrirEditarItem({{ $item->id }})"
                                                                    class="px-2 py-1 text-[10px] font-bold text-amber-700 bg-amber-100 rounded hover:bg-amber-200 transition whitespace-nowrap">
                                                                    <i class="fas fa-edit mr-0.5"></i> Completar
                                                                </button>
                                                            @endif
                                                        </div>

                                                    </div>
                                                @endforeach

                                            </div>

                                        </div>

                                    @endif

                                    @if (empty($agrupados) && empty($sinGrupo))

                                        <p class="text-gray-400 text-sm text-center py-6">
                                            Sin unidades.
                                        </p>

                                    @endif

                                @elseif ($tipoDetalle === 'sueltosCantidad')

                                    @php
                                        $stockDetalle = $sueltosCantidadDetalle ?? collect();
                                    @endphp

                                    @forelse ($stockDetalle as $stock)

                                        <div class="bg-white border border-gray-200 rounded-xl p-4 mb-2">

                                            <div class="flex items-center justify-between">

                                                <div class="flex items-center gap-3">

                                                    <div class="w-9 h-9 rounded-lg bg-indigo-50 flex items-center justify-center shrink-0">
                                                        <i class="fas fa-cubes text-indigo-500 text-sm"></i>
                                                    </div>

                                                    <div>

                                                        <p class="text-sm font-bold text-gray-800">
                                                            {{ $stock->producto?->nombre ?? 'Producto' }}
                                                        </p>

                                                        <p class="text-[10px] text-gray-400">
                                                            {{ $stock->sede?->nombre ?? '—' }}
                                                        </p>

                                                    </div>

                                                </div>

                                                <div class="text-right">

                                                    <p class="text-lg font-black text-indigo-600">
                                                        {{ $stock->cantidad }}
                                                    </p>

                                                    <p class="text-[10px] text-gray-400 uppercase">
                                                        unidades
                                                    </p>

                                                </div>

                                            </div>

                                        </div>

                                    @empty

                                        <p class="text-gray-400 text-sm text-center py-6">
                                            Sin stock por cantidad.
                                        </p>

                                    @endforelse

                                @else

                                    @forelse ($detallesInventario as $item)

                                        <div class="bg-white border border-gray-200 rounded-xl p-3 mb-2">

                                            <div class="flex items-center justify-between text-xs">

                                                <div class="flex items-center gap-2">

                                                    <span class="font-mono text-gray-400">
                                                        #{{ $item->id }}
                                                    </span>

                                                    @if ($item->serie)

                                                        <span class="font-medium text-gray-700">
                                                            {{ $item->serie }}
                                                        </span>

                                                    @endif

                                                </div>

                                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-gray-100 text-gray-600">
                                                    {{ ucfirst($item->estado) }}
                                                </span>

                                            </div>

                                        </div>

                                    @empty

                                        <p class="text-gray-400 text-sm text-center py-6">
                                            Sin unidades.
                                        </p>

                                    @endforelse

                                @endif

                            </div>

                            <div class="px-6 py-3 border-t border-gray-200 flex justify-end bg-gray-50 rounded-b-xl">

                                <button
                                    wire:click="$set('detalleProductoId', null)"
                                    class="px-5 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition font-medium text-sm">
                                    Cerrar
                                </button>

                            </div>

                        </div>

                    </div>

                </div>

            @endif

        @endif

        @if ($vistaActual === 'kits')

            @php
                $kitsSell = $kits['sellados'] ?? collect();
                $kitsIncomp = $kits['incompletos'] ?? collect();
                $kitsCons = $kits['consumidos'] ?? collect();
                $kitsComp = $kits['completados'] ?? collect();
            @endphp

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

                <div class="bg-gray-50 rounded-xl border border-gray-200 p-4">

                    <h3 class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-3 flex items-center gap-2">

                        <i class="fas fa-box text-amber-500"></i>

                        Kits sellados

                        <span class="text-xs font-normal text-gray-400">
                            ({{ $kitsSell->flatten()->count() }})
                        </span>

                    </h3>

                    <div class="space-y-2 max-h-80 overflow-y-auto pr-1" x-data>

                        @forelse ($kitsSell as $productoId => $items)

                            @php
                                $prod = $items->first()?->producto;
                            @endphp

                            <div class="bg-white border border-gray-200 rounded-xl p-3 flex items-center gap-3 cursor-pointer hover:border-amber-400 hover:shadow-sm transition-all"
                                 @click="$dispatch('ver-componentes-kit', { productoId: {{ $productoId }} })">

                                <div class="w-10 h-10 rounded-lg bg-amber-50 flex items-center justify-center shrink-0">
                                    <i class="fas fa-box text-amber-500 text-sm"></i>
                                </div>

                                <div class="min-w-0 flex-1">

                                    <p class="text-sm font-bold text-gray-800 truncate">
                                        {{ $prod?->nombre ?? 'Producto' }}
                                    </p>

                                    <p class="text-[10px] text-gray-400">
                                        {{ $items->count() }} unidad(es) · {{ $items->first()?->sede?->nombre ?? '—' }}
                                    </p>

                                </div>

                                <span class="px-2 py-0.5 bg-amber-100 text-amber-700 text-[10px] font-bold rounded-full">
                                    {{ $items->count() }}
                                </span>

                            </div>

                        @empty

                            <p class="text-gray-400 text-xs text-center py-4">
                                Sin kits sellados
                            </p>

                        @endforelse

                    </div>

                </div>

                <div class="bg-gray-50 rounded-xl border border-gray-200 p-4">

                    <h3 class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-3 flex items-center gap-2">

                        <i class="fas fa-box-open text-orange-500"></i>

                        Kits incompletos

                        <span class="text-xs font-normal text-gray-400">
                            ({{ $kitsIncomp->flatten()->count() }})
                        </span>

                    </h3>

                    <div class="space-y-2 max-h-80 overflow-y-auto pr-1" x-data>

                        @forelse ($kitsIncomp as $productoId => $items)

                            @php
                                $prod = $items->first()?->producto;
                            @endphp

                            @foreach ($items as $kitItem)

                                @php

                                    $receta = \App\Models\KitComponente::where(
                                        'producto_kit_id',
                                        $kitItem->producto_id
                                    )->get();

                                    $piezasAct = $kitItem->piezasEnKit ?? collect();

                                    $countAct = $piezasAct
                                        ->pluck('producto_id')
                                        ->countBy()
                                        ->toArray();

                                    $faltanComps = $receta->filter(function ($r) use ($countAct) {
                                        return ($countAct[$r->producto_componente_id] ?? 0) < $r->cantidad_esperada;
                                    });

                                @endphp

                                <div class="bg-white border border-gray-200 rounded-xl p-3">

                                    <div class="flex items-center gap-3">

                                        <div class="w-10 h-10 rounded-lg bg-orange-50 flex items-center justify-center shrink-0">
                                            <i class="fas fa-box-open text-orange-500 text-sm"></i>
                                        </div>

                                        <div class="min-w-0 flex-1">

                                            <p class="text-sm font-bold text-gray-800 truncate">
                                                {{ $prod?->nombre ?? 'Producto' }}
                                            </p>

                                            <p class="text-[10px] text-gray-400">
                                                {{ $kitItem->sede?->nombre ?? '—' }}
                                                · #{{ $kitItem->id }}
                                            </p>

                                        </div>

                                        <button
                                            wire:click="abrirCompletarKit({{ $kitItem->id }})"
                                            class="px-3 py-1.5 bg-indigo-600 text-white text-[10px] font-bold rounded-lg hover:bg-indigo-700 transition whitespace-nowrap">

                                            <i class="fas fa-plus mr-1"></i>
                                            Completar

                                        </button>

                                    </div>

                                    @if ($faltanComps->isNotEmpty())

                                        <div
                                            x-data="{ open: false }"
                                            class="mt-2 pt-2 border-t border-gray-100">

                                            <button
                                                x-on:click="open = !open"
                                                type="button"
                                                class="inline-flex items-center gap-1.5 text-[11px] font-semibold text-orange-600 hover:text-orange-700">

                                                <i
                                                    class="fas"
                                                    :class="open ? 'fa-chevron-up' : 'fa-chevron-down'">
                                                </i>

                                                Faltan {{ $faltanComps->count() }}

                                            </button>

                                            <div
                                                x-show="open"
                                                x-collapse
                                                class="mt-1.5 space-y-1">

                                                @foreach ($faltanComps as $f)

                                                    <div class="flex items-center gap-2 text-[11px] p-1.5 bg-orange-50 border border-orange-200 rounded-lg">

                                                        <i class="fas fa-exclamation-circle text-orange-500 text-[10px]"></i>

                                                         <span class="font-semibold text-gray-700">
                                                             {{ $f->componente->nombre ?? '—' }}
                                                         </span>

                                                         <span class="text-gray-500">
                                                             ×{{ $f->cantidad_esperada - ($countAct[$f->producto_componente_id] ?? 0) }}
                                                        </span>

                                                    </div>

                                                @endforeach

                                            </div>

                                        </div>

                                    @endif

                                </div>

                            @endforeach

                        @empty

                            <p class="text-gray-400 text-xs text-center py-4">
                                Sin kits incompletos
                            </p>

                        @endforelse

                    </div>

                </div>

            </div>

            @if ($kitsComp->isNotEmpty())
            <div class="bg-gray-50 rounded-xl border border-gray-200 p-4">

                <h3 class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-3 flex items-center gap-2">

                    <i class="fas fa-check-circle text-purple-500"></i>

                    Kits completados

                    <span class="text-xs font-normal text-gray-400">
                        ({{ $kitsComp->flatten()->count() }})
                    </span>

                </h3>

                <div class="space-y-2 max-h-80 overflow-y-auto pr-1" x-data>

                    @forelse ($kitsComp as $productoId => $items)

                        @php
                            $prod = $items->first()?->producto;
                        @endphp

                        @foreach ($items as $kitItem)

                            <div class="bg-white border border-gray-200 rounded-xl p-3 flex items-center gap-3 cursor-pointer hover:border-purple-400 hover:shadow-sm transition-all"
                                 @click="$dispatch('ver-componentes-kit', { productoId: {{ $productoId }} })">

                                <div class="w-10 h-10 rounded-lg bg-purple-50 flex items-center justify-center shrink-0">
                                    <i class="fas fa-check-circle text-purple-500 text-sm"></i>
                                </div>

                                <div class="min-w-0 flex-1">

                                    <p class="text-sm font-bold text-gray-800 truncate">
                                        {{ $prod?->nombre ?? 'Producto' }}
                                    </p>

                                    <p class="text-[10px] text-gray-400">
                                        {{ $kitItem->sede?->nombre ?? '—' }}
                                        · #{{ $kitItem->id }}
                                    </p>

                                </div>

                                <span class="px-2 py-0.5 bg-purple-100 text-purple-700 text-[10px] font-bold rounded-full">
                                    COMPLETADO
                                </span>

                            </div>

                        @endforeach

                    @empty

                        <p class="text-gray-400 text-xs text-center py-4">
                            Sin kits completados
                        </p>

                    @endforelse

                </div>

            </div>
            @endif

            @if ($kitsCons->isNotEmpty())
            <div class="bg-gray-50 rounded-xl border border-gray-200 p-4">

                <h3 class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-3 flex items-center gap-2">

                    <i class="fas fa-fire text-red-500"></i>

                    Kits consumidos

                    <span class="text-xs font-normal text-gray-400">
                        ({{ $kitsCons->flatten()->count() }})
                    </span>

                </h3>

                <div class="space-y-2 max-h-80 overflow-y-auto pr-1" x-data>

                    @forelse ($kitsCons as $productoId => $items)

                        @php
                            $prod = $items->first()?->producto;
                        @endphp

                        @foreach ($items as $kitItem)

                            <div class="bg-white border border-gray-200 rounded-xl p-3 flex items-center gap-3 cursor-pointer hover:border-red-400 hover:shadow-sm transition-all"
                                 @click="$dispatch('ver-componentes-kit', { productoId: {{ $productoId }} })">

                                <div class="w-10 h-10 rounded-lg bg-red-50 flex items-center justify-center shrink-0">
                                    <i class="fas fa-fire text-red-500 text-sm"></i>
                                </div>

                                <div class="min-w-0 flex-1">

                                    <p class="text-sm font-bold text-gray-800 truncate">
                                        {{ $prod?->nombre ?? 'Producto' }}
                                    </p>

                                    <p class="text-[10px] text-gray-400">
                                        {{ $kitItem->sede?->nombre ?? '—' }}
                                        · #{{ $kitItem->id }}
                                    </p>

                                    @if ($kitItem->serviceOrder)
                                        <p class="text-[10px] text-gray-400 mt-0.5">
                                            <i class="fas fa-file-alt mr-1"></i>
                                            SO #{{ $kitItem->service_order_id }}
                                            · {{ $kitItem->serviceOrder->tecnico?->name ?? '—' }}
                                        </p>
                                    @endif

                                </div>

                                <span class="px-2 py-0.5 bg-red-100 text-red-700 text-[10px] font-bold rounded-full">
                                    CONSUMIDO
                                </span>

                            </div>

                        @endforeach

                    @empty

                        <p class="text-gray-400 text-xs text-center py-4">
                            Sin kits consumidos
                        </p>

                    @endforelse

                </div>

            </div>
            @endif

        @endif

        @if ($vistaActual === 'catalogo')

            <div
                class="bg-white rounded-xl border border-gray-200 overflow-hidden"
                x-data="{ expandir: false }">

                <div class="px-4 py-3">

                    <div class="flex flex-wrap items-center gap-3">

                        <div class="flex items-center bg-gray-50 rounded-lg px-3 py-2 flex-1 min-w-[200px]">

                            <i class="fas fa-search text-gray-400 text-sm mr-2"></i>

                            <input
                                class="bg-transparent outline-none text-sm w-full border-none focus:ring-0"
                                type="text"
                                wire:model.live.debounce.400ms="buscar"
                                placeholder="Buscar por nombre o código...">

                        </div>

                        <button
                            type="button"
                            x-on:click="expandir = !expandir"
                            class="px-3 py-2 text-xs font-semibold text-gray-600 bg-gray-100 hover:bg-gray-200 rounded-lg transition-colors flex items-center gap-1.5">

                            <i class="fas fa-filter"></i>
                            Más filtros

                            <i
                                class="fas fa-chevron-down text-[10px] transition-transform"
                                x-bind:class="expandir ? 'rotate-180' : ''">
                            </i>

                        </button>

                        <button
                            type="button"
                            wire:click="resetFilters"
                            class="px-3 py-2 text-xs font-semibold text-gray-500 bg-gray-100 hover:bg-gray-200 rounded-lg transition-colors flex items-center gap-1.5">

                            <i class="fas fa-eraser"></i>
                            Limpiar

                        </button>

                    </div>

                    <div x-show="expandir" x-collapse>

                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 mt-4 pt-4 border-t border-gray-100">

                            <div>

                                <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">
                                    Stock
                                </label>

                                <select
                                    wire:model.live="filterStock"
                                    class="w-full text-sm border border-gray-200 rounded-lg px-3 py-2 bg-white focus:ring-2 focus:ring-indigo-500">

                                    <option value="todos">
                                        Todos
                                    </option>

                                    <option value="bajo">
                                        Stock bajo
                                    </option>

                                    <option value="sin">
                                        Sin stock
                                    </option>

                                </select>

                            </div>

                            <div>

                                <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">
                                    Proveedor
                                </label>

                                <input
                                    type="text"
                                    wire:model.live="filterProveedor"
                                    placeholder="Buscar proveedor..."
                                    class="w-full text-sm border border-gray-200 rounded-lg px-3 py-2 bg-white focus:ring-2 focus:ring-indigo-500">

                            </div>

                        </div>

                    </div>

                </div>

            </div>

            @if ($productos->count())

                <div class="max-h-[65vh] overflow-y-auto pr-1">
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">

                        @foreach ($productos as $p)

                            <div class="bg-white rounded-xl border border-gray-200 p-4 hover:shadow-md transition-shadow flex flex-col">

                                <div class="flex items-start justify-between mb-3">

                                    <div class="flex-1 min-w-0">

                                        <p class="text-sm font-bold text-gray-800 truncate">
                                            {{ $p->nombre }}
                                        </p>

                                        <p class="text-xs text-gray-400 mt-0.5">
                                            {{ $p->categoria->nombre }}
                                        </p>

                                        @if ($p->marca)

                                            <p class="text-xs text-gray-400">
                                                {{ $p->marca }}
                                            </p>

                                        @endif

                                    </div>

                                    @if ($p->categoria->es_kit)

                                        <span class="px-2 py-0.5 bg-purple-100 text-purple-700 text-[10px] font-bold rounded-full flex-shrink-0 ml-2">
                                            KIT
                                        </span>

                                    @endif

                                </div>

                                <div class="mb-4">

                                    @php

                                        $stock = $p->categoria->es_kit
                                            ? $p->stockTotal()
                                            : $p->stock_disponible;

                                        $stockColor = $stock > 0
                                            ? 'text-green-600'
                                            : 'text-red-500';

                                    @endphp

                                    <p class="text-2xl font-bold {{ $stockColor }}">
                                        {{ $stock }}
                                    </p>

                                    <p class="text-[10px] text-gray-400 uppercase tracking-wider">
                                        disponible
                                    </p>

                                </div>

                                <div class="mt-auto pt-3 border-t border-gray-100 flex items-center justify-between">

                                    <div class="flex items-center gap-1">

                                        @if ($p->categoria->es_kit)

                                            <button
                                                wire:click="$dispatch('ver-componentes-kit', { productoId: {{ $p->id }} })"
                                                class="w-8 h-8 flex items-center justify-center text-amber-600 bg-amber-50 hover:bg-amber-100 rounded-lg transition-colors"
                                                title="Ver componentes">

                                                <i class="fa-solid fa-puzzle-piece text-xs"></i>

                                            </button>

                                        @endif

                                        <button
                                            wire:click="$dispatch('abrir-modal-entrada', { productoId: {{ $p->id }} })"
                                            class="w-8 h-8 flex items-center justify-center text-emerald-600 bg-emerald-50 hover:bg-emerald-100 rounded-lg transition-colors"
                                            title="Registrar entrada">

                                            <i class="fa-solid fa-plus text-xs"></i>

                                        </button>

                                        <button
                                            wire:click="$dispatch('abrir-modal-editar-producto', { productoId: {{ $p->id }} })"
                                            class="w-8 h-8 flex items-center justify-center text-blue-600 bg-blue-50 hover:bg-blue-100 rounded-lg transition-colors"
                                            title="Editar">

                                            <i class="fa-solid fa-edit text-xs"></i>

                                        </button>

                                    </div>

                                </div>

                            </div>

                        @endforeach

                    </div>
                </div>

                <div class="mt-3">
                    {{ $productos->links('pagination::tailwind') }}
                </div>

            @else

                <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">

                    <div class="px-6 py-16 text-center">

                        <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                            <i class="fas fa-box-open text-gray-400 text-2xl"></i>
                        </div>

                        <p class="text-gray-500 text-sm font-medium">
                            No hay productos registrados
                        </p>

                    </div>

                </div>

            @endif

        @endif

    </div>

    <livewire:almacen.productos.crear />
    <livewire:almacen.productos.registrar-entrada />
    <livewire:almacen.productos.editar />


    @if ($modalCompletarKitAbierto)
        @php
            $faltantes = collect($completarKitComponentes)->filter(fn($c) => $c['faltan'] > 0);
            $seleccion = $completarKitSeleccion;
            $puedeCompletar = $faltantes->every(function ($c) use ($seleccion) {
                $elegidos = collect($seleccion[$c['producto_id']] ?? [])->filter()->count();
                return $elegidos >= $c['faltan'];
            });
        @endphp
        <div class="fixed inset-0 z-50 overflow-y-auto" aria-modal="true" role="dialog">
            <div class="fixed inset-0 bg-gray-500/75 transition-opacity" wire:click="cerrarCompletarKit"></div>
            <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
                <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
                    <div class="relative transform overflow-hidden rounded-xl bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-2xl border border-gray-200"
                         wire:click.away="cerrarCompletarKit">
                        <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-3">
                                    <div class="w-9 h-9 rounded-lg bg-indigo-100 flex items-center justify-center">
                                        <i class="fas fa-puzzle-piece text-indigo-600 text-sm"></i>
                                    </div>
                                    <div>
                                        <h3 class="text-base font-bold text-gray-800">Completar kit</h3>
                                        <p class="text-xs text-gray-500 mt-0.5">{{ $completarKitNombre }}</p>
                                    </div>
                                </div>
                                <button wire:click="cerrarCompletarKit" class="text-gray-400 hover:text-gray-600 transition w-8 h-8 rounded-lg hover:bg-gray-200 flex items-center justify-center">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                        </div>
                        <div class="px-6 py-4 space-y-4 overflow-y-auto" style="max-height: 60vh;">
                            @foreach ($completarKitComponentes as $comp)
                            @error('general')
    <div class="mb-3 p-3 bg-red-50 border border-red-200 text-red-700 text-sm rounded-lg">
        <i class="fas fa-exclamation-triangle mr-1"></i> {{ $message }}
    </div>
@enderror
                                @if ($comp['faltan'] > 0)
                                    <div class="border border-amber-200 bg-amber-50 rounded-xl overflow-hidden">
                                        <div class="px-4 py-3 flex items-center justify-between bg-amber-100/50">
                                            <div class="flex items-center gap-2">
                                                <i class="fas fa-exclamation-circle text-amber-600 text-xs"></i>
                                                <span class="text-sm font-bold text-gray-800">{{ $comp['nombre'] }}</span>
                                            </div>
                                            <span class="px-2 py-0.5 bg-amber-200 text-amber-800 text-[10px] font-bold rounded-full">
                                                Falta{{ $comp['faltan'] > 1 ? 'n' : '' }} {{ $comp['faltan'] }}
                                            </span>
                                        </div>
                                        @if (($comp['disponibles'] instanceof \Illuminate\Support\Collection ? $comp['disponibles'] : collect($comp['disponibles']))->isNotEmpty())
                                            <div class="p-3 space-y-1.5">
                                                @foreach ($comp['disponibles'] as $item)
                                                    @php
                                                        $elegido = in_array($item['id'], is_array($completarKitSeleccion[$comp['producto_id']] ?? null) ? $completarKitSeleccion[$comp['producto_id']] : []);
                                                    @endphp
                                                    <label class="flex items-center gap-3 p-2.5 rounded-lg border cursor-pointer transition-all {{ $elegido ? 'bg-indigo-50 border-indigo-300 shadow-sm' : 'bg-white border-gray-200 hover:border-gray-300' }}">
                                                        <input type="checkbox"
                                                            wire:model.live="completarKitSeleccion.{{ $comp['producto_id'] }}"
                                                            value="{{ $item['id'] }}"
                                                            class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                                                        <div class="flex-1 min-w-0">
                                                            <div class="flex items-center gap-2">
                                                                <span class="font-mono text-xs font-bold text-gray-800">{{ $item['serie'] }}</span>
                                                                @if ($item['atributos']['produce'] ?? $item->atributos['produce'] ?? null)
                                                                    <span class="px-1.5 py-0.5 bg-green-100 text-green-700 text-[10px] font-semibold rounded">{{ $item['atributos']['produce'] ?? $item->atributos['produce'] }}</span>
                                                                @endif
                                                            </div>
                                                            <span class="text-[10px] text-gray-400">{{ $item['atributos']['fecha_recepcion'] ?? $item['atributos']['recepcion_fecha'] ?? $item['atributos']['fecha'] ?? '' }}</span>
                                                        </div>
                                                        @if ($elegido)
                                                            <i class="fas fa-check-circle text-indigo-500 text-sm"></i>
                                                        @endif
                                                    </label>
                                                @endforeach
                                            </div>
                                        @else
                                            <div class="p-3 text-center text-xs text-red-500 font-semibold">
                                                <i class="fas fa-times-circle mr-1"></i>Sin items disponibles en almacén
                                            </div>
                                        @endif
                                    </div>
                                @else
                                    <div class="flex items-center gap-3 p-3 bg-green-50 border border-green-200 rounded-xl">
                                        <div class="w-8 h-8 rounded-lg bg-green-100 flex items-center justify-center shrink-0">
                                            <i class="fas fa-check text-green-500 text-xs"></i>
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <p class="text-sm font-bold text-gray-800">{{ $comp['nombre'] }}</p>
                                            <p class="text-[10px] text-gray-500">{{ $comp['cantidad_esperada'] - $comp['faltan'] }} / {{ $comp['cantidad_esperada'] }}</p>
                                        </div>
                                        <span class="px-2 py-0.5 bg-green-200 text-green-800 text-[10px] font-bold rounded-full">Completo</span>
                                    </div>
                                @endif
                            @endforeach
                        </div>
                        <div class="bg-gray-50 px-6 py-4 flex items-center justify-between border-t border-gray-200 rounded-b-xl">
                            <button wire:click="cerrarCompletarKit" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition">Cancelar</button>
                            @if ($puedeCompletar)
                                <button wire:click="completarKit" wire:loading.attr="disabled"
                                    class="px-5 py-2 text-sm font-bold text-white bg-indigo-600 rounded-lg hover:bg-indigo-700 transition disabled:opacity-50">
                                    <span wire:loading.remove wire:target="completarKit"><i class="fas fa-check mr-1"></i> Completar kit</span>
                                    <span wire:loading wire:target="completarKit">Guardando...</span>
                                </button>
                            @else
                                <span class="text-xs text-amber-600 font-semibold"><i class="fas fa-hand-pointer mr-1"></i>Elegí los items que faltan</span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif


    {{-- ═══════════════════════════════════════════════════════════════
         MODAL DETALLE KIT — por kit individual
    ═══════════════════════════════════════════════════════════════ --}}
    <div
        x-data="kitComponentsModal"
        x-on:ver-componentes-kit.window="abierto = true; productoId = event.detail.productoId; cargarComponentes();"
        x-on:keydown.escape.window="abierto = false"
        x-show="abierto"
        x-cloak
        class="fixed inset-0 z-50 overflow-hidden"
        style="display: none;">

        {{-- Backdrop --}}
        <div class="fixed inset-0 bg-black/50 transition-opacity"
             x-show="abierto"
             x-transition:leave="ease-in duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             x-on:click="abierto = false"></div>

        <div class="flex min-h-full items-center justify-center p-4">
            <div class="bg-white rounded-xl shadow-2xl w-full max-w-2xl transform transition-all border border-gray-200"
                 x-show="abierto"
                 x-transition:enter="ease-out duration-300"
                 x-transition:enter-start="opacity-0 scale-95"
                 x-transition:enter-end="opacity-100 scale-100"
                 x-transition:leave="ease-in duration-200"
                 x-transition:leave-start="opacity-100 scale-100"
                 x-transition:leave-end="opacity-0 scale-95">

                {{-- Header --}}
                <div class="px-6 py-4 border-b border-gray-200 bg-gray-50 rounded-t-xl flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-lg bg-indigo-100 flex items-center justify-center">
                            <i class="fas fa-puzzle-piece text-indigo-600"></i>
                        </div>
                        <div>
                            <h3 class="text-base font-bold text-gray-800" x-text="producto?.nombre || 'Kit'"></h3>
                            <p class="text-xs text-gray-500 mt-0.5" x-text="kits.length + ' kit(s) registrado(s)'"></p>
                        </div>
                    </div>
                    <button x-on:click="abierto = false"
                            class="text-gray-400 hover:text-gray-600 transition-colors w-8 h-8 rounded-lg hover:bg-gray-200 flex items-center justify-center">
                        <i class="fas fa-times"></i>
                    </button>
                </div>

                {{-- Body --}}
                <div class="px-6 py-4 max-h-[65vh] overflow-y-auto">

                    {{-- Loading --}}
                    <template x-if="cargando">
                        <div class="text-center py-8">
                            <div class="w-10 h-10 border-4 border-gray-200 border-t-indigo-600 rounded-full animate-spin mx-auto"></div>
                            <p class="text-gray-500 mt-3 text-sm">Cargando...</p>
                        </div>
                    </template>

                    <template x-if="!cargando">
                        <div class="space-y-4">

                            {{-- ─── RESUMEN ─── --}}
                            <div class="flex flex-wrap gap-2" x-show="kits.length > 0">
                                <div class="bg-gray-50 rounded-lg px-3 py-2 text-center border border-gray-100">
                                    <p class="text-lg font-bold text-gray-800" x-text="kits.length"></p>
                                    <p class="text-[10px] text-gray-500 uppercase font-semibold">Total</p>
                                </div>
                                <template x-if="kits.filter(k => k.estado === 'en_stock').length > 0">
                                    <div class="bg-green-50 rounded-lg px-3 py-2 text-center border border-green-100">
                                        <p class="text-lg font-bold text-green-700" x-text="kits.filter(k => k.estado === 'en_stock').length"></p>
                                        <p class="text-[10px] text-green-600 uppercase font-semibold">Sellados</p>
                                    </div>
                                </template>
                                <template x-if="kits.filter(k => k.estado === 'asignado').length > 0">
                                    <div class="bg-yellow-50 rounded-lg px-3 py-2 text-center border border-yellow-100">
                                        <p class="text-lg font-bold text-yellow-700" x-text="kits.filter(k => k.estado === 'asignado').length"></p>
                                        <p class="text-[10px] text-yellow-600 uppercase font-semibold">Asignados</p>
                                    </div>
                                </template>
                                <template x-if="kits.filter(k => k.estado === 'instalado').length > 0">
                                    <div class="bg-blue-50 rounded-lg px-3 py-2 text-center border border-blue-100">
                                        <p class="text-lg font-bold text-blue-700" x-text="kits.filter(k => k.estado === 'instalado').length"></p>
                                        <p class="text-[10px] text-blue-600 uppercase font-semibold">Instalados</p>
                                    </div>
                                </template>
                                <template x-if="kits.filter(k => k.estado === 'consumido').length > 0">
                                    <div class="bg-red-50 rounded-lg px-3 py-2 text-center border border-red-100">
                                        <p class="text-lg font-bold text-red-700" x-text="kits.filter(k => k.estado === 'consumido').length"></p>
                                        <p class="text-[10px] text-red-600 uppercase font-semibold">Consumidos</p>
                                    </div>
                                </template>
                                <template x-if="kits.filter(k => k.estado === 'abierto').length > 0">
                                    <div class="bg-orange-50 rounded-lg px-3 py-2 text-center border border-orange-100">
                                        <p class="text-lg font-bold text-orange-700" x-text="kits.filter(k => k.estado === 'abierto').length"></p>
                                        <p class="text-[10px] text-orange-600 uppercase font-semibold">Abiertos</p>
                                    </div>
                                </template>
                                <template x-if="kits.filter(k => k.estado === 'completado').length > 0">
                                    <div class="bg-purple-50 rounded-lg px-3 py-2 text-center border border-purple-100">
                                        <p class="text-lg font-bold text-purple-700" x-text="kits.filter(k => k.estado === 'completado').length"></p>
                                        <p class="text-[10px] text-purple-600 uppercase font-semibold">Completados</p>
                                    </div>
                                </template>
                            </div>

                            {{-- ─── RECETA DEL KIT ─── --}}
                            <div x-show="receta.length > 0" class="bg-gray-50 rounded-lg p-3 border border-gray-200">
                                <h4 class="text-[10px] font-bold text-gray-500 uppercase tracking-wider mb-2 flex items-center gap-1.5">
                                    <i class="fas fa-list-ul text-gray-400"></i> Receta del kit
                                </h4>
                                <div class="flex flex-wrap gap-2">
                                    <template x-for="(r, idx) in receta" :key="'rec-' + idx">
                                        <span class="inline-flex items-center gap-1.5 text-xs bg-white px-2.5 py-1 rounded-lg border border-gray-200">
                                            <i class="fas text-[10px]"
                                               :class="r.es_serializado ? 'fa-microchip text-indigo-500' : 'fa-cubes text-amber-500'"></i>
                                            <span class="font-medium text-gray-700" x-text="r.nombre"></span>
                                            <span class="text-gray-400" x-text="'×' + r.cantidad"></span>
                                        </span>
                                    </template>
                                </div>
                            </div>

                            {{-- ─── LISTA DE KITS ─── --}}
                            <div x-show="kits.length > 0" class="space-y-3">
                                <h4 class="text-[10px] font-bold text-gray-500 uppercase tracking-wider flex items-center gap-1.5">
                                    <i class="fas fa-box text-indigo-500"></i> Kits individuales
                                </h4>

                                <template x-for="(kit, kitIdx) in kits" :key="'kit-' + kit.id">
                                    <div class="border border-gray-200 rounded-xl overflow-hidden">

                                        {{-- Cabecera del kit --}}
                                        <div class="px-4 py-3 bg-white flex items-center gap-3">
                                            <div class="w-8 h-8 rounded-lg flex items-center justify-center shrink-0"
                                                 :class="{
                                                     'bg-green-100': kit.estado === 'en_stock',
                                                     'bg-blue-100': kit.estado === 'instalado',
                                                     'bg-red-100': kit.estado === 'consumido',
                                                     'bg-orange-100': kit.estado === 'abierto',
                                                     'bg-purple-100': kit.estado === 'completado',
                                                     'bg-gray-100': !['en_stock','instalado','consumido','abierto','completado'].includes(kit.estado)
                                                 }">
                                                <i class="fas text-xs"
                                                   :class="{
                                                       'fa-box text-green-600': kit.estado === 'en_stock',
                                                       'fa-wrench text-blue-600': kit.estado === 'instalado',
                                                       'fa-fire text-red-600': kit.estado === 'consumido',
                                                       'fa-folder-open text-orange-600': kit.estado === 'abierto',
                                                       'fa-check-circle text-purple-600': kit.estado === 'completado',
                                                       'fa-circle text-gray-500': !['en_stock','instalado','consumido','abierto','completado'].includes(kit.estado)
                                                   }"></i>
                                            </div>
                                            <div class="flex-1 min-w-0">
                                                <div class="flex items-center gap-2">
                                                    <span class="text-xs font-bold px-2 py-0.5 rounded-full"
                                                          :class="{
                                                              'bg-green-100 text-green-700': kit.estado === 'en_stock',
                                                              'bg-blue-100 text-blue-700': kit.estado === 'instalado',
                                                              'bg-red-100 text-red-700': kit.estado === 'consumido',
                                                              'bg-orange-100 text-orange-700': kit.estado === 'abierto',
                                                              'bg-purple-100 text-purple-700': kit.estado === 'completado',
                                                              'bg-gray-100 text-gray-600': !['en_stock','instalado','consumido','abierto','completado'].includes(kit.estado)
                                                          }"
                                                          x-text="kit.estado"></span>
                                                    <span class="text-[10px] text-gray-400" x-text="kit.sede"></span>
                                                    <span class="text-[10px] text-gray-400" x-text="kit.created_at"></span>
                                                </div>
                                                <p class="text-[10px] text-gray-400 mt-0.5" x-show="kit.tecnico"
                                                   x-text="'Técnico: ' + kit.tecnico"></p>
                                            </div>
                                            <button x-on:click="kit._open = !kit._open"
                                                    class="text-gray-400 hover:text-gray-600 transition text-xs">
                                                <i class="fas" :class="kit._open ? 'fa-chevron-up' : 'fa-chevron-down'"></i>
                                            </button>
                                        </div>

                                        {{-- Componentes del kit (colapsable) --}}
                                        <div x-show="kit._open" x-transition class="px-4 pb-3 bg-gray-50 border-t border-gray-100">

                                            {{-- Serializados --}}
                                            <template x-if="kit.serializados && kit.serializados.length > 0">
                                                <div class="mt-3">
                                                    <p class="text-[10px] font-bold text-gray-500 uppercase tracking-wider mb-1.5 flex items-center gap-1">
                                                        <i class="fas fa-microchip text-indigo-500"></i>
                                                        Serializados
                                                        <span class="text-gray-400 font-normal" x-text="'(' + kit.serializados.length + ')'"></span>
                                                    </p>
                                                    <div class="space-y-1">
                                                        <template x-for="(s, sIdx) in kit.serializados" :key="'s-' + kit.id + '-' + sIdx">
                                                            <div class="flex items-center gap-2 text-xs bg-white px-3 py-1.5 rounded-lg border border-gray-100">
                                                                <span class="font-medium text-gray-700" x-text="s.nombre + ':'"></span>
                                                                <span class="font-mono font-bold text-gray-900" x-text="s.serie"></span>
                                                                <span class="ml-auto text-[10px] px-1.5 py-0.5 rounded"
                                                                      :class="{
                                                                          'bg-green-100 text-green-700': s.estado === 'en_stock',
                                                                          'bg-blue-100 text-blue-700': s.estado === 'instalado',
                                                                          'bg-red-100 text-red-700': s.estado === 'consumido',
                                                                          'bg-gray-100 text-gray-600': !['en_stock','instalado','consumido'].includes(s.estado)
                                                                      }"
                                                                      x-text="s.estado"></span>
                                                            </div>
                                                        </template>
                                                    </div>
                                                </div>
                                            </template>

                                            {{-- Por cantidad --}}
                                            <template x-if="kit.cantidad && kit.cantidad.length > 0">
                                                <div class="mt-3">
                                                    <button x-on:click="kit._showCantidad = !kit._showCantidad"
                                                            class="text-[10px] font-bold text-gray-500 uppercase tracking-wider mb-1.5 flex items-center gap-1 hover:text-gray-700 transition">
                                                        <i class="fas fa-cubes text-amber-500"></i>
                                                        Items generales
                                                        <span class="text-gray-400 font-normal" x-text="'(' + kit.cantidad.length + ')'"></span>
                                                        <i class="fas text-[8px] ml-1 transition-transform"
                                                           :class="kit._showCantidad ? 'fa-chevron-up' : 'fa-chevron-down'"></i>
                                                    </button>
                                                    <div x-show="kit._showCantidad" x-transition class="space-y-1">
                                                        <template x-for="(c, cIdx) in kit.cantidad" :key="'c-' + kit.id + '-' + cIdx">
                                                            <div class="flex items-center gap-2 text-xs bg-amber-50 px-3 py-1.5 rounded-lg border border-amber-100">
                                                                <i class="fas fa-cubes text-amber-500 text-[10px]"></i>
                                                                <span class="font-medium text-amber-800" x-text="c.nombre"></span>
                                                                <span class="text-amber-600" x-text="'×' + c.cantidad"></span>
                                                            </div>
                                                        </template>
                                                    </div>
                                                </div>
                                            </template>

                                            {{-- Sin piezas --}}
                                            <template x-if="(!kit.serializados || kit.serializados.length === 0) && (!kit.cantidad || kit.cantidad.length === 0)">
                                                <p class="text-[10px] text-gray-400 mt-2 italic">Sin componentes registrados</p>
                                            </template>

                                        </div>
                                    </div>
                                </template>
                            </div>

                            {{-- Sin kits --}}
                            <div x-show="kits.length === 0 && receta.length === 0" class="text-center py-8">
                                <i class="fas fa-box-open text-3xl text-gray-300 mb-2"></i>
                                <p class="text-gray-400 text-sm">Sin kits ni componentes registrados</p>
                            </div>

                        </div>
                    </template>
                </div>

                {{-- Footer --}}
                <div class="px-6 py-3 border-t border-gray-200 flex justify-end bg-gray-50 rounded-b-xl">
                    <button x-on:click="abierto = false"
                            class="px-5 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition font-medium text-sm">
                        Cerrar
                    </button>
                </div>

            </div>
        </div>
    </div>

    {{-- ═══════════════════════════════════════════════════════════════
         ALPINE — kitComponentsModal
    ═══════════════════════════════════════════════════════════════ --}}
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('kitComponentsModal', () => ({
                abierto: false,
                productoId: null,
                producto: null,
                receta: [],
                kits: [],
                cargando: false,

                async cargarComponentes() {
                    if (!this.productoId) return;
                    this.cargando = true;
                    this.kits = [];
                    this.receta = [];
                    try {
                        const resp = await fetch(`/api/kit-componentes/${this.productoId}`);
                        const data = await resp.json();
                        this.producto = data.producto;
                        this.receta = data.receta || [];
                        this.kits = (data.kits || []).map(k => ({ ...k, _open: false, _showCantidad: false }));
                    } catch (e) {
                        console.error('Error cargando kit:', e);
                    } finally {
                        this.cargando = false;
                    }
                }
            }));
        });
    </script>

    <script wire:script>
        document.addEventListener('livewire:init', () => {

            Livewire.on('swal', (data) => {

                Swal.fire({

                    icon: data.tipo || 'info',

                    title: data.titulo || '',

                    text: data.mensaje || '',

                    confirmButtonColor: '#4F46E5',

                });

            });

        });
    </script>

    @if ($modalEditarItemAbierto)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4">
            <div class="absolute inset-0 bg-black/60 backdrop-blur-sm" wire:click="cerrarEditarItem"></div>

            <div class="relative bg-white rounded-2xl shadow-2xl border border-gray-200 w-full max-w-md max-h-[85vh] overflow-hidden flex flex-col">

                <div class="px-6 py-4 border-b border-gray-100 bg-amber-50">
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="text-lg font-bold text-amber-800">
                                <i class="fas fa-edit mr-1"></i> Completar item
                            </h3>
                            <p class="text-sm text-amber-600 mt-0.5">
                                {{ $editarItemId ? 'Item #' . $editarItemId : '' }}
                            </p>
                        </div>
                        <button wire:click="cerrarEditarItem" class="text-gray-400 hover:text-gray-600 transition">
                            <i class="fas fa-times text-xl"></i>
                        </button>
                    </div>
                </div>

                <div class="px-6 py-4 flex-1 overflow-y-auto">
                    @php
                        $item = \App\Models\ItemSerializado::with('producto.categoria')->find($editarItemId);
                        $esquema = $item?->producto->categoria->esquema_atributos ?? ['serie'];
                        $campos = is_string($esquema) ? json_decode($esquema, true) : $esquema;
                    @endphp

                    <div class="space-y-4">
                        @foreach ($campos as $campo)
                            <div>
                                <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1">
                                    {{ $campo === 'capacidad' ? 'Capacidad' : ucfirst($campo) }}
                                </label>
                                <input type="text"
                                    wire:model="editarItemData.{{ $campo }}"
                                    class="w-full text-sm border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-amber-500 focus:border-amber-500"
                                    placeholder="{{ ucfirst($campo) }}"
                                    maxlength="50">
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="px-6 py-4 border-t border-gray-100 bg-gray-50 flex gap-3">
                    <button wire:click="cerrarEditarItem" type="button"
                        class="flex-1 px-4 py-2.5 bg-gray-200 text-gray-700 rounded-xl hover:bg-gray-300 transition font-medium text-sm">
                        Cancelar
                    </button>
                    <button wire:click="guardarEditarItem" wire:loading.attr="disabled" type="button"
                        class="flex-1 px-4 py-2.5 bg-amber-600 hover:bg-amber-700 text-white rounded-xl transition font-semibold text-sm shadow-md">
                        <i class="fas fa-save mr-1"></i> Guardar
                    </button>
                </div>
            </div>
        </div>
    @endif

</div>