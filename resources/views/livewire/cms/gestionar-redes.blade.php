<div>
    <style>
        @keyframes modalFadeIn { from { opacity: 0; transform: scale(0.95) translateY(10px); } to { opacity: 1; transform: scale(1) translateY(0); } }
        @keyframes errorSlideIn { 0% { opacity: 0; transform: translateY(-20px); } 60% { transform: translateX(6px); } 80% { transform: translateX(-4px); } 100% { opacity: 1; transform: translateY(0) translateX(0); } }
        @keyframes successFlash { 0% { opacity: 0; transform: scale(0.9); } 50% { opacity: 1; transform: scale(1.02); } 100% { opacity: 1; transform: scale(1); } }
        @keyframes spin { to { transform: rotate(360deg); } }
        @keyframes emptyPulse { 0%, 100% { opacity: 1; } 50% { opacity: 0.6; } }
        @keyframes cardEntry { from { opacity: 0; transform: translateY(20px) scale(0.97); } to { opacity: 1; transform: translateY(0) scale(1); } }
        @keyframes iconSwap { 0% { transform: scale(1); } 50% { transform: scale(0.6) rotate(10deg); } 100% { transform: scale(1) rotate(0deg); } }
        @keyframes deleteShake { 0%, 100% { transform: translateX(0); } 20% { transform: translateX(-4px); } 40% { transform: translateX(4px); } 60% { transform: translateX(-2px); } 80% { transform: translateX(2px); } }
    </style>

    <div class="flex justify-between items-center mb-8 pb-4 border-b border-gray-200 m-4">
        <h4 class="text-2xl font-bold text-gray-900 flex items-center gap-3">
            <i class="fa-solid fa-share-nodes text-blue-600"></i>Redes Sociales
        </h4>
        <button class="bg-blue-600 text-white px-5 py-2.5 rounded-xl hover:bg-blue-700 font-semibold transition-all shadow-sm hover:shadow-md" wire:click="create">
            <i class="fa-solid fa-plus mr-1.5"></i>Nueva Red
        </button>
    </div>

    {{-- Transient success message --}}
    @if($successMessage)
        <div x-data="{ show: true }" x-init="setTimeout(() => { show = false; $wire.clearSuccessMessage() }, 3000)"
             x-show="show" x-transition:leave="transition ease-in duration-300" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
             class="bg-green-50 border border-green-200 text-green-700 px-5 py-3.5 rounded-xl mb-6 flex justify-between items-center shadow-sm" style="animation: successFlash 0.4s ease-out">
            <span class="flex items-center gap-2 font-medium"><i class="fa-solid fa-circle-check"></i>{{ $successMessage }}</span>
            <button @click="show = false; $wire.clearSuccessMessage()" class="text-green-500 hover:text-green-700"><i class="fa-solid fa-xmark"></i></button>
        </div>
    @endif

    @if (session()->has('success') && !$successMessage)
        <div class="bg-green-50 border border-green-200 text-green-700 px-5 py-3.5 rounded-xl mb-6 flex justify-between items-center shadow-sm" style="animation: successFlash 0.4s ease-out">
            <span class="flex items-center gap-2 font-medium"><i class="fa-solid fa-circle-check"></i>{{ session('success') }}</span>
            <button onclick="this.parentElement.remove()" class="text-green-500 hover:text-green-700"><i class="fa-solid fa-xmark"></i></button>
        </div>
    @endif

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6 m-4">
        @forelse ($links as $link)
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 hover:shadow-lg hover:-translate-y-0.5 transition-all duration-200 overflow-hidden" style="animation: cardEntry 0.4s ease-out {{ $loop->index * 0.06 }}s both">
                <div class="p-6 text-center">
                    <div class="w-14 h-14 rounded-2xl bg-blue-50 flex items-center justify-center text-blue-600 text-2xl mx-auto mb-4 border border-blue-100">
                        <i class="{{ $link['icon'] ?? 'fa-solid fa-link' }}"></i>
                    </div>
                    <h6 class="font-bold text-gray-900 capitalize mb-1">{{ $link['platform'] }}</h6>
                    <p class="text-gray-500 text-sm truncate" title="{{ $link['url'] }}">{{ $link['url'] }}</p>
                    <span class="inline-block mt-3 text-xs font-semibold px-3 py-1.5 rounded-full {{ $link['is_active'] ? 'bg-green-50 text-green-600 border border-green-200' : 'bg-gray-50 text-gray-400 border border-gray-200' }}">
                        {{ $link['is_active'] ? 'Activa' : 'Inactiva' }}
                    </span>
                </div>
                <div class="border-t border-gray-100 bg-gray-50/80 px-4 py-3.5 flex justify-center gap-2">
                    <button class="w-9 h-9 flex items-center justify-center rounded-lg bg-amber-50 border border-amber-200 text-amber-600 hover:bg-amber-100 transition-all" wire:click="edit({{ $link['id'] }})">
                        <i class="fa-solid fa-pen text-xs"></i>
                    </button>
                    <button class="w-9 h-9 flex items-center justify-center rounded-lg border transition-all {{ $link['is_active'] ? 'bg-gray-50 border-gray-200 text-gray-400 hover:bg-gray-100' : 'bg-green-50 border-green-200 text-green-600 hover:bg-green-100' }}" wire:click="toggleActive({{ $link['id'] }})">
                        <i class="fa-solid fa-{{ $link['is_active'] ? 'eye-slash' : 'eye' }} text-xs"></i>
                    </button>
                    <button class="w-9 h-9 flex items-center justify-center rounded-lg bg-red-50 border border-red-200 text-red-500 hover:bg-red-100 transition-all"
                            onclick="window.dispatchEvent(new CustomEvent('confirm-modal:show', { detail: { title: 'Eliminar red social', message: '¿Seguro que querés eliminar esta red social? Esta acción no se puede deshacer.', action: { componentId: $wire.__instance.id, method: 'delete', params: [{{ $link['id'] }}] } } }))">
                        <i class="fa-solid fa-trash text-xs"></i>
                    </button>
                </div>
            </div>
        @empty
            <div class="sm:col-span-2 lg:col-span-3 xl:col-span-4 bg-white rounded-2xl shadow-sm border border-gray-100 p-16 text-center" style="animation: emptyPulse 3s ease-in-out infinite">
                <div class="w-16 h-16 rounded-2xl bg-blue-50 flex items-center justify-center mx-auto mb-4 border border-blue-100">
                    <i class="fa-solid fa-share-nodes text-2xl text-blue-400"></i>
                </div>
                <p class="text-gray-500 font-medium mb-1">No hay redes sociales configuradas</p>
                <p class="text-gray-400 text-sm">Agregá las redes para mostrar en el landing</p>
            </div>
        @endforelse
    </div>

    @if($showForm)
        <div class="fixed inset-0 bg-black/50 backdrop-blur-sm flex items-start justify-center z-50 overflow-y-auto py-8 px-4">
            <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg my-auto" style="animation: modalFadeIn 0.3s ease-out">
                <div class="sticky top-0 bg-white border-b border-gray-100 px-6 py-4 flex justify-between items-center rounded-t-2xl z-10">
                    <h5 class="text-lg font-bold text-gray-900">{{ $editingId ? 'Editar' : 'Nueva' }} Red Social</h5>
                    <button wire:click="resetForm" class="w-9 h-9 flex items-center justify-center rounded-lg text-gray-400 hover:text-gray-600 hover:bg-gray-100 transition-all">
                        <i class="fa-solid fa-xmark text-lg"></i>
                    </button>
                </div>
                <div class="px-6 py-5 space-y-4">
                    @if ($errors->any())
                        <div class="bg-red-50 border border-red-200 text-red-600 px-4 py-3 rounded-xl text-sm" style="animation: errorSlideIn 0.4s ease-out">
                            @foreach ($errors->all() as $error)
                                <p class="flex items-start gap-2"><i class="fa-solid fa-circle-exclamation mt-0.5 text-xs"></i>{{ $error }}</p>
                            @endforeach
                        </div>
                    @endif
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1.5">Plataforma *</label>
                            <select class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all" wire:model.live="platform">
                                @foreach($platforms as $key => $label)
                                    <option value="{{ $key }}">{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div x-data="{ prevIcon: '' }"
                             x-effect="if(icon !== prevIcon) { prevIcon = icon; }">
                            <label class="block text-sm font-semibold text-gray-700 mb-1.5">Ícono</label>
                            <div class="flex items-center gap-2 h-11">
                                <div class="w-11 h-11 rounded-lg bg-blue-50 flex items-center justify-center text-blue-600 text-lg border border-blue-100 shrink-0"
                                     x-data="{ icon: @entangle('icon') }"
                                     :style="icon !== prevIcon ? 'animation: iconSwap 0.3s ease-out' : ''">
                                    <i :class="icon || 'fa-solid fa-cog'"></i>
                                </div>
                                <x-icon-picker model="icon" label="" />
                            </div>
                        </div>
                    </div>
                    <div x-data="{ urlPreview: @entangle('url') }">
                        <label class="block text-sm font-semibold text-gray-700 mb-1.5">URL *</label>
                        <input type="url" class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all" wire:model="url" placeholder="https://facebook.com/tupagina"
                               @input="urlPreview = $el.value">
                        @if($url)
                            <div class="mt-2 flex items-center gap-2 p-2 bg-gray-50 rounded-lg border border-gray-100">
                                <img :src="'https://www.google.com/s2/favicons?domain=' + encodeURIComponent(url) + '&sz=32'" class="w-4 h-4" onerror="this.style.display='none'">
                                <span class="text-xs text-gray-400 truncate" x-text="url"></span>
                                <a :href="url" target="_blank" class="text-blue-500 hover:text-blue-700 ml-auto"><i class="fa-solid fa-external-link text-[10px]"></i></a>
                            </div>
                        @endif
                    </div>
                    <div>
                        <label class="flex items-center gap-2.5 cursor-pointer">
                            <input type="checkbox" class="w-4 h-4 text-blue-600 rounded-lg focus:ring-blue-500 border-gray-300" wire:model="active">
                            <span class="text-sm font-medium text-gray-700">Activa</span>
                        </label>
                    </div>
                </div>
                <div class="sticky bottom-0 bg-white border-t border-gray-100 px-6 py-4 flex justify-end gap-3 rounded-b-2xl">
                    <button type="button" class="px-5 py-2.5 rounded-xl bg-gray-100 text-gray-600 hover:bg-gray-200 font-semibold transition-all" wire:click="resetForm">Cancelar</button>
                    <button type="button" class="px-5 py-2.5 rounded-xl bg-blue-600 text-white hover:bg-blue-700 font-semibold transition-all shadow-sm relative overflow-hidden"
                            wire:click="save"
                            wire:loading.attr="disabled"
                            wire:target="save">
                        <span wire:loading.remove wire:target="save"><i class="fa-solid fa-check mr-1"></i>Guardar</span>
                        <span wire:loading wire:target="save" class="flex items-center gap-2">
                            <svg class="animate-spin h-4 w-4" style="animation: spin 1s linear infinite" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" fill="none"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>
                            Guardando...
                        </span>
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>
