<div>
    <div class="max-w-4xl mx-auto py-6 sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
            {{-- Header --}}
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h1 class="text-2xl font-bold text-gray-800">Registrar Series Instaladas</h1>
                    <p class="text-sm text-gray-500 mt-1">
                        Orden #{{ $orden->id }} — {{ $orden->vehiculo->placa ?? 'N/A' }}
                    </p>
                </div>
                <a href="{{ route('conversiones.realizar', $orden->id) }}" 
                   class="text-sm text-gray-500 hover:text-gray-700">
                    ← Volver a conversión
                </a>
            </div>

            {{-- Error general --}}
            @error('general')
                <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg mb-4">
                    {{ $message }}
                </div>
            @enderror

            {{-- Instrucciones --}}
            <div class="bg-blue-50 border border-blue-200 text-blue-700 px-4 py-3 rounded-lg mb-6">
                <p class="text-sm">
                    Registra el número de serie de cada componente que se instaló en el vehículo. 
                    Esto es para trazabilidad: saber qué pieza está en qué vehículo.
                </p>
            </div>

            {{-- Items a registrar --}}
            @if(empty($items))
                <div class="bg-gray-50 border border-gray-200 text-gray-700 px-4 py-3 rounded-lg">
                    No hay items serializados asignados a esta orden.
                </div>
            @else
                <div class="space-y-4">
                    @foreach($items as $index => $item)
                        <div class="border rounded-lg p-4">
                            <div class="flex items-center justify-between mb-2">
                                <h3 class="font-semibold text-gray-800">{{ $item['producto_nombre'] }}</h3>
                                <span class="text-xs text-gray-500">Serie actual: {{ $item['serie_actual'] }}</span>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Serie instalada en el vehículo</label>
                                <input type="text" 
                                       wire:model="items.{{ $index }}.serie_instalada"
                                       placeholder="Ingrese el número de serie instalado"
                                       class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif

            {{-- Botones --}}
            <div class="flex justify-end gap-3 mt-6 pt-4 border-t">
                <a href="{{ route('conversiones.realizar', $orden->id) }}" 
                   class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition">
                    Cancelar
                </a>
                <button wire:click="guardar"
                        wire:loading.attr="disabled"
                        class="px-6 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition disabled:opacity-50">
                    <span wire:loading.remove wire:target="guardar">💾 Guardar Series</span>
                    <span wire:loading wire:target="guardar">Guardando...</span>
                </button>
            </div>
        </div>
    </div>
</div>
