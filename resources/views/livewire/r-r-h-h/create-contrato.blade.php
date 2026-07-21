<div>
    <x-dialog-modal wire:model="abierto">
        <x-slot name="title">
            <div class="flex items-center">
                <i class="fas {{ $contratoId ? 'fa-edit text-blue-500' : 'fa-plus-circle text-orange-500' }} mr-2"></i>
                {{ $contratoId ? 'Editar Contrato' : 'Registrar Nuevo Contrato' }}
            </div>
        </x-slot>

        <x-slot name="content">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                {{-- Selección de Usuario --}}
                <div class="md:col-span-2">
                    <x-label value="Seleccionar Empleado" />
                    <select wire:model="user_id" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500">
                        <option value="">Seleccione un usuario...</option>
                        @foreach($usuarios as $user)
                            <option value="{{ $user->id }}">{{ $user->name }} - DNI: {{ $user->dni }}</option>
                        @endforeach
                    </select>
                    <x-input-error for="user_id" />
                </div>

                {{-- Fechas --}}
                <div>
                    <x-label value="Fecha Ingreso Empresa (Antigüedad)" />
                    <x-input type="date" class="w-full" wire:model="fecha_ingreso" />
                    <x-input-error for="fecha_ingreso" />
                </div>
                <div>
                    <x-label value="Fecha Inicio Contrato Actual" />
                    <x-input type="date" class="w-full" wire:model="fecha_inicio_contrato" />
                    <x-input-error for="fecha_inicio_contrato" />
                </div>
                <div>
                    <x-label value="Fecha Vencimiento" />
                    <x-input type="date" class="w-full" wire:model="fecha_vencimiento" />
                    <x-input-error for="fecha_vencimiento" />
                </div>

                {{-- Información Laboral --}}
                <div>
                    <x-label value="Cargo" />
                    <x-input type="text" class="w-full" wire:model="cargo" placeholder="Ej. Administrador" />
                    <x-input-error for="cargo" />
                </div>
                <div>
                    <x-label value="Tipo de Contrato" />
                    <select wire:model="tipo_contrato" class="w-full border-gray-300 rounded-md shadow-sm">
                        <option value="Plazo Fijo">Plazo Fijo</option>
                        <option value="Indeterminado">Indeterminado</option>
                        <option value="Temporal">Temporal</option>
                        <option value="Por Locación">Por Locación</option>
                    </select>
                </div>

                {{-- Estado del Contrato --}}
                <div>
                    <x-label value="Estado del Contrato" />
                    <select wire:model="status"
                            class="w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 {{ $status == 'Activo' ? 'text-green-600' : 'text-red-600' }}">
                        <option value="Activo">Activo</option>
                        <option value="Vencido">Vencido</option>
                        <option value="Finalizado">Finalizado (Cese/Renuncia)</option>
                    </select>
                    <x-input-error for="status" />
                </div>

                {{-- Sueldos --}}
                <div class="bg-gray-50 p-3 rounded-lg border border-gray-200">
                    <x-label value="Sueldo Bruto (En Contrato)" />
                    <x-input type="number" step="0.01" class="w-full" wire:model="sueldo_bruto" />
                    <x-input-error for="sueldo_bruto" />
                </div>
                <div class="bg-green-50 p-3 rounded-lg border border-green-200">
                    <x-label value="Sueldo Neto (A pagar)" />
                    <x-input type="number" step="0.01" class="w-full" wire:model="sueldo_neto" />
                    <x-input-error for="sueldo_neto" />
                </div>
            </div>
        </x-slot>

        <x-slot name="footer">
            <x-secondary-button wire:click="$set('abierto', false)">
                Cancelar
            </x-secondary-button>
            <x-danger-button class="ml-3" wire:click="guardar" wire:loading.attr="disabled">
                <i class="fas fa-save mr-2"></i> Guardar Contrato
            </x-danger-button>
        </x-slot>
    </x-dialog-modal>
</div>
