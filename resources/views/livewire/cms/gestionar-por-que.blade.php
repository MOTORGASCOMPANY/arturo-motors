<x-cms.layout
    title="Por Qué Elegirnos"
    description="Administra las tarjetas de ventajas competitivas que se muestran en el landing page"
    headerIcon='<i class="fa-solid fa-shield-halved text-blue-600"></i>'
>
    {{-- Header with Create Button --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-8 pb-4 border-b border-blue-100">
        <div>
            <h2 class="text-xl font-semibold text-blue-950">{{ count($cards) ?? 0 }} tarjetas de ventajas</h2>
            <p class="text-blue-700/70 text-sm">Destaca por qué los clientes deben elegir Arturo Motors</p>
        </div>
        <button wire:click="create"
                class="w-full sm:w-auto px-5 py-2.5 rounded-xl bg-blue-600 text-white font-semibold shadow-sm shadow-blue-200 hover:bg-blue-700 hover:shadow-md hover:shadow-blue-300 hover:-translate-y-0.5 transition-all duration-300 flex items-center justify-center gap-2">
            <i class="fa-solid fa-plus"></i> Nueva Tarjeta
        </button>
    </div>

    {{-- Success Message --}}
    @if($successMessage)
        <div x-data="{ show: true }"
             x-show="show"
             x-transition:leave="transition ease-in duration-300"
             x-init="setTimeout(() => { show = false; @this.call('clearSuccessMessage') }, 3000)"
             class="mb-6 flex items-center gap-3 p-4 rounded-xl bg-green-50 border border-green-200 text-green-700 shadow-sm animate-slide-down">
            <i class="fa-solid fa-circle-check text-lg text-green-600"></i>
            <span class="flex-1 font-medium">{{ $successMessage }}</span>
            <button @click="show = false; @this.call('clearSuccessMessage')" class="text-green-500 hover:text-green-700 p-1.5 rounded-lg transition-colors">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>
    @endif

    {{-- Cards Grid --}}
    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
        @forelse ($cards as $card)
            <x-cms.card class="flex flex-col h-full group bg-white border border-blue-100 hover:border-blue-300 hover:shadow-lg hover:shadow-blue-100/50 transition-all duration-300" style="animation: cardEntry 0.4s ease-out {{ $loop->index * 0.06 }}s both">
                <div class="p-6 flex-1">
                    <div class="flex items-start justify-between mb-4">
                        <div class="w-12 h-12 rounded-xl bg-blue-50 flex items-center justify-center text-blue-600 border border-blue-100 group-hover:bg-blue-600 group-hover:text-white transition-colors duration-300">
                            <i class="{{ $card['icon'] ?? 'fa-solid fa-star' }} text-xl"></i>
                        </div>
                        <x-cms.status-badge :active="$card['is_active']" />
                    </div>
                    <h5 class="font-bold text-blue-950 text-lg mb-2 group-hover:text-blue-700 transition-colors">{{ $card['title'] }}</h5>
                    <p class="text-slate-600 text-sm leading-relaxed">{{ $card['description'] }}</p>
                </div>
                <div class="border-t border-blue-50 bg-blue-50/30 px-6 py-3.5 flex items-center justify-between">
                    <div class="flex gap-1">
                        {{-- Los botones de acción mantienen sus colores semánticos por requerimiento --}}
                        <x-cms.action-button icon="fa-solid fa-pen" variant="warning" wireClick="edit({{ $card['id'] }})" title="Editar" />
                        <x-cms.action-button icon="fa-solid fa-{{ $card['is_active'] ? 'eye-slash' : 'eye' }}" variant="{{ $card['is_active'] ? 'ghost' : 'success' }}" onclick="confirmToggleCard({{ $card['id'] }})" title="{{ $card['is_active'] ? 'Desactivar' : 'Activar' }}" />
                        <x-cms.action-button icon="fa-solid fa-trash" variant="danger" onclick="confirmDeleteCard({{ $card['id'] }})" title="Eliminar" />
                    </div>
                </div>
            </x-cms.card>
        @empty
            <div class="col-span-full bg-white rounded-2xl shadow-sm border border-gray-100 p-16 text-center" style="animation: emptyPulse 3s ease-in-out infinite">
                <div class="w-16 h-16 rounded-2xl bg-blue-50 flex items-center justify-center mx-auto mb-4 border border-blue-100">
                    <i class="fa-solid fa-shield-halved text-2xl text-blue-400"></i>
                </div>
                <p class="text-gray-500 font-medium mb-1">No hay tarjetas de ventajas</p>
                <p class="text-gray-400 text-sm mb-4">Crea la primera tarjeta para destacar tus ventajas</p>
                <button wire:click="create" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-blue-600 text-white text-sm font-semibold hover:bg-blue-700 transition-all">
                    <i class="fa-solid fa-plus"></i> Crear Tarjeta
                </button>
            </div>
        @endforelse
    </div>

    {{-- Create/Edit Modal --}}
    @if($showForm)
        <div class="fixed inset-0 bg-black/50 backdrop-blur-sm flex items-center justify-center z-50 overflow-y-auto py-8 px-4" x-data="{}" x-init="$watch('showForm', v => { if(v) document.body.style.overflow = 'hidden'; else document.body.style.overflow = ''; })">
            <div class="bg-white rounded-2xl shadow-2xl w-full max-w-xl max-h-[90vh] overflow-y-auto" style="animation: modalFadeIn 0.3s ease-out">
                <div class="sticky top-0 bg-white border-b border-gray-100 px-6 py-4 flex justify-between items-center rounded-t-2xl z-10">
                    <h4 class="text-lg font-bold text-gray-900">{{ $editingId ? 'Editar' : 'Nueva' }} Tarjeta</h4>
                    <button wire:click="resetForm"
                            class="w-9 h-9 flex items-center justify-center rounded-lg text-gray-400 hover:text-gray-600 hover:bg-gray-100 transition-all"
                            aria-label="Cerrar">
                        <i class="fa-solid fa-xmark text-lg"></i>
                    </button>
                </div>
                <div class="px-6 py-5">
                    @if ($errors->any())
                        <div class="mb-4 p-4 rounded-xl bg-red-50 border border-red-200 text-red-700 text-sm animate-slide-down">
                            @foreach ($errors->all() as $error)
                                <p class="flex items-start gap-2"><i class="fa-solid fa-circle-exclamation mt-0.5 text-xs"></i>{{ $error }}</p>
                            @endforeach
                        </div>
                    @endif
                    <div class="space-y-5">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1.5 pl-3 border-l-3 border-blue-500">Título *</label>
                            <input type="text"
                                   class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all bg-white shadow-sm"
                                   wire:model="title"
                                   placeholder="Ej: Garantía 100%">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1.5 pl-3 border-l-3 border-blue-500">Descripción</label>
                            <textarea class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all bg-white shadow-sm"
                                      rows="3"
                                      wire:model="description"
                                      placeholder="Descripción de la ventaja..."></textarea>
                        </div>

                        {{-- ================================================= --}}
                        {{-- ÍCONO: menú visual desplegable (se ve el dibujo, no el nombre de código) --}}
                        {{-- ================================================= --}}
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1.5 pl-3 border-l-3 border-blue-500">Ícono (opcional)</label>

                            @php
                                $cardIconOptions = [
                                    'fa-solid fa-shield-halved' => 'Garantía / Seguridad',
                                    'fa-solid fa-medal' => 'Calidad Certificada',
                                    'fa-solid fa-award' => 'Premiado',
                                    'fa-solid fa-star' => 'Excelencia',
                                    'fa-solid fa-thumbs-up' => 'Satisfacción Garantizada',
                                    'fa-solid fa-heart' => 'Compromiso',
                                    'fa-solid fa-hand-holding-dollar' => 'Mejor Precio',
                                    'fa-solid fa-dollar-sign' => 'Precios Justos',
                                    'fa-solid fa-truck' => 'Entrega / Transporte',
                                    'fa-solid fa-clock' => 'Rapidez',
                                    'fa-solid fa-headset' => 'Atención al Cliente',
                                    'fa-solid fa-certificate' => 'Certificación',
                                    'fa-solid fa-users' => 'Equipo Experto',
                                    'fa-solid fa-trophy' => 'Trayectoria',
                                    'fa-solid fa-gem' => 'Calidad Premium',
                                    'fa-solid fa-lock' => 'Confianza y Seguridad',
                                    'fa-solid fa-tools' => 'Servicio Técnico',
                                    'fa-solid fa-car' => 'Especialistas Automotrices',
                                ];
                            @endphp

                            <div class="relative" x-data="{ open: false }">

                                <button type="button"
                                        @click="open = !open"
                                        @click.outside="open = false"
                                        class="w-full flex items-center gap-3 border border-gray-200 rounded-xl px-3.5 py-2.5 bg-white hover:border-blue-400 transition-all text-left">

                                    <div class="w-9 h-9 shrink-0 rounded-lg bg-gray-100 border border-gray-200 flex items-center justify-center text-gray-500 text-base">
                                        <i class="{{ $icon ?: 'fa-solid fa-image' }}"></i>
                                    </div>

                                    <span class="flex-1 text-sm text-gray-700 truncate">
                                        {{ $icon ? ($cardIconOptions[$icon] ?? 'Ícono personalizado') : 'Selecciona un ícono...' }}
                                    </span>

                                    <i class="fa-solid fa-chevron-down text-xs text-gray-400 transition-transform" :class="open ? 'rotate-180' : ''"></i>

                                </button>

                                <div x-show="open"
                                     x-cloak
                                     x-transition:enter="transition ease-out duration-150"
                                     x-transition:enter-start="opacity-0 scale-95"
                                     x-transition:enter-end="opacity-100 scale-100"
                                     class="absolute z-30 mt-2 w-full bg-white border border-gray-200 rounded-xl shadow-xl p-3">

                                    <div class="grid grid-cols-5 sm:grid-cols-6 gap-2 max-h-56 overflow-y-auto pr-1">

                                        <button type="button"
                                                wire:click="$set('icon', '')"
                                                @click="open = false"
                                                title="Sin ícono"
                                                class="aspect-square rounded-lg border flex items-center justify-center text-sm transition-all {{ !$icon ? 'bg-blue-600 text-white border-blue-600' : 'bg-gray-50 text-gray-400 border-gray-200 hover:bg-gray-100' }}">
                                            <i class="fa-solid fa-ban"></i>
                                        </button>

                                        @foreach($cardIconOptions as $iconClass => $iconLabel)
                                            <button type="button"
                                                    wire:click="$set('icon', '{{ $iconClass }}')"
                                                    @click="open = false"
                                                    title="{{ $iconLabel }}"
                                                    class="aspect-square rounded-lg border flex items-center justify-center text-lg transition-all {{ $icon === $iconClass ? 'bg-blue-600 text-white border-blue-600' : 'bg-gray-50 text-gray-600 border-gray-200 hover:bg-blue-50 hover:border-blue-300 hover:text-blue-600' }}">
                                                <i class="{{ $iconClass }}"></i>
                                            </button>
                                        @endforeach

                                    </div>

                                </div>

                            </div>

                        </div>

                        <div class="flex items-center gap-2.5">
                            <input type="checkbox"
                                   class="w-4 h-4 text-blue-600 rounded-lg focus:ring-blue-500 border-gray-300"
                                   wire:model="active">
                            <span class="text-sm font-medium text-gray-700">Activo</span>
                        </div>
                    </div>
                </div>
                <div class="sticky bottom-0 bg-gray-50/80 backdrop-blur-sm border-t border-gray-200 px-6 py-4 flex justify-end gap-3 rounded-b-2xl">
                    <button type="button"
                            class="px-5 py-2.5 rounded-xl bg-white text-gray-600 hover:bg-gray-100 font-semibold transition-all border border-gray-200 shadow-sm"
                            wire:click="resetForm">
                        Cancelar
                    </button>
                    <button type="button"
                            class="px-5 py-2.5 rounded-xl bg-blue-600 text-white hover:bg-blue-700 font-semibold transition-all shadow-md shadow-blue-200/50 border border-blue-700"
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



    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        function confirmDeleteCard(id) {
            Swal.fire({
                title: '¿Eliminar tarjeta?',
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
        function confirmToggleCard(id) {
            Swal.fire({
                title: '¿Cambiar estado?',
                text: 'Se activará o desactivará esta tarjeta.',
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