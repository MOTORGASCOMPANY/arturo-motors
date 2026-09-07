<div wire:loading.class="opacity-50 pointer-events-none" class="max-w-3xl mx-auto py-12 space-y-6">
    <!-- Titulo y subtitulo -->
    <div class="bg-gray-200 p-8 rounded-xl w-full">
        <h2 class="text-gray-600 font-semibold text-2xl"><i class="fas fa-truck mr-2"></i>Nuevo traslado</h2>
        <span class="text-xs">Enviar equipos o repuestos desde Arturo Motors hacia otra sede</span>
    </div>

    <x-input-error for="general" />

    <!-- Sede destino y observaciones -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200/80 p-6">
        <x-label for="sedeDestinoId" value="Sede destino" />
        <select wire:model="sedeDestinoId" class="w-full rounded-lg border-gray-300 text-sm">
            <option value="">-- Selecciona --</option>
            @foreach ($this->sedes as $s)
                <option value="{{ $s->id }}">{{ $s->nombre }}</option>
            @endforeach
        </select>
        <x-input-error for="sedeDestinoId" class="mt-1" />

        <div class="mt-3">
            <x-label for="observaciones" value="Observaciones (opcional)" />
            <x-input wire:model="observaciones" class="w-full rounded-lg border-gray-300" placeholder="Ej: para conversión pendiente en Ventanilla" />
        </div>
    </div>

    <!-- Kits sellados -->
    <div class="bg-emerald-50/60 rounded-xl shadow-sm border border-emerald-200/80 p-6">
        <h3 class="font-semibold text-emerald-800 mb-1">📦 Kits sellados (caja completa, sin abrir)</h3>
        <p class="text-xs text-emerald-700/80 mb-3">Selecciona uno para enviarlo tal cual, sin desarmar.</p>

        <input type="text" wire:model.live.debounce.300ms="buscarItem" placeholder="Buscar por serie o nombre del kit..."
            class="w-full rounded-lg border-emerald-200 focus:border-emerald-500 focus:ring-emerald-500 text-sm mb-3 bg-white">

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 max-h-56 overflow-y-auto">
            @forelse ($this->kitsCerrados as $item)
                <label class="flex items-center gap-2 border rounded-lg px-3 py-2 text-sm cursor-pointer transition-colors duration-150
                            {{ isset($itemsSeleccionados[$item->id]) 
                                ? 'border-emerald-600 bg-emerald-100/80 text-emerald-950 shadow-sm' 
                                : 'border-emerald-200/70 bg-white hover:bg-emerald-100/40 text-gray-700' }}">
                    <input type="checkbox" wire:click="toggleItem({{ $item->id }})" @checked(isset($itemsSeleccionados[$item->id]))
                        class="rounded border-emerald-300 text-emerald-600 focus:ring-emerald-500">
                    <span>
                        <span class="font-medium">{{ $item->producto->nombre }}</span>
                        <br><span class="text-xs text-emerald-700/70">Lote/serie: {{ $item->serie }}</span>
                    </span>
                </label>
            @empty
                <p class="text-sm text-emerald-600/70 col-span-2">No hay kits sellados disponibles en Arturo Motors.</p>
            @endforelse
        </div>
    </div>

    <!-- Armar kit completo -->
    <div class="bg-purple-50 border border-purple-200 rounded-xl p-6">
        <h3 class="font-semibold text-purple-800 mb-3"><i class="fas fa-box-open mr-1"></i> Agregar un kit completo</h3>
        <p class="text-xs text-purple-600 mb-3">Selecciona un tipo de kit y se agregarán automáticamente todos sus componentes al traslado.</p>

        <div class="flex gap-2">
            <select wire:model="kitParaArmar" class="flex-1 rounded-lg border-gray-300 text-sm">
                <option value="">-- Selecciona un kit --</option>
                @foreach ($this->kitsDisponibles as $k)
                    <option value="{{ $k->id }}">{{ $k->nombre }}</option>
                @endforeach
            </select>
            <x-secondary-button wire:click="agregarKitCompleto" type="button">Agregar kit completo</x-secondary-button>
        </div>
        <x-input-error for="kitParaArmar" class="mt-1" />
    </div>    

    <!-- Equipos individuales -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200/80 p-6">
        <h3 class="font-semibold text-gray-800 mb-1">Equipos individuales (reductor, tanque...)</h3>
        <p class="text-xs text-gray-500 mb-3">Piezas sueltas con número de serie propio, fuera de un kit.</p>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 max-h-56 overflow-y-auto">
            @forelse ($this->equiposIndividuales as $item)
                <label class="flex items-center gap-2 border rounded-lg px-3 py-2 text-sm cursor-pointer
                            {{ isset($itemsSeleccionados[$item->id]) ? 'border-blue-600 bg-blue-50' : 'border-gray-200' }}">
                    <input type="checkbox" wire:click="toggleItem({{ $item->id }})" @checked(isset($itemsSeleccionados[$item->id]))>
                    <span>{{ $item->producto->nombre }} — <span class="text-xs text-gray-500">Serie: {{ $item->serie }}</span></span>
                </label>
            @empty
                <p class="text-sm text-gray-400 col-span-2">No hay equipos individuales disponibles.</p>
            @endforelse
        </div>
    </div>

    <!-- Equipos serializados -->
    {{--
    <div class="bg-white rounded-xl shadow-sm border border-gray-200/80 p-6">
        <h3 class="font-semibold text-gray-800 mb-3">Equipos (reductor, tanque...)</h3>
        <input type="text" wire:model.live.debounce.300ms="buscarItem" placeholder="Buscar por serie o producto..."
               class="w-full rounded-lg border-gray-300 text-sm mb-3">

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 max-h-56 overflow-y-auto">
            @forelse ($this->itemsDisponibles as $item)
                <label class="flex items-center gap-2 border rounded-lg px-3 py-2 text-sm cursor-pointer
                              {{ isset($itemsSeleccionados[$item->id]) ? 'border-blue-600 bg-blue-50' : 'border-gray-200' }}">
                    <input type="checkbox" wire:click="toggleItem({{ $item->id }})" @checked(isset($itemsSeleccionados[$item->id]))>
                    <span>{{ $item->producto->nombre }} — <span class="text-xs text-gray-500">Serie: {{ $item->serie }}</span></span>
                </label>
            @empty
                <p class="text-sm text-gray-400 col-span-2">No hay equipos disponibles en Arturo Motors.</p>
            @endforelse
        </div>

        @if ($this->itemsCarrito->count())
            <div class="mt-3 pt-3 border-t space-y-1">
                @foreach ($this->itemsCarrito as $item)
                    <div class="flex justify-between text-sm bg-gray-50 rounded-lg px-3 py-2">
                        {{ $item->producto->nombre }} — Serie {{ $item->serie }}
                        <button wire:click="toggleItem({{ $item->id }})" type="button" class="text-red-600 text-xs">Quitar</button>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
    --}}

    <!-- Repuestos por cantidad -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200/80 p-6">
        <h3 class="font-semibold text-gray-800 mb-3">Repuestos y componentes (por cantidad)</h3>

        <div class="flex gap-2">
            <select wire:model="productoRepuestoId" class="flex-1 rounded-lg border-gray-300 text-sm">
                <option value="">-- Selecciona --</option>
                @foreach ($this->productosRepuesto as $p)
                    <option value="{{ $p->id }}">{{ $p->nombre }} (disponible: {{ $p->stockEnSede(1) }})</option>
                @endforeach
            </select>
            <input type="number" min="1" wire:model="cantidadRepuesto" class="w-20 rounded-lg border-gray-300 text-sm">
            <x-secondary-button wire:click="agregarRepuesto" type="button">Agregar</x-secondary-button>
        </div>
        <x-input-error for="cantidadRepuesto" class="mt-1" />

        @if ($this->repuestosCarrito->count())
            <ul class="mt-3 space-y-1">
                @foreach ($this->repuestosCarrito as $p)
                    <li class="flex justify-between text-sm bg-gray-50 rounded-lg px-3 py-2">
                        {{ $p->nombre }} × {{ $p->cantidad_solicitada }}
                        <button wire:click="quitarRepuesto({{ $p->id }})" type="button" class="text-red-600 text-xs">Quitar</button>
                    </li>
                @endforeach
            </ul>
        @endif
    </div>

    <x-button wire:click="confirmarTraslado" wire:loading.attr="disabled" class="w-full justify-center">
        Confirmar traslado
    </x-button>
</div>