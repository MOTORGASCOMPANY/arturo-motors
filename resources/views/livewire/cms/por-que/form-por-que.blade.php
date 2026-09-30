<div>
@if ($mostrarModal)
    <x-dialog-modal wire:model="mostrarModal" maxWidth="xl">
        <x-slot name="title">
            <div class="flex items-center justify-between gap-3">
                <h5 class="text-lg font-bold text-white">{{ $editingId ? 'Editar' : 'Nueva' }} Tarjeta</h5>
                <button wire:click="cerrar" aria-label="Cerrar"
                        class="w-9 h-9 flex items-center justify-center rounded-lg text-white/70 hover:text-white hover:bg-white/10 transition-all">
                    <i class="fa-solid fa-xmark text-lg"></i>
                </button>
            </div>
        </x-slot>

        <x-slot name="content">
                @if ($errors->any())
                    <div class="bg-red-50 border border-red-200 text-red-600 px-4 py-3 rounded-xl text-sm mb-4">
                        @foreach ($errors->all() as $error)
                            <p class="flex items-start gap-2"><i class="fa-solid fa-circle-exclamation mt-0.5 text-xs"></i>{{ $error }}</p>
                        @endforeach
                    </div>
                @endif

                <div class="space-y-5">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1.5 pl-3 border-l-3 border-blue-600">Título *</label>
                        <input type="text" class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-blue-600 focus:border-blue-600 transition-all bg-white shadow-sm" wire:model.live="title" placeholder="Ej: Garantía 100%">
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1.5 pl-3 border-l-3 border-blue-600">Descripción</label>
                        <textarea class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-blue-600 focus:border-blue-600 transition-all bg-white shadow-sm"
                                  rows="3"
                                  wire:model="description"
                                  placeholder="Descripción de la ventaja..."></textarea>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1.5 pl-3 border-l-3 border-blue-600">Ícono (opcional)</label>
                        <div class="relative" x-data="{ open: false }">
                            <button type="button" @click="open = !open" @click.outside="open = false"
                                    class="w-full flex items-center gap-3 border border-gray-200 rounded-xl px-3.5 py-2.5 bg-white hover:border-blue-400 transition-all text-left">
                                <div class="w-9 h-9 shrink-0 rounded-lg bg-blue-50 border border-blue-100 flex items-center justify-center text-blue-600 text-base">
                                    <i class="{{ $icon ?: 'fa-solid fa-image' }}"></i>
                                </div>
                                <span class="flex-1 text-sm text-gray-700 truncate">
                                    {{ $icon ? ($iconOptions[$icon] ?? 'Ícono personalizado') : 'Selecciona un ícono...' }}
                                </span>
                                <i class="fa-solid fa-chevron-down text-xs text-gray-400 transition-transform" :class="open ? 'rotate-180' : ''"></i>
                            </button>

                            <div x-show="open" x-cloak x-transition:enter="transition ease-out duration-150"
                                 x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
                                 class="absolute z-30 mt-2 w-full bg-white border border-gray-200 rounded-xl shadow-xl p-3">
                                <div class="grid grid-cols-5 sm:grid-cols-6 gap-2 max-h-56 overflow-y-auto pr-1">
                                    <button type="button" wire:click="$set('icon', '')" @click="open = false" title="Sin ícono"
                                            class="aspect-square rounded-lg border flex items-center justify-center text-sm transition-all {{ !$icon ? 'bg-blue-600 text-white border-blue-600' : 'bg-gray-50 text-gray-400 border-gray-200 hover:bg-gray-100' }}">
                                        <i class="fa-solid fa-ban"></i>
                                    </button>
                                    @foreach($iconOptions as $iconClass => $iconLabel)
                                        <button type="button" wire:click="$set('icon', '{{ $iconClass }}')" @click="open = false" title="{{ $iconLabel }}"
                                                class="aspect-square rounded-lg border flex items-center justify-center text-lg transition-all {{ $icon === $iconClass ? 'bg-blue-600 text-white border-blue-600' : 'bg-gray-50 text-gray-600 border-gray-200 hover:bg-blue-50 hover:border-blue-200 hover:text-blue-600' }}">
                                            <i class="{{ $iconClass }}"></i>
                                        </button>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="flex items-center gap-2.5">
                        <input type="checkbox" class="w-4 h-4 text-blue-600 rounded-lg focus:ring-blue-600 border-gray-300" wire:model="active">
                        <span class="text-sm font-medium text-gray-700">Activo</span>
                    </div>
                </div>
        </x-slot>

        <x-slot name="footer">
            <div class="flex items-center justify-end gap-3">
                <button type="button" class="px-5 py-2.5 rounded-xl bg-white text-gray-600 hover:bg-gray-100 font-semibold transition-all border border-gray-200 shadow-sm"
                        wire:click="cerrar">
                    Cancelar
                </button>
                <button type="button" class="px-5 py-2.5 rounded-xl bg-blue-600 text-white hover:bg-blue-700 font-semibold transition-all border border-blue-600"
                        wire:click="guardar" wire:loading.attr="disabled">
                    <span wire:loading.remove><i class="fa-solid fa-check mr-1"></i>Guardar</span>
                    <span wire:loading class="flex items-center gap-2">
                        <svg class="animate-spin h-4 w-4" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" fill="none"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>
                        Guardando...
                    </span>
                </button>
            </div>
        </x-slot>
    </x-dialog-modal>
@endif
</div>
