<div>
    <x-dialog-modal wire:model="abierto">
        <x-slot name="title">
            <div class="flex items-center text-gray-700">
                <i class="fas fa-calendar-check mr-2 text-orange-500"></i>
                Asignar Periodo de Vacaciones
            </div>
        </x-slot>

        <x-slot name="content">
            @if($contrato)
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">

                {{-- Info lateral de saldo --}}
                <div class="md:col-span-1 bg-gray-50 p-4 rounded-lg border border-gray-200 flex flex-col justify-center text-center">
                    <span class="text-[10px] text-gray-400 font-bold uppercase">Disponible</span>
                    <span class="text-3xl font-black text-indigo-600">{{ $vacacionMaestra->dias_restantes ?? 0 }}</span>
                    <span class="text-[10px] text-gray-400 uppercase mt-2 font-semibold">Días hábiles</span>
                </div>

                {{-- Formulario --}}
                <div class="md:col-span-3 space-y-4">
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <x-label value="Fecha de Inicio" />
                            <x-input type="date" class="w-full" wire:model="f_inicio" />
                            <x-input-error for="f_inicio" />
                        </div>
                        <div>
                            <x-label value="Días a Tomar" />
                            <x-input type="number" class="w-full" wire:model="d_tomados" />
                            <x-input-error for="d_tomados" />
                        </div>
                    </div>

                    <div>
                        <x-label value="Tipo / Concepto" />
                        <select wire:model="tipo" class="w-full border-gray-300 rounded-md shadow-sm">
                            <option value="Vacaciones">Vacaciones</option>
                            <option value="Descanso">Descanso Médico</option>
                            <option value="Permiso">Permiso Especial</option>
                        </select>
                    </div>

                    <div>
                        <x-label value="Motivo Corto" />
                        <x-input type="text" class="w-full" wire:model="razon" placeholder="Ej. Vacaciones 2024" />
                        <x-input-error for="razon" />
                    </div>

                    <div>
                        <x-label value="Observaciones (Opcional)" />
                        <textarea wire:model="observacion" class="w-full border-gray-300 rounded-md shadow-sm h-20 text-sm"></textarea>
                    </div>

                    <div class="flex items-center">
                        <x-checkbox wire:model="especial" id="modal_especial" />
                        <label for="modal_especial" class="ml-2 text-xs font-bold text-gray-500 uppercase">¿Asignación especial / extraordinaria?</label>
                    </div>
                </div>
            </div>
            @endif
        </x-slot>

        <x-slot name="footer">
            <x-secondary-button wire:click="$set('abierto', false)">
                Cancelar
            </x-secondary-button>
            <x-danger-button class="ml-3" wire:click="guardar">
                Confirmar y Descontar
            </x-danger-button>
        </x-slot>
    </x-dialog-modal>
</div>
