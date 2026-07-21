<!-- resources/views/livewire/profile/update-signature-form.blade.php -->
<x-action-section>
    <x-slot name="title">Firma Digital</x-slot>
    <x-slot name="description">Cargue su firma para procesos de firma digital posteriores.</x-slot>

    <x-slot name="content">
        <div class="col-span-6 shadow p-4 rounded-lg bg-gray-50">
            <div class="mt-2">
                @if ($firma)
                    <p class="text-xs font-semibold text-orange-500 mb-1 uppercase">Vista previa de la nueva firma:</p>
                    <img src="{{ $firma->temporaryUrl() }}" class="h-32 border-2 border-dashed border-orange-300 p-1 rounded">
                @elseif ($user->ruta_firma)
                    <p class="text-xs font-semibold text-indigo-600 mb-1 uppercase">Firma actual registrada:</p>
                    <img src="{{ Storage::url($user->ruta_firma) }}" class="h-32 border p-1 rounded bg-white">
                @else
                    <div class="h-32 flex items-center justify-center border-2 border-dashed rounded text-gray-400">
                        Sin firma registrada
                    </div>
                @endif
            </div>

            <form wire:submit.prevent="saveSignature" class="mt-4">
                <input type="file" wire:model="firma" id="firma_input" class="hidden" accept="image/png,image/jpeg">

                <div class="flex gap-2">
                    <x-secondary-button type="button" onclick="document.getElementById('firma_input').click()">
                        {{ __('Seleccionar Firma') }}
                    </x-secondary-button>

                    @if ($firma)
                        <x-button class="bg-indigo-600">
                            {{ __('Confirmar y Guardar') }}
                        </x-button>
                    @endif
                </div>

                <x-input-error for="firma" class="mt-2" />
            </form>
        </div>
    </x-slot>
</x-action-section>
