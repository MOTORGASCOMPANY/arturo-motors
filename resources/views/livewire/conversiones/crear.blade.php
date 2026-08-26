<div class="max-w-3xl mx-auto px-4 py-8 my-6 lg:my-10">
    <div class="bg-gray-200 rounded-2xl shadow-sm border border-gray-200/80 overflow-hidden transition-all"> 
        @if ($ordenCreadaId)
            <!-- Estado: Orden de Conversión Creada -->
            <div class="p-8 bg-white/80 text-center space-y-4">
                <div class="w-16 h-16 bg-emerald-100 text-emerald-600 rounded-full flex items-center justify-center mx-auto ring-8 ring-emerald-50">
                    <i class="fas fa-check text-2xl"></i>
                </div>
                
                <div>
                    <h3 class="text-2xl font-bold text-gray-800">Orden de conversión #{{ $ordenCreadaId }} creada</h3>
                    <p class="text-sm text-gray-500 mt-1">Queda pendiente que el jefe de taller le asigne un técnico de conversión.</p>
                </div>

                <div class="pt-4">
                    <a href="{{ route('ordenes.listado') }}" 
                       class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white font-medium px-6 py-2.5 rounded-xl shadow-sm transition text-sm">
                        <i class="fas fa-list-ul"></i>
                        Ver listado de órdenes
                    </a>
                </div>
            </div>
        @else
            <!-- Encabezado del Formulario -->
            <div class="p-6 sm:p-8 border-b border-gray-200/60">
                <h2 class="text-2xl font-bold tracking-tight text-gray-800 flex items-center gap-3">
                    <i class="fas fa-gas-pump text-blue-600"></i>
                    Nueva Orden de Conversión
                </h2>
                <p class="text-xs text-gray-600 mt-1">Completa los datos del cliente, vehículo y tipo de sistema a instalar.</p>
            </div>

            <!-- Cuerpo del Formulario -->
            <div class="p-6 sm:p-8 bg-white/60">
                <div class="space-y-6">
                    
                    {{-- 
                    <!-- Búsqueda y Selección de Cliente -->
                    <div class="relative">
                        <x-label for="buscarCliente" value="Buscar Cliente" class="mb-1 text-gray-700 font-medium" /> 
                        <div class="relative">
                            <x-input id="buscarCliente" type="text" 
                                    class="w-full pl-10 border-gray-300 focus:border-blue-500 focus:ring-blue-500 rounded-xl" 
                                    wire:model.live.debounce.300ms="buscarCliente" 
                                    placeholder="Escribe el nombre, apellido o documento..." />
                            
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                                <i class="fas fa-search"></i>
                            </div>
                        </div>

                        <!-- Menú desplegable de resultados -->
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
                                <p class="text-amber-800">No se encontró ningún cliente con ese nombre o documento.</p>
                                <button wire:click="abrirModalNuevoCliente" type="button" class="text-blue-600 font-semibold text-xs">+ Registrar cliente</button>
                            </div>
                        @endif

                        <x-input-error for="clienteId" class="mt-1" />
                    </div>

                    <!-- Selección de Vehículo -->
                    @if ($clienteId)
                        <div class="p-4 bg-gray-50 border border-gray-200/80 rounded-xl space-y-3">
                            <x-label for="vehiculoId" value="Vehículo Registrado" class="text-gray-700 font-medium" /> 
                            <select id="vehiculoId" wire:model="vehiculoId" class="w-full border-gray-300 focus:border-blue-500 focus:ring-blue-500 rounded-xl shadow-sm text-sm">
                                <option value="">-- Selecciona un vehículo --</option>
                                @foreach ($this->vehiculos as $v)
                                    <option value="{{ $v->id }}">{{ $v->placa }} — {{ $v->marca }} {{ $v->modelo }}</option>
                                @endforeach
                            </select>
                            <x-input-error for="vehiculoId" class="mt-1" />

                            <div>
                                <button wire:click="abrirModalNuevoVehiculo" type="button" class="inline-flex items-center text-xs font-semibold text-blue-600 hover:text-blue-800 transition">
                                    <i class="fas fa-plus-circle mr-1"></i>
                                    Agregar un nuevo vehículo a este cliente
                                </button>
                            </div>
                        </div>
                    @endif
                    --}}

                    <!-- En lugar de todo el bloque HTML de búsqueda y modales -->
                    <livewire:selector-cliente-vehiculo />
                    <x-input-error for="clienteId" class="mt-1" />
                    <x-input-error for="vehiculoId" class="mt-1" />

                    <!-- Selección de Servicio de Conversión -->
                    @if ($vehiculoId)
                        <div>
                            <x-label value="Tipo de Conversión" class="mb-2 text-gray-700 font-medium" /> 
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                @foreach ($servicios as $s)
                                    <div wire:click="seleccionarServicio({{ $s->id }})"
                                         class="border rounded-xl p-4 cursor-pointer transition-all flex items-center justify-between {{ $serviceId === $s->id ? 'border-blue-600 bg-blue-50/50 ring-2 ring-blue-500/20 shadow-sm' : 'border-gray-200 bg-white hover:border-gray-300 hover:bg-gray-50/50' }}">
                                        <div class="flex items-center gap-3">
                                            <div class="w-4 h-4 rounded-full border border-gray-300 flex items-center justify-center {{ $serviceId === $s->id ? 'border-blue-600 bg-blue-600' : '' }}">
                                                @if($serviceId === $s->id)
                                                    <div class="w-1.5 h-1.5 rounded-full bg-white"></div>
                                                @endif
                                            </div>
                                            <span class="font-medium text-gray-800 text-sm">{{ $s->nombre }}</span>
                                        </div>
                                        <span class="font-bold text-gray-900 text-sm">S/ {{ number_format($s->precio_base, 2) }}</span>
                                    </div>
                                @endforeach
                            </div>
                            <x-input-error for="serviceId" class="mt-2" />
                        </div>

                        <!-- Ajuste de Precio -->
                        @if ($serviceId)
                            <div class="p-4 bg-gray-50 border border-gray-200/80 rounded-xl grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div>
                                    <x-label for="precioFinal" value="Precio Acordado (S/)" class="text-gray-700 font-medium" />
                                    <input id="precioFinal" type="number" step="0.01" wire:model.live="precioFinal" class="w-full font-semibold text-gray-900 mt-1 border-gray-300 focus:border-blue-500 focus:ring-blue-500 rounded-lg" />
                                    <span class="text-xs text-gray-500 mt-1 block">Precio base sugerido: S/ {{ number_format($precioLista, 2) }}</span>
                                </div>
                                <div>
                                    <x-label for="descuentoMotivo" value="Motivo del Ajuste / Descuento" class="text-gray-700 font-medium" />
                                    <x-input id="descuentoMotivo" type="text" wire:model.live="descuentoMotivo" placeholder="Ej: Descuento por kit en oferta" class="w-full mt-1 border-gray-300 focus:border-blue-500 focus:ring-blue-500 rounded-lg" />
                                    <x-input-error for="descuentoMotivo" class="mt-1" />
                                </div>
                            </div>
                        @endif            
                    @endif

                    <x-input-error for="general" class="mt-2" />

                    <!-- Acción Final -->
                    <div class="pt-4 flex justify-end">
                        <x-button wire:click="crearOrden" wire:loading.attr="disabled" type="button" class="bg-blue-600 hover:bg-blue-700">
                            <i class="fas fa-file-contract mr-2" wire:loading.remove wire:target="crearOrden"></i>
                            <span wire:loading.remove wire:target="crearOrden">Crear Orden de Conversión</span>
                            <span wire:loading wire:target="crearOrden">Procesando...</span>
                        </x-button>
                    </div>
                </div>
            </div>
        @endif
    </div>

    {{-- 
    <!-- MODAL CREAR CLIENTE NUEVO -->
    <x-dialog-modal wire:model="creandoClienteNuevo">
        <x-slot name="title">
            <span class="text-gray-800 font-bold"><i class="fas fa-user-plus mr-2 text-blue-600"></i>Registrar Nuevo Cliente</span>
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
            <x-secondary-button wire:click="$set('creandoClienteNuevo', false)">
                Cancelar
            </x-secondary-button>
            <x-button class="ml-2 bg-blue-600 hover:bg-blue-700" wire:click="guardarClienteNuevo">
                Guardar Cliente
            </x-button>
        </x-slot>
    </x-dialog-modal>
    <!-- MODAL CREAR VEHÍCULO NUEVO -->
    <x-dialog-modal wire:model="creandoVehiculoNuevo">
        <x-slot name="title">
            <span class="text-gray-800 font-bold"><i class="fas fa-car mr-2 text-blue-600"></i>Registrar Nuevo Vehículo</span>
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
            <x-secondary-button wire:click="$set('creandoVehiculoNuevo', false)">
                Cancelar
            </x-secondary-button>
            <x-button class="ml-2 bg-blue-600 hover:bg-blue-700" wire:click="guardarVehiculoNuevo">
                Guardar Vehículo
            </x-button>
        </x-slot>
    </x-dialog-modal>
    --}}
</div>