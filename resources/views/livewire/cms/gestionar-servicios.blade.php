<div>
    <style>
        @keyframes modalFadeIn { from { opacity: 0; transform: scale(0.95) translateY(10px); } to { opacity: 1; transform: scale(1) translateY(0); } }
        @keyframes errorSlideIn { 0% { opacity: 0; transform: translateY(-20px); } 60% { transform: translateX(6px); } 80% { transform: translateX(-4px); } 100% { opacity: 1; transform: translateY(0) translateX(0); } }
        @keyframes successFlash { 0% { opacity: 0; transform: scale(0.9); } 50% { opacity: 1; transform: scale(1.02); } 100% { opacity: 1; transform: scale(1); } }
        @keyframes spin { to { transform: rotate(360deg); } }
        @keyframes emptyPulse { 0%, 100% { opacity: 1; } 50% { opacity: 0.6; } }
        @keyframes cardEntry { from { opacity: 0; transform: translateY(20px) scale(0.97); } to { opacity: 1; transform: translateY(0) scale(1); } }
        @keyframes deleteShake { 0%, 100% { transform: translateX(0); } 20% { transform: translateX(-4px); } 40% { transform: translateX(4px); } 60% { transform: translateX(-2px); } 80% { transform: translateX(2px); } }
    </style>

    <div class="flex justify-between items-center mb-8 pb-4 border-b border-gray-200 m-4">
        <h4 class="text-2xl font-bold text-gray-900 flex items-center gap-3">
            <i class="fa-solid fa-wrench text-blue-600"></i>Servicios del Landing
        </h4>
        <button class="bg-blue-600 text-white px-5 py-2.5 rounded-xl hover:bg-blue-700 font-semibold transition-all shadow-sm hover:shadow-md" wire:click="create">
            <i class="fa-solid fa-plus mr-1.5"></i>Nuevo Servicio
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

    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6 m-4">
        @forelse ($services as $service)
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 hover:shadow-lg hover:-translate-y-0.5 transition-all duration-200 overflow-hidden flex flex-col" style="animation: cardEntry 0.4s ease-out {{ $loop->index * 0.06 }}s both">
                <div class="p-6 flex-1">
                    <div class="flex items-start justify-between mb-4">
                        <div class="w-12 h-12 rounded-xl bg-blue-50 flex items-center justify-center text-blue-600 text-xl border border-blue-100">
                            <i class="{{ $service['icon'] ?? 'fa-solid fa-cog' }}"></i>
                        </div>
                        <span class="text-xs font-semibold px-3 py-1 rounded-full {{ $service['is_active'] ? 'bg-green-50 text-green-600 border border-green-200' : 'bg-gray-50 text-gray-400 border border-gray-200' }}">
                            {{ $service['is_active'] ? 'Activo' : 'Inactivo' }}
                        </span>
                    </div>
                    <h6 class="font-bold text-gray-900 text-lg mb-2">{{ $service['title'] }}</h6>
                    <p class="text-gray-500 text-sm leading-relaxed mb-3">{{ Str::limit($service['description'], 100) }}</p>
                    @if($service['features'])
                        <div class="flex flex-wrap gap-1.5">
                            @foreach(array_slice($service['features'], 0, 3) as $f)
                                <span class="bg-gray-50 text-gray-600 text-xs px-2.5 py-1 rounded-lg border border-gray-100">{{ $f }}</span>
                            @endforeach
                            @if(count($service['features']) > 3)
                                <span class="text-gray-400 text-xs">+{{ count($service['features']) - 3 }}</span>
                            @endif
                        </div>
                    @endif
                </div>
                <div class="border-t border-gray-100 bg-gray-50/80 px-6 py-3.5 flex items-center justify-between">
                    <div class="flex gap-2">
                        <button class="w-9 h-9 flex items-center justify-center rounded-lg bg-white border border-gray-200 text-gray-400 hover:text-gray-600 hover:border-gray-300 transition-all" wire:click="moveUp({{ $service['id'] }})" title="Subir">
                            <i class="fa-solid fa-arrow-up text-xs"></i>
                        </button>
                        <button class="w-9 h-9 flex items-center justify-center rounded-lg bg-white border border-gray-200 text-gray-400 hover:text-gray-600 hover:border-gray-300 transition-all" wire:click="moveDown({{ $service['id'] }})" title="Bajar">
                            <i class="fa-solid fa-arrow-down text-xs"></i>
                        </button>
                    </div>
                    <div class="flex gap-2">
                        <button class="w-9 h-9 flex items-center justify-center rounded-lg bg-amber-50 border border-amber-200 text-amber-600 hover:bg-amber-100 transition-all" wire:click="edit({{ $service['id'] }})">
                            <i class="fa-solid fa-pen text-xs"></i>
                        </button>
                        <button class="w-9 h-9 flex items-center justify-center rounded-lg border transition-all {{ $service['is_active'] ? 'bg-gray-50 border-gray-200 text-gray-400 hover:bg-gray-100' : 'bg-green-50 border-green-200 text-green-600 hover:bg-green-100' }}" wire:click="toggleActive({{ $service['id'] }})">
                            <i class="fa-solid fa-{{ $service['is_active'] ? 'eye-slash' : 'eye' }} text-xs"></i>
                        </button>
                        <button class="w-9 h-9 flex items-center justify-center rounded-lg bg-red-50 border border-red-200 text-red-500 hover:bg-red-100 transition-all"
                                onclick="window.dispatchEvent(new CustomEvent('confirm-modal:show', { detail: { title: 'Eliminar servicio', message: '¿Seguro que querés eliminar este servicio? Esta acción no se puede deshacer.', action: { componentId: $wire.__instance.id, method: 'delete', params: [{{ $service['id'] }}] } } }))">
                            <i class="fa-solid fa-trash text-xs"></i>
                        </button>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-span-full bg-white rounded-2xl shadow-sm border border-gray-100 p-16 text-center" style="animation: emptyPulse 3s ease-in-out infinite">
                <div class="w-16 h-16 rounded-2xl bg-blue-50 flex items-center justify-center mx-auto mb-4 border border-blue-100">
                    <i class="fa-solid fa-wrench text-2xl text-blue-400"></i>
                </div>
                <p class="text-gray-500 font-medium mb-1">No hay servicios creados</p>
                <p class="text-gray-400 text-sm">Creá el primer servicio para mostrar en el landing</p>
            </div>
        @endforelse
    </div>

    @if($showForm)
        <div class="fixed inset-0 bg-black/50 backdrop-blur-sm flex items-start justify-center z-50 overflow-y-auto py-8 px-4">
            <div class="bg-white rounded-2xl shadow-2xl w-full max-w-2xl my-auto" style="animation: modalFadeIn 0.3s ease-out">
                <div class="sticky top-0 bg-white border-b border-gray-100 px-6 py-4 flex justify-between items-center rounded-t-2xl z-10">
                    <h5 class="text-lg font-bold text-gray-900">{{ $editingId ? 'Editar' : 'Nuevo' }} Servicio</h5>
                    <button wire:click="resetForm" class="w-9 h-9 flex items-center justify-center rounded-lg text-gray-400 hover:text-gray-600 hover:bg-gray-100 transition-all">
                        <i class="fa-solid fa-xmark text-lg"></i>
                    </button>
                </div>
                <div class="px-6 py-5">
                    @if ($errors->any())
                        <div class="bg-red-50 border border-red-200 text-red-600 px-4 py-3 rounded-xl text-sm mb-4" style="animation: errorSlideIn 0.4s ease-out">
                            @foreach ($errors->all() as $error)
                                <p class="flex items-start gap-2"><i class="fa-solid fa-circle-exclamation mt-0.5 text-xs"></i>{{ $error }}</p>
                            @endforeach
                        </div>
                    @endif
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <div class="md:col-span-2">
                            <label class="block text-sm font-semibold text-gray-700 mb-1.5">Título *</label>
                            <input type="text" class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all" wire:model.live="title" placeholder="Ej: Conversión a GNV / GLP">
                            <p class="text-xs text-gray-400 mt-1.5">El ícono se asigna automáticamente según el título</p>
                        </div>
                        <div>
                            <x-icon-picker model="icon" label="Ícono" />
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1.5">Texto del Botón CTA</label>
                            <input type="text" class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all" wire:model="ctaText" placeholder="Cotizar Conversión">
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-sm font-semibold text-gray-700 mb-1.5">Descripción</label>
                            <textarea class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all" rows="3" wire:model="description"></textarea>
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-sm font-semibold text-gray-700 mb-1.5">Features (una por línea)</label>
                            <textarea class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all" rows="3" wire:model="features" placeholder="Equipos italianas&#10;Garantía 1 año&#10;Certificación inicial"></textarea>
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-sm font-semibold text-gray-700 mb-1.5">Link del Botón (WhatsApp)</label>
                            <input type="text" class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all" wire:model="ctaLink" placeholder="https://wa.me/51943694464?text=...">
                        </div>
                        <div class="md:col-span-2">
                            <label class="flex items-center gap-2.5 cursor-pointer">
                                <input type="checkbox" class="w-4 h-4 text-blue-600 rounded-lg focus:ring-blue-500 border-gray-300" wire:model="active">
                                <span class="text-sm font-medium text-gray-700">Activo</span>
                            </label>
                        </div>
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
