<div>
    <x-almacen.info-banner icon="fa-box" color="indigo"
        :title="'Registrando <strong>Kits</strong>'"
        action-label="Cambiar" action-method="volverAEleccion" />

    <div class="mb-6">
        <label class="block text-sm font-bold text-gray-700 mb-3">Kits a recibir</label>
        <div class="space-y-3">
            @forelse ($this->kitsDisponibles as $kit)
                @php $gen = $kit->atributos['generacion'] ?? '—'; @endphp
                <div wire:key="kit-{{ $kit->id }}" class="p-4 bg-gray-50 rounded-xl border border-gray-200 hover:border-indigo-300 transition-colors">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-4">
                            <div class="w-12 h-12 rounded-xl bg-indigo-100 flex items-center justify-center">
                                <i class="fas fa-box text-indigo-600 text-lg"></i>
                            </div>
                            <div>
                                <p class="text-sm font-bold text-gray-800">{{ $kit->nombre }}</p>
                                <div class="flex items-center gap-2 mt-0.5">
                                    <span class="px-2 py-0.5 bg-indigo-100 text-indigo-700 text-[10px] font-bold rounded-full">{{ $gen }}</span>
                                    <span class="text-xs text-gray-400">En stock: {{ $kit->stockTotal() }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="flex items-center gap-2">
                            <button type="button"
                                data-id="{{ $kit->id }}" data-nombre="{{ $kit->nombre }}" data-gen="{{ $gen === '—' ? '' : $gen }}"
                                x-on:click="almacenAcciones.editarKit($wire, $el.dataset)"
                                class="w-8 h-8 rounded-lg bg-gray-200 hover:bg-gray-300 flex items-center justify-center text-gray-600 transition" title="Editar kit">
                                <i class="fas fa-pen text-xs"></i>
                            </button>
                            <button type="button"
                                data-id="{{ $kit->id }}" data-nombre="{{ $kit->nombre }}"
                                x-on:click="almacenAcciones.eliminarKit($wire, $el.dataset)"
                                class="w-8 h-8 rounded-lg bg-red-100 hover:bg-red-200 flex items-center justify-center text-red-600 transition" title="Desactivar kit">
                                <i class="fas fa-trash text-xs"></i>
                            </button>
                            <x-almacen.quantity-stepper :model="'cantidades.' . $kit->id" :productId="$kit->id" color="indigo" />
                        </div>
                    </div>
                </div>
            @empty
                <p class="text-gray-400 text-sm text-center py-6">Todavía no hay kits registrados. Crea el primero abajo.</p>
            @endforelse
        </div>
    </div>

    <div class="mb-6">
        @if (! $this->mostrandoFormKit)
            <x-almacen.dashed-toggle label="Registrar kit nuevo" toggle="toggleFormKit" />
        @else
            <x-almacen.panel-form title="Nuevo kit" toggle="toggleFormKit">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 mb-3">
                    <div class="sm:col-span-2">
                        <label class="block text-xs font-medium text-gray-600 mb-1">Nombre del kit *</label>
                        <input type="text" wire:model.live="nuevoKitNombre" placeholder="Ej: Equipo 5TA"
                               class="w-full text-sm border-gray-300 rounded-lg focus:border-indigo-500 focus:ring-indigo-500">
                    </div>
                    <div class="sm:col-span-2">
                        <label class="block text-xs font-medium text-gray-600 mb-1">Generación</label>
                        <input type="text" wire:model.live="nuevoKitGeneracion" placeholder="Ej: 3ra Generación"
                               class="w-full text-sm border-gray-300 rounded-lg focus:border-indigo-500 focus:ring-indigo-500">
                    </div>
                </div>
                <x-almacen.wire-submit target="registrarKitNuevo" label="Registrar kit"
                    onclick="almacenAcciones.validarYLlamar($wire, [['nuevoKitNombre', 'El nombre del kit']], 'registrarKitNuevo')" />
            </x-almacen.panel-form>
        @endif
    </div>

    <x-almacen.notas placeholder="Ej: Kit incompleto, falta válvula..." />

    <x-almacen.total-banner :total="$this->totalKits" icon="fa-box" bg="gray" icon-color="indigo" suffix="kit(s).">
        En el siguiente paso registrarás los componentes y series.
    </x-almacen.total-banner>

    <x-almacen.flow-actions method="guardar" cancel-action="volverAEleccion"
        :label="'Recibir ' . ($this->totalKits > 0 ? $this->totalKits . ' kit(s)' : '')"
        :data="[
            'data-total' => $this->totalKits,
            'data-titulo' => '¿Continuar con ' . $this->totalKits . ' kit(s)?',
            'data-texto' => 'Después registrarás los componentes y series de cada kit.',
            'data-aviso' => 'Selecciona la cantidad de al menos un kit para continuar.',
        ]" />
</div>
