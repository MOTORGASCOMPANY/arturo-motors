<div wire:loading.class="opacity-50 pointer-events-none" class="max-w-6xl mx-auto px-4 py-8">
    <div class="bg-white rounded-2xl shadow-md border border-gray-200 overflow-hidden">

        {{-- Header --}}
        <div class="p-6 border-b border-gray-100 bg-blue-50">
            <h2 class="text-xl font-bold text-gray-900">
                <i class="fas fa-clipboard-list mr-2 text-blue-600"></i>
                Registrar Items del Kit
            </h2>
            <p class="text-sm text-gray-600 mt-1">
                Orden #{{ $orden->id }} — {{ $orden->cliente->nombre }} {{ $orden->cliente->apellido }} 
                — <span class="font-bold">{{ $orden->vehiculo->placa }}</span>
            </p>
        </div>

        <div class="p-6 space-y-6">

            {{-- Instrucciones --}}
            <div class="bg-blue-50 border border-blue-200 rounded-xl p-4">
                <div class="flex items-start gap-3">
                    <div class="w-10 h-10 rounded-full bg-blue-100 flex items-center justify-center flex-shrink-0">
                        <i class="fas fa-info-circle text-blue-600"></i>
                    </div>
                    <div>
                        <p class="text-sm font-semibold text-blue-800">¿Qué debes hacer?</p>
                        <p class="text-xs text-blue-600 mt-1">
                            Registra los números de serie de cada pieza que vas a instalar. 
                            Estos números se usarán para el registro de la instalación.
                        </p>
                    </div>
                </div>
            </div>

            <x-input-error for="general" />

            @if($sinKit)
                {{-- Sin Kit Asignado --}}
                <div class="text-center py-8 bg-amber-50 rounded-xl border border-amber-200">
                    <div class="w-16 h-16 rounded-full bg-amber-100 flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-box-open text-amber-500 text-2xl"></i>
                    </div>
                    <p class="text-lg font-bold text-amber-800">No hay kit asignado</p>
                    <p class="text-sm text-amber-600 mt-2 max-w-md mx-auto">
                        Esta orden aún no tiene un kit asignado. Primero el almacén debe asignar el kit de instalación.
                    </p>
                    <button wire:click="irAAsignarEquipos" 
                            type="button"
                            class="mt-6 px-6 py-3 bg-amber-600 text-white rounded-xl hover:bg-amber-700 transition font-semibold text-sm shadow-md">
                        <i class="fas fa-boxes-packing mr-1"></i> Asignar Kit Ahora
                    </button>
                </div>

            @elseif(!$guardado)
                {{-- Lista de Items - 3 tarjetas en fila --}}
                <div>
                    <h3 class="text-xs font-bold text-gray-600 uppercase tracking-wider mb-3">
                        Piezas a registrar ({{ count($items) }})
                    </h3>

                    @if(count($items) > 0)
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            @foreach($items as $index => $item)
                                <div class="border border-gray-200 rounded-xl p-4 space-y-3 bg-gray-50">
                                    {{-- Info Producto --}}
                                    <div class="text-center">
                                        <span class="px-2 py-0.5 bg-blue-100 text-blue-700 rounded-full text-xs font-semibold">
                                            Pendiente
                                        </span>
                                        <span class="text-xs font-bold text-gray-500 uppercase block mt-2">{{ $item['categoria'] }}</span>
                                        <p class="text-sm font-bold text-gray-800 mt-1">{{ $item['producto_nombre'] }}</p>
                                        <p class="text-xs text-gray-500 font-mono mt-1">
                                            Actual: {{ $item['serie_actual'] ?: 'Sin serie' }}
                                        </p>
                                    </div>

                                    {{-- Campo Serie Nueva --}}
                                    <div>
                                        <label class="block text-xs font-bold text-gray-600 mb-1">
                                            Serie instalada *
                                        </label>
                                        <input type="text" 
                                               wire:model="items.{{ $index }}.serie_nueva"
                                               placeholder="Nº de serie"
                                               class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none text-center">
                                    </div>

                                    {{-- Observaciones --}}
                                    <div>
                                        <label class="block text-xs font-bold text-gray-600 mb-1">
                                            Obs. (opcional)
                                        </label>
                                        <input type="text" 
                                               wire:model="items.{{ $index }}.observaciones"
                                               placeholder="Detalle..."
                                               class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none text-center">
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        {{-- Botón Guardar --}}
                        <div class="flex gap-3 pt-4 border-t border-gray-100 mt-4">
                            <button wire:click="guardar" 
                                    wire:loading.attr="disabled"
                                    wire:confirm="¿Estás seguro de guardar las series?"
                                    type="button"
                                    class="flex-1 px-4 py-3 bg-blue-600 text-white rounded-xl hover:bg-blue-700 transition font-semibold text-sm shadow-md">
                                <i class="fas fa-save mr-1"></i> Guardar series
                            </button>
                        </div>
                    @else
                        <div class="text-center py-8 bg-gray-50 rounded-xl">
                            <i class="fas fa-box-open text-gray-400 text-3xl mb-2"></i>
                            <p class="text-gray-500">No hay items asignados a esta orden.</p>
                        </div>
                    @endif
                </div>

            @else
                {{-- Éxito --}}
                <div class="text-center py-8">
                    <div class="w-16 h-16 rounded-full bg-green-100 flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-check-circle text-green-500 text-3xl"></i>
                    </div>
                    <p class="text-lg font-bold text-gray-800">¡Items registrados correctamente!</p>
                    <p class="text-sm text-gray-500 mt-1">
                        Ya puedes iniciar la conversión.
                    </p>

                    <button wire:click="irAIniciar" 
                            type="button"
                            class="mt-6 px-6 py-3 bg-purple-600 text-white rounded-xl hover:bg-purple-700 transition font-semibold text-sm shadow-md">
                        <i class="fas fa-play mr-1"></i> Ir a Iniciar Conversión
                    </button>
                </div>
            @endif
        </div>
    </div>
</div>
