<div>
    <x-dialog-modal wire:model="mostrarModal">
        <x-slot name="title">
            Registrar serie — {{ $kitItem?->producto->nombre }} (Lote: {{ $kitItem?->serie }})
        </x-slot>

        <x-slot name="content">
            <p class="text-xs text-gray-500 mb-3">
                El almacenero destapa la caja solo para leer la serie, sin separar ninguna pieza. El kit se registra como sellado.
            </p>

            <x-label for="serieReductor" :value="'Serie del ' . ($productoReductor?->nombre ?? 'reductor')" />
            <x-input wire:model="serieReductor" placeholder="Serie impresa en el reductor" class="w-full rounded-lg border-gray-300" />
            <x-input-error for="serieReductor" class="mt-1" />
        </x-slot>

        <x-slot name="footer">
            <x-secondary-button wire:click="cerrar">Cancelar</x-secondary-button>
            <x-button wire:click="guardar" wire:loading.attr="disabled" class="ml-2">Guardar serie</x-button>
        </x-slot>
    </x-dialog-modal>
</div>