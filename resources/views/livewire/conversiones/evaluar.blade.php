<div class="max-w-3xl mx-auto px-4 py-8">
    <div class="bg-gray-200 rounded-2xl shadow-sm border border-gray-300 overflow-hidden">

        <!-- Cabecera -->
        <div class="p-6 border-b border-gray-300 flex items-center justify-between flex-wrap gap-4">
            <div>
                <h2 class="text-xl font-bold text-gray-800">Evaluación técnica previa</h2>
                <p class="text-sm text-gray-600 mt-1">
                    {{ $orden->cliente?->nombre }} {{ $orden->cliente?->apellido }} —
                    <span class="font-semibold text-gray-800">{{ $orden->vehiculo?->placa }}</span> 
                    ({{ $orden->vehiculo?->marca }} {{ $orden->vehiculo?->modelo }})
                </p>
            </div>
            
            <!-- Acciones rápidas de checklist -->
            <div class="flex items-center gap-2">
                <button type="button" wire:click="marcarTodo" class="text-xs font-medium text-emerald-800 bg-emerald-100 hover:bg-emerald-200 border border-emerald-300 px-2.5 py-1.5 rounded-lg transition cursor-pointer">
                    Marcar todo
                </button>
                <button type="button" wire:click="desmarcarTodo" class="text-xs font-medium text-gray-700 bg-white hover:bg-gray-100 border border-gray-300 px-2.5 py-1.5 rounded-lg transition cursor-pointer">
                    Desmarcar todo
                </button>
            </div>
        </div>

        <div class="p-6 space-y-6">
            <x-input-error for="general" />

            @foreach ($gruposChecklist as $grupo => $items)
                <div>
                    <h3 class="text-xs font-bold text-gray-600 uppercase tracking-wider mb-2">{{ $grupo }}</h3>
                    <div class="grid grid-cols-2 sm:grid-cols-3 gap-2">
                        @foreach ($items as $clave => $label)
                            <label class="flex items-center gap-2 text-sm border rounded-lg px-3 py-2 cursor-pointer transition-colors
                                          {{ ($checklist[$clave] ?? false) ? 'border-emerald-500 bg-emerald-50 text-emerald-900 font-medium shadow-xs' : 'border-gray-300 bg-white text-gray-700 hover:bg-gray-50' }}">
                                <input type="checkbox" wire:model.live="checklist.{{ $clave }}"
                                       class="rounded text-emerald-600 focus:ring-emerald-500 border-gray-300">
                                <span class="select-none">{{ $label }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>
            @endforeach

            <div class="pt-2">
                <x-label for="observaciones" value="Observaciones (obligatorio si se rechaza)" class="font-medium text-gray-700" />
                <textarea wire:model="observaciones" rows="3"
                          class="w-full bg-white rounded-lg border-gray-300 text-sm focus:border-emerald-500 focus:ring-emerald-500 mt-1 shadow-xs"
                          placeholder="Ej: falta llanta de repuesto y el vehículo tiene fuga de aceite"></textarea>
                <x-input-error for="observaciones" class="mt-1" />
            </div>
        </div>

        <!-- Botones de Acción -->
        <div class="p-6 border-t border-gray-300 flex justify-between gap-3">
            <button wire:click="guardarEvaluacion(false)" 
                    wire:loading.attr="disabled" 
                    type="button"
                    class="flex-1 bg-red-600 hover:bg-red-700 disabled:opacity-50 text-white font-semibold py-2.5 rounded-xl text-sm transition flex items-center justify-center gap-2 cursor-pointer">
                <span wire:loading.remove wire:target="guardarEvaluacion(false)">No apto</span>
                <span wire:loading wire:target="guardarEvaluacion(false)">Guardando...</span>
            </button>
            
            <button wire:click="guardarEvaluacion(true)" 
                    wire:loading.attr="disabled" 
                    type="button"
                    class="flex-1 bg-emerald-600 hover:bg-emerald-700 disabled:opacity-50 text-white font-semibold py-2.5 rounded-xl text-sm transition flex items-center justify-center gap-2 cursor-pointer">
                <span wire:loading.remove wire:target="guardarEvaluacion(true)">Apto para conversión</span>
                <span wire:loading wire:target="guardarEvaluacion(true)">Guardando...</span>
            </button>
        </div>
    </div>
</div>