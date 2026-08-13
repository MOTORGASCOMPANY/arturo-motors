<div>
    <x-dialog-modal wire:model="mostrarModal">
        <x-slot name="title">
            Registrar entrada — {{ $producto?->nombre }}
        </x-slot>

        <x-slot name="content">
            @if ($producto)
                <p class="text-sm text-gray-500 mb-4">{{ $producto->categoria->nombre }}</p>

                @if ($producto->categoria->es_serializado)
                    <div class="flex gap-2">
                        <x-input wire:model="nuevaSerie" placeholder="Número de serie" class="flex-1 rounded-lg border-gray-300" />
                        <x-secondary-button wire:click="agregarSerie" type="button">Agregar</x-secondary-button>
                    </div>
                    <x-input-error for="nuevaSerie" class="mt-1" />

                    @if (count($seriesPendientes))
                        <ul class="space-y-1 mt-3">
                            @foreach ($seriesPendientes as $i => $serie)
                                <li class="flex justify-between bg-gray-50 rounded-lg px-3 py-2 text-sm">
                                    {{ $serie }}
                                    <button wire:click="quitarSerie({{ $i }})" type="button" class="text-red-600 text-xs">Quitar</button>
                                </li>
                            @endforeach
                        </ul>
                    @endif
                @else
                    <div>
                        <x-label for="cantidadEntrada" value="Cantidad a ingresar" />
                        <x-input type="number" min="1" wire:model="cantidadEntrada" class="w-full rounded-lg border-gray-300" />
                        <p class="text-xs text-gray-500 mt-1">Stock actual: {{ $producto->stock }}</p>
                    </div>
                @endif
            @endif
        </x-slot>

        <x-slot name="footer">
            <x-secondary-button wire:click="cerrar">Cancelar</x-secondary-button>
            @if ($producto?->categoria?->es_serializado)
                <x-button wire:click="guardarSeries" wire:loading.attr="disabled" class="ml-2">
                    Guardar {{ count($seriesPendientes) }} unidad(es)
                </x-button>
            @else
                <x-button wire:click="guardarCantidad" wire:loading.attr="disabled" class="ml-2">Registrar entrada</x-button>
            @endif
        </x-slot>
    </x-dialog-modal>
</div>