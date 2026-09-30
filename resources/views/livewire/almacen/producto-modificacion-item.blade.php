<div class="contents">
    @if ($modalEditarItemAbierto)
        @php
            $item = \App\Models\ItemSerializado::with('producto.categoria')->find($editarItemId);
            $esquema = $item?->producto->categoria->esquema_atributos ?? ['serie'];
            $campos = is_string($esquema) ? json_decode($esquema, true) : $esquema;
        @endphp

        <x-dialog-modal wire:model="modalEditarItemAbierto" maxWidth="md">
            <x-slot name="title">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-lg bg-white/15 flex items-center justify-center shrink-0">
                        <i class="fas fa-edit text-white"></i>
                    </div>
                    <div class="min-w-0 flex-1">
                        <h3 class="text-base font-bold text-white">Completar datos</h3>
                        <p class="text-sm text-slate-300 truncate">
                            {{ $item?->producto?->nombre ?? 'Item' }}
                            @if ($editarItemId) <span class="text-white/60">#{{ $editarItemId }}</span> @endif
                        </p>
                        @if ($editarItemKitInfo)
                            <div class="mt-1.5 pt-1.5 border-t border-white/10 text-xs">
                                <p class="text-slate-300">
                                    <i class="fas fa-puzzle-piece mr-1 text-indigo-300"></i>
                                    Kit: <span class="font-medium text-white">{{ $editarItemKitInfo['kit_nombre'] }}</span>
                                    @if ($editarItemKitInfo['kit_serie']) <span class="text-white/60">#{{ $editarItemKitInfo['kit_serie'] }}</span> @endif
                                </p>
                                <p class="text-slate-300 mt-0.5">
                                    <i class="fas fa-cube mr-1 text-amber-300"></i>
                                    Componente: <span class="font-medium text-white">{{ $editarItemKitInfo['componente_nombre'] }}</span>
                                    <span class="text-white/60 ml-1">({{ $editarItemKitInfo['componente_presentes'] }}/{{ $editarItemKitInfo['componente_esperados'] }})</span>
                                </p>
                            </div>
                        @endif
                    </div>
                    <button type="button" wire:click="cerrarEditarItem" aria-label="Cerrar"
                        class="w-9 h-9 flex items-center justify-center rounded-lg text-white/70 hover:text-white hover:bg-white/10 transition focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-amber-500">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </x-slot>

            <x-slot name="content">
                <div class="max-h-[65vh] overflow-y-auto space-y-4">
                    @foreach ($campos as $campo)
                        <div>
                            <label for="editar-item-{{ $campo }}" class="block text-sm font-semibold text-gray-700 mb-1">
                                {{ $campo === 'capacidad' ? 'Capacidad' : ucfirst($campo) }}
                            </label>
                            <input type="text"
                                id="editar-item-{{ $campo }}"
                                wire:model="editarItemData.{{ $campo }}"
                                wire:keydown.enter="guardarEditarItem"
                                @if ($loop->first) autofocus @endif
                                class="w-full text-sm border border-gray-300 rounded-lg px-3 py-2.5 focus:ring-2 focus:ring-amber-500 focus:border-amber-500 {{ $campo === 'serie' ? 'font-mono' : '' }}"
                                placeholder="{{ ucfirst($campo) }}"
                                maxlength="50">
                        </div>
                    @endforeach
                </div>
            </x-slot>

            <x-slot name="footer">
                <div class="flex w-full items-center gap-3">
                    <button type="button" wire:click="cerrarEditarItem"
                        class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-amber-500">
                        Cancelar
                    </button>
                    <button type="button" wire:click="guardarEditarItem" wire:loading.attr="disabled"
                        class="ml-auto px-5 py-2 text-sm font-bold text-white bg-amber-600 hover:bg-amber-700 rounded-lg transition disabled:opacity-50 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-amber-500 focus-visible:ring-offset-1">
                        <i class="fas fa-save mr-1"></i> Guardar
                    </button>
                </div>
            </x-slot>
        </x-dialog-modal>
    @endif
</div>
