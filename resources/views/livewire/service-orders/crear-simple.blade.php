<div class="max-w-4xl mx-auto px-4 py-8 my-6 lg:my-10">
    <div class="bg-gray-200 rounded-2xl shadow-sm border border-gray-200/80 overflow-hidden transition-all">        
        <!-- Encabezado del Formulario -->
        <div class="p-6 sm:p-8 border-b border-gray-200/60">
            <h2 class="text-2xl font-bold tracking-tight text-gray-800 flex items-center gap-3">
                <svg class="w-7 h-7 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                Crear Orden de Servicio
            </h2>
            <p class="text-xs text-gray-500 mt-1">Completa los pasos requeridos para procesar la recepción y cobro.</p>
        </div>

        <!-- Indicador de Pasos (Stepper) -->
        <div class="bg-gray-50/50 border-b border-gray-200/60 px-6 py-4">
            <div class="flex items-center justify-between max-w-2xl mx-auto">                
                <!-- Paso 1 -->
                <div class="flex items-center gap-2">
                    <div class="w-8 h-8 rounded-full flex items-center justify-center font-semibold text-xs transition-colors {{ $paso >= 1 ? 'bg-blue-600 text-white ring-4 ring-blue-100' : 'bg-gray-200 text-gray-500' }}">
                        1
                    </div>
                    <span class="text-xs sm:text-sm font-medium {{ $paso >= 1 ? 'text-gray-800 font-semibold' : 'text-gray-400' }}">Cliente y Vehículo</span>
                </div>
                <div class="flex-1 mx-3 h-0.5 {{ $paso >= 2 ? 'bg-blue-600' : 'bg-gray-200' }}"></div>
                
                <!-- Paso 2 -->
                <div class="flex items-center gap-2">
                    <div class="w-8 h-8 rounded-full flex items-center justify-center font-semibold text-xs transition-colors {{ $paso >= 2 ? 'bg-blue-600 text-white ring-4 ring-blue-100' : 'bg-gray-200 text-gray-500' }}">
                        2
                    </div>
                    <span class="text-xs sm:text-sm font-medium {{ $paso >= 2 ? 'text-gray-800 font-semibold' : 'text-gray-400' }}">Servicio</span>
                </div>
                <div class="flex-1 mx-3 h-0.5 {{ $paso >= 3 ? 'bg-blue-600' : 'bg-gray-200' }}"></div>
                
                <!-- Paso 3 -->
                <div class="flex items-center gap-2">
                    <div class="w-8 h-8 rounded-full flex items-center justify-center font-semibold text-xs transition-colors {{ $paso >= 3 ? 'bg-blue-600 text-white ring-4 ring-blue-100' : 'bg-gray-200 text-gray-500' }}">
                        3
                    </div>
                    <span class="text-xs sm:text-sm font-medium {{ $paso >= 3 ? 'text-gray-800 font-semibold' : 'text-gray-400' }}">Cobro</span>
                </div>
                <div class="flex-1 mx-3 h-0.5 {{ $paso >= 4 ? 'bg-blue-600' : 'bg-gray-200' }}"></div>
                
                <!-- Paso 4 -->
                <div class="flex items-center gap-2">
                    <div class="w-8 h-8 rounded-full flex items-center justify-center font-semibold text-xs transition-colors {{ $paso >= 4 ? 'bg-emerald-600 text-white ring-4 ring-emerald-100' : 'bg-gray-200 text-gray-500' }}">
                        ✓
                    </div>
                    <span class="text-xs sm:text-sm font-medium {{ $paso >= 4 ? 'text-gray-800 font-semibold' : 'text-gray-400' }}">Listo</span>
                </div>
            </div>
        </div>

        <!-- Cuerpo del Formulario -->
        <div class="p-6 sm:p-8 bg-white/60">
            <!-- PASO 1: CLIENTE Y VEHÍCULO -->
            @if ($paso === 1)
                <div class="space-y-6">
                    <div class="relative">
                        <x-label for="buscarCliente" value="Buscar Cliente" class="mb-1 text-gray-700 font-medium" />                        
                        <div class="relative">
                            <x-input id="buscarCliente" type="text" class="w-full pl-10 border-gray-300 focus:border-blue-500 focus:ring-blue-500 rounded-xl" 
                                    wire:model.live.debounce.300ms="buscarCliente" 
                                    placeholder="Escribe el nombre, apellido o documento..." />
                            
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                            </div>
                        </div>
                        {{-- Autocomplete overlay --}}
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
                            <div class="mt-2 p-3 bg-amber-50 border border-amber-200 rounded-xl text-sm">
                                <p class="text-amber-800 mb-2">No se encontró ningún cliente con ese nombre o documento.</p>
                                <button wire:click="$set('creandoClienteNuevo', true)" type="button"
                                        class="text-blue-600 font-semibold text-xs">+ Registrar cliente nuevo</button>
                            </div>
                        @endif

                        @if ($creandoClienteNuevo)
                            <div class="mt-3 p-4 bg-white rounded-xl border border-gray-200 shadow-sm space-y-3">
                                <h4 class="text-xs font-bold text-gray-700 uppercase">Nuevo Cliente</h4>
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                                    <x-input type="text" wire:model="nuevoNombre" placeholder="Nombre" class="rounded-lg border-gray-300 text-sm" />
                                    <x-input type="text" wire:model="nuevoApellido" placeholder="Apellido" class="rounded-lg border-gray-300 text-sm" />
                                    <x-input type="text" wire:model="nuevoDocumento" placeholder="DNI / RUC" class="rounded-lg border-gray-300 text-sm" />
                                    <x-input type="text" wire:model="nuevoTelefono" placeholder="Teléfono" class="rounded-lg border-gray-300 text-sm" />
                                </div>
                                <x-input-error for="nuevoDocumento" class="mt-1" />
                                <div class="flex justify-end gap-2 pt-2">
                                    <x-secondary-button wire:click="$set('creandoClienteNuevo', false)" type="button" class="text-xs rounded-lg">
                                        Cancelar
                                    </x-secondary-button>
                                    <x-button wire:click="guardarClienteNuevo" type="button" class="text-xs bg-blue-600 hover:bg-blue-700 rounded-lg">
                                        Guardar Cliente
                                    </x-button>
                                </div>
                            </div>
                        @endif

                        <x-input-error for="clienteId" class="mt-1" />
                    </div>

                    @if ($clienteId)
                        <div class="p-4 bg-gray-50 border border-gray-200/80 rounded-xl space-y-4">
                            <div>
                                <x-label for="vehiculoId" value="Vehículo Registrado" class="mb-1 text-gray-700 font-medium" />                                
                                <select id="vehiculoId" wire:model="vehiculoId" 
                                        class="w-full border-gray-300 focus:border-blue-500 focus:ring-blue-500 rounded-xl shadow-sm text-sm">
                                    <option value="">-- Selecciona un vehículo --</option>
                                    @foreach ($this->vehiculos as $v)
                                        <option value="{{ $v->id }}">{{ $v->placa }} — {{ $v->marca }} {{ $v->modelo }}</option>
                                    @endforeach
                                </select>
                                <x-input-error for="vehiculoId" class="mt-1" />
                            </div>
                            <button wire:click="$set('creandoVehiculoNuevo', true)" type="button"
                                    class="inline-flex items-center text-xs font-semibold text-blue-600 hover:text-blue-800 transition">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                                Agregar un nuevo vehículo a este cliente
                            </button>
                            <!-- Form crear nuevo vehiculo -->
                            @if ($creandoVehiculoNuevo)
                                <div class="mt-3 p-4 bg-white rounded-xl border border-gray-200 shadow-sm space-y-3">
                                    <h4 class="text-xs font-bold text-gray-700 uppercase">Nuevo Vehículo</h4>
                                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-2">
                                        <x-input type="text" wire:model="nuevaPlaca" placeholder="Placa (ej: ABC-123)" class="rounded-lg border-gray-300 text-sm" />
                                        <x-input type="text" wire:model="nuevaMarca" placeholder="Marca (ej: Toyota)" class="rounded-lg border-gray-300 text-sm" />
                                        <x-input type="text" wire:model="nuevoModelo" placeholder="Modelo (ej: Yaris)" class="rounded-lg border-gray-300 text-sm" />
                                    </div>
                                    <div class="flex justify-end gap-2 pt-2">
                                        <x-secondary-button wire:click="$set('creandoVehiculoNuevo', false)" type="button" class="text-xs rounded-lg">
                                            Cancelar
                                        </x-secondary-button>
                                        <x-button wire:click="guardarVehiculoNuevo" type="button" class="text-xs bg-blue-600 hover:bg-blue-700 rounded-lg">
                                            Guardar Vehículo
                                        </x-button>
                                    </div>
                                </div>
                            @endif
                        </div>
                    @endif

                    <div class="pt-4 flex justify-end">
                        <button wire:click="irAPaso2" type="button" class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white font-medium px-5 py-2.5 rounded-xl shadow-sm transition text-sm">
                            Siguiente
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                        </button>
                    </div>
                </div>
            @endif

            <!-- PASO 2: SERVICIO Y AJUSTES -->
            @if ($paso === 2)
                <div class="space-y-6">
                    <div>
                        <x-label value="Selecciona el Servicio" class="mb-3 text-gray-700 font-medium" />                        
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

                    @if ($serviceId)
                        <div class="p-4 bg-gray-50 border border-gray-200/80 rounded-xl grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <x-label for="precioFinal" value="Precio a Cobrar (S/)" class="text-gray-700" />
                                <x-input id="precioFinal" type="number" step="0.01" wire:model="precioFinal" class="w-full font-semibold text-gray-900 mt-1 border-gray-300 focus:border-blue-500 focus:ring-blue-500 rounded-lg" />
                                <span class="text-xs text-gray-500 mt-1 block">Precio base sugerido: S/ {{ number_format($precioLista, 2) }}</span>
                            </div>
                            <div>
                                <x-label for="descuentoMotivo" value="Motivo del Ajuste / Descuento" class="text-gray-700" />
                                <x-input id="descuentoMotivo" type="text" wire:model="descuentoMotivo" placeholder="Ej: Descuento por cliente frecuente" class="w-full mt-1 border-gray-300 focus:border-blue-500 focus:ring-blue-500 rounded-lg" />
                                <x-input-error for="descuentoMotivo" class="mt-1" />
                            </div>
                        </div>
                    @endif

                    <div class="pt-4 flex justify-between items-center">
                        <x-secondary-button wire:click="$set('paso', 1)" type="button" class="rounded-xl">
                            ← Regresar
                        </x-secondary-button>                        
                        <button wire:click="irAPaso3" type="button" class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white font-medium px-5 py-2.5 rounded-xl shadow-sm transition text-sm">
                            Siguiente
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                        </button>
                    </div>
                </div>
            @endif

            <!-- PASO 3: PROCESAR COBRO -->
            @if ($paso === 3)
                <div class="space-y-6">
                    <!-- Caja de resumen clara en lugar del azul oscuro slate -->
                    <div class="p-6 bg-blue-50/70 border border-blue-100 text-center rounded-xl shadow-xs">
                        <span class="text-xs uppercase tracking-wider text-blue-600 font-bold block mb-1">Monto Total a Cobrar</span>
                        <div class="text-4xl font-black text-blue-700">S/ {{ number_format($precioFinal, 2) }}</div>
                    </div>

                    <div>
                        <x-label for="metodoPago" value="Método de Pago" class="mb-1 text-gray-700 font-medium" />
                        <select id="metodoPago" wire:model="metodoPago" 
                                class="w-full border-gray-300 focus:border-blue-500 focus:ring-blue-500 rounded-xl shadow-sm text-sm">
                            <option value="efectivo">💵 Efectivo</option>
                            <option value="tarjeta">💳 Tarjeta (Débito/Crédito)</option>
                            <option value="transferencia">📲 Transferencia / Yape / Plin</option>
                            <option value="otro">Otros</option>
                        </select>
                    </div>

                    <x-input-error for="caja" class="mt-1" />

                    <div class="pt-4 flex justify-between items-center">
                        <x-secondary-button wire:click="$set('paso', 2)" type="button" class="rounded-xl">
                            ← Regresar
                        </x-secondary-button>

                        <button wire:click="procesarCobro" wire:loading.attr="disabled"  type="button" class="inline-flex items-center gap-2 bg-emerald-600 hover:bg-emerald-700 text-white font-semibold px-6 py-2.5 rounded-xl shadow-sm transition text-sm">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                            <span wire:loading.remove wire:target="procesarCobro">Confirmar y Registrar Orden</span>
                            <span wire:loading wire:target="procesarCobro">Procesando...</span>
                        </button>
                    </div>
                </div>
            @endif

            <!-- PASO 4: ORDEN COMPLETADA -->
            @if ($paso === 4)
                <div class="text-center py-6 space-y-4">
                    <div class="w-16 h-16 bg-emerald-100 text-emerald-600 rounded-full flex items-center justify-center mx-auto ring-8 ring-emerald-50">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                    </div>
                    
                    <div>
                        <h3 class="text-2xl font-bold text-gray-800">¡Orden Registrada con Éxito!</h3>
                        <p class="text-sm text-gray-500 mt-1">Nro. de Registro: <span class="font-bold text-gray-700">#{{ $ordenCreadaId }}</span></p>
                    </div>

                    <div class="inline-block bg-gray-100 px-4 py-2 rounded-xl text-sm text-gray-700 font-medium">
                        Folio Generado: <strong class="text-blue-600">{{ $folioGenerado }}</strong>
                    </div>

                    <div class="pt-4">
                        <a href="{{ route('comprobantes.pdf', $ordenCreadaId) }}" target="_blank"
                           class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white font-medium px-6 py-3 rounded-xl shadow-sm transition-all text-sm">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                            Descargar / Imprimir Comprobante PDF
                        </a>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>