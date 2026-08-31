<div>
    <x-dialog-modal wire:model="mostrarModal" maxWidth="lg">
        <x-slot name="title">
            Componentes de {{ $producto?->nombre }}
        </x-slot>

        <x-slot name="content">
            @if ($producto)
                <div class="space-y-3 mb-4">
                    @forelse ($this->componentes as $kc)
                        <div class="flex justify-between items-center bg-gray-50 rounded-lg px-3 py-2 text-sm">
                            <span>
                                {{ $kc->componente->nombre }}
                                @if ($kc->componente->categoria->es_serializado)
                                    <span class="text-xs bg-purple-100 text-purple-700 px-1.5 py-0.5 rounded-full ml-1">Con serie</span>
                                @endif
                            </span>
                            <div class="flex items-center gap-2">
                                <span class="text-gray-500">× {{ $kc->cantidad_esperada }}</span>
                                <button wire:click="quitarComponente({{ $kc->id }})" type="button" class="text-red-600 text-xs">Quitar</button>
                            </div>
                        </div>
                    @empty
                        <p class="text-sm text-gray-400">Este kit aún no tiene componentes definidos.</p>
                    @endforelse
                </div>

                <div class="flex gap-2 items-end border-t pt-3">
                    <div class="flex-1">
                        <x-label for="componenteId" value="Agregar componente" />
                        <select wire:model="componenteId" class="w-full rounded-lg border-gray-300 text-sm">
                            <option value="">-- Selecciona --</option>
                            @foreach ($this->productosDisponibles as $p)
                                <option value="{{ $p->id }}">{{ $p->nombre }} ({{ $p->categoria->nombre }})</option>
                            @endforeach
                        </select>
                        <x-input-error for="componenteId" class="mt-1" />
                    </div>
                    <div class="w-20">
                        <x-label for="cantidadEsperada" value="Cant." />
                        <x-input type="number" min="1" wire:model="cantidadEsperada" class="w-full rounded-lg border-gray-300" />
                    </div>
                    <x-secondary-button wire:click="agregarComponente" type="button">Agregar</x-secondary-button>
                </div>
            @endif
        </x-slot>

        <x-slot name="footer">
            <x-button wire:click="cerrar">Listo</x-button>
        </x-slot>
    </x-dialog-modal>
</div>