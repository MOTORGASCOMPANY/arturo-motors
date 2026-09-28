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
                        @foreach ($this->categoria->esquema_atributos as $campo => $valor)
                            <div>
                                @if (is_int($campo))

                                    <x-label :value="ucfirst($valor)" />
                                    <x-input wire:model="atributos.{{ $valor }}" class="w-full rounded-lg border-gray-300 text-sm" />
                                @else

                                    <x-label :value="ucfirst($campo)" />
                                    <select wire:model="atributos.{{ $campo }}" class="w-full rounded-lg border-gray-300 text-sm">
                                        <option value="">-- Selecciona --</option>
                                        @foreach ((array) $valor as $op)
                                            <option value="{{ $op }}">{{ $op }}</option>
                                        @endforeach
                                    </select>
                                @endif
                            </div>
                        @endforeach
                    </div>
                @endif
                @if ($this->categoria)
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <x-label for="precioReferencial" value="Precio referencial (S/)" />
                            <x-input type="number" step="0.01" wire:model="precioReferencial" class="w-full rounded-lg border-gray-300" />
                        </div>
                        <div>
                            <x-label for="stockMinimo" value="Stock mínimo" />
                            <x-input type="number" min="0" wire:model="stockMinimo" class="w-full rounded-lg border-gray-300" />
                            <p class="text-xs text-gray-500 mt-1">Se alertará cuando el disponible baje de este número.</p>
                        </div>
                    </div>

                    @if (!$this->categoria->es_serializado)
                        <div class="mt-3">
                            <x-label for="stockInicial" value="Stock inicial" />
                            <x-input type="number" min="0" wire:model="stockInicial" class="w-full rounded-lg border-gray-300" />
                            <p class="text-xs text-gray-500 mt-1">Cantidad de unidades a registrar en inventario al crear.</p>
                        </div>
                    @else
                        <p class="text-xs text-amber-600 bg-amber-50 border border-amber-200 rounded-lg p-2 mt-3">
                            <i class="fas fa-barcode mr-1"></i>
                            Este producto es serializado. Registra las entradas con número de serie desde "Registrar entrada".
                        </p>
                    @endif
                @endif
            </div>
        </x-slot>

        <x-slot name="footer">
            <x-secondary-button wire:click="cerrar">Cancelar</x-secondary-button>
            <x-button wire:click="guardar" wire:loading.attr="disabled" class="ml-2">Guardar producto</x-button>
        </x-slot>
    </x-dialog-modal>
</div>