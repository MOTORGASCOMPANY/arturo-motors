<div wire:loading.class="opacity-60 pointer-events-none" class="max-w-4xl mx-auto px-3 sm:px-0 py-6 sm:py-12 pb-28 sm:pb-12 space-y-5 sm:space-y-6">

    <div class="flex items-center gap-2 sm:gap-3">
        <a href="{{ route('almacen.traslados.listado') }}" wire:navigate
            class="px-3 sm:px-4 py-2.5 sm:py-2 bg-white border border-gray-200 rounded-lg text-xs sm:text-sm font-medium text-gray-700 hover:bg-gray-50 hover:border-gray-300 active:scale-[0.98] transition flex items-center gap-2 shadow-sm">
            <i class="fas fa-arrow-left text-xs"></i> <span>Volver</span>
        </a>
    </div>

    <div class="relative overflow-hidden bg-indigo-600 text-white p-5 sm:p-8 rounded-2xl w-full">
        <div class="absolute -right-8 -top-8 w-40 h-40 bg-white/10 rounded-full"></div>
        <div class="absolute -right-2 -bottom-10 w-28 h-28 bg-white/10 rounded-full"></div>
        <div class="relative flex items-start gap-3 sm:gap-4">
            <div class="w-11 h-11 sm:w-14 sm:h-14 rounded-xl bg-white/15 flex items-center justify-center shrink-0">
                <i class="fas fa-truck text-lg sm:text-2xl"></i>
            </div>
            <div>
                <h2 class="font-semibold text-lg sm:text-2xl leading-tight">Nuevo traslado</h2>
                <p class="text-xs sm:text-sm text-indigo-100 mt-1">Envía un kit o piezas sueltas desde Arturo Motors hacia otra sede</p>
            </div>
        </div>
    </div>

    <x-input-error for="general" />

    <x-almacen.traslado-configuracion />

    @if ($sedeDestinoId)
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 sm:p-6">
            <div class="flex flex-wrap items-center gap-2 mb-4">
                <span class="w-7 h-7 {{ !$this->resumenVacio ? 'bg-emerald-600 text-white' : 'bg-indigo-600 text-white' }} text-xs font-bold rounded-full flex items-center justify-center shrink-0 transition-colors">
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

            <x-almacen.traslado-seleccion-kits />

            <x-almacen.traslado-seleccion-piezas />

            <x-almacen.traslado-seleccion-cantidad />

        </div>
    @endif

    <x-almacen.traslado-resumen />

    <button wire:click="confirmarTraslado" wire:loading.attr="disabled"
        @if ($this->resumenVacio || !$sedeDestinoId) disabled @endif
        class="hidden sm:flex w-full items-center justify-center {{ $this->resumenVacio || !$sedeDestinoId ? 'bg-gray-200 text-gray-400 cursor-not-allowed' : 'bg-indigo-600 text-white hover:bg-indigo-700 active:scale-[0.99]' }} rounded-xl py-3.5 sm:py-3 font-semibold text-sm transition-all">
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
            class="w-full flex items-center justify-center {{ $this->resumenVacio || !$sedeDestinoId ? 'bg-gray-200 text-gray-400 cursor-not-allowed' : 'bg-indigo-600 text-white hover:bg-indigo-700 active:scale-[0.99]' }} rounded-xl py-3.5 font-semibold text-sm transition-all">
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

    <x-almacen.traslado-checklist />

</div>