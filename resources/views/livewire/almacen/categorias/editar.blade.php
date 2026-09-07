<div>
    <x-dialog-modal wire:model="mostrarModal">
        <x-slot name="title">Editar categoría</x-slot>

        <x-slot name="content">
            <div class="space-y-4">
                <div>
                    <x-label for="nombre" value="Nombre" />
                    <x-input wire:model="nombre" class="w-full rounded-lg border-gray-300" />
                    <x-input-error for="nombre" class="mt-1" />
                </div>

                @if ($tieneProductos)
                    <div class="p-3 bg-amber-50 border border-amber-200 rounded-lg text-xs text-amber-700">
                        Esta categoría ya tiene productos registrados, así que no se puede cambiar si es serializada o si es un kit.
                    </div>
                @endif

                <label class="flex items-center gap-2 text-sm {{ $tieneProductos ? 'opacity-50' : '' }}">
                    <input type="checkbox" wire:model.live="esSerializado" class="rounded" @disabled($tieneProductos)>
                    Se controla por número de serie
                </label>

                @if ($esSerializado)
                    <div>
                        <x-label for="atributosTexto" value="Atributos adicionales (separados por coma)" />
                        <x-input wire:model="atributosTexto" class="w-full rounded-lg border-gray-300" />
                    </div>
                    <label class="flex items-center gap-2 text-sm {{ $tieneProductos ? 'opacity-50' : '' }}">
                        <input type="checkbox" wire:model="esKit" class="rounded" @disabled($tieneProductos)>
                        Es un kit que se puede abrir en componentes
                    </label>
                @endif
            </div>
        </x-slot>

        <x-slot name="footer">
            <x-secondary-button wire:click="cerrar">Cancelar</x-secondary-button>
            <x-button wire:click="guardar" wire:loading.attr="disabled" class="ml-2">Guardar cambios</x-button>
        </x-slot>
    </x-dialog-modal>
</div>