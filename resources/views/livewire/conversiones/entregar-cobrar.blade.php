<div wire:loading.class="opacity-50 pointer-events-none">
    <div class="max-w-5xl mx-auto py-12">
        <div class="bg-gray-200 p-8 rounded-xl w-full">
            
            <!-- Encabezado -->
            <div class="flex items-center justify-between pb-4 border-b border-gray-300 mb-6">
                <div>
                    <h2 class="text-gray-700 font-semibold text-2xl flex items-center gap-2">
                        <i class="fas fa-hand-holding-usd text-emerald-600"></i> Entrega y cobro de conversión
                    </h2>
                    <span class="text-xs text-gray-500">Orden de Servicio #{{ $orden->id }}</span>
                </div>
                <div>
                    <a href="{{ route('conversiones.entregas-pendientes') }}" 
                       class="bg-gray-500 hover:bg-gray-600 text-white text-xs font-semibold px-4 py-2 rounded-lg transition inline-flex items-center gap-2 shadow-sm">
                        <i class="fas fa-arrow-left"></i> Volver a pendientes
                    </a>
                </div>
            </div>

            @if (!$completado)
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <!-- Resumen de la Orden -->
                    <div class="md:col-span-2 bg-white p-6 rounded-xl border border-gray-200 space-y-4 shadow-sm">
                        <h3 class="text-gray-700 font-semibold text-base border-b border-gray-100 pb-2 flex items-center gap-2">
                            <i class="fas fa-info-circle text-gray-400"></i> Detalle del servicio
                        </h3>

                        <div class="grid grid-cols-2 gap-4 text-sm">
                            <div>
                                <span class="text-xs text-gray-500 uppercase font-bold block">Cliente</span>
                                <span class="text-gray-800 font-medium">{{ $orden->cliente->nombre }} {{ $orden->cliente->apellido }}</span>
                            </div>
                            <div>
                                <span class="text-xs text-gray-500 uppercase font-bold block">Vehículo</span>
                                <span class="text-gray-800 font-bold bg-gray-100 px-2 py-0.5 rounded border border-gray-200 inline-block">
                                    {{ $orden->vehiculo->placa }}
                                </span>
                            </div>
                            <div>
                                <span class="text-xs text-gray-500 uppercase font-bold block">Servicio</span>
                                <span class="text-gray-800">{{ $orden->service->nombre }}</span>
                            </div>
                            <div>
                                <span class="text-xs text-gray-500 uppercase font-bold block">Fecha Fin Conversión</span>
                                <span class="text-gray-800">
                                    {{ $orden->fecha_fin_conversion ? $orden->fecha_fin_conversion->format('d/m/Y H:i') : '—' }}
                                </span>
                            </div>
                        </div>

                        <!-- Monto total -->
                        <div class="mt-6 p-4 bg-emerald-50 border border-emerald-200 text-center rounded-xl">
                            <span class="text-xs uppercase tracking-wider text-emerald-700 font-bold block mb-1">Monto Total a Cobrar</span>
                            <div class="text-3xl font-black text-emerald-700">S/ {{ number_format($orden->precio_final, 2) }}</div>
                        </div>
                    </div>

                    <!-- Formulario de Cobro -->
                    <div class="bg-white p-6 rounded-xl border border-gray-200 shadow-sm flex flex-col justify-between space-y-4">
                        <div class="space-y-4">
                            <h3 class="text-gray-700 font-semibold text-base border-b border-gray-100 pb-2 flex items-center gap-2">
                                <i class="fas fa-wallet text-gray-400"></i> Pago
                            </h3>

                            <div>
                                <x-label for="metodoPago" value="Método de pago" class="font-semibold text-gray-700 mb-1" />
                                <select wire:model="metodoPago" id="metodoPago" 
                                        class="w-full rounded-lg border-gray-300 text-sm focus:border-emerald-500 focus:ring-emerald-500 shadow-sm">
                                    <option value="efectivo">💵 Efectivo</option>
                                    <option value="tarjeta">💳 Tarjeta (Débito/Crédito)</option>
                                    <option value="transferencia">📲 Transferencia / Yape / Plin</option>
                                    <option value="otro">Otros</option>
                                </select>
                                <x-input-error for="metodoPago" class="mt-1" />
                            </div>

                            <x-input-error for="caja" class="mt-1" />
                        </div>

                        <button wire:click="procesarCobro" 
                                wire:loading.attr="disabled" 
                                type="button"
                                class="w-full bg-emerald-600 hover:bg-emerald-700 text-white font-semibold py-3 px-4 rounded-xl transition shadow-sm flex items-center justify-center gap-2">
                            <i class="fas fa-check-circle"></i> Confirmar cobro y entregar
                        </button>
                    </div>
                </div>
            @else
                <!-- Estado completado -->
                <div class="bg-white p-8 rounded-xl border border-gray-200 shadow-sm text-center space-y-4 max-w-lg mx-auto">
                    <div class="w-16 h-16 bg-emerald-100 text-emerald-600 rounded-full flex items-center justify-center mx-auto">
                        <i class="fas fa-check text-2xl"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-800">¡Entrega y cobro completados!</h3>
                    <p class="text-sm text-gray-600">
                        Comprobante generado: <span class="font-bold text-gray-800">{{ $folioGenerado }}</span>
                    </p>

                    <div class="pt-4 flex flex-col sm:flex-row gap-3 justify-center">
                        <a href="{{ route('comprobantes.pdf', $orden->id) }}" target="_blank"
                           class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2.5 rounded-lg text-sm font-semibold transition inline-flex items-center justify-center gap-2 shadow-sm">
                            <i class="fas fa-file-pdf"></i> Ver comprobante PDF
                        </a>
                        <a href="{{ route('conversiones.entregas-pendientes') }}"
                           class="bg-gray-200 hover:bg-gray-300 text-gray-700 px-5 py-2.5 rounded-lg text-sm font-semibold transition inline-flex items-center justify-center gap-2">
                            Volver a la lista
                        </a>
                    </div>
                </div>
            @endif

        </div>
    </div>
</div>