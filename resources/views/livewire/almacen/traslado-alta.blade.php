<div wire:loading.class="opacity-60 pointer-events-none"
    class="max-w-4xl mx-auto px-3 sm:px-0 py-6 sm:py-10 pb-28 sm:pb-12 space-y-5 sm:space-y-6">

    {{-- ── Encabezado compacto (sin hero a página completa) ── --}}
    <header class="flex flex-wrap items-start justify-between gap-3">
        <div class="flex items-start gap-3 min-w-0">
            <div class="w-11 h-11 sm:w-12 sm:h-12 rounded-xl bg-indigo-600 text-white flex items-center justify-center shrink-0">
                <i class="fas fa-truck text-lg"></i>
            </div>
            <div class="min-w-0">
                <h1 class="text-xl sm:text-2xl font-bold text-gray-900 leading-tight">Nuevo traslado</h1>
                <p class="text-sm text-gray-500 mt-0.5">Envía un kit o piezas sueltas desde Arturo Motors hacia otra sede</p>
            </div>
        </div>
        <a href="{{ route('almacen.traslados.listado') }}" wire:navigate
            class="shrink-0 px-4 py-2.5 bg-white border border-gray-200 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50 hover:border-gray-300 active:scale-[0.98] transition flex items-center gap-2 shadow-sm">
            <i class="fas fa-arrow-left text-xs"></i> <span>Volver</span>
        </a>
    </header>

    {{-- ── Progreso: 4 pasos, indigo = activo, emerald = hecho ── --}}
    @php
        $pasos = [
            ['n' => 1, 't' => 'Configuración', 'i' => 'fa-sliders'],
            ['n' => 2, 't' => 'Selección', 'i' => 'fa-boxes-stacked'],
            ['n' => 3, 't' => 'Resumen', 'i' => 'fa-list-check'],
            ['n' => 4, 't' => 'Envío', 'i' => 'fa-circle-check'],
        ];
        $pasoActual = $this->pasoActual;
    @endphp
    <ol class="grid grid-cols-2 sm:grid-cols-4 gap-2">
        @foreach ($pasos as $paso)
            @php
                $hecho = $pasoActual > $paso['n'];
                $activo = $pasoActual === $paso['n'];
            @endphp
            <li class="flex items-center gap-2 rounded-xl border px-3 py-2.5 transition-colors
                {{ $activo ? 'border-indigo-500 bg-indigo-50' : ($hecho ? 'border-emerald-200 bg-emerald-50' : 'border-gray-200 bg-white') }}">
                <span class="w-6 h-6 rounded-full flex items-center justify-center text-[11px] font-bold shrink-0
                    {{ $hecho ? 'bg-emerald-600 text-white' : ($activo ? 'bg-indigo-600 text-white' : 'bg-gray-200 text-gray-500') }}">
                    @if ($hecho)
                        <i class="fas fa-check text-[10px]"></i>
                    @else
                        {{ $paso['n'] }}
                    @endif
                </span>
                <span class="text-xs font-semibold truncate
                    {{ $activo ? 'text-indigo-700' : ($hecho ? 'text-emerald-700' : 'text-gray-500') }}">
                    {{ $paso['t'] }}
                </span>
            </li>
        @endforeach
    </ol>

    <x-input-error for="general" />

    {{-- ── Paso 1: configuración ── --}}
    <x-almacen.traslado-configuracion />

    {{-- ── Paso 2: selección de items ── --}}
    @if ($sedeDestinoId)
        <section class="bg-white rounded-2xl shadow-sm border border-gray-200 p-4 sm:p-6">
            <div class="flex flex-wrap items-center gap-2 mb-4">
                <span class="w-7 h-7 {{ !$this->resumenVacio ? 'bg-emerald-600 text-white' : 'bg-indigo-600 text-white' }} text-xs font-bold rounded-full flex items-center justify-center shrink-0 transition-colors">
                    @if (!$this->resumenVacio)
                        <i class="fas fa-check text-[11px]"></i>
                    @else
                        2
                    @endif
                </span>
                <h2 class="font-semibold text-gray-800 text-sm sm:text-base min-w-0">¿Qué vas a enviar?</h2>
                @if (!$this->resumenVacio)
                    <span class="sm:ml-auto max-w-full text-xs font-bold text-indigo-600 bg-indigo-50 border border-indigo-100 px-2.5 py-1 rounded-full flex items-center gap-1 break-words">
                        <i class="fas fa-check-circle text-[10px]"></i> {{ $this->seleccionCount }} seleccionados
                    </span>
                @endif
            </div>

            <div class="flex gap-1 bg-gray-100 rounded-xl p-1 mb-4">
                <button wire:click="$set('tabSeleccion', 'kits')" type="button"
                    class="flex-1 py-2.5 px-1 sm:px-3 rounded-lg text-xs sm:text-sm font-medium transition-all whitespace-nowrap
                        {{ $tabSeleccion === 'kits' ? 'bg-white shadow-sm text-gray-900' : 'text-gray-500 hover:text-gray-700' }}">
                    <i class="fas fa-box sm:mr-1.5"></i> <span>Kits</span>
                    <span class="hidden sm:inline text-[11px] {{ $tabSeleccion === 'kits' ? 'text-indigo-500' : 'text-gray-400' }}">({{ $this->kitsDisponibles->count() }})</span>
                </button>
                <button wire:click="$set('tabSeleccion', 'piezas')" type="button"
                    class="flex-1 py-2.5 px-1 sm:px-3 rounded-lg text-xs sm:text-sm font-medium transition-all whitespace-nowrap
                        {{ $tabSeleccion === 'piezas' ? 'bg-white shadow-sm text-gray-900' : 'text-gray-500 hover:text-gray-700' }}">
                    <i class="fas fa-puzzle-piece sm:mr-1.5"></i> <span>Items serializados</span>
                    <span class="hidden sm:inline text-[11px] {{ $tabSeleccion === 'piezas' ? 'text-indigo-500' : 'text-gray-400' }}">({{ $this->piezasSueltas->count() }})</span>
                </button>
                <button wire:click="$set('tabSeleccion', 'cantidad')" type="button"
                    class="flex-1 py-2.5 px-1 sm:px-3 rounded-lg text-xs sm:text-sm font-medium transition-all whitespace-nowrap
                        {{ $tabSeleccion === 'cantidad' ? 'bg-white shadow-sm text-gray-900' : 'text-gray-500 hover:text-gray-700' }}">
                    <i class="fas fa-cubes sm:mr-1.5"></i> <span>Por cantidad</span>
                    <span class="hidden sm:inline text-[11px] {{ $tabSeleccion === 'cantidad' ? 'text-amber-600' : 'text-gray-400' }}">({{ $this->piezasCantidadCarrito->count() }})</span>
                </button>
            </div>

            @unless ($tabSeleccion === 'cantidad')
                <div class="flex items-center bg-gray-50 rounded-xl px-3 py-2.5 mb-4 border border-gray-200 focus-within:border-indigo-400 focus-within:ring-1 focus-within:ring-indigo-400 transition">
                    <i class="fas fa-search text-gray-400 text-sm mr-2"></i>
                    <input
                        class="bg-transparent outline-none text-sm w-full border-none focus:ring-0 p-0"
                        type="text"
                        wire:model.live.debounce.300ms="buscar"
                        placeholder="Buscar por nombre..."
                    >
                </div>
            @endunless

            <x-almacen.traslado-seleccion-kits />

            <x-almacen.traslado-seleccion-piezas />

            <x-almacen.traslado-seleccion-cantidad />

        </section>
    @endif

    {{-- ── Paso 3: resumen (siempre visible) ── --}}
    <x-almacen.traslado-resumen />

    {{-- ── CTA escritorio ── --}}
    <button wire:click="confirmarTraslado" wire:loading.attr="disabled"
        @disabled(! $this->accionHabilitada)
        class="hidden sm:flex w-full items-center justify-center {{ $this->accionHabilitada ? 'bg-indigo-600 text-white hover:bg-indigo-700 active:scale-[0.99]' : 'bg-gray-200 text-gray-400 cursor-not-allowed' }} rounded-xl py-3.5 font-semibold text-sm transition-all">
        <span wire:loading.remove wire:target="confirmarTraslado">
            <i class="fas fa-truck mr-2"></i>{{ $this->accionLabel }}
        </span>
        <span wire:loading wire:target="confirmarTraslado">
            <i class="fas fa-circle-notch fa-spin mr-2"></i> Procesando...
        </span>
    </button>

    {{-- ── CTA móvil fijo ── --}}
    <div class="sm:hidden fixed bottom-0 left-0 right-0 z-30 bg-white border-t border-gray-200 p-3 shadow-[0_-4px_12px_rgba(0,0,0,0.05)]">
        <button wire:click="confirmarTraslado" wire:loading.attr="disabled"
            @disabled(! $this->accionHabilitada)
            class="w-full flex items-center justify-center {{ $this->accionHabilitada ? 'bg-indigo-600 text-white hover:bg-indigo-700 active:scale-[0.99]' : 'bg-gray-200 text-gray-400 cursor-not-allowed' }} rounded-xl py-3.5 font-semibold text-sm transition-all">
            <span wire:loading.remove wire:target="confirmarTraslado">
                <i class="fas fa-truck mr-2"></i>{{ $this->accionHabilitada ? $this->accionLabel . ' (' . $this->seleccionCount . ')' : $this->accionLabel }}
            </span>
            <span wire:loading wire:target="confirmarTraslado">
                <i class="fas fa-circle-notch fa-spin mr-2"></i> Procesando...
            </span>
        </button>
    </div>

    <x-almacen.traslado-checklist />

</div>
