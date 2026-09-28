<div class="border-t border-dashed border-gray-300 pt-4 mt-4">
    @if (! $this->mostrandoFormNuevo)
        <x-almacen.dashed-toggle label="Registrar componente nuevo" toggle="toggleFormNuevo" />
    @else
        <x-almacen.panel-form title="Nuevo componente" toggle="toggleFormNuevo">
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 mb-3">
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Tipo</label>
                    <select wire:model.live="nuevoTipo" class="w-full text-sm border-gray-300 rounded-lg focus:border-indigo-500 focus:ring-indigo-500">
                        <option value="serializado">Serializado</option>
                        <option value="cantidad">Por cantidad</option>
                    </select>
                </div>
                @if ($this->nuevoTipo === 'serializado')
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Categoría *</label>
                        <select wire:model.live="nuevoCategoriaId" class="w-full text-sm border-gray-300 rounded-lg focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="">Seleccionar categoría...</option>
                            @foreach ($this->categoriasSerializadas as $cat)
                                <option value="{{ $cat->id }}">{{ $cat->nombre }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Serie</label>
                        <input type="text" wire:model.live="nuevoSerie" placeholder="Ej: ABC-123"
                               class="w-full text-sm border-gray-300 rounded-lg focus:border-indigo-500 focus:ring-indigo-500">
                    </div>
                @endif
                @if ($this->nuevoTipo === 'cantidad')
                    <div class="sm:col-span-2">
                        <label class="block text-xs font-medium text-gray-600 mb-1">Nombre del producto *</label>
                        <input type="text" wire:model.live="nuevoNombre" placeholder="Ej: Manómetro 150psi"
                               class="w-full text-sm border-gray-300 rounded-lg focus:border-indigo-500 focus:ring-indigo-500">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Cantidad</label>
                        <input type="number" min="1" max="99" wire:model.live="nuevaCantidad"
                               class="w-full text-sm border-gray-300 rounded-lg focus:border-indigo-500 focus:ring-indigo-500">
                    </div>
                @endif
            </div>
            <x-almacen.wire-submit target="registrarComponenteNuevo" label="Registrar y agregar"
                onclick="recepcionSwal.registrarComponenteNuevo($wire)" />
        </x-almacen.panel-form>
    @endif
</div>
