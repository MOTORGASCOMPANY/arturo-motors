<div wire:loading.class="opacity-60 pointer-events-none" class="max-w-4xl mx-auto px-3 sm:px-0 py-6 sm:py-12 pb-28 sm:pb-12 space-y-5 sm:space-y-6">

    <div class="flex items-center justify-between gap-2 sm:gap-3">
        <a href="{{ route('almacen.traslados.listado') }}" wire:navigate
            class="px-3 sm:px-4 py-2.5 sm:py-2 bg-white border border-gray-200 rounded-lg text-xs sm:text-sm font-medium text-gray-700 hover:bg-gray-50 hover:border-gray-300 active:scale-[0.98] transition flex items-center gap-2 shadow-sm">
            <i class="fas fa-arrow-left text-xs"></i> <span>Volver</span>
        </a>
        <a href="{{ route('almacen.traslados.listado') }}" wire:navigate
            class="px-3 sm:px-4 py-2.5 sm:py-2 bg-white border border-gray-200 rounded-lg text-xs sm:text-sm font-medium text-gray-700 hover:bg-gray-50 hover:border-gray-300 active:scale-[0.98] transition flex items-center gap-2 shadow-sm">
            <i class="fas fa-history text-xs"></i> Historial
        </a>
    </div>

    <div class="relative overflow-hidden bg-gray-900 text-white p-5 sm:p-8 rounded-2xl w-full">
        <div class="absolute -right-8 -top-8 w-40 h-40 bg-white/5 rounded-full"></div>
        <div class="absolute -right-2 -bottom-10 w-28 h-28 bg-white/5 rounded-full"></div>
        <div class="relative flex items-start gap-3 sm:gap-4">
            <div class="w-11 h-11 sm:w-14 sm:h-14 rounded-xl bg-white/10 flex items-center justify-center shrink-0">
                <i class="fas fa-truck text-lg sm:text-2xl"></i>
            </div>
            <div>
                <h2 class="font-semibold text-lg sm:text-2xl leading-tight">Nuevo traslado</h2>
                <p class="text-xs sm:text-sm text-gray-400 mt-1">Envía un kit o piezas sueltas desde Arturo Motors hacia otra sede</p>
            </div>
        </div>
    </div>

    <x-input-error for="general" />

    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 sm:p-6"
        x-data="{ hasSede: {{ $sedeDestinoId ? 'true' : 'false' }} }">
        <div class="flex items-center gap-2 mb-4">
            <span class="w-7 h-7 {{ $sedeDestinoId ? 'bg-emerald-600 text-white' : 'bg-gray-900 text-white' }} text-xs font-bold rounded-full flex items-center justify-center shrink-0 transition-colors">
                @if ($sedeDestinoId)
                    <i class="fas fa-check text-[11px]"></i>
                @else
                    1
                @endif
            </span>
            <h3 class="font-semibold text-gray-800 text-sm sm:text-base">Configuración del envío</h3>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <x-label for="sedeDestinoId" value="Sede destino *" />
                <div class="relative mt-1">
                    <select wire:model="sedeDestinoId" x-on:change="hasSede = $event.target.value !== ''"
                        class="w-full rounded-lg border-gray-300 text-sm py-2.5 sm:py-2 pr-9 appearance-none focus:border-indigo-500 focus:ring-indigo-500">
                        <option value="">-- Selecciona --</option>
                        @foreach ($this->sedes as $s)
                            <option value="{{ $s->id }}">{{ $s->nombre }}</option>
                        @endforeach
                    </select>
                    <i class="fas fa-chevron-down text-gray-400 text-xs absolute right-3 top-1/2 -translate-y-1/2 pointer-events-none"></i>
                </div>
                <x-input-error for="sedeDestinoId" class="mt-1" />
            </div>

            <div>
                <x-label for="observaciones" value="Observaciones (opcional)" />
                <x-input wire:model="observaciones"
                    class="w-full rounded-lg border-gray-300 text-sm py-2.5 sm:py-2 mt-1 focus:border-indigo-500 focus:ring-indigo-500"
                    placeholder="Ej: para conversión pendiente en Ancón" />
            </div>
        </div>

        @unless ($sedeDestinoId)
            <div class="mt-4 pt-4 border-t border-gray-100 flex items-center justify-between gap-3">
                <p class="text-[11px] text-gray-400"><i class="fas fa-circle-info mr-1"></i>Elige una sede y presiona Continuar para seleccionar los items</p>
                <button type="button" wire:click="$refresh" wire:loading.attr="disabled"
                    x-bind:disabled="!hasSede"
                    x-bind:class="hasSede ? 'bg-gray-900 text-white hover:bg-gray-800' : 'bg-gray-200 text-gray-400 cursor-not-allowed'"
                    class="shrink-0 px-5 py-2.5 rounded-lg text-sm font-semibold transition active:scale-[0.98]">
                    Continuar <i class="fas fa-arrow-right ml-1 text-xs"></i>
                </button>
            </div>
        @else
            <div class="mt-4 pt-4 border-t border-gray-100 flex items-center justify-between gap-2">
                <p class="text-xs text-emerald-600 font-medium"><i class="fas fa-check-circle mr-1"></i>Sede seleccionada: {{ $this->sedes->firstWhere('id', $sedeDestinoId)?->nombre }}</p>
                <button type="button" wire:click="$refresh" class="text-xs text-gray-400 hover:text-gray-600 underline underline-offset-2">
                    Actualizar
                </button>
            </div>
        @endunless
    </div>

    @if ($sedeDestinoId)
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 sm:p-6">
            <div class="flex flex-wrap items-center gap-2 mb-4">
                <span class="w-7 h-7 {{ !$this->resumenVacio ? 'bg-emerald-600 text-white' : 'bg-gray-900 text-white' }} text-xs font-bold rounded-full flex items-center justify-center shrink-0 transition-colors">
                    @if (!$this->resumenVacio)
                        <i class="fas fa-check text-[11px]"></i>
                    @else
                        2
                    @endif
                </span>
                <h3 class="font-semibold text-gray-800 text-sm sm:text-base">¿Qué vas a enviar?</h3>
                @if (!$this->resumenVacio)
                    <span class="sm:ml-auto text-xs font-bold text-indigo-600 bg-indigo-50 px-2.5 py-1 rounded-full flex items-center gap-1">
                        <i class="fas fa-check-circle text-[10px]"></i> {{ $this->seleccionCount }} seleccionados
                    </span>
                @endif
            </div>

            <div class="flex gap-1 bg-gray-100 rounded-lg p-1 mb-4">
                <button wire:click="$set('tabSeleccion', 'kits')" type="button"
                    class="flex-1 py-2.5 sm:py-2 px-1 sm:px-3 rounded-md text-xs sm:text-sm font-medium transition-all whitespace-nowrap
                        {{ $tabSeleccion === 'kits' ? 'bg-white shadow-sm text-gray-900' : 'text-gray-500 hover:text-gray-700' }}">
                    <i class="fas fa-box sm:mr-1.5"></i> <span>Kits</span>
                </button>
                <button wire:click="$set('tabSeleccion', 'piezas')" type="button"
                    class="flex-1 py-2.5 sm:py-2 px-1 sm:px-3 rounded-md text-xs sm:text-sm font-medium transition-all whitespace-nowrap
                        {{ $tabSeleccion === 'piezas' ? 'bg-white shadow-sm text-gray-900' : 'text-gray-500 hover:text-gray-700' }}">
                    <i class="fas fa-puzzle-piece sm:mr-1.5"></i> <span>Items serializados</span>
                </button>
                <button wire:click="$set('tabSeleccion', 'cantidad')" type="button"
                    class="flex-1 py-2.5 sm:py-2 px-1 sm:px-3 rounded-md text-xs sm:text-sm font-medium transition-all whitespace-nowrap
                        {{ $tabSeleccion === 'cantidad' ? 'bg-white shadow-sm text-gray-900' : 'text-gray-500 hover:text-gray-700' }}">
                    <i class="fas fa-cubes sm:mr-1.5"></i> <span>Items por cantidad</span>
                </button>
            </div>

            @unless ($tabSeleccion === 'cantidad')
                <div class="flex items-center bg-gray-50 rounded-lg px-3 py-2.5 sm:py-2 mb-4 border border-gray-200 focus-within:border-indigo-400 focus-within:ring-1 focus-within:ring-indigo-400 transition">
                    <i class="fas fa-search text-gray-400 text-sm mr-2"></i>
                    <input
                        class="bg-transparent outline-none text-sm w-full border-none focus:ring-0 p-0"
                        type="text"
                        wire:model.live.debounce.300ms="buscar"
                        placeholder="Buscar por nombre..."
                    >
                </div>
            @endunless

            @if ($tabSeleccion === 'kits')
                @php $kits = $this->kitsDisponibles; @endphp

                @if ($kits->isEmpty())
                    <div class="text-center py-14 text-gray-400">
                        <i class="fas fa-box-open text-4xl mb-3 text-gray-300"></i>
                        <p class="text-sm font-medium text-gray-500">No hay kits disponibles en esta sede</p>
                        <p class="text-xs mt-1">Prueba con otro filtro de búsqueda o revisa la pestaña de piezas sueltas</p>
                    </div>
                @else
                    <div class="space-y-3 max-h-[500px] overflow-y-auto pr-1 -mr-1">

                        @foreach ($kits as $kit)
                            @php
                                $isSelected = isset($itemsSeleccionados[$kit->id]);
                                $isInspecting = $kitInspeccionId === $kit->id;
                            @endphp

                            <div class="border-2 rounded-xl transition-all duration-150
                                {{ $isSelected ? 'border-indigo-500 bg-indigo-50/60 ring-1 ring-indigo-100' : 'border-gray-200 hover:border-gray-300' }}">

                                <label class="flex items-center gap-3 p-3 cursor-pointer">
                                    <input type="checkbox"
                                        wire:click="toggleKit({{ $kit->id }})"
                                        @checked($isSelected)
                                        class="w-4.5 h-4.5 sm:w-4 sm:h-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500 shrink-0 cursor-pointer">

                                    <div class="flex-1 min-w-0">
                                        <div class="flex flex-wrap items-center gap-1.5 sm:gap-2">
                                            <p class="text-sm font-bold text-gray-800 truncate">
                                                {{ $kit->producto->nombre }}
                                            </p>
                                            @if ($kit->es_sellado)
                                                <span class="px-1.5 py-0.5 bg-emerald-100 text-emerald-700 text-[10px] font-bold rounded-full shrink-0">
                                                    Sellado
                                                </span>
                                            @else
                                                <span class="px-1.5 py-0.5 bg-amber-100 text-amber-700 text-[10px] font-bold rounded-full shrink-0">
                                                    Abierto
                                                </span>
                                            @endif
                                        </div>

                                        <p class="text-[11px] text-gray-400 mt-0.5 flex flex-wrap items-center gap-x-1.5">
                                            <span>#{{ $kit->id }}</span>
                                            <span class="text-gray-300">·</span>
                                            <span>{{ $kit->totalEsperado }} piezas esperadas</span>
                                            @if (! $kit->es_sellado)
                                                <span class="text-gray-300">·</span>
                                                <span>{{ $kit->hijosCount }} presentes</span>
                                            @endif
                                            <span class="text-gray-300">·</span>
                                            <span class="inline-flex items-center gap-1"><i class="fas fa-location-dot text-[9px]"></i>{{ $kit->sede?->nombre ?? '—' }}</span>
                                        </p>
                                    </div>

                                    <button
                                        wire:click.stop="toggleInspeccion({{ $kit->id }})"
                                        type="button"
                                        class="w-9 h-9 flex items-center justify-center rounded-lg text-gray-400 hover:text-gray-700 hover:bg-gray-100 transition shrink-0"
                                        title="Ver componentes">
                                        <i class="fas {{ $isInspecting ? 'fa-chevron-up' : 'fa-chevron-down' }} text-xs transition-transform"></i>
                                    </button>
                                </label>

                                @if ($isInspecting)
                                    @php $insp = $this->kitInspeccion; @endphp

                                    @if ($insp && $insp['kit']->id === $kit->id)
                                        <div class="px-3 pb-3 pt-2 border-t border-gray-100 bg-white/60 rounded-b-xl">
                                            <div class="flex items-center justify-between mb-2">
                                                <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wider">
                                                    Componentes
                                                </p>
                                                <span class="text-[10px] font-bold px-2 py-0.5 rounded-full {{ $insp['hijosSeleccionadosCount'] >= $insp['totalEsperado'] ? 'bg-emerald-100 text-emerald-700' : 'bg-gray-100 text-gray-500' }}">
                                                    {{ $insp['hijosSeleccionadosCount'] }}/{{ $insp['totalEsperado'] }} para envío
                                                </span>
                                            </div>
                                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-1.5">
                                                @foreach ($insp['receta'] as $r)
                                                    @php
                                                        $presente = $insp['hijos'][$r->producto_componente_id] ?? 0;
                                                        $completo = $presente >= $r->cantidad_esperada;
                                                        $hijosComponente = $insp['hijosItems']->filter(fn($h) => $h->producto_id === $r->producto_componente_id);
                                                    @endphp
                                                    <div class="p-2 rounded-lg border
                                                        {{ $completo ? 'bg-green-50 border-green-200' : 'bg-red-50 border-red-200' }}">
                                                        <div class="flex items-center gap-2 text-[11px]">
                                                            <i class="fas {{ $completo ? 'fa-check-circle text-green-500' : 'fa-exclamation-circle text-red-500' }} text-[10px] shrink-0"></i>
                                                            <span class="font-semibold text-gray-700 truncate">{{ $r->componente?->nombre ?? '—' }}</span>
                                                            <span class="text-gray-500 ml-auto shrink-0 font-medium">
                                                                {{ $presente }}/{{ $r->cantidad_esperada }}
                                                            </span>
                                                        </div>
                                                        @if ($hijosComponente->count() > 0)
                                                            <div class="mt-1.5 ml-5 space-y-1 border-l-2 border-gray-200 pl-2">
                                                                @foreach ($hijosComponente as $hijo)
                                                                    @php $isChildSelected = isset($itemsSeleccionados[$hijo->id]); @endphp
                                                                    <label class="flex items-center gap-1.5 cursor-pointer text-[10px] py-0.5 {{ $isChildSelected ? 'text-emerald-700 font-semibold' : 'text-gray-500' }}">
                                                                        <input type="checkbox"
                                                                            wire:click="toggleHijoKit({{ $hijo->id }})"
                                                                            @checked($isChildSelected)
                                                                            class="w-3.5 h-3.5 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500 shrink-0">
                                                                        <span class="font-mono truncate">{{ $hijo->serie ?? '(sin serie)' }}</span>
                                                                    </label>
                                                                @endforeach
                                                            </div>
                                                        @endif
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    @endif
                                @endif

                            </div>
                        @endforeach

                    </div>
                @endif
            @endif

            @if ($tabSeleccion === 'piezas')
                @php $piezas = $this->piezasSueltas; @endphp

                @if ($piezas->isEmpty())
                    <div class="text-center py-14 text-gray-400">
                        <i class="fas fa-puzzle-piece text-4xl mb-3 text-gray-300"></i>
                        <p class="text-sm font-medium text-gray-500">No hay piezas sueltas disponibles</p>
                        <p class="text-xs mt-1">Prueba con otro término de búsqueda</p>
                    </div>
                @else
                    <div class="space-y-2 max-h-[500px] overflow-y-auto pr-1 -mr-1">
                        @foreach ($piezas as $pieza)
                            @php $isSelected = isset($itemsSeleccionados[$pieza->id]); @endphp

                            <label class="flex items-center gap-3 border-2 rounded-xl px-3 sm:px-4 py-3 cursor-pointer transition-all duration-150
                                {{ $isSelected ? 'border-indigo-500 bg-indigo-50/60 ring-1 ring-indigo-100' : 'border-gray-100 hover:border-gray-300 bg-white' }}">
                                <input type="checkbox"
                                    wire:click="togglePieza({{ $pieza->id }})"
                                    @checked($isSelected)
                                    class="w-4.5 h-4.5 sm:w-4 sm:h-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500 shrink-0">

                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-semibold text-gray-800 truncate">{{ $pieza->producto->nombre }}</p>
                                    <p class="text-[11px] text-gray-400 truncate mt-0.5">
                                        <i class="fas fa-barcode mr-1"></i>{{ $pieza->serie ?? 'Sin serie' }}
                                        <span class="text-gray-300 mx-1">·</span>
                                        <i class="fas fa-location-dot mr-1"></i>{{ $pieza->sede?->nombre ?? '—' }}
                                    </p>
                                </div>

                                @if ($isSelected)
                                    <i class="fas fa-circle-check text-indigo-500 text-sm shrink-0"></i>
                                @endif
                            </label>
                        @endforeach
                    </div>
                @endif
            @endif

            @if ($tabSeleccion === 'cantidad')
                @php $productos = $this->productosCantidad; @endphp

                <div class="bg-gray-50 border border-gray-200 rounded-xl p-3 mb-3">
                    <div class="flex flex-col sm:flex-row gap-2">
                        <div class="relative flex-1">
                            <select wire:model="productoCantidadId"
                                class="w-full rounded-lg border-gray-300 text-sm py-2.5 sm:py-2 pr-9 appearance-none focus:border-indigo-500 focus:ring-indigo-500">
                                <option value="">-- Selecciona pieza --</option>
                                @foreach ($productos as $p)
                                    <option value="{{ $p->id }}">{{ $p->nombre }} (disponible: {{ $p->disponible }})</option>
                                @endforeach
                            </select>
                            <i class="fas fa-chevron-down text-gray-400 text-xs absolute right-3 top-1/2 -translate-y-1/2 pointer-events-none"></i>
                        </div>
                        <div class="flex gap-2">
                            <input type="number" min="1" wire:model="cantidadPieza"
                                class="w-20 rounded-lg border-gray-300 text-sm py-2.5 sm:py-2 text-center focus:border-indigo-500 focus:ring-indigo-500"
                                placeholder="Cant.">
                            <button wire:click="agregarPiezaCantidad" type="button"
                                class="flex-1 sm:flex-none px-4 py-2.5 sm:py-2 bg-gray-900 text-white rounded-lg text-sm font-medium hover:bg-gray-800 active:scale-[0.98] transition whitespace-nowrap">
                                <i class="fas fa-plus mr-1"></i> Agregar
                            </button>
                        </div>
                    </div>
                    <x-input-error for="cantidadPieza" class="mt-2" />
                </div>

                @if ($this->piezasCantidadCarrito->count())
                    <ul class="space-y-1.5">
                        @foreach ($this->piezasCantidadCarrito as $p)
                            <li class="flex justify-between items-center gap-2 text-sm bg-white border border-gray-200 rounded-lg px-3 py-2.5 sm:py-2 shadow-sm">
                                <span class="flex items-center gap-2 text-gray-700 truncate">
                                    <span class="w-2 h-2 rounded-full bg-amber-400 shrink-0"></span>
                                    {{ $p->nombre }}
                                    <span class="font-bold text-gray-900 shrink-0">×{{ $p->cantidad_solicitada }}</span>
                                </span>
                                <button wire:click="quitarPiezaCantidad({{ $p->id }})" type="button"
                                    class="text-red-400 hover:text-red-600 hover:bg-red-50 rounded-lg text-xs w-8 h-8 flex items-center justify-center shrink-0 transition"
                                    title="Quitar">
                                    <i class="fas fa-times"></i>
                                </button>
                            </li>
                        @endforeach
                    </ul>
                @else
                    <div class="text-center py-10 text-gray-400">
                        <i class="fas fa-cubes text-3xl mb-2 text-gray-300"></i>
                        <p class="text-sm font-medium text-gray-500">Aún no agregaste piezas por cantidad</p>
                        <p class="text-xs mt-1">Elige una pieza arriba, indica la cantidad y presiona "Agregar"</p>
                    </div>
                @endif
            @endif

        </div>
    @endif

    @unless ($this->resumenVacio)
        <div class="bg-gray-50 rounded-xl border border-gray-200 p-4 sm:p-5">
            <h3 class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-3 flex items-center gap-2">
                <i class="fas fa-list-check"></i> Resumen del envío
            </h3>

            <div class="space-y-1">
                @php
                    $kitsSeleccionados = collect(array_keys($itemsSeleccionados))
                        ->map(fn ($id) => \App\Models\ItemSerializado::with('producto')->find($id))
                        ->filter(fn ($i) => $i && is_null($i->kit_padre_id) && $i->producto?->categoria?->es_kit);
                @endphp

                @foreach ($kitsSeleccionados as $kit)
                    <div class="flex items-center gap-2 text-sm py-1.5 border-b border-gray-100 last:border-0">
                        <span class="w-2 h-2 rounded-full bg-emerald-400 shrink-0"></span>
                        <i class="fas fa-box text-gray-300 text-xs shrink-0"></i>
                        <span class="text-gray-700 font-medium truncate">{{ $kit->producto->nombre }}</span>
                        <span class="text-xs text-gray-400 shrink-0 ml-auto">#{{ $kit->id }}</span>
                    </div>
                @endforeach

                @php
                    $sueltosSeleccionados = collect(array_keys($itemsSeleccionados))
                        ->map(fn ($id) => \App\Models\ItemSerializado::with('producto')->find($id))
                        ->filter(fn ($i) => $i && is_null($i->kit_padre_id) && !$i->producto?->categoria?->es_kit);
                @endphp

                @foreach ($sueltosSeleccionados as $pieza)
                    <div class="flex items-center gap-2 text-sm py-1.5 border-b border-gray-100 last:border-0">
                        <span class="w-2 h-2 rounded-full bg-blue-400 shrink-0"></span>
                        <i class="fas fa-puzzle-piece text-gray-300 text-xs shrink-0"></i>
                        <span class="text-gray-700 truncate">{{ $pieza->producto->nombre }}</span>
                        <span class="text-xs text-gray-400 shrink-0 ml-auto">{{ $pieza->serie ?? 's/serie' }}</span>
                    </div>
                @endforeach

                @foreach ($this->piezasCantidadCarrito as $p)
                    <div class="flex items-center gap-2 text-sm py-1.5 border-b border-gray-100 last:border-0">
                        <span class="w-2 h-2 rounded-full bg-amber-400 shrink-0"></span>
                        <i class="fas fa-cubes text-gray-300 text-xs shrink-0"></i>
                        <span class="text-gray-700 truncate">{{ $p->nombre }}</span>
                        <span class="text-xs text-gray-400 shrink-0 ml-auto">×{{ $p->cantidad_solicitada }}</span>
                    </div>
                @endforeach
            </div>

            <div class="mt-3 pt-3 border-t border-gray-200 flex justify-between items-center text-sm">
                <span class="text-gray-500">Total de items</span>
                <span class="font-bold text-gray-900 text-base">{{ $this->seleccionCount }}</span>
            </div>
        </div>
    @endunless

    <button wire:click="confirmarTraslado" wire:loading.attr="disabled"
        @if ($this->resumenVacio || !$sedeDestinoId) disabled @endif
        class="hidden sm:flex w-full items-center justify-center {{ $this->resumenVacio || !$sedeDestinoId ? 'bg-gray-200 text-gray-400 cursor-not-allowed' : 'bg-gray-900 text-white hover:bg-gray-800 active:scale-[0.99]' }} rounded-xl py-3.5 sm:py-3 font-semibold text-sm transition-all">
        <span wire:loading.remove wire:target="confirmarTraslado">
            <i class="fas fa-truck mr-2"></i>
            @if (!$sedeDestinoId)
                Selecciona una sede destino
            @elseif ($this->resumenVacio)
                Selecciona al menos un item
            @else
                Confirmar traslado
            @endif
        </span>
        <span wire:loading wire:target="confirmarTraslado">
            <i class="fas fa-circle-notch fa-spin mr-2"></i> Procesando...
        </span>
    </button>

    <div class="sm:hidden fixed bottom-0 left-0 right-0 z-30 bg-white border-t border-gray-200 p-3 shadow-[0_-4px_12px_rgba(0,0,0,0.05)]">
        <button wire:click="confirmarTraslado" wire:loading.attr="disabled"
            @if ($this->resumenVacio || !$sedeDestinoId) disabled @endif
            class="w-full flex items-center justify-center {{ $this->resumenVacio || !$sedeDestinoId ? 'bg-gray-200 text-gray-400 cursor-not-allowed' : 'bg-gray-900 text-white hover:bg-gray-800 active:scale-[0.99]' }} rounded-xl py-3.5 font-semibold text-sm transition-all">
            <span wire:loading.remove wire:target="confirmarTraslado">
                <i class="fas fa-truck mr-2"></i>
                @if (!$sedeDestinoId)
                    Selecciona una sede
                @elseif ($this->resumenVacio)
                    Selecciona items para enviar
                @else
                    Confirmar traslado ({{ $this->seleccionCount }})
                @endif
            </span>
            <span wire:loading wire:target="confirmarTraslado">
                <i class="fas fa-circle-notch fa-spin mr-2"></i> Procesando...
            </span>
        </button>
    </div>

    @if ($mostrarChecklist)
        <div class="fixed inset-0 z-50 flex items-end sm:items-center justify-center sm:p-4" x-data>
            <div class="absolute inset-0 bg-black/60 backdrop-blur-sm" wire:click="cerrarChecklist"></div>

            <div class="relative bg-white sm:rounded-2xl rounded-t-2xl shadow-2xl border border-gray-200 w-full max-h-[88vh] sm:max-h-[85vh] sm:max-w-lg overflow-hidden flex flex-col">

                <div class="px-4 sm:px-6 py-4 border-b border-gray-100 bg-gray-50 shrink-0">
                    <div class="flex items-start justify-between gap-2">
                        <div class="min-w-0">
                            <h3 class="text-base sm:text-lg font-bold text-gray-800 flex items-center gap-2">
                                <i class="fas fa-clipboard-check text-gray-600"></i> Confirmar envío
                            </h3>
                            <p class="text-xs sm:text-sm text-gray-500 mt-0.5 truncate">
                                <i class="fas fa-location-dot mr-1"></i>{{ $this->sedes->firstWhere('id', $sedeDestinoId)?->nombre ?? '' }}
                                <span class="text-gray-300 mx-1">·</span>
                                {{ $this->seleccionCount }} items
                            </p>
                        </div>
                        <button wire:click="cerrarChecklist" type="button" class="text-gray-400 hover:text-gray-600 hover:bg-gray-200 rounded-lg transition w-9 h-9 flex items-center justify-center shrink-0 -mr-1">
                            <i class="fas fa-times text-lg sm:text-xl"></i>
                        </button>
                    </div>
                </div>

                <div class="px-4 sm:px-6 py-4 flex-1 overflow-y-auto">
                    <div class="space-y-3">

                        @forelse ($checklistData as $comp)
                            <div class="border border-gray-200 rounded-xl overflow-hidden">

                                <div class="flex items-center gap-2 sm:gap-3 p-3 bg-white">
                                    <div class="w-8 h-8 rounded-full flex items-center justify-center shrink-0
                                        {{ $comp['tipo'] === 'kit' ? 'bg-emerald-100' : ($comp['tipo'] === 'pieza' ? 'bg-blue-100' : 'bg-amber-100') }}">
                                        <i class="fas text-sm
                                            {{ $comp['tipo'] === 'kit' ? 'fa-box text-emerald-600' : ($comp['tipo'] === 'pieza' ? 'fa-puzzle-piece text-blue-600' : 'fa-cubes text-amber-600') }}"></i>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <div class="flex flex-wrap items-center gap-1.5 sm:gap-2">
                                            <span class="text-sm font-semibold text-gray-800 truncate">{{ $comp['nombre'] }}</span>
                                            @if ($comp['tipo'] === 'kit')
                                                @if ($comp['es_completo'])
                                                    <span class="px-1.5 py-0.5 rounded text-[10px] font-bold bg-emerald-100 text-emerald-700 shrink-0">Completo</span>
                                                @else
                                                    <span class="px-1.5 py-0.5 rounded text-[10px] font-bold bg-amber-100 text-amber-700 shrink-0">Incompleto</span>
                                                @endif
                                            @endif
                                        </div>
                                        <span class="text-xs text-gray-400 truncate block mt-0.5">{{ $comp['detalle'] }}</span>
                                    </div>
                                </div>

                                @if ($comp['tipo'] === 'kit' && isset($comp['componentes']))
                                    <div class="px-3 pb-3 pt-2 bg-gray-50 border-t border-gray-100">
                                        <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-1.5">
                                            {{ $comp['totalPresente'] }}/{{ $comp['totalEsperado'] }} piezas
                                        </p>
                                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-1">
                                            @foreach ($comp['componentes'] as $c)
                                                <div class="flex items-center gap-1.5 text-[11px] p-1.5 rounded
                                                    {{ $c['completo'] ? 'text-green-700 bg-green-50/50' : 'text-red-600 bg-red-50/50' }}">
                                                    <i class="fas {{ $c['completo'] ? 'fa-check text-green-500' : 'fa-times text-red-500' }} text-[9px] shrink-0"></i>
                                                    <span class="truncate">{{ $c['nombre'] }}</span>
                                                    <span class="text-gray-400 ml-auto shrink-0 font-medium">{{ $c['presente'] }}/{{ $c['esperada'] }}</span>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                @endif

                            </div>
                        @empty
                            <div class="text-center py-10 text-gray-400">
                                <i class="fas fa-box-open text-4xl mb-3 text-gray-300"></i>
                                <p class="text-sm font-medium text-gray-500">No hay items en el envío</p>
                            </div>
                        @endforelse

                    </div>
                </div>

                <div class="px-4 sm:px-6 py-4 border-t border-gray-100 bg-gray-50 flex gap-3 shrink-0">
                    <button wire:click="cerrarChecklist" type="button"
                        class="flex-1 px-4 py-3 sm:py-2.5 bg-gray-200 text-gray-700 rounded-xl hover:bg-gray-300 active:scale-[0.98] transition font-medium text-sm">
                        Volver
                    </button>
                    <button wire:click="confirmarEnvio" wire:loading.attr="disabled" type="button"
                        class="flex-1 px-4 py-3 sm:py-2.5 bg-gray-900 hover:bg-gray-800 active:scale-[0.98] text-white rounded-xl transition font-semibold text-sm flex items-center justify-center gap-2">
                        <span wire:loading.remove wire:target="confirmarEnvio"><i class="fas fa-check mr-1"></i> Confirmar envío</span>
                        <span wire:loading wire:target="confirmarEnvio"><i class="fas fa-circle-notch fa-spin mr-1"></i> Enviando...</span>
                    </button>
                </div>
            </div>
        </div>
    @endif

</div>