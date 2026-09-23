<div wire:loading.class="opacity-50 pointer-events-none" class="container mx-auto py-12">
    <div class="bg-gray-200 p-8 rounded-xl w-full max-w-4xl mx-auto space-y-6">
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

        <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-200">
            <h3 class="font-semibold text-gray-800 mb-3 flex items-center">
                <i class="fas fa-box text-green-600 mr-2"></i>Seleccionar Kit Completo
            </h3>
            <p class="text-sm text-gray-500 mb-4">Selecciona un kit sellado para asignar a esta conversión.</p>

            @if($this->kitsDisponibles->isEmpty() && empty($filtroGeneracion))
                <div class="text-center py-6 text-sm text-gray-500 bg-amber-50 rounded-lg border border-amber-200">
                    <i class="fas fa-box-open text-2xl mb-2 block text-amber-400"></i>
                    <span class="font-medium">Sin kit en esta sede</span>
                    <p class="text-xs text-gray-400 mt-1">No hay kits sellados disponibles en esta sede. Recibir kits en <strong>Recepción de Kits</strong> o trasladar desde otra sede.</p>
                </div>
            @else
                {{-- Paso 1: Seleccionar generación --}}
                @if(!empty($this->generacionesDisponibles) && count($this->generacionesDisponibles) > 1)
                    <div class="mb-4">
                        <label class="block text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">Paso 1: Tipo de kit</label>
                        <div class="flex gap-2">
                            <button wire:click="$set('filtroGeneracion', '')" type="button"
                                    class="px-4 py-2 rounded-lg text-sm font-semibold transition
                                    {{ empty($filtroGeneracion) ? 'bg-green-600 text-white shadow-md' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}">
                                Todos
                            </button>
                            @foreach($this->generacionesDisponibles as $gen)
                                <button wire:key="gen-{{ $gen }}" wire:click="$set('filtroGeneracion', '{{ $gen }}')" type="button"
                                        class="px-4 py-2 rounded-lg text-sm font-semibold transition
                                        {{ $filtroGeneracion === $gen ? 'bg-green-600 text-white shadow-md' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}">
                                    {{ $gen }}
                                </button>
                            @endforeach
                        </div>
                    </div>
                @endif

                {{-- Paso 2: Seleccionar kit --}}
                <div>
                    <label class="block text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">
                        {{ !empty($this->generacionesDisponibles) && count($this->generacionesDisponibles) > 1 ? 'Paso 2: ' : '' }}Kit disponible
                        <span class="text-gray-400 font-normal normal-case">({{ $this->kitsDisponibles->count() }} en stock)</span>
                    </label>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        @foreach($this->kitsDisponibles as $kit)
                            <label wire:key="kit-{{ $kit->id }}"
                                   class="flex items-center gap-3 border rounded-lg p-4 cursor-pointer {{ $kitItemId == $kit->id ? 'border-green-600 bg-green-50' : 'border-gray-200 hover:bg-gray-50' }}">
                                <input type="radio"
                                       wire:model.live="kitItemId"
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
                </div>
            @endif
        </div>

        {{-- Inputs de serie por componente (solo si kit seleccionado y hay componentes sin serie) --}}
        @if($kitItemId && $this->componentesKit->isNotEmpty())
            <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-200">
                <h3 class="font-semibold text-gray-800 mb-1 flex items-center">
                    <i class="fas fa-barcode text-blue-600 mr-2"></i>Números de Serie
                </h3>
                <p class="text-xs text-gray-400 mb-4">Ingrese el serie de las piezas que aún no tienen serie registrada.</p>

                <div class="space-y-3">
                    @foreach($this->componentesKit as $comp)
                        <div class="flex items-center gap-3" wire:key="comp-{{ $comp->producto_id }}">
                            <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center flex-shrink-0">
                                <i class="fas fa-microchip text-blue-600 text-xs"></i>
                            </div>
                            <div class="flex-1">
                                <label class="block text-xs font-semibold text-gray-600 mb-1">{{ $comp->nombre }}</label>
                                <input type="text"
                                       wire:model="seriesKit.{{ $comp->producto_id }}"
                                       placeholder="Ej: 31312313"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm font-mono focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @elseif($kitItemId && $this->componentesKit->isEmpty())
            @php
                $kit = \App\Models\ItemSerializado::with('producto.componentes.componente')->find($kitItemId);
                $itemsKit = \App\Models\ItemSerializado::where('kit_padre_id', $kitItemId)->get();
                $tieneSerializables = false;
                if ($kit) {
                    foreach ($kit->producto->componentes as $kc) {
                        if ($kc->componente->categoria->es_serializado ?? false) {
                            $tieneSerializables = true;
                            break;
                        }
                    }
                }
            @endphp
            @if($tieneSerializables)
                <div class="bg-green-50 rounded-xl shadow-sm p-6 border border-green-200">
                    <h3 class="font-semibold text-green-800 mb-2 flex items-center">
                        <i class="fas fa-check-circle text-green-600 mr-2"></i>Series ya registradas
                    </h3>
                    <p class="text-sm text-green-700 mb-3">Este kit ya tiene sus piezas serializadas registradas en el sistema.</p>
                    <div class="space-y-1">
                        @foreach($kit->producto->componentes as $kc)
                            @php
                                $comp = $kc->componente;
                                $esSerial = $comp->categoria->es_serializado ?? false;
                            @endphp
                            @if($esSerial)
                                @php
                                    $itemComp = $itemsKit->where('producto_id', $comp->id)->first();
                                @endphp
                                <div class="flex items-center gap-2 text-sm">
                                    <i class="fas fa-microchip text-green-600 text-xs"></i>
                                    <span class="font-medium text-gray-700">{{ $comp->nombre }}</span>
                                    @if($itemComp && $itemComp->serie)
                                        <code class="bg-green-100 text-green-800 px-2 py-0.5 rounded text-xs font-mono">{{ $itemComp->serie }}</code>
                                    @else
                                        <span class="text-xs text-gray-400 italic">sin serie</span>
                                    @endif
                                </div>
                            @endif
                        @endforeach
                    </div>
                </div>
            @endif
        @endif

<div class="bg-white rounded-xl shadow-sm p-6 border border-gray-200">
            <h3 class="font-semibold text-gray-800 mb-1 flex items-center">
                <i class="fas fa-layer-group text-purple-600 mr-2"></i>Repuestos Varios (Por Cantidad)
                <span class="ml-2 text-xs font-normal text-gray-400 bg-gray-100 px-2 py-0.5 rounded">(Opcional)</span>
            </h3>
            <p class="text-xs text-gray-400 mb-4">Stock suelto del almacén: solo items por cantidad (sin serie).</p>

            <div class="flex flex-col sm:flex-row items-end gap-2 mb-4">
                <div class="flex-1 w-full">
                    <x-label value="Seleccionar repuesto" class="mb-1" />
                    <select wire:model="productoRepuestoId" class="w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <option value="">-- Seleccionar un repuesto --</option>
                        @foreach ($this->productosRepuesto as $p)
                            <option value="{{ $p->producto_id }}">{{ $p->producto->nombre }} (Stock: {{ $p->cantidad_disponible }})</option>
                        @endforeach
                    </select>
                </div>
                <div class="w-full sm:w-28">
                    <x-label value="Cantidad" class="mb-1" />
                    <x-input type="number" min="1" wire:model="cantidadRepuesto" class="w-full text-center text-sm" />
                    <x-input-error for="cantidadRepuesto" class="mt-1" />
                </div>
                <div class="w-full sm:w-auto">
                    <x-secondary-button wire:click="agregarRepuesto" type="button" class="w-full justify-center">
                        <i class="fas fa-plus mr-1"></i>Agregar
                    </x-secondary-button>
                </div>
            </div>

            @if ($this->repuestosCarrito->count())
                <div class="mt-4 pt-4 border-t border-gray-200">
                    <p class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Repuestos listos para entregar</p>
                    <ul class="space-y-1">
                        @foreach ($this->repuestosCarrito as $p)
                            <li wire:key="rep-cart-{{ $p->producto_id }}" class="flex justify-between items-center text-sm bg-purple-50/50 border border-purple-100 rounded-lg px-3 py-2">
                                <div>
                                    <span class="font-medium text-gray-800">{{ $p->producto->nombre }}</span>
                                    <span class="text-xs text-purple-700 font-bold ml-2">× {{ $p->cantidad_solicitada }}</span>
                                </div>
                                <button wire:click="quitarRepuesto({{ $p->producto_id }})" type="button" class="text-red-600 hover:text-red-800 transition-colors text-xs font-semibold">
                                    <i class="fas fa-trash mr-1"></i>Quitar
                                </button>
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endif
        </div>

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

<script>
    document.addEventListener('livewire:initialized', () => {
        Livewire.on('entrega-confirmada', (event) => {
            const data = Array.isArray(event) ? event[0] : event;
            const redirectUrl = data && data.redirectUrl ? data.redirectUrl : null;
            if (redirectUrl) {
                window.location.href = redirectUrl;
            }
        });

        Livewire.on('entrega-error', (event) => {
            const data = Array.isArray(event) ? event[0] : event;
            const mensaje = data && data.mensaje ? data.mensaje : 'Ocurrió un error';

            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: mensaje,
                confirmButtonText: 'Cerrar'
            });
        });
    });
</script>