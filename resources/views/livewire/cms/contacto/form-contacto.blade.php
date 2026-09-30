<div>
@if ($mostrarModal)
    <x-dialog-modal wire:model="mostrarModal" maxWidth="lg">
        <x-slot name="title">
            <div class="flex items-center justify-between gap-3">
                <h5 class="text-lg font-bold text-white">{{ $editingId ? 'Editar' : 'Nuevo' }} Contacto</h5>
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

            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">Tipo *</label>
                    <select class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-blue-600 focus:border-blue-600 transition-all bg-white shadow-sm"
                            wire:model.live="type">
                        @foreach($types as $key => $label)
                            <option value="{{ $key }}">{{ $label }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">Etiqueta *</label>
                    <input type="text" class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-blue-600 focus:border-blue-600 transition-all bg-white shadow-sm"
                           wire:model="label" placeholder="Ej: Dirección principal">
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">Valor *</label>
                    @if($type === 'map_iframe')
                        <textarea class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-blue-600 focus:border-blue-600 transition-all bg-white shadow-sm"
                                  rows="3" wire:model="value" placeholder="<iframe src=...></iframe>"></textarea>
                    @else
                        <input type="text" class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-blue-600 focus:border-blue-600 transition-all bg-white shadow-sm"
                               wire:model="value" placeholder="Valor del contacto">
                    @endif
                </div>

                <div>
                    <label class="flex items-center gap-2.5 cursor-pointer">
                        <input type="checkbox" class="w-4 h-4 text-blue-600 rounded-lg focus:ring-blue-600 border-gray-300" wire:model="active">
                        <span class="text-sm font-medium text-gray-700">Activo</span>
                    </label>
                </div>
            </div>
        </x-slot>

        <x-slot name="footer">
            <div class="flex items-center justify-end gap-3">
                <button type="button" class="px-5 py-2.5 rounded-xl bg-white text-gray-600 hover:bg-gray-50 font-semibold transition-all border border-gray-200 shadow-sm" wire:click="cerrar">Cancelar</button>
                <button type="button" class="px-5 py-2.5 rounded-xl bg-blue-600 text-white hover:bg-blue-700 font-semibold transition-all border border-blue-600"
                        wire:click="guardar"
                        wire:loading.attr="disabled">
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
