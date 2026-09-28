    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 sm:p-6"
        x-data="{ hasSede: {{ $this->sedeDestinoId ? 'true' : 'false' }} }">
        <div class="flex items-center gap-2 mb-4">
            <span class="w-7 h-7 {{ $this->sedeDestinoId ? 'bg-emerald-600 text-white' : 'bg-indigo-600 text-white' }} text-xs font-bold rounded-full flex items-center justify-center shrink-0 transition-colors">
                @if ($this->sedeDestinoId)
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

        @unless ($this->sedeDestinoId)
            <div class="mt-4 pt-4 border-t border-gray-100 flex items-center justify-between gap-3">
                <p class="text-[11px] text-gray-400"><i class="fas fa-circle-info mr-1"></i>Elige una sede y presiona Continuar para seleccionar los items</p>
                <button type="button" wire:click="$refresh" wire:loading.attr="disabled"
                    x-bind:disabled="!hasSede"
                    x-bind:class="hasSede ? 'bg-indigo-600 text-white hover:bg-indigo-700' : 'bg-gray-200 text-gray-400 cursor-not-allowed'"
                    class="shrink-0 px-5 py-2.5 rounded-lg text-sm font-semibold transition active:scale-[0.98]">
                    Continuar <i class="fas fa-arrow-right ml-1 text-xs"></i>
                </button>
            </div>
        @else
            <div class="mt-4 pt-4 border-t border-gray-100 flex items-center justify-between gap-2">
                <p class="text-xs text-emerald-600 font-medium"><i class="fas fa-check-circle mr-1"></i>Sede seleccionada: {{ $this->sedes->firstWhere('id', $this->sedeDestinoId)?->nombre }}</p>
                <button type="button" wire:click="$refresh" class="text-xs text-gray-400 hover:text-gray-600 underline underline-offset-2">
                    Actualizar
                </button>
            </div>
        @endunless
    </div>
