<div wire:loading.class="opacity-50 pointer-events-none" class="container mx-auto py-12">
    <div class="bg-gray-200 p-8 rounded-xl w-full max-w-4xl mx-auto space-y-6">        
        {{-- Header --}}
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

        {{-- ═══════════════════════════════════════════ --}}
        {{-- SELECCIONAR KIT COMPLETO --}}
        {{-- ═══════════════════════════════════════════ --}}
        <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-200">
            <h3 class="font-semibold text-gray-800 mb-3 flex items-center">
                <i class="fas fa-box text-green-600 mr-2"></i>Seleccionar Kit Completo
            </h3>
            <p class="text-sm text-gray-500 mb-4">Selecciona un kit sellado para asignar a esta conversión. El kit se abrirá y sus piezas quedarán reservadas para esta orden.</p>

            @if($this->kitsDisponibles->isEmpty())
                <div class="text-center py-6 text-sm text-gray-500 bg-amber-50 rounded-lg border border-amber-200">
                    <i class="fas fa-box-open text-2xl mb-2 block text-amber-400"></i>
                    <span class="font-medium">Sin kit en esta sede</span>
                    <p class="text-xs text-gray-400 mt-1">No hay kits sellados disponibles en esta sede. Recibir kits en <strong>Recepción de Kits</strong> o trasladar desde otra sede.</p>
                </div>
            @else
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    @foreach($this->kitsDisponibles as $kit)
                        <label class="flex items-center gap-3 border rounded-lg p-4 {{ $kitItemId == $kit->id ? 'border-green-600 bg-green-50' : 'border-gray-200 hover:bg-gray-50 cursor-pointer' }}">
                            <input type="radio" 
                                   wire:model="kitItemId" 
                                   value="{{ $kit->id }}"
                                   class="mt-1 rounded border-gray-300 text-green-600 focus:ring-green-500">
                            <div class="flex-1">
                                <div class="font-semibold text-gray-800">{{ $kit->producto->nombre }}</div>
                                @if(isset($kit->atributos['proveedor']))
                                    <div class="text-xs text-gray-400 mt-1">Proveedor: {{ $kit->atributos['proveedor'] }}</div>
                                @endif
                                <div class="text-xs text-green-600 mt-1">
                                    <i class="fas fa-check-circle mr-1"></i>Al confirmar se descuenta del almacén
                                </div>
                            </div>
                        </label>
                    @endforeach
                </div>
            @endif
        </div>

        {{-- ═══════════════════════════════════════════ --}}
        {{-- REPUESTOS POR CANTIDAD (OPCIONAL) --}}
        {{-- ═══════════════════════════════════════════ --}}
        <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-200">
            <h3 class="font-semibold text-gray-800 mb-1 flex items-center">
                <i class="fas fa-layer-group text-purple-600 mr-2"></i>Repuestos Varios
                <span class="ml-2 text-xs font-normal text-gray-400 bg-gray-100 px-2 py-0.5 rounded">(Opcional)</span>
            </h3>
            <p class="text-xs text-gray-400 mb-4">Solo si necesitás piezas adicionales fuera del kit.</p>

            <div class="flex flex-col sm:flex-row items-end gap-2">
                <div class="flex-1 w-full">
                    <x-label value="Seleccionar repuesto" class="mb-1" />
                    <select wire:model="productoRepuestoId" class="w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <option value="">-- Selecciona un repuesto --</option>
                        @foreach ($this->productosRepuesto as $p)
                            <option value="{{ $p->id }}">{{ $p->nombre }} (Stock: {{ $p->stock_disponible }})</option>
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
                                <button wire:click="quitarRepuesto({{ $p->id }})" type="button" class="text-red-600 hover:text-red-800 transition-colors text-xs font-semibold">
                                    <i class="fas fa-trash mr-1"></i>Quitar
                                </button>
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endif
        </div>

        {{-- Botón Principal --}}
        <div class="pt-2">
            <x-button wire:click="confirmarEntrega" 
                      wire:loading.attr="disabled"
                      wire:target="confirmarEntrega"
                      class="w-full justify-center py-3 text-sm font-semibold">
                <span wire:loading.remove wire:target="confirmarEntrega">
                    <i class="fas fa-check-circle mr-2"></i>Confirmar asignación
                </span>
                <span wire:loading wire:target="confirmarEntrega" class="inline-flex items-center">
                    <i class="fas fa-spinner fa-spin mr-2"></i>Procesando...
                </span>
            </x-button>
            <p class="text-xs text-gray-400 text-center mt-2">El kit es suficiente. Los repuestos son solo si necesitás piezas extra.</p>
        </div>

    </div>
</div>
