    @php
        $sedeDestino = $this->sedeDestinoId
            ? $this->sedes->firstWhere('id', $this->sedeDestinoId)
            : null;
    @endphp

    <section class="bg-white rounded-2xl shadow-sm border border-gray-200 p-4 sm:p-6">
        <div class="flex items-center gap-2 mb-4">
            <span class="w-7 h-7 {{ $this->sedeDestinoId ? 'bg-emerald-600 text-white' : 'bg-indigo-600 text-white' }} text-xs font-bold rounded-full flex items-center justify-center shrink-0 transition-colors">
                @if ($this->sedeDestinoId)
                    <i class="fas fa-check text-[11px]"></i>
                @else
                    1
                @endif
            </span>
            <h2 class="font-semibold text-gray-800 text-sm sm:text-base min-w-0">Configuración del envío</h2>
        </div>

        {{-- Origen siempre visible: en un traslado importa de dónde sale. --}}
        <div class="flex items-center gap-2 mb-4 rounded-xl bg-gray-50 border border-gray-200 px-3 py-2.5">
            <i class="fas fa-location-dot text-indigo-500 text-sm shrink-0"></i>
            <span class="text-xs text-gray-500">Origen</span>
            <span class="text-sm font-semibold text-gray-800 truncate">{{ $this->sedeOrigen?->nombre ?? '—' }}</span>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <x-label for="sedeDestinoId" value="Sede destino *" />
                <div class="relative mt-1">
                    <select wire:model.live="sedeDestinoId"
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

        {{-- El paso 2 se revela solo al elegir sede: no hace falta botón "Continuar". --}}
        <div class="mt-4 pt-4 border-t border-gray-100">
            @unless ($this->sedeDestinoId)
                <p class="text-xs text-gray-400">
                    <i class="fas fa-circle-info mr-1"></i>Elige una sede destino para desbloquear la selección de kits y piezas
                </p>
            @else
                <p class="text-xs text-emerald-600 font-medium">
                    <i class="fas fa-check-circle mr-1"></i>Destino: {{ $sedeDestino?->nombre }} — ya podés elegir qué enviar
                </p>
            @endunless
        </div>
    </section>
