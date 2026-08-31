<x-cms.layout
    title="Servicios del Landing"
    description="Administra los servicios que se muestran en el landing page"
    headerIcon='<i class="fa-solid fa-wrench text-blue-600"></i>'
>
    {{-- Header with Create Button --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-8 pb-4 border-b border-gray-200">
        <div>
            <h2 class="text-xl font-semibold text-gray-900">{{ count($services) ?? 0 }} servicios</h2>
            <p class="text-gray-500 text-sm">Gestiona los servicios que ofrece Arturo Motors</p>
        </div>
        <button wire:click="create"
                class="w-full sm:w-auto px-5 py-2.5 rounded-xl bg-blue-600 text-white font-semibold shadow-sm hover:bg-blue-700 hover:shadow-md transition-all flex items-center justify-center gap-2">
            <i class="fa-solid fa-plus"></i> Nuevo Servicio
        </button>
    </div>

    {{-- Success Message --}}
    @if($successMessage)
        <div x-data="{ show: true }"
             x-show="show"
             x-transition:leave="transition ease-in duration-300"
             x-init="setTimeout(() => { show = false; @this.call('clearSuccessMessage') }, 3000)"
             class="mb-6 flex items-center gap-3 p-4 rounded-xl bg-green-50 border border-green-200 text-green-700 shadow-sm animate-slide-down">
            <i class="fa-solid fa-circle-check text-lg"></i>
            <span class="flex-1 font-medium">{{ $successMessage }}</span>
            <button @click="show = false; @this.call('clearSuccessMessage')" class="text-green-500 hover:opacity-70"><i class="fa-solid fa-xmark"></i></button>
        </div>
    @endif

    @if (session()->has('success') && !$successMessage)
        <div x-data="{ show: true }"
             x-show="show"
             x-transition:leave="transition ease-in duration-300"
             x-init="setTimeout(() => { show = false }, 3000)"
             class="mb-6 flex items-center gap-3 p-4 rounded-xl bg-green-50 border border-green-200 text-green-700 shadow-sm animate-slide-down">
            <i class="fa-solid fa-circle-check text-lg"></i>
            <span class="flex-1 font-medium">{{ session('success') }}</span>
            <button @click="show = false" class="text-green-500 hover:opacity-70"><i class="fa-solid fa-xmark"></i></button>
        </div>
    @endif

    {{-- Services Grid --}}
    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
        @forelse ($services as $service)
            <x-cms.card
                wire:key="service-{{ $service['id'] }}"
                x-data="{ highlight: false }"
                x-on:service-moved.window="if ($event.detail.id === {{ $service['id'] }}) { highlight = true; setTimeout(() => highlight = false, 650) }"
                :class="{ 'ring-2 ring-blue-400 shadow-lg scale-[1.015]': highlight }"
                class="flex flex-col h-full group transition-all duration-500 ease-out"
                style="animation: cardEntry 0.4s ease-out {{ $loop->index * 0.06 }}s both">
                <div class="p-6 flex-1">
                    <div class="flex items-start justify-between mb-4">
                        <div class="w-12 h-12 rounded-xl bg-blue-50 flex items-center justify-center text-blue-600 text-xl border border-blue-100">
                            <i class="{{ $service['icon'] ?? 'fa-solid fa-cog' }}"></i>
                        </div>
                        <x-cms.status-badge :active="$service['is_active']" />
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
                    <div class="flex gap-1">
                        <button wire:click="moveUp({{ $service['id'] }})"
                                @if($loop->first) disabled @endif
                                title="Subir"
                                class="w-8 h-8 flex items-center justify-center rounded-lg transition-all duration-200
                                       {{ $loop->first
                                            ? 'text-gray-300 cursor-not-allowed'
                                            : 'text-gray-400 hover:bg-blue-50 hover:text-blue-600 hover:-translate-y-0.5 active:scale-90' }}">
                            <i class="fa-solid fa-arrow-up text-xs"></i>
                        </button>
                        <button wire:click="moveDown({{ $service['id'] }})"
                                @if($loop->last) disabled @endif
                                title="Bajar"
                                class="w-8 h-8 flex items-center justify-center rounded-lg transition-all duration-200
                                       {{ $loop->last
                                            ? 'text-gray-300 cursor-not-allowed'
                                            : 'text-gray-400 hover:bg-blue-50 hover:text-blue-600 hover:translate-y-0.5 active:scale-90' }}">
                            <i class="fa-solid fa-arrow-down text-xs"></i>
                        </button>
                    </div>
                    <div class="flex gap-1">
                        <x-cms.action-button icon="fa-solid fa-pen" variant="warning" wireClick="edit({{ $service['id'] }})" title="Editar" />
                        <x-cms.action-button icon="fa-solid fa-{{ $service['is_active'] ? 'eye-slash' : 'eye' }}" variant="{{ $service['is_active'] ? 'ghost' : 'success' }}" onclick="confirmToggleService({{ $service['id'] }})" title="{{ $service['is_active'] ? 'Desactivar' : 'Activar' }}" />
                        <x-cms.action-button icon="fa-solid fa-trash" variant="danger" onclick="confirmDeleteService({{ $service['id'] }})" title="Eliminar" />
                    </div>
                </div>
            </x-cms.card>
        @empty
            <div class="col-span-full bg-white rounded-2xl shadow-sm border border-gray-100 p-16 text-center" style="animation: emptyPulse 3s ease-in-out infinite">
                <div class="w-16 h-16 rounded-2xl bg-blue-50 flex items-center justify-center mx-auto mb-4 border border-blue-100">
                    <i class="fa-solid fa-wrench text-2xl text-blue-400"></i>
                </div>
                <p class="text-gray-500 font-medium mb-1">No hay servicios creados</p>
                <p class="text-gray-400 text-sm mb-4">Creá el primer servicio para mostrar en el landing</p>
                <button wire:click="create" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-blue-600 text-white text-sm font-semibold hover:bg-blue-700 transition-all">
                    <i class="fa-solid fa-plus"></i> Crear Servicio
                </button>
            </div>
        @endforelse
    </div>

    {{-- Create/Edit Modal --}}
    @if($showForm)
        <div class="fixed inset-0 bg-black/50 backdrop-blur-sm flex items-center justify-center z-50 overflow-y-auto py-8 px-4" x-data="{}" x-init="$watch('showForm', v => { if(v) document.body.style.overflow = 'hidden'; else document.body.style.overflow = ''; })">
            <div class="bg-white rounded-2xl shadow-2xl w-full max-w-2xl max-h-[90vh] overflow-y-auto" style="animation: modalFadeIn 0.3s ease-out">
                <div class="sticky top-0 bg-white border-b border-gray-100 px-6 py-4 flex justify-between items-center rounded-t-2xl z-10">
                    <h5 class="text-lg font-bold text-gray-900">{{ $editingId ? 'Editar' : 'Nuevo' }} Servicio</h5>
                    <button wire:click="resetForm" class="w-9 h-9 flex items-center justify-center rounded-lg text-gray-400 hover:text-gray-600 hover:bg-gray-100 transition-all">
                        <i class="fa-solid fa-xmark text-lg"></i>
                    </button>
                </div>
                <div class="px-6 py-5">
                    @if ($errors->any())
                        <div class="bg-red-50 border border-red-200 text-red-600 px-4 py-3 rounded-xl text-sm mb-4 animate-slide-down">
                            @foreach ($errors->all() as $error)
                                <p class="flex items-start gap-2"><i class="fa-solid fa-circle-exclamation mt-0.5 text-xs"></i>{{ $error }}</p>
                            @endforeach
                        </div>
                    @endif
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <div class="md:col-span-2">
                            <label class="block text-sm font-semibold text-gray-700 mb-1.5 pl-3 border-l-3 border-blue-500">Título *</label>
                            <input type="text" class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all bg-white shadow-sm" wire:model.live="title" placeholder="Ej: Conversión a GNV / GLP">
                            <p class="text-xs text-gray-400 mt-1.5">El ícono se asigna automáticamente según el título</p>
                        </div>
                        <div>
                            <x-icon-picker model="icon" label="Ícono" />
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1.5 pl-3 border-l-3 border-blue-500">Texto del Botón CTA</label>
                            <input type="text" class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all bg-white shadow-sm" wire:model="ctaText" placeholder="Cotizar Conversión">
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-sm font-semibold text-gray-700 mb-1.5 pl-3 border-l-3 border-blue-500">Descripción</label>
                            <textarea class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all bg-white shadow-sm" rows="3" wire:model="description"></textarea>
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-sm font-semibold text-gray-700 mb-1.5 pl-3 border-l-3 border-blue-500">Features (una por línea)</label>
                            <textarea class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all bg-white shadow-sm" rows="3" wire:model="features" placeholder="Equipos italianos&#10;Garantía 1 año&#10;Certificación inicial"></textarea>
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-sm font-semibold text-gray-700 mb-1.5 pl-3 border-l-3 border-blue-500">Link del Botón (WhatsApp)</label>
                            <input type="text" class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all bg-white shadow-sm" wire:model="ctaLink" placeholder="https://wa.me/51943694464?text=...">
                        </div>
                        <div class="md:col-span-2">
                            <label class="flex items-center gap-2.5 cursor-pointer">
                                <input type="checkbox" class="w-4 h-4 text-blue-600 rounded-lg focus:ring-blue-500 border-gray-300" wire:model="active">
                                <span class="text-sm font-medium text-gray-700">Activo</span>
                            </label>
                        </div>
                    </div>
                </div>
                <div class="sticky bottom-0 bg-gray-50/80 backdrop-blur-sm border-t border-gray-200 px-6 py-4 flex justify-end gap-3 rounded-b-2xl">
                    <button type="button" class="px-5 py-2.5 rounded-xl bg-white text-gray-600 hover:bg-gray-100 font-semibold transition-all border border-gray-200 shadow-sm" wire:click="resetForm">Cancelar</button>
                    <button type="button" class="px-5 py-2.5 rounded-xl bg-blue-600 text-white hover:bg-blue-700 font-semibold transition-all shadow-md shadow-blue-200/50 border border-blue-700"
                            wire:click="save"
                            wire:loading.attr="disabled">
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

    {{-- Styles --}}
    <style>
        @keyframes modalFadeIn { from { opacity: 0; transform: scale(0.95) translateY(10px); } to { opacity: 1; transform: scale(1) translateY(0); } }
        @keyframes slideDown { from { opacity: 0; transform: translateY(-10px); } to { opacity: 1; transform: translateY(0); } }
        @keyframes cardEntry { from { opacity: 0; transform: translateY(20px) scale(0.97); } to { opacity: 1; transform: translateY(0) scale(1); } }
        @keyframes emptyPulse { 0%, 100% { opacity: 1; } 50% { opacity: 0.6; } }
        .animate-slide-down { animation: slideDown 0.3s ease-out; }
        [x-cloak] { display: none !important; }
    </style>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        function confirmDeleteService(id) {
            Swal.fire({
                title: '¿Eliminar servicio?',
                text: 'Esta acción no se puede deshacer.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc2626',
                cancelButtonColor: '#64748b',
                confirmButtonText: 'Sí, eliminar',
                cancelButtonText: 'Cancelar',
                customClass: { popup: 'rounded-2xl shadow-xl', confirmButton: 'rounded-xl px-5 py-2.5 font-semibold text-sm', cancelButton: 'rounded-xl px-5 py-2.5 font-semibold text-sm' }
            }).then((result) => {
                if (result.isConfirmed) { @this.call('delete', id) }
            });
        }
        function confirmToggleService(id) {
            Swal.fire({
                title: '¿Cambiar estado?',
                text: 'Se activará o desactivará este servicio.',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#2563eb',
                cancelButtonColor: '#64748b',
                confirmButtonText: 'Sí, cambiar',
                cancelButtonText: 'Cancelar',
                customClass: { popup: 'rounded-2xl shadow-xl', confirmButton: 'rounded-xl px-5 py-2.5 font-semibold text-sm', cancelButton: 'rounded-xl px-5 py-2.5 font-semibold text-sm' }
            }).then((result) => {
                if (result.isConfirmed) { @this.call('toggleActive', id) }
            });
        }
    </script>
</x-cms.layout>