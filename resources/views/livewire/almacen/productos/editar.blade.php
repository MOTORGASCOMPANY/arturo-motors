<div>
    <x-dialog-modal wire:model="mostrarModal">
        <x-slot name="title">Editar producto</x-slot>

        <x-slot name="content">
            @if ($producto)
                <div class="space-y-4">
                    <div>
                        <x-label for="categoriaId" value="Categoría" />
                        <select wire:model.live="categoriaId" class="w-full rounded-lg border-gray-300 text-sm" @disabled($categoriaBloqueada)>
                            @foreach ($categorias as $c)
                                <option value="{{ $c->id }}">{{ $c->nombre }}</option>
                            @endforeach
                        </select>
                        @if ($categoriaBloqueada)
                            <p class="text-xs text-amber-600 mt-1">Ya tiene stock registrado, no se puede cambiar de categoría.</p>
                        @endif
                    </div>

                    <div>
                        <x-label for="nombre" value="Nombre del producto / modelo" />
                        <x-input wire:model="nombre" class="w-full rounded-lg border-gray-300" />
                        <x-input-error for="nombre" class="mt-1" />
                    </div>

                    <div>
                        <x-label for="marca" value="Marca" />
                        <x-input wire:model="marca" class="w-full rounded-lg border-gray-300" />
                    </div>

                    @if ($this->categoria && $this->categoria->esquema_atributos)
                        <div class="p-4 bg-gray-50 rounded-lg border space-y-2">
                            <p class="text-xs font-bold text-gray-500 uppercase">Atributos de {{ $this->categoria->nombre }}</p>
                            @foreach ($this->categoria->esquema_atributos as $campo)
                                <div>
                                    <x-label :value="ucfirst($campo)" />
                                    <x-input wire:model="atributos.{{ $campo }}" class="w-full rounded-lg border-gray-300 text-sm" />
                                </div>
                            @endforeach
                        </div>
                    @endif

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <x-label for="precioReferencial" value="Precio referencial (S/)" />
                            <x-input type="number" step="0.01" wire:model="precioReferencial" class="w-full rounded-lg border-gray-300" />
                        </div>
                        <div>
                            <x-label for="stockMinimo" value="Stock mínimo" />
                            <x-input type="number" min="0" wire:model="stockMinimo" class="w-full rounded-lg border-gray-300" />
                        </div>
                    </div>
                </div>
            @endif
        </x-slot>

        <x-slot name="footer">
            <x-secondary-button wire:click="cerrar">Cancelar</x-secondary-button>
            <x-button wire:click="guardar" wire:loading.attr="disabled" class="ml-2">Guardar cambios</x-button>
        </x-slot>
    </x-dialog-modal>
</div>