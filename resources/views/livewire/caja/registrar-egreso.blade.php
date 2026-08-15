<div class="max-w-md mx-auto px-4 py-8 my-6 lg:my-10 p-6 bg-white rounded-xl shadow border border-gray-200">
    <h3 class="text-lg font-bold text-gray-800 mb-4">Registrar egreso de caja</h3>

    @if (session('mensaje'))
        <div class="mb-3 p-2 bg-emerald-50 text-emerald-700 text-sm rounded-lg">{{ session('mensaje') }}</div>
    @endif

    <div class="space-y-3">
        <div>
            <x-label for="monto" value="Monto (S/)" />
            <x-input id="monto" type="number" step="0.01" wire:model="monto" class="w-full rounded-lg border-gray-300" />
            <x-input-error for="monto" class="mt-1" />
        </div>
        <div>
            <x-label for="concepto" value="Concepto / motivo" />
            <x-input id="concepto" type="text" wire:model="concepto" placeholder="Ej: Compra de útiles de oficina" class="w-full rounded-lg border-gray-300" />
            <x-input-error for="concepto" class="mt-1" />
        </div>
        <x-input-error for="general" class="mt-1" />
        <x-button wire:click="registrar" type="button" class="w-full justify-center bg-red-600 hover:bg-red-700 rounded-lg">
            Registrar egreso
        </x-button>
    </div>
</div>