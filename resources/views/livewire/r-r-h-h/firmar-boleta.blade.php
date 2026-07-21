<div>
    <x-dialog-modal wire:model="abierto">
        <x-slot name="title">
                <h3 class="text-xl font-bold">Recepción de Boleta</h3>
        </x-slot>

        <x-slot name="content">
            <div class="space-y-4">
                <div class="bg-gray-50 p-2 rounded-xl border border-gray-100">
                    <p class="text-xs font-bold text-gray-400 uppercase mb-1">Empleado</p>
                    <p class="text-lg font-bold text-gray-800">{{ Auth::user()->name ?? null}}</p>
                    <p class="text-sm text-gray-500 italic">Documento: {{ Auth::user()->dni ?? null }}</p>
                </div>

                <div class="bg-indigo-50/50 p-6 rounded-xl border border-indigo-100 relative overflow-hidden">
                    <div class="flex items-start space-x-4 relative z-10">
                        <div class="pt-1">
                            <input type="checkbox" wire:model.live="confirmacion"
                                   class="w-5 h-5 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500 cursor-pointer">
                        </div>
                        <div>
                            <p class="font-bold text-indigo-900 text-base">
                                "Recibí conforme, acepto y autorizo mi firma digital."
                            </p>
                            <p class="text-sm text-indigo-700 mt-1">
                                Al marcar esta casilla, usted declara la conformidad de la boleta de pago recibida.
                            </p>
                        </div>
                    </div>
                    <i class="fas fa-file-signature absolute -right-4 -bottom-4 text-6xl text-indigo-100/50"></i>
                </div>
            </div>
        </x-slot>

        <x-slot name="footer">
            <div class="flex space-x-3">
                <x-secondary-button wire:click="$set('abierto', false)">
                    CANCELAR
                </x-secondary-button>

                <button wire:click="procesarFirma" @disabled(!$confirmacion)
                        class="px-6 py-2 bg-slate-800 hover:bg-slate-900 text-white rounded-lg font-bold transition disabled:opacity-50 disabled:cursor-not-allowed flex items-center shadow-lg">
                    <i class="fas fa-fingerprint mr-2"></i>
                    FIRMAR DIGITALMENTE
                </button>
            </div>
        </x-slot>
    </x-dialog-modal>
</div>
