<div class="space-y-6">
    <!-- BÚSQUEDA Y SELECCIÓN DE CLIENTE -->
    <div class="relative">
        <x-label for="buscarCliente" value="Buscar Cliente" class="mb-1 text-gray-700 font-medium" /> 
        <div class="relative">
            <x-input id="buscarCliente" type="text" 
                    class="w-full pl-10 border-gray-300 focus:border-blue-500 focus:ring-blue-500 rounded-xl shadow-sm text-sm" 
                    wire:model.live.debounce.300ms="buscarCliente" 
                    placeholder="Escribe el nombre, apellido o documento..." />
            
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                <i class="fas fa-search"></i>
            </div>
        </div>

        <!-- Dropdown con coincidencias -->
        @if (count($clientesEncontrados))
            <ul class="absolute z-20 w-full mt-1 bg-white border border-gray-200 rounded-xl shadow-lg divide-y divide-gray-100 max-h-56 overflow-y-auto">
                @foreach ($clientesEncontrados as $c)
                    <li wire:click="seleccionarCliente({{ $c['id'] }})"
                        class="px-4 py-3 hover:bg-blue-50/50 cursor-pointer transition flex justify-between items-center text-sm">
                        <div>
                            <p class="font-medium text-gray-800">{{ $c['nombre'] }} {{ $c['apellido'] }}</p>
                            <p class="text-xs text-gray-500">Doc: {{ $c['documento'] }}</p>
                        </div>
                        <span class="text-xs bg-blue-50 text-blue-700 font-semibold px-2.5 py-1 rounded-md border border-blue-100">Seleccionar</span>
                    </li>
                @endforeach
            </ul>
        @endif

        @if (strlen(trim($buscarCliente)) >= 3 && count($clientesEncontrados) === 0 && !$clienteId)
            <div class="mt-2 p-3 bg-amber-50 border border-amber-200 rounded-xl text-sm flex items-center justify-between">
                <p class="text-amber-800">No se encontró ningún cliente registrado.</p>
                <button wire:click="abrirModalNuevoCliente" type="button" class="text-blue-600 font-semibold text-xs">+ Registrar cliente</button>
                {{--
                <x-button wire:click="abrirModalNuevoCliente" type="button" class="bg-blue-600 hover:bg-blue-700 text-xs">
                    <i class="fas fa-plus mr-1"></i> Registrar cliente
                </x-button>
                --}}
            </div>
        @endif
    </div>

    <!-- SELECCIÓN DE VEHÍCULO -->
    @if ($clienteId)
        <div class="p-4 bg-gray-50 border border-gray-200/80 rounded-xl space-y-3">
            <x-label for="vehiculoId" value="Vehículo Registrado" class="text-gray-700 font-medium" /> 
            <select id="vehiculoId" wire:model.live="vehiculoId" class="w-full border-gray-300 focus:border-blue-500 focus:ring-blue-500 rounded-xl shadow-sm text-sm">
                <option value="">-- Selecciona un vehículo --</option>
                @foreach ($this->vehiculos as $v)
                    <option value="{{ $v->id }}">{{ $v->placa }} — {{ $v->marca }} {{ $v->modelo }}</option>
                @endforeach
            </select>

            <div>
                <button wire:click="abrirModalNuevoVehiculo" type="button" class="inline-flex items-center text-xs font-semibold text-blue-600 hover:text-blue-800 transition">
                    <i class="fas fa-plus-circle mr-1"></i>
                    Agregar un nuevo vehículo a este cliente
                </button>
            </div>
        </div>
    @endif

    <!-- MODAL CREAR CLIENTE NUEVO -->
    <x-dialog-modal wire:model="creandoClienteNuevo">
        <x-slot name="title">
            <span class="text-white font-bold"><i class="fas fa-user-plus mr-2 text-blue-600"></i>Registrar Nuevo Cliente</span>
        </x-slot>

        <x-slot name="content">
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <x-label for="nuevoNombre" value="Nombre" />
                    <x-input id="nuevoNombre" type="text" class="mt-1 w-full" wire:model="nuevoNombre" placeholder="Ej: Juan" />
                    <x-input-error for="nuevoNombre" class="mt-1" />
                </div>
                <div>
                    <x-label for="nuevoApellido" value="Apellido" />
                    <x-input id="nuevoApellido" type="text" class="mt-1 w-full" wire:model="nuevoApellido" placeholder="Ej: Pérez" />
                    <x-input-error for="nuevoApellido" class="mt-1" />
                </div>
                <div>
                    <x-label for="nuevoDocumento" value="Documento (DNI/RUC)" />
                    <x-input id="nuevoDocumento" type="text" class="mt-1 w-full" wire:model="nuevoDocumento" placeholder="Ej: 71234567" />
                    <x-input-error for="nuevoDocumento" class="mt-1" />
                </div>
                <div>
                    <x-label for="nuevoTelefono" value="Teléfono" />
                    <x-input id="nuevoTelefono" type="text" class="mt-1 w-full" wire:model="nuevoTelefono" placeholder="Ej: 987654321" />
                    <x-input-error for="nuevoTelefono" class="mt-1" />
                </div>
            </div>
        </x-slot>

        <x-slot name="footer">
            <x-secondary-button wire:click="$set('creandoClienteNuevo', false)">Cancelar</x-secondary-button>
            <x-button class="ml-2 bg-blue-600 hover:bg-blue-700" wire:click="guardarClienteNuevo">Guardar Cliente</x-button>
        </x-slot>
    </x-dialog-modal>

    <!-- MODAL CREAR VEHÍCULO NUEVO -->
    <x-dialog-modal wire:model="creandoVehiculoNuevo">
        <x-slot name="title">
            <span class="text-white font-bold"><i class="fas fa-car mr-2 text-blue-600"></i>Registrar Nuevo Vehículo</span>
        </x-slot>

        <x-slot name="content">
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div>
                    <x-label for="nuevaPlaca" value="Placa" />
                    <x-input id="nuevaPlaca" type="text" class="mt-1 w-full uppercase" wire:model="nuevaPlaca" placeholder="ABC-123" />
                    <x-input-error for="nuevaPlaca" class="mt-1" />
                </div>
                <div>
                    <x-label for="nuevaMarca" value="Marca" />
                    <x-input id="nuevaMarca" type="text" class="mt-1 w-full" wire:model="nuevaMarca" placeholder="Toyota" />
                    <x-input-error for="nuevaMarca" class="mt-1" />
                </div>
                <div>
                    <x-label for="nuevoModelo" value="Modelo" />
                    <x-input id="nuevoModelo" type="text" class="mt-1 w-full" wire:model="nuevoModelo" placeholder="Yaris" />
                    <x-input-error for="nuevoModelo" class="mt-1" />
                </div>
            </div>
        </x-slot>

        <x-slot name="footer">
            <x-secondary-button wire:click="$set('creandoVehiculoNuevo', false)">Cancelar</x-secondary-button>
            <x-button class="ml-2 bg-blue-600 hover:bg-blue-700" wire:click="guardarVehiculoNuevo">Guardar Vehículo</x-button>
        </x-slot>
    </x-dialog-modal>
</div>