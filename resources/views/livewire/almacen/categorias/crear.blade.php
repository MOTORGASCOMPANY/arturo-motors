<div>
    <x-dialog-modal wire:model="mostrarModal">
        <x-slot name="title">Nueva categoría</x-slot>

        <x-slot name="content">
            <div class="space-y-4">
                <div>
                    <x-label for="nombre" value="Nombre" />
                    <x-input wire:model="nombre" class="w-full rounded-lg border-gray-300" />
                    <x-input-error for="nombre" class="mt-1" />
                </div>

                <label class="flex items-center gap-2 text-sm">
                    <input type="checkbox" wire:model="esSerializado" class="rounded">
                    Se controla por número de serie (ej: reductores, tanques)
                </label>

                @if ($esSerializado)
                    <div>
                        <x-label for="atributosTexto" value="Atributos adicionales (separados por coma)" />
                        <x-input wire:model="atributosTexto" placeholder="generacion, capacidad" class="w-full rounded-lg border-gray-300" />
                    </div>
                @endif
            </div>
        </x-slot>

        <x-slot name="footer">
            <x-secondary-button wire:click="cerrar">Cancelar</x-secondary-button>
            <x-button wire:click="guardar" wire:loading.attr="disabled" class="ml-2">Guardar categoría</x-button>
        </x-slot>
    </x-dialog-modal>
</div>