<div>
    <x-dialog-modal wire:model="mostrarModal" maxWidth="lg">
        <x-slot name="title">
            Abrir kit — {{ $kitItem?->producto->nombre }} ({{ $kitItem?->serie }})
        </x-slot>

        <x-slot name="content">
            @if ($componentesKit)
                <x-input-error for="general" class="mb-3" />

                <p class="text-xs text-gray-500 mb-3">Al confirmar, estos componentes pasarán al stock de Arturo Motors.</p>

                <div class="space-y-3">
                    @foreach ($componentesKit as $kc)
                        <div class="bg-gray-50 rounded-lg px-3 py-2">
                            @if ($kc->componente->categoria->es_serializado)
                                <x-label :value="$kc->componente->nombre . ' — número de serie'" />
                                <x-input wire:model="seriesComponentes.{{ $kc->producto_componente_id }}"
                                         placeholder="Serie del {{ strtolower($kc->componente->nombre) }}"
                                         class="w-full rounded-lg border-gray-300 text-sm mt-1" />
                                <x-input-error for="seriesComponentes.{{ $kc->producto_componente_id }}" class="mt-1" />
                            @else
                                <div class="flex justify-between text-sm">
                                    <span>{{ $kc->componente->nombre }}</span>
                                    <span class="text-gray-500">+{{ $kc->cantidad_esperada }} al stock</span>
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>
            @endif
        </x-slot>

        <x-slot name="footer">
            <x-secondary-button wire:click="cerrar">Cancelar</x-secondary-button>
            <x-button wire:click="confirmarApertura" wire:loading.attr="disabled" class="ml-2">Confirmar apertura</x-button>
        </x-slot>
    </x-dialog-modal>
</div>