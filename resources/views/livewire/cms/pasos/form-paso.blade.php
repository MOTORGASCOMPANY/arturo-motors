<div>
@if ($mostrarModal)
    <div class="fixed inset-0 bg-black/50 backdrop-blur-sm flex items-center justify-center z-50 overflow-y-auto py-8 px-4"
         x-data
         x-init="$watch('$wire.mostrarModal', v => { if(v) document.body.style.overflow = 'hidden'; else document.body.style.overflow = ''; })">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-xl max-h-[90vh] overflow-y-auto"
             style="animation: modalFadeIn 0.3s ease-out">

            <div class="sticky top-0 bg-white border-b border-gray-100 px-6 py-4 flex justify-between items-center rounded-t-2xl z-10">
                <h5 class="text-lg font-bold text-gray-900">{{ $editingId ? 'Editar' : 'Nuevo' }} Paso</h5>
                <button wire:click="cerrar" class="w-9 h-9 flex items-center justify-center rounded-lg text-gray-400 hover:text-gray-600 hover:bg-gray-100 transition-all">
                    <i class="fa-solid fa-xmark text-lg"></i>
                </button>
            </div>

            <div class="px-6 py-5">
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
                        <input type="text" class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-blue-600 focus:border-blue-600 transition-all bg-white shadow-sm" wire:model.live="title" placeholder="Ej: Diagnóstico Inicial">
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1.5 pl-3 border-l-3 border-blue-600">Número de Paso *</label>
                        <input type="text" class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-blue-600 focus:border-blue-600 transition-all bg-white shadow-sm" wire:model.live="stepNumber" placeholder="01" maxlength="10">
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1.5 pl-3 border-l-3 border-blue-600">Descripción</label>
                        <textarea class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-blue-600 focus:border-blue-600 transition-all bg-white shadow-sm"
                                  rows="3"
                                  wire:model="description"
                                  placeholder="Descripción del paso..."></textarea>
                    </div>

                    <div class="flex items-center gap-2.5">
                        <input type="checkbox" class="w-4 h-4 text-blue-600 rounded-lg focus:ring-blue-600 border-gray-300" wire:model="active">
                        <span class="text-sm font-medium text-gray-700">Activo</span>
                    </div>
                </div>
            </div>

            <div class="sticky bottom-0 bg-gray-50/80 backdrop-blur-sm border-t border-gray-200 px-6 py-4 flex justify-end gap-3 rounded-b-2xl">
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
        </div>
    </div>
@endif
</div>
