<div>
    <div class="max-w-4xl mx-auto py-6 sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h1 class="text-2xl font-bold text-gray-800 flex items-center gap-2">
                        <i class="fas fa-truck text-indigo-600"></i>
                        Recepción de Kits
                    </h1>
                    <p class="text-sm text-gray-500 mt-1">Registrar ingreso de kits al almacén</p>
                </div>
                <a href="{{ route('almacen.recepciones.listado') }}"
                   class="text-sm text-gray-500 hover:text-gray-700">
                    ← Volver al historial
                </a>
            </div>

            <form wire:submit="guardar">
                {{-- Proveedor + Sede --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Proveedor</label>
                        <select wire:model="proveedorId" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="">Seleccionar proveedor...</option>
                            @foreach($this->proveedores as $proveedor)
                                <option value="{{ $proveedor }}">{{ $proveedor }}</option>
                            @endforeach
                        </select>
                        @error('proveedorId') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Sede destino</label>
                        <select wire:model="sedeId" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            @foreach(\App\Models\Sede::activas()->get() as $sede)
                                <option value="{{ $sede->id }}">{{ $sede->nombre }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                {{-- Kits por cantidad --}}
                <div class="mb-6">
                    <label class="block text-sm font-bold text-gray-700 mb-3">Cantidad de kits a recibir</label>
                    <div class="space-y-3">
                        @foreach ($this->kitsDisponibles as $kit)
                            @php
                                $gen = $kit->atributos['generacion'] ?? '—';
                            @endphp
                            <div class="flex items-center justify-between p-4 bg-gray-50 rounded-xl border border-gray-200 hover:border-indigo-300 transition-colors">
                                <div class="flex items-center gap-4">
                                    <div class="w-12 h-12 rounded-xl bg-indigo-100 flex items-center justify-center">
                                        <i class="fas fa-box text-indigo-600 text-lg"></i>
                                    </div>
                                    <div>
                                        <p class="text-sm font-bold text-gray-800">{{ $kit->nombre }}</p>
                                        <div class="flex items-center gap-2 mt-0.5">
                                            <span class="px-2 py-0.5 bg-indigo-100 text-indigo-700 text-[10px] font-bold rounded-full">
                                                {{ $gen }}
                                            </span>
                                            <span class="text-xs text-gray-400">
                                                En stock: {{ $kit->stockTotal() }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                <div class="flex items-center gap-3">
                                    <button type="button" wire:click="$set('cantidades.{{ $kit->id }}', max(0, {{ $cantidades[$kit->id] ?? 0 }} - 1))"
                                        class="w-9 h-9 rounded-lg bg-gray-200 hover:bg-gray-300 flex items-center justify-center text-gray-600 font-bold transition-colors">
                                        −
                                    </button>
                                    <input type="number" min="0" max="99"
                                        wire:model.live="cantidades.{{ $kit->id }}"
                                        class="w-20 text-center text-lg font-bold border border-gray-300 rounded-lg py-1.5 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                                    <button type="button" wire:click="$set('cantidades.{{ $kit->id }}', {{ $cantidades[$kit->id] ?? 0 }} + 1)"
                                        class="w-9 h-9 rounded-lg bg-indigo-100 hover:bg-indigo-200 flex items-center justify-center text-indigo-700 font-bold transition-colors">
                                        +
                                    </button>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                {{-- Notas --}}
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Notas (opcional)</label>
                    <textarea wire:model="notas" rows="2" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" placeholder="Ej: Kit incompleto, falta válvula..."></textarea>
                </div>

                {{-- Resumen --}}
                @if ($this->total > 0)
                    <div class="bg-amber-50 border border-amber-200 rounded-lg p-4 mb-6">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 bg-amber-100 rounded-full flex items-center justify-center">
                                <i class="fas fa-box text-amber-600 text-sm"></i>
                            </div>
                            <p class="text-sm font-semibold text-amber-800">
                                Se recibirán <strong>{{ $this->total }}</strong> kit(s) como unidades independientes en stock.
                            </p>
                        </div>
                    </div>
                @endif

                <div class="flex justify-end gap-3 mt-6 pt-4 border-t">
                    <a href="{{ route('almacen.recepciones.listado') }}"
                       class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition">
                        Cancelar
                    </a>
                    <button type="submit"
                            wire:loading.attr="disabled"
                            :disabled="$this->total === 0"
                            class="px-6 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition disabled:opacity-50 disabled:cursor-not-allowed">
                        <span wire:loading.remove wire:target="guardar">
                            <i class="fas fa-truck mr-1"></i> Recibir {{ $this->total > 0 ? $this->total . ' kit(s)' : '' }}
                        </span>
                        <span wire:loading wire:target="guardar">Guardando...</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
