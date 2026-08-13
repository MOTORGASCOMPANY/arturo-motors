<div class="max-w-md mx-auto px-4 py-8 my-6 lg:my-10">
    <div class="bg-gray-200 rounded-2xl shadow-sm border border-gray-300/80 overflow-hidden transition-all">
        <!-- Encabezado del Tarjetón -->
        <div class="p-6 border-b border-gray-300/70">
            <h2 class="text-xl font-bold tracking-tight text-gray-800 flex items-center gap-2.5">
                <i class="fas fa-cash-register text-blue-600"></i>
                 Apertura de Caja
            </h2>
            <p class="text-xs text-gray-600 mt-1">Gestión de inicio de turno para operaciones diarias.</p>
        </div>

        <!-- Contenido principal -->
        <div class="p-6 bg-white/60">
            @if ($sesionActiva)
                <!-- ESTADO: CAJA ABIERTA -->
                <div class="text-center space-y-4 py-2">
                    <div class="w-16 h-16 bg-emerald-100/80 text-emerald-600 rounded-full flex items-center justify-center mx-auto ring-8 ring-emerald-50">
                        <i class="fas fa-check text-2xl"></i>
                    </div>

                    <div class="space-y-1">
                        <h3 class="text-lg font-bold text-gray-800">Caja Actual Abierta</h3>
                        <p class="text-xs text-gray-600">Actualmente hay una sesión activa en el sistema.</p>
                    </div>

                    <div class="bg-white border border-gray-300/70 rounded-xl p-4 text-left space-y-2.5 shadow-xs">
                        <div class="flex justify-between items-center text-xs">
                            <span class="text-gray-500 font-medium"><i class="fas fa-user text-gray-400 mr-1.5"></i> Responsable:</span>
                            <span class="font-semibold text-gray-800">{{ $sesionActiva->abiertaPor->name ?? 'Usuario' }}</span>
                        </div>
                        <div class="flex justify-between items-center text-xs border-t border-gray-100 pt-2">
                            <span class="text-gray-500 font-medium"><i class="fas fa-clock text-gray-400 mr-1.5"></i> Apertura:</span>
                            <span class="font-semibold text-gray-800">{{ $sesionActiva->abierta_en ? $sesionActiva->abierta_en->format('d/m/Y H:i') : '-' }}</span>
                        </div>
                        <div class="flex justify-between items-center text-xs border-t border-gray-100 pt-2">
                            <span class="text-gray-500 font-medium"><i class="fas fa-coins text-gray-400 mr-1.5"></i> Monto Inicial:</span>
                            <span class="font-bold text-emerald-700 text-sm">S/ {{ number_format($sesionActiva->monto_apertura, 2) }}</span>
                        </div>
                    </div>
                </div>
            @else
                <!-- FORMULARIO: ABRIR CAJA -->
                <form wire:submit.prevent="abrir" class="space-y-5">
                    <div>
                        <x-label for="montoApertura" value="Monto Inicial en Caja (S/)" class="text-gray-700 font-medium mb-1.5 text-xs uppercase tracking-wider" />
                        
                        <div class="relative rounded-xl shadow-xs">
                            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-gray-400 font-semibold text-sm">
                                S/
                            </div>
                            <x-input id="montoApertura" type="number" step="0.01" wire:model="montoApertura"
                                     class="w-full pl-9 pr-4 py-2.5 bg-white border-gray-300 focus:border-blue-500 focus:ring-blue-500 rounded-xl text-gray-900 font-semibold text-base" 
                                     placeholder="0.00" />
                        </div>

                        <x-input-error for="montoApertura" class="mt-1.5" />
                        <x-input-error for="general" class="mt-1.5" />
                    </div>

                    <div class="pt-2">
                        <x-button type="submit" wire:loading.attr="disabled" class="w-full justify-center bg-blue-600 hover:bg-blue-700 active:bg-blue-800 text-white font-medium py-3 rounded-xl shadow-xs transition-all text-sm">
                            <i class="fas fa-key mr-2" wire:loading.remove wire:target="abrir"></i>
                            <span wire:loading.remove wire:target="abrir">Abrir Caja Ahora</span>
                            <span wire:loading wire:target="abrir">Procesando...</span>
                        </x-button>
                    </div>
                </form>
            @endif
        </div>
    </div>
</div>