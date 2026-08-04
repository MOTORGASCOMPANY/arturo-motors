<div class="max-w-md mx-auto px-4 py-8 my-6 lg:my-10 p-6 bg-white rounded-xl shadow border border-gray-200">
    @if ($sesionActiva)
        <div class="text-center space-y-2">
            <div class="w-14 h-14 bg-emerald-100 text-emerald-600 rounded-full flex items-center justify-center mx-auto">
                <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
            </div>
            <h3 class="text-lg font-bold text-gray-800">Caja abierta</h3>
            <p class="text-sm text-gray-500">
                Abierta por {{ $sesionActiva->abiertaPor->name }}<br>
                desde {{ $sesionActiva->abierta_en->format('d/m/Y H:i') }}
            </p>
            <p class="text-sm text-gray-700">Monto inicial: S/ {{ number_format($sesionActiva->monto_apertura, 2) }}</p>
        </div>
    @else
        <h3 class="text-lg font-bold text-gray-800 mb-4">Abrir caja</h3>
        <div class="space-y-3">
            <div>
                <x-label for="montoApertura" value="Monto inicial en caja (S/)" />
                <x-input id="montoApertura" type="number" step="0.01" wire:model="montoApertura"
                         class="w-full rounded-lg border-gray-300" />
                <x-input-error for="montoApertura" class="mt-1" />
                <x-input-error for="general" class="mt-1" />
            </div>
            <x-button wire:click="abrir" type="button" class="w-full justify-center bg-blue-600 hover:bg-blue-700 rounded-lg">
                Abrir caja
            </x-button>
        </div>
    @endif
</div>