<div>
    <x-dialog-modal wire:model="mostrarModal">
        <x-slot name="title">Nuevo producto</x-slot>

        <x-slot name="content">
            <div class="space-y-4">
                <div>
                    <x-label for="categoriaId" value="Categoría" />
                    <select wire:model.live="categoriaId" class="w-full rounded-lg border-gray-300 text-sm">
                        <option value="">-- Selecciona --</option>
                        @foreach ($categorias as $c)
                            <option value="{{ $c->id }}">{{ $c->nombre }} ({{ $c->es_serializado ? 'por serie' : 'por cantidad' }})</option>
                        @endforeach
                    </select>
                    <x-input-error for="categoriaId" class="mt-1" />
                </div>

                <div>
                    <x-label for="nombre" value="Nombre del producto / modelo" />
                    <x-input wire:model="nombre" placeholder="Ej: Reductor OMVL Dream XXI" class="w-full rounded-lg border-gray-300" />
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

                @if ($this->categoria && !$this->categoria->es_serializado)
                    <div>
                        <x-label for="precioReferencial" value="Precio referencial de venta (S/)" />
                        <x-input type="number" step="0.01" wire:model="precioReferencial" class="w-full rounded-lg border-gray-300" />
                    </div>
                @endif
            </div>
        </x-slot>

        <x-slot name="footer">
            <x-secondary-button wire:click="cerrar">Cancelar</x-secondary-button>
            <x-button wire:click="guardar" wire:loading.attr="disabled" class="ml-2">Guardar producto</x-button>
        </x-slot>
    </x-dialog-modal>
</div>