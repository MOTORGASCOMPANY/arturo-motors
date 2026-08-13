<div class="max-w-md mx-auto px-4 py-8 my-6 lg:my-10">
    <div class="bg-gray-200 rounded-2xl shadow-sm border border-gray-300/80 overflow-hidden transition-all">
        <!-- Encabezado del Tarjetón -->
        <div class="p-6 border-b border-gray-300/70">
            <h2 class="text-xl font-bold tracking-tight text-gray-800 flex items-center gap-2.5">
                <i class="fas fa-arrow-circle-up text-red-600"></i>
                Registrar Egreso
            </h2>
            <p class="text-xs text-gray-600 mt-1">Salida manual de dinero o pago desde la caja chica.</p>
        </div>

        <!-- Contenido principal -->
        <div class="p-6 bg-white/60">
            @if (!$sesionActiva)
                <!-- ESTADO: SIN CAJA ABIERTA -->
                <div class="text-center space-y-3 py-4">
                    <div class="w-16 h-16 bg-gray-300/60 text-gray-500 rounded-full flex items-center justify-center mx-auto ring-8 ring-gray-200/50">
                        <i class="fas fa-inbox text-2xl"></i>
                    </div>
                    <div class="space-y-1">
                        <h3 class="text-base font-bold text-gray-700">Caja Cerrada</h3>
                        <p class="text-xs text-gray-500">Debe abrir una sesión de caja antes de registrar salidas de dinero.</p>
                    </div>
                </div>
            @else
                <!-- FORMULARIO DE EGRESO -->
                <form wire:submit.prevent="registrar" class="space-y-4">
                    <!-- Campo Monto -->
                    <div>
                        <x-label for="monto" value="Monto del Egreso (S/)" class="text-gray-700 font-medium mb-1.5 text-xs uppercase tracking-wider" />
                        
                        <div class="relative rounded-xl shadow-xs">
                            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-gray-400 font-semibold text-sm">
                                S/
                            </div>
                            <x-input id="monto" type="number" step="0.01" wire:model="monto"
                                     class="w-full pl-9 pr-4 py-2.5 bg-white border-gray-300 focus:border-red-500 focus:ring-red-500 rounded-xl text-gray-900 font-semibold text-base" 
                                     placeholder="0.00" />
                        </div>

                        <x-input-error for="monto" class="mt-1.5" />
                    </div>

                    <!-- Campo Concepto -->
                    <div>
                        <x-label for="concepto" value="Concepto / Motivo" class="text-gray-700 font-medium mb-1.5 text-xs uppercase tracking-wider" />
                        <x-input id="concepto" type="text" wire:model="concepto"
                                 class="w-full px-3.5 py-2.5 bg-white border-gray-300 focus:border-red-500 focus:ring-red-500 rounded-xl text-gray-900 text-sm"
                                 placeholder="Ej: Compra de útiles de oficina" />
                        <x-input-error for="concepto" class="mt-1.5" />
                    </div>

                    <x-input-error for="general" class="mt-1.5" />

                    <!-- Botón de Registro -->
                    <div class="pt-2">
                        <x-button type="submit" wire:loading.attr="disabled" class="w-full justify-center bg-red-600 hover:bg-red-700 active:bg-red-800 text-white font-medium py-3 rounded-xl shadow-xs transition-all text-sm">
                            <i class="fas fa-minus-circle mr-2" wire:loading.remove wire:target="registrar"></i>
                            <span wire:loading.remove wire:target="registrar">Registrar Egreso</span>
                            <span wire:loading wire:target="registrar">Guardando Egreso...</span>
                        </x-button>
                    </div>
                </form>
            @endif
        </div>
    </div>
</div>