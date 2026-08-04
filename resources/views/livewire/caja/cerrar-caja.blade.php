<div class="max-w-md mx-auto px-4 py-8 my-6 lg:my-10 p-6 bg-white rounded-xl shadow border border-gray-200">
    @if (!$sesion)
        <p class="text-gray-500 text-sm text-center">No hay ninguna caja abierta para cerrar.</p>
    @else
        <h3 class="text-lg font-bold text-gray-800 mb-4">Cierre de caja</h3>

        <div class="space-y-2 text-sm text-gray-700 mb-4">
            <div class="flex justify-between"><span>Monto de apertura</span><span>S/ {{ number_format($sesion->monto_apertura, 2) }}</span></div>
            <div class="flex justify-between text-emerald-600"><span>+ Ingresos</span><span>S/ {{ number_format($this->totalIngresos, 2) }}</span></div>
            <div class="flex justify-between text-red-600"><span>− Egresos</span><span>S/ {{ number_format($this->totalEgresos, 2) }}</span></div>
            <div class="flex justify-between font-bold border-t pt-2"><span>Monto esperado en caja</span><span>S/ {{ number_format($this->montoEsperado, 2) }}</span></div>
        </div>

        <div>
            <x-label for="montoCierre" value="Monto real contado en caja (S/)" />
            <x-input id="montoCierre" type="number" step="0.01" wire:model="montoCierre" class="w-full rounded-lg border-gray-300" />
            <x-input-error for="montoCierre" class="mt-1" />
            <x-input-error for="general" class="mt-1" />
        </div>

        <x-button wire:click="cerrar" type="button" class="w-full justify-center mt-4 bg-blue-600 hover:bg-blue-700 rounded-lg">
            Cerrar caja
        </x-button>
    @endif
</div>