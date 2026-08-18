<div class="space-y-4">
    <!-- BÚSQUEDA Y SELECCIÓN -->
    <div class="relative">
        <x-label for="buscarVehiculo" value="Buscar por Placa o Cliente" class="mb-1 text-gray-700 font-medium" /> 
        <div class="relative">
            <x-input id="buscarVehiculo" type="text" 
                    class="w-full pl-10 pr-10 border-gray-300 focus:border-blue-500 focus:ring-blue-500 rounded-xl shadow-sm text-sm" 
                    wire:model.live.debounce.300ms="buscarVehiculo" 
                    placeholder="Escribe la placa, nombre, RUC/DNI o razón social..." />
            
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                <i class="fas fa-car"></i>
            </div>

            @if($vehiculoId)
                <button type="button" wire:click="deseleccionar" class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-red-500">
                    <i class="fas fa-times-circle"></i>
                </button>
            @endif
        </div>

        <!-- Dropdown con coincidencias -->
        @if (count($vehiculosEncontrados) && !$vehiculoId)
            <ul class="absolute z-20 w-full mt-1 bg-white border border-gray-200 rounded-xl shadow-lg divide-y divide-gray-100 max-h-64 overflow-y-auto">
                @foreach ($vehiculosEncontrados as $v)
                    <li wire:click="seleccionarVehiculo({{ $v['id'] }})"
                        class="px-4 py-3 hover:bg-blue-50/50 cursor-pointer transition flex justify-between items-center text-sm">
                        <div>
                            <div class="flex items-center gap-2">
                                <span class="font-bold text-blue-700 bg-blue-50 px-2 py-0.5 rounded border border-blue-100 text-xs">
                                    {{ $v['placa'] }}
                                </span>
                                <span class="text-gray-700 font-semibold">
                                    {{ $v['marca'] }} {{ $v['modelo'] }}
                                </span>
                            </div>

                            @if (!empty($v['clientes']))
                                <div class="mt-1 flex flex-wrap gap-1">
                                    @foreach ($v['clientes'] as $c)
                                        <span class="inline-flex items-center text-xs text-gray-600 bg-gray-100 px-2 py-0.5 rounded">
                                            @if($c['pivot']['es_principal'])
                                                <i class="fas fa-star text-amber-500 text-[10px] mr-1" title="Principal"></i>
                                            @endif
                                            {{ $c['tipo_persona'] === 'JURIDICA' ? $c['razon_social'] : trim("{$c['nombre']} {$c['apellido']}") }}
                                            <span class="text-gray-400 ml-1">({{ $c['documento'] }})</span>
                                        </span>
                                    @endforeach
                                </div>
                            @else
                                <p class="text-xs text-amber-600 mt-0.5">Sin clientes vinculados</p>
                            @endif
                        </div>
                        <span class="text-xs bg-gray-100 text-gray-700 font-semibold px-2.5 py-1 rounded-md border border-gray-200 whitespace-nowrap">
                            Seleccionar
                        </span>
                    </li>
                @endforeach
            </ul>
        @endif

        <!-- Opción si no se encuentra el vehículo -->
        @if (strlen(trim($buscarVehiculo)) >= 2 && count($vehiculosEncontrados) === 0 && !$vehiculoId && !$mostrarSeleccionAsociados)
            <div class="mt-2 p-3 bg-amber-50 border border-amber-200 rounded-xl text-sm flex items-center justify-between">
                <p class="text-amber-800">No se encontró ningún vehículo con esa búsqueda.</p>
                <button wire:click="abrirModalNuevoVehiculo" type="button" class="text-white bg-blue-600 hover:bg-blue-700 font-semibold text-xs px-3 py-1.5 rounded-lg shadow-sm">
                    + Registrar Vehículo
                </button>
            </div>
        @endif
    </div>

    <!-- SELECCIÓN CUANDO HAY MÚLTIPLES ASOCIADOS -->
    @if ($mostrarSeleccionAsociados)
        <div class="p-4 bg-blue-50/70 border border-blue-200 rounded-xl space-y-3">
            <div class="flex items-center justify-between">
                <div>
                    <h4 class="text-sm font-bold text-blue-900">
                        Vehículo {{ $vehiculoSeleccionadoTemp['placa'] }} tiene múltiples asociados
                    </h4>
                    <p class="text-xs text-blue-700">Selecciona quién será el titular / solicitante para esta orden:</p>
                </div>
                <button wire:click="$set('mostrarSeleccionAsociados', false)" type="button" class="text-xs text-gray-500 hover:text-gray-700">
                    Cancelar
                </button>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-2">
                @foreach ($asociadosDisponibles as $asociado)
                    <div wire:click="seleccionarTitular({{ $asociado['id'] }})"
                         class="p-3 bg-white border border-blue-100 rounded-lg shadow-sm hover:border-blue-400 cursor-pointer transition flex items-center justify-between">
                        <div>
                            <div class="flex items-center gap-1">
                                <span class="text-sm font-semibold text-gray-800">{{ $asociado['nombre_completo'] }}</span>
                                @if ($asociado['es_principal'])
                                    <span class="text-[10px] bg-amber-100 text-amber-800 font-bold px-1.5 py-0.5 rounded">Principal</span>
                                @endif
                            </div>
                            <p class="text-xs text-gray-500">Doc: {{ $asociado['documento'] }} | Relación: {{ $asociado['relacion'] }}</p>
                        </div>
                        <i class="fas fa-chevron-right text-blue-500 text-xs"></i>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    <!-- RESUMEN SELECCIONADO -->
    @if ($vehiculoId && $clienteId)
        <div class="p-3.5 bg-emerald-50 border border-emerald-200 rounded-xl flex items-center justify-between">
            <div class="flex items-center gap-2">
                <i class="fas fa-check-circle text-emerald-600 text-base"></i>
                <div>
                    <span class="text-xs font-semibold text-emerald-900 block">Vehículo y Cliente Titular seleccionados</span>
                    <span class="text-xs text-emerald-700 font-medium">{{ $buscarVehiculo }}</span>
                </div>
            </div>

            {{-- 
            <button wire:click="abrirModalAsignarOtroCliente" type="button" class="text-xs text-blue-700 font-semibold hover:underline flex items-center gap-1">
                <i class="fas fa-user-plus text-[11px]"></i>
                + Vincular otro Propietario
            </button>
            --}}
        </div>
    @endif

    <!-- REGISTRAR VEHÍCULO Y PROPIETARIO(S) -->
    <x-dialog-modal wire:model.live="creandoVehiculoNuevo" maxWidth="3xl">
        <x-slot name="title">
            {{ __('Registrar Nuevo Vehículo y Propietario') }}
        </x-slot>

        <x-slot name="content">
            <div class="space-y-5 max-h-[70vh] overflow-y-auto pr-1">                
                <!-- SECCIÓN 1: DATOS DEL VEHÍCULO -->
                <div class="p-3.5 bg-gray-50 border rounded-xl space-y-3">
                    <h3 class="text-xs font-bold text-gray-700 uppercase tracking-wider flex items-center gap-1.5">
                        <i class="fas fa-car text-blue-600"></i> Datos del Vehículo
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                        <div>
                            <x-label for="nuevaPlaca" value="Placa *" />
                            <x-input id="nuevaPlaca" type="text" class="w-full text-sm uppercase" wire:model="nuevaPlaca" />
                            <x-input-error for="nuevaPlaca" class="mt-1" />
                        </div>
                        <div>
                            <x-label for="nuevaMarca" value="Marca *" />
                            <x-input id="nuevaMarca" type="text" class="w-full text-sm" wire:model="nuevaMarca" />
                            <x-input-error for="nuevaMarca" class="mt-1" />
                        </div>
                        <div>
                            <x-label for="nuevoModelo" value="Modelo *" />
                            <x-input id="nuevoModelo" type="text" class="w-full text-sm" wire:model="nuevoModelo" />
                            <x-input-error for="nuevoModelo" class="mt-1" />
                        </div>
                        <div>
                            <x-label for="nuevoAnio" value="Año" />
                            <x-input id="nuevoAnio" type="number" class="w-full text-sm" wire:model="nuevoAnio" />
                            <x-input-error for="nuevoAnio" class="mt-1" />
                        </div>
                        <div>
                            <x-label for="nuevoCombustible" value="Combustible *" />
                            <select id="nuevoCombustible" wire:model="nuevoCombustible" class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm w-full text-sm">
                                <option value="GASOLINA">GASOLINA</option>
                                <option value="GLP">GLP</option>
                                <option value="GNV">GNV</option>
                                <option value="DIESEL">DIESEL</option>
                            </select>
                            <x-input-error for="nuevoCombustible" class="mt-1" />
                        </div>
                        <div>
                            <x-label for="nuevoColor" value="Color" />
                            <x-input id="nuevoColor" type="text" class="w-full text-sm" wire:model="nuevoColor" />
                            <x-input-error for="nuevoColor" class="mt-1" />
                        </div>
                    </div>
                </div>
                <!-- SECCIÓN 2: DATOS DEL PROPIETARIO PRINCIPAL -->
                <div class="p-3.5 bg-blue-50/50 border border-blue-200 rounded-xl space-y-3">
                    <h3 class="text-xs font-bold text-blue-800 uppercase tracking-wider flex items-center gap-1.5">
                        <i class="fas fa-user-check text-blue-600"></i> Propietario Principal
                    </h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                        <div>
                            <x-label for="tipoPersona" value="Tipo de Persona *" />
                            <select id="tipoPersona" wire:model.live="tipoPersona" class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm w-full text-sm">
                                <option value="NATURAL">NATURAL</option>
                                <option value="JURIDICA">JURÍDICA</option>
                            </select>
                        </div>
                        <div>
                            <x-label for="nuevoDocumento" value="Nro. Documento (DNI/RUC) *" />
                            <x-input id="nuevoDocumento" type="text" class="w-full text-sm" wire:model="nuevoDocumento" />
                            <x-input-error for="nuevoDocumento" class="mt-1" />
                        </div>
                        <div>
                            <x-label for="nuevoTelefono" value="Teléfono" />
                            <x-input id="nuevoTelefono" type="text" class="w-full text-sm" wire:model="nuevoTelefono" />
                        </div>
                    </div>

                    @if ($tipoPersona === 'JURIDICA')
                        <div>
                            <x-label for="nuevaRazonSocial" value="Razón Social *" />
                            <x-input id="nuevaRazonSocial" type="text" class="w-full text-sm" wire:model="nuevaRazonSocial" />
                            <x-input-error for="nuevaRazonSocial" class="mt-1" />
                        </div>
                    @else
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                            <div>
                                <x-label for="nuevoNombre" value="Nombres *" />
                                <x-input id="nuevoNombre" type="text" class="w-full text-sm" wire:model="nuevoNombre" />
                                <x-input-error for="nuevoNombre" class="mt-1" />
                            </div>
                            <div>
                                <x-label for="nuevoApellido" value="Apellidos" />
                                <x-input id="nuevoApellido" type="text" class="w-full text-sm" wire:model="nuevoApellido" />
                                <x-input-error for="nuevoApellido" class="mt-1" />
                            </div>
                        </div>
                    @endif
                </div>
                <!-- SECCIÓN 3: SEGUNDO CLIENTE / COPROPIETARIO (OPCIONAL) -->
                <div class="p-3.5 bg-gray-50 border rounded-xl space-y-3">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" wire:model.live="incluirSegundoCliente" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                        <span class="text-xs font-bold text-gray-700 uppercase tracking-wider">¿Tiene un Segundo Propietario / Copropietario?</span>
                    </label>

                    @if ($incluirSegundoCliente)
                        <div class="pt-2 border-t border-gray-200 space-y-3">
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                                <div>
                                    <x-label for="tipoPersona2" value="Tipo de Persona *" />
                                    <select id="tipoPersona2" wire:model.live="tipoPersona2" class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm w-full text-sm">
                                        <option value="NATURAL">NATURAL</option>
                                        <option value="JURIDICA">JURÍDICA</option>
                                    </select>
                                </div>
                                <div>
                                    <x-label for="nuevoDocumento2" value="Nro. Documento *" />
                                    <x-input id="nuevoDocumento2" type="text" class="w-full text-sm" wire:model="nuevoDocumento2" />
                                    <x-input-error for="nuevoDocumento2" class="mt-1" />
                                </div>
                                <div>
                                    <x-label for="nuevoTelefono2" value="Teléfono" />
                                    <x-input id="nuevoTelefono2" type="text" class="w-full text-sm" wire:model="nuevoTelefono2" />
                                </div>
                            </div>

                            @if ($tipoPersona2 === 'JURIDICA')
                                <div>
                                    <x-label for="nuevaRazonSocial2" value="Razón Social *" />
                                    <x-input id="nuevaRazonSocial2" type="text" class="w-full text-sm" wire:model="nuevaRazonSocial2" />
                                    <x-input-error for="nuevaRazonSocial2" class="mt-1" />
                                </div>
                            @else
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                    <div>
                                        <x-label for="nuevoNombre2" value="Nombres *" />
                                        <x-input id="nuevoNombre2" type="text" class="w-full text-sm" wire:model="nuevoNombre2" />
                                        <x-input-error for="nuevoNombre2" class="mt-1" />
                                    </div>
                                    <div>
                                        <x-label for="nuevoApellido2" value="Apellidos" />
                                        <x-input id="nuevoApellido2" type="text" class="w-full text-sm" wire:model="nuevoApellido2" />
                                        <x-input-error for="nuevoApellido2" class="mt-1" />
                                    </div>
                                </div>
                            @endif
                        </div>
                    @endif
                </div>
            </div>
        </x-slot>

        <x-slot name="footer">
            <x-secondary-button wire:click="$set('creandoVehiculoNuevo', false)">
                {{ __('Cancelar') }}
            </x-secondary-button>

            <x-button class="ms-3" wire:click="guardarVehiculoNuevo">
                {{ __('Guardar Registro Completo') }}
            </x-button>
        </x-slot>
    </x-dialog-modal>

    <!-- VINCULAR OTRO PROPIETARIO A VEHÍCULO YA EXISTENTE -->
    {{--
    <x-dialog-modal wire:model.live="vinculandoSegundoCliente">
        <x-slot name="title">
            {{ __('Vincular Copropietario Existente') }}
        </x-slot>
        <x-slot name="content">
            <div class="space-y-3">
                <div class="relative">
                    <x-label value="Buscar Cliente a Vincular" />
                    <x-input type="text" class="w-full text-sm" placeholder="Buscar por nombre o documento..." wire:model.live.debounce.300ms="buscarClientePivote" />
                    
                    @if (count($clientesPivoteEncontrados))
                        <ul class="absolute z-30 w-full mt-1 bg-white border rounded-lg shadow-lg max-h-40 overflow-y-auto divide-y divide-gray-100">
                            @foreach ($clientesPivoteEncontrados as $cp)
                                <li wire:click="$set('segundoClienteId', {{ $cp['id'] }})" class="p-2 text-xs hover:bg-blue-50 cursor-pointer flex justify-between">
                                    <span>{{ $cp['nombre_completo'] }}</span>
                                    <span class="text-gray-400">{{ $cp['documento'] }}</span>
                                </li>
                            @endforeach
                        </ul>
                    @endif
                    <x-input-error for="segundoClienteId" class="mt-1" />
                </div>
            </div>
        </x-slot>
        <x-slot name="footer">
            <x-secondary-button wire:click="$set('vinculandoSegundoCliente', false)">
                {{ __('Cancelar') }}
            </x-secondary-button>

            <x-button class="ms-3" wire:click="vincularClienteExistenteAVehiculo">
                {{ __('Vincular Propietario') }}
            </x-button>
        </x-slot>
    </x-dialog-modal>
    --}}
</div>