<div class="max-w-3xl mx-auto px-4 py-8">
    <div class="bg-white rounded-2xl shadow-sm border border-gray-200/80 overflow-hidden">

        <div class="p-6 border-b border-gray-200/60">
            <h2 class="text-xl font-bold text-gray-800">Evaluación técnica previa</h2>
            <p class="text-sm text-gray-500 mt-1">
                {{ $orden->cliente->nombre }} {{ $orden->cliente->apellido }} —
                {{ $orden->vehiculo->placa }} ({{ $orden->vehiculo->marca }} {{ $orden->vehiculo->modelo }})
            </p>
        </div>

        <div class="p-6 space-y-6">
            <x-input-error for="general" />

            @foreach ($gruposChecklist as $grupo => $items)
                <div>
                    <h3 class="text-xs font-bold text-gray-500 uppercase mb-2">{{ $grupo }}</h3>
                    <div class="grid grid-cols-2 sm:grid-cols-3 gap-2">
                        @foreach ($items as $clave => $label)
                            <label class="flex items-center gap-2 text-sm border rounded-lg px-3 py-2 cursor-pointer
                                          {{ ($checklist[$clave] ?? false) ? 'border-emerald-400 bg-emerald-50' : 'border-gray-200' }}">
                                <input type="checkbox" wire:model="checklist.{{ $clave }}"
                                       class="rounded text-emerald-600 focus:ring-emerald-500">
                                {{ $label }}
                            </label>
                        @endforeach
                    </div>
                </div>
            @endforeach

            <div>
                <x-label for="observaciones" value="Observaciones (obligatorio si se rechaza)" />
                <textarea wire:model="observaciones" rows="3"
                          class="w-full rounded-lg border-gray-300 text-sm"
                          placeholder="Ej: falta llanta de repuesto y el vehículo tiene fuga de aceite"></textarea>
                <x-input-error for="observaciones" class="mt-1" />
            </div>
        </div>

        <div class="p-6 border-t border-gray-200/60 flex justify-between gap-3">
            <button wire:click="guardarEvaluacion(false)" wire:loading.attr="disabled" type="button"
                    class="flex-1 bg-red-600 hover:bg-red-700 text-white font-semibold py-2.5 rounded-xl text-sm">
                No apto
            </button>
            <button wire:click="guardarEvaluacion(true)" wire:loading.attr="disabled" type="button"
                    class="flex-1 bg-emerald-600 hover:bg-emerald-700 text-white font-semibold py-2.5 rounded-xl text-sm">
                Apto para conversión
            </button>
        </div>
    </div>
</div>