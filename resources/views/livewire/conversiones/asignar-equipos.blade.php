<div wire:loading.class="opacity-50 pointer-events-none" class="container mx-auto py-12">
    <div class="bg-gray-200 p-8 rounded-xl w-full max-w-4xl mx-auto space-y-6">        
        <!-- Encabezado con detalles y botón de retorno -->
        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 border-b border-gray-300 pb-4">
            <div>
                <h2 class="text-gray-600 font-semibold text-2xl">
                    <i class="fas fa-boxes-packing mr-2"></i>Asignar equipos — Orden #{{ $orden->id }}
                </h2>
                <span class="text-xs text-gray-600 block mt-1">
                    <strong>Cliente:</strong> {{ $orden->cliente->nombre }} {{ $orden->cliente->apellido }} | 
                    <strong>Vehículo:</strong> <span class="font-bold">{{ $orden->vehiculo->placa }}</span> | 
                    <strong>Servicio:</strong> {{ $orden->service->nombre }}
                </span>
            </div>
            <a href="{{ route('conversiones.almacen-pendientes') }}" 
                class="inline-flex items-center gap-2 px-3 py-1.5 bg-white border border-gray-300 rounded-lg text-xs font-semibold text-gray-700 hover:bg-gray-50 hover:text-gray-900 transition-all shadow-sm">
                <i class="fas fa-arrow-left text-gray-500"></i> Volver a Pendientes
            </a>            
        </div>

        <x-input-error for="general" class="mb-2" />

        <!-- Sección de Equipos Serializados -->
        <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-200">
            <h3 class="font-semibold text-gray-800 mb-3 flex items-center">
                <i class="fas fa-barcode text-blue-600 mr-2"></i>Equipos Serializados (Reductor, Tanque, Chip...)
            </h3>

            <div class="mb-4">
                <x-label value="Buscar por serie, producto o marca" class="mb-1" />
                <x-input type="text" 
                         wire:model.live.debounce.300ms="buscarItem"
                         placeholder="Ingresa número de serie o nombre..." 
                         class="w-full text-sm" />
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 max-h-64 overflow-y-auto pr-1">
                @forelse ($this->itemsDisponibles as $item)
                    <label wire:key="item-disp-{{ $item->id }}" 
                        class="flex items-start gap-3 border rounded-lg p-3 text-sm cursor-pointer transition {{ isset($itemsSeleccionados[$item->id]) ? 'border-blue-600 bg-blue-50/70' : 'border-gray-200 hover:bg-gray-50' }}">
                        <input type="checkbox" wire:click="toggleItem({{ $item->id }})"
                            @checked(isset($itemsSeleccionados[$item->id]))
                            class="mt-1 rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                        <div class="flex-1">
                            <div class="font-semibold text-gray-800">{{ $item->producto->categoria->nombre }}</div>
                            <div class="text-xs text-gray-600">{{ $item->producto->nombre }} ({{ $item->producto->marca }})</div>
                            <div class="text-xs font-mono font-bold text-blue-700 mt-1">Serie: {{ $item->serie }}</div>
                        </div>
                    </label>
                @empty
                    <div class="col-span-2 text-center py-6 text-sm text-gray-400">
                        <i class="fas fa-search-minus text-2xl mb-1 block"></i>
                        No hay equipos disponibles con ese criterio.
                    </div>
                @endforelse
            </div>

            @if ($this->itemsCarrito->count())
                <div class="mt-4 pt-4 border-t border-gray-200">
                    <p class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Equipos listos para entregar</p>
                    <ul class="space-y-1">
                        @foreach ($this->itemsCarrito as $item)
                            <li wire:key="item-cart-{{ $item->id }}" class="flex justify-between items-center text-sm bg-blue-50/50 border border-blue-100 rounded-lg px-3 py-2">
                                <div>
                                    <span class="font-medium text-gray-800">{{ $item->producto->nombre }}</span> 
                                    <span class="text-xs text-gray-500">(Serie: <strong class="font-mono text-gray-700">{{ $item->serie }}</strong>)</span>
                                </div>
                                <button wire:click="toggleItem({{ $item->id }})" type="button" class="text-red-600 hover:text-red-800 text-xs font-semibold">
                                    <i class="fas fa-trash mr-1"></i>Quitar
                                </button>
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endif
        </div>

        <!-- Sección de Repuestos Varios -->
        <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-200">
            <h3 class="font-semibold text-gray-800 mb-3 flex items-center">
                <i class="fas fa-layer-group text-purple-600 mr-2"></i>Repuestos Varios (Por Cantidad)
            </h3>

            <div class="flex flex-col sm:flex-row items-end gap-2">
                <div class="flex-1 w-full">
                    <x-label value="Seleccionar repuesto" class="mb-1" />
                    <select wire:model="productoRepuestoId" class="w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <option value="">-- Selecciona un repuesto --</option>
                        @foreach ($this->productosRepuesto as $p)
                            <option value="{{ $p->id }}">{{ $p->nombre }} (Stock: {{ $p->stock }})</option>
                        @endforeach
                    </select>
                </div>
                <div class="w-full sm:w-28">
                    <x-label value="Cantidad" class="mb-1" />
                    <x-input type="number" min="1" wire:model="cantidadRepuesto" class="w-full text-center text-sm" />
                </div>
                <div class="w-full sm:w-auto">
                    <x-secondary-button wire:click="agregarRepuesto" type="button" class="w-full justify-center">
                        <i class="fas fa-plus mr-1"></i>Agregar
                    </x-secondary-button>
                </div>
            </div>
            <x-input-error for="cantidadRepuesto" class="mt-1" />

            @if ($this->repuestosCarrito->count())
                <div class="mt-4 pt-4 border-t border-gray-200">
                    <p class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Repuestos listos para entregar</p>
                    <ul class="space-y-1">
                        @foreach ($this->repuestosCarrito as $p)
                            <li wire:key="rep-cart-{{ $p->id }}" class="flex justify-between items-center text-sm bg-purple-50/50 border border-purple-100 rounded-lg px-3 py-2">
                                <div>
                                    <span class="font-medium text-gray-800">{{ $p->nombre }}</span> 
                                    <span class="text-xs text-purple-700 font-bold ml-2">× {{ $p->cantidad_solicitada }}</span>
                                </div>
                                <button wire:click="quitarRepuesto({{ $p->id }})" type="button" class="text-red-600 hover:text-red-800 text-xs font-semibold">
                                    <i class="fas fa-trash mr-1"></i>Quitar
                                </button>
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endif
        </div>

        <!-- Botón Principal Jetstream -->
        <div class="pt-2">
            <x-button wire:click="confirmarEntrega" 
                      wire:loading.attr="disabled"
                      wire:target="confirmarEntrega"
                      class="w-full justify-center py-3 text-sm font-semibold">
                <span wire:loading.remove wire:target="confirmarEntrega">
                    <i class="fas fa-check-circle mr-2"></i>Confirmar entrega de equipos
                </span>
                <span wire:loading wire:target="confirmarEntrega" class="inline-flex items-center">
                    <i class="fas fa-spinner fa-spin mr-2"></i>Procesando entrega...
                </span>
            </x-button>
        </div>

    </div>
</div>