<div>
    <div class="max-w-4xl mx-auto py-6 sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h1 class="text-2xl font-bold text-gray-800">Completar Kit</h1>
                    <p class="text-sm text-gray-500 mt-1">
                        Kit: {{ $kit->producto->nombre }} — Serie: {{ $kit->serie }}
                    </p>
                </div>
                <a href="{{ route('almacen.stock') }}" 
                   class="text-sm text-gray-500 hover:text-gray-700">
                    ← Volver al stock
                </a>
            </div>

            @error('general')
                <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg mb-4">
                    {{ $message }}
                </div>
            @enderror

            <div class="mb-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-3">Componentes Faltantes</h3>

                @if(empty($faltantes))
                    <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg">
                        ✅ El kit está completo. No faltan componentes.
                    </div>
                @else
                    <div class="space-y-3">
                        @foreach($faltantes as $index => $faltante)
                            <div class="flex items-center gap-4 p-3 rounded-lg {{ $faltante['agregado'] ? 'bg-green-50 border border-green-200' : 'bg-gray-50 border border-gray-200' }}">
                                @if($faltante['agregado'])
                                    <span class="text-green-600">✅</span>
                                @else
                                    <span class="text-gray-400">⏳</span>
                                @endif

                                <div class="flex-1">
                                    <span class="font-medium text-gray-800">{{ $faltante['nombre'] }}</span>
                                    @if($faltante['es_serializado'])
                                        <span class="ml-2 text-xs bg-blue-100 text-blue-800 px-2 py-0.5 rounded">Serializado</span>
                                    @else
                                        <span class="ml-2 text-xs bg-purple-100 text-purple-800 px-2 py-0.5 rounded">×{{ $faltante['cantidad'] }}</span>
                                    @endif
                                </div>

                                @if($faltante['agregado'])
                                    @if($faltante['es_serializado'])
                                        <span class="text-sm text-gray-600 font-mono">{{ $faltante['serie'] }}</span>
                                    @endif
                                    <button wire:click="quitarFaltante({{ $index }})" 
                                            type="button"
                                            class="text-red-600 text-sm hover:text-red-800">
                                        Quitar
                                    </button>
                                @endif
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            @if(!empty($faltantes) && collect($faltantes)->where('agregado', false)->isNotEmpty())
                <div class="border-t pt-4 mb-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-3">Buscar Pieza Disponible</h3>
                    <p class="text-sm text-gray-500 mb-3">Busca por serie o nombre para encontrar piezas en stock.</p>

                    <input type="text" 
                           wire:model.live="buscarPieza" 
                           placeholder="Buscar pieza..."
                           class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">

                    @if(count($piezasEncontradas) > 0)
                        <div class="mt-3 space-y-2">
                            @foreach($piezasEncontradas as $pieza)
                                <div class="flex items-center justify-between p-3 bg-white border rounded-lg hover:bg-gray-50">
                                    <div>
                                        <span class="font-medium text-gray-800">{{ $pieza['producto']['nombre'] }}</span>
                                        <span class="text-sm text-gray-500 ml-2 font-mono">{{ $pieza['serie'] }}</span>
                                    </div>
                                    <button wire:click="agregarPiezaDisponible({{ $pieza['producto_id'] }}, '{{ $pieza['serie'] }}')"
                                            type="button"
                                            class="text-indigo-600 text-sm hover:text-indigo-800 font-semibold">
                                        + Agregar
                                    </button>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            @endif

            <div class="flex justify-end gap-3 mt-6 pt-4 border-t">
                <a href="{{ route('almacen.stock') }}" 
                   class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition">
                    Cancelar
                </a>
                <button wire:click="guardar"
                        wire:loading.attr="disabled"
                        class="px-6 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition disabled:opacity-50">
                    <span wire:loading.remove wire:target="guardar">💾 Guardar Cambios</span>
                    <span wire:loading wire:target="guardar">Guardando...</span>
                </button>
            </div>
        </div>
    </div>
</div>