<div>
    <x-almacen.info-banner icon="fa-cubes" color="amber"
        :title="'Registrando <strong>Productos por cantidad</strong>'"
        action-label="Volver" action-method="volverAProductos" />

    <div class="flex items-center justify-between mb-4">
        <div class="flex items-center bg-gray-50 rounded-lg px-3 py-2 flex-1 min-w-[200px] max-w-sm">
            <i class="fas fa-search text-gray-400 text-sm mr-2"></i>
            <input class="bg-transparent outline-none text-sm w-full border-none focus:ring-0"
                type="text" wire:model.live="buscarCantidad" placeholder="Buscar producto...">
        </div>
        <button type="button" wire:click="toggleFormNuevoCantidad"
            class="ml-3 px-4 py-2 bg-amber-600 hover:bg-amber-700 text-white text-sm font-semibold rounded-lg transition-colors flex items-center gap-2 shrink-0">
            <i class="fas fa-plus text-xs"></i> Crear nuevo
        </button>
    </div>

    @if ($this->mostrandoFormNuevoCantidad)
        <div class="bg-amber-50 border border-amber-200 rounded-xl p-4 mb-4">
            <h4 class="text-sm font-bold text-gray-800 mb-3 flex items-center gap-2">
                <i class="fas fa-box text-amber-600"></i> Nuevo producto
            </h4>
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                <div>
                    <label class="block text-[10px] font-bold text-gray-500 uppercase mb-1">Nombre *</label>
                    <input type="text" wire:model="nuevoCantidadNombre"
                        class="w-full text-sm border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-amber-500" placeholder="Ej: Tuerca M8">
                </div>
                <div>
                    <label class="block text-[10px] font-bold text-gray-500 uppercase mb-1">Categoría *</label>
                    <select wire:model="nuevoCantidadCategoriaId"
                        class="w-full text-sm border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-amber-500">
                        <option value="">Seleccionar...</option>
                        @foreach ($this->categoriasCantidad as $cat)
                            <option value="{{ $cat->id }}">{{ $cat->nombre }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-[10px] font-bold text-gray-500 uppercase mb-1">Stock inicial *</label>
                    <input type="number" min="1" max="999" wire:model="nuevoCantidadStockInicial"
                        class="w-full text-sm border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-amber-500">
                </div>
            </div>
            <div class="flex justify-end gap-2 mt-3">
                <button type="button" wire:click="toggleFormNuevoCantidad" class="px-3 py-1.5 text-xs font-medium text-gray-600 bg-white border border-gray-300 rounded-lg hover:bg-gray-50">Cancelar</button>
                <button type="button"
                    x-on:click="almacenAcciones.validarYLlamar($wire, [['nuevoCantidadNombre', 'El nombre'], ['nuevoCantidadCategoriaId', 'La categoría'], ['nuevoCantidadStockInicial', 'El stock inicial']], 'crearProductoCantidad')"
                    class="px-4 py-1.5 text-xs font-bold text-white bg-amber-600 rounded-lg hover:bg-amber-700 transition">
                    <i class="fas fa-plus mr-1"></i> Crear y agregar
                </button>
            </div>
        </div>
    @endif

    <div class="space-y-2">
        @forelse ($this->filtradosCantidad as $producto)
            <div wire:key="cant-{{ $producto->id }}" class="flex items-center gap-3 p-3 bg-gray-50 rounded-xl border border-gray-200 hover:border-amber-300 transition-colors">
                <div class="w-10 h-10 rounded-xl bg-amber-100 flex items-center justify-center shrink-0">
                    <i class="fas fa-cubes text-amber-600 text-sm"></i>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-bold text-gray-800 truncate">{{ $producto->nombre }}</p>
                    <p class="text-[10px] text-gray-400">{{ $producto->categoria->nombre ?? '—' }}</p>
                </div>
                <div class="flex items-center gap-2 shrink-0">
                    <x-almacen.quantity-stepper :model="'cantidadesCantidad.' . $producto->id" :productId="$producto->id" variant="cantidad" color="amber" />
                </div>
            </div>
        @empty
            <p class="text-gray-400 text-sm text-center py-6">No hay productos por cantidad registrados. Usa "Crear nuevo" para agregar el primero.</p>
        @endforelse
    </div>

    <x-almacen.notas class="mt-5" placeholder="Ej: Recepción parcial, faltan piezas..." />

    <x-almacen.total-banner :total="$this->totalCantidad" icon="fa-cubes" bg="amber" icon-color="amber"
        suffix="unidad(es) en total." />

    <x-almacen.flow-actions method="guardarCantidad" cancel-action="volverAProductos" color="amber"
        :label="'Recibir ' . ($this->totalCantidad > 0 ? $this->totalCantidad . ' unidad(es)' : '')"
        :data="[
            'data-total' => $this->totalCantidad,
            'data-titulo' => '¿Recibir ' . $this->totalCantidad . ' unidad(es)?',
            'data-texto' => 'Se sumarán al stock de la sede.',
            'data-aviso' => 'Indica la cantidad de al menos un producto para continuar.',
        ]" />
</div>
