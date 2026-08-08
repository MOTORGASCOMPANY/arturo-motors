<div class="max-w-2xl mx-auto px-4 py-8 my-6 lg:my-10">
    <div class="bg-gray-200 rounded-2xl shadow-sm border border-gray-200/80 overflow-hidden transition-all"> 
        @if ($ordenCreadaId)
            <div class="text-center space-y-3 py-6">
                <div class="w-14 h-14 bg-emerald-100 text-emerald-600 rounded-full flex items-center justify-center mx-auto">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                </div>
                <h3 class="text-lg font-bold text-gray-800">Orden de conversión #{{ $ordenCreadaId }} creada</h3>
                <p class="text-sm text-gray-500">Queda pendiente que el jefe de taller le asigne un técnico.</p>
                <a href="{{ route('ordenes.listado') }}" class="inline-block text-blue-600 text-sm font-semibold">Ver listado de órdenes →</a>
            </div>
        @else
            <!-- Encabezado del Formulario -->
            <div class="p-6 sm:p-8 border-b border-gray-200/60">
                <h2 class="text-2xl font-bold tracking-tight text-gray-800 flex items-center gap-3">
                    <svg class="w-7 h-7 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    Nueva orden de conversión
                </h2>
                <p class="text-xs text-gray-500 mt-1">Completa los pasos requeridos para procesar la recepción y cobro.</p>
            </div>
            <div class="p-6 sm:p-8 bg-white/60">
                <div class="space-y-6">
                    <!-- Cliente -->
                    <div class="relative">
                        <x-label for="buscarCliente" value="Buscar cliente" />
                        <x-input id="buscarCliente" type="text" wire:model.live.debounce.300ms="buscarCliente"
                                placeholder="Nombre, apellido o documento..." class="w-full rounded-lg border-gray-300" />

                        @if (count($clientesEncontrados))
                            <ul class="absolute z-20 w-full mt-1 bg-white border rounded-lg shadow-lg divide-y max-h-56 overflow-y-auto">
                                @foreach ($clientesEncontrados as $c)
                                    <li wire:click="seleccionarCliente({{ $c['id'] }})" class="px-4 py-2 hover:bg-blue-50 cursor-pointer text-sm">
                                        {{ $c['nombre'] }} {{ $c['apellido'] }} — {{ $c['documento'] }}
                                    </li>
                                @endforeach
                            </ul>
                        @endif

                        @if (strlen(trim($buscarCliente)) >= 3 && count($clientesEncontrados) === 0 && !$clienteId)
                            <div class="mt-2 p-3 bg-amber-50 border border-amber-200 rounded-lg text-sm">
                                <p class="text-amber-800 mb-2">No se encontró ningún cliente.</p>
                                <button wire:click="$set('creandoClienteNuevo', true)" type="button" class="text-blue-600 font-semibold text-xs">+ Registrar cliente nuevo</button>
                            </div>
                        @endif

                        @if ($creandoClienteNuevo)
                            <div class="mt-3 p-4 bg-gray-50 rounded-lg border space-y-2">
                                <div class="grid grid-cols-2 gap-2">
                                    <x-input wire:model="nuevoNombre" placeholder="Nombre" class="rounded-lg border-gray-300 text-sm" />
                                    <x-input wire:model="nuevoApellido" placeholder="Apellido" class="rounded-lg border-gray-300 text-sm" />
                                    <x-input wire:model="nuevoDocumento" placeholder="DNI / RUC" class="rounded-lg border-gray-300 text-sm" />
                                    <x-input wire:model="nuevoTelefono" placeholder="Teléfono" class="rounded-lg border-gray-300 text-sm" />
                                </div>
                                <x-input-error for="nuevoDocumento" />
                                <x-button wire:click="guardarClienteNuevo" type="button" class="text-xs bg-blue-600 rounded-lg">Guardar cliente</x-button>
                            </div>
                        @endif
                        <x-input-error for="clienteId" />
                    </div>

                    <!-- Vehículo -->
                    @if ($clienteId)
                        <div class="p-4 bg-gray-50 rounded-lg border space-y-3">
                            <x-label for="vehiculoId" value="Vehículo" />
                            <select wire:model.live.debounce.300ms="vehiculoId" class="w-full rounded-lg border-gray-300 text-sm">
                                <option value="">-- Selecciona --</option>
                                @foreach ($this->vehiculos as $v)
                                    <option value="{{ $v->id }}">{{ $v->placa }} — {{ $v->marca }} {{ $v->modelo }}</option>
                                @endforeach
                            </select>
                            <x-input-error for="vehiculoId" />
                            <button wire:click="$set('creandoVehiculoNuevo', true)" type="button" class="text-xs text-blue-600 font-semibold">+ Vehículo nuevo</button>

                            @if ($creandoVehiculoNuevo)
                                <div class="grid grid-cols-3 gap-2 pt-2">
                                    <x-input wire:model="nuevaPlaca" placeholder="Placa" class="rounded-lg border-gray-300 text-sm" />
                                    <x-input wire:model="nuevaMarca" placeholder="Marca" class="rounded-lg border-gray-300 text-sm" />
                                    <x-input wire:model="nuevoModelo" placeholder="Modelo" class="rounded-lg border-gray-300 text-sm" />
                                </div>
                                <x-button wire:click="guardarVehiculoNuevo" type="button" class="text-xs bg-blue-600 rounded-lg mt-2">Guardar vehículo</x-button>
                            @endif
                        </div>
                    @endif

                    <!-- Servicio -->
                    @if ($vehiculoId)
                        <div>
                            <x-label value="Tipo de conversión" />
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 mt-2">
                                @foreach ($servicios as $s)
                                    <div wire:click="seleccionarServicio({{ $s->id }})"
                                        class="border rounded-xl p-4 cursor-pointer {{ $serviceId === $s->id ? 'border-blue-600 bg-blue-50 ring-2 ring-blue-500/20' : 'border-gray-200' }}">
                                        <p class="font-medium text-sm">{{ $s->nombre }}</p>
                                        <p class="text-xs text-gray-500">S/ {{ number_format($s->precio_base, 2) }}</p>
                                    </div>
                                @endforeach
                            </div>
                            <x-input-error for="serviceId" />
                        </div>

                        @if ($serviceId)
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <x-label for="precioFinal" value="Precio acordado (S/)" />
                                    <x-input type="number" step="0.01" wire:model="precioFinal" class="w-full rounded-lg border-gray-300" />
                                </div>
                                <div>
                                    <x-label for="descuentoMotivo" value="Motivo del ajuste (si aplica)" />
                                    <x-input wire:model="descuentoMotivo" placeholder="Ej: Descuento por cliente frecuente" class="w-full rounded-lg border-gray-300" />
                                    <x-input-error for="descuentoMotivo" />
                                </div>
                            </div>
                        @endif            
                    @endif

                    <x-input-error for="general" />

                    <div class="pt-4 flex justify-end">
                        <button wire:click="crearOrden" wire:loading.attr="disabled" type="button" class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white font-medium px-5 py-2.5 rounded-xl shadow-sm transition text-sm">
                            Crear orden de conversión
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                        </button>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>