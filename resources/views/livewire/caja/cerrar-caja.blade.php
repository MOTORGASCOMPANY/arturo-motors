<div class="max-w-md mx-auto px-4 py-8 my-6 lg:my-10">
    <div class="bg-gray-200 rounded-2xl shadow-sm border border-gray-300/80 overflow-hidden transition-all">
        <!-- Encabezado del Tarjetón -->
        <div class="p-6 border-b border-gray-300/70">
            <h2 class="text-xl font-bold tracking-tight text-gray-800 flex items-center gap-2.5">
                <i class="fas fa-lock text-red-600"></i>
                Cierre de Caja
            </h2>
            <p class="text-xs text-gray-600 mt-1">Arqueo y finalización del turno actual de caja.</p>
        </div>

        <!-- Contenido principal -->
        <div class="p-6 bg-white/60">
            @if (!$sesion)
                <!-- ESTADO: SIN CAJA ABIERTA -->
                <div class="text-center space-y-3 py-4">
                    <div class="w-16 h-16 bg-gray-300/60 text-gray-500 rounded-full flex items-center justify-center mx-auto ring-8 ring-gray-200/50">
                        <i class="fas fa-inbox text-2xl"></i>
                    </div>
                    <div class="space-y-1">
                        <h3 class="text-base font-bold text-gray-700">Sin Caja Activa</h3>
                        <p class="text-xs text-gray-500">No hay ninguna sesión de caja abierta para realizar el arqueo o cierre.</p>
                    </div>
                </div>
            @else
                <!-- DESGLOSE DE MONTOS Y FORMULARIO -->
                <form wire:submit.prevent="cerrar" class="space-y-5">

                    <!-- Desglose por método de pago -->
                    <div class="bg-white border border-gray-300/70 rounded-xl p-4 space-y-2.5 shadow-xs">
                        <h4 class="text-xs font-bold text-gray-700 uppercase tracking-wider mb-3">
                            <i class="fas fa-chart-pie mr-1 text-blue-600"></i> Desglose de Ingresos
                        </h4>

                        <div class="flex justify-between items-center text-xs">
                            <span class="text-emerald-700 font-medium"><i class="fas fa-money-bill-wave text-emerald-500 mr-1.5"></i> Efectivo:</span>
                            <span class="font-semibold text-emerald-700">S/ {{ number_format($this->efectivoIngresos, 2) }}</span>
                        </div>

                        <div class="flex justify-between items-center text-xs">
                            <span class="text-blue-700 font-medium"><i class="fas fa-credit-card text-blue-500 mr-1.5"></i> Tarjeta:</span>
                            <span class="font-semibold text-blue-700">S/ {{ number_format($this->tarjetaIngresos, 2) }}</span>
                        </div>

                        <div class="flex justify-between items-center text-xs">
                            <span class="text-purple-700 font-medium"><i class="fas fa-university text-purple-500 mr-1.5"></i> Transferencia:</span>
                            <span class="font-semibold text-purple-700">S/ {{ number_format($this->transferenciaIngresos, 2) }}</span>
                        </div>

                        <div class="flex justify-between items-center text-xs">
                            <span class="text-gray-600 font-medium"><i class="fas fa-ellipsis-h text-gray-400 mr-1.5"></i> Otro:</span>
                            <span class="font-semibold text-gray-600">S/ {{ number_format($this->otroIngresos, 2) }}</span>
                        </div>

                        <div class="flex justify-between items-center text-xs border-t border-gray-200 pt-2 mt-1">
                            <span class="text-gray-800 font-bold"><i class="fas fa-calculator text-gray-500 mr-1.5"></i> Total Ingresos:</span>
                            <span class="font-bold text-gray-900">S/ {{ number_format($this->totalIngresos, 2) }}</span>
                        </div>
                    </div>

                    <!-- Resumen de caja (efectivo) -->
                    <div class="bg-white border border-gray-300/70 rounded-xl p-4 space-y-2.5 shadow-xs">
                        <h4 class="text-xs font-bold text-gray-700 uppercase tracking-wider mb-3">
                            <i class="fas fa-cash-register mr-1 text-red-600"></i> Arqueo de Efectivo
                        </h4>

                        <div class="flex justify-between items-center text-xs">
                            <span class="text-gray-600 font-medium"><i class="fas fa-vault text-gray-400 mr-1.5"></i> Apertura:</span>
                            <span class="font-semibold text-gray-800">S/ {{ number_format($sesion->monto_apertura, 2) }}</span>
                        </div>

                        <div class="flex justify-between items-center text-xs">
                            <span class="text-emerald-700 font-medium"><i class="fas fa-arrow-down text-emerald-500 mr-1.5"></i> + Efectivo:</span>
                            <span class="font-semibold text-emerald-700">S/ {{ number_format($this->efectivoIngresos, 2) }}</span>
                        </div>

                        <div class="flex justify-between items-center text-xs">
                            <span class="text-red-700 font-medium"><i class="fas fa-arrow-up text-red-500 mr-1.5"></i> − Egresos:</span>
                            <span class="font-semibold text-red-700">S/ {{ number_format($this->totalEgresos, 2) }}</span>
                        </div>

                        <div class="flex justify-between items-center text-xs border-t-2 border-gray-200 pt-2.5 mt-1 font-bold">
                            <span class="text-gray-800"><i class="fas fa-calculator text-blue-600 mr-1.5"></i> Efectivo Esperado:</span>
                            <span class="text-sm text-blue-700">S/ {{ number_format($this->montoEsperado, 2) }}</span>
                        </div>
                    </div>

                    <!-- Input Monto Real Contado -->
                    <div>
                        <x-label for="montoCierre" value="Monto Real Contado en Caja (S/)" class="text-gray-700 font-medium mb-1.5 text-xs uppercase tracking-wider" />

                        <div class="relative rounded-xl shadow-xs">
                            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-gray-400 font-semibold text-sm">
                                S/
                            </div>
                            <x-input id="montoCierre" type="number" step="0.01" wire:model="montoCierre"
                                     class="w-full pl-9 pr-4 py-2.5 bg-white border-gray-300 focus:border-red-500 focus:ring-red-500 rounded-xl text-gray-900 font-semibold text-base"
                                     placeholder="0.00" />
                        </div>

                        <x-input-error for="montoCierre" class="mt-1.5" />
                        <x-input-error for="general" class="mt-1.5" />
                    </div>

                    <!-- Botón Cierre de Caja -->
                    <div class="pt-2">
                        <x-button type="submit" wire:loading.attr="disabled" class="w-full justify-center bg-red-600 hover:bg-red-700 active:bg-red-800 text-white font-medium py-3 rounded-xl shadow-xs transition-all text-sm">
                            <i class="fas fa-lock mr-2" wire:loading.remove wire:target="cerrar"></i>
                            <span wire:loading.remove wire:target="cerrar">Cerrar Caja Ahora</span>
                            <span wire:loading wire:target="cerrar">Procesando Cierre...</span>
                        </x-button>
                    </div>
                </form>
            @endif
        </div>
    </div>
</div>
