<x-cms.layout
    title="Proceso de Trabajo"
    description="Administra los pasos del proceso que se muestran en el landing page"
    headerIcon='<i class="fa-solid fa-route text-blue-600"></i>'
>
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

    {{-- Steps Grid --}}
    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-6">
        @forelse ($steps as $step)
            <x-cms.card class="flex flex-col h-full group" style="animation: cardEntry 0.4s ease-out {{ $loop->index * 0.06 }}s both">
                <div class="p-6 flex-1 text-center">
                    <div class="w-16 h-16 rounded-2xl bg-blue-50 flex items-center justify-center mx-auto mb-4 border border-blue-100">
                        <span class="text-2xl font-bold text-blue-600">{{ $step['step_number'] }}</span>
                    </div>
                    <h5 class="font-bold text-gray-900 text-lg mb-2">{{ $step['title'] }}</h5>
                    <p class="text-gray-500 text-sm">{{ $step['description'] }}</p>
                    @if($step['icon'])
                        <div class="mt-3 text-center">
                            <i class="{{ $step['icon'] }} text-3xl text-gray-300"></i>
                        </div>
                    @endif
                </div>
                <div class="border-t border-gray-100 bg-gray-50/80 px-6 py-3.5 flex items-center justify-center gap-1">
                    <x-cms.action-button icon="fa-solid fa-pen" variant="warning" wireClick="edit({{ $step['id'] }})" title="Editar" />
                    <x-cms.action-button icon="fa-solid fa-{{ $step['is_active'] ? 'eye-slash' : 'eye' }}" variant="{{ $step['is_active'] ? 'ghost' : 'success' }}" wireClick="toggleActive({{ $step['id'] }})" title="{{ $step['is_active'] ? 'Desactivar' : 'Activar' }}" />
                    <x-cms.action-button icon="fa-solid fa-trash" variant="danger" wireClick="delete({{ $step['id'] }})" title="Eliminar" />
                </div>
            </x-cms.card>
        @empty
            <div class="col-span-full x-cms.card p-16 text-center" style="animation: emptyPulse 3s ease-in-out infinite">
                <div class="w-16 h-16 rounded-2xl bg-blue-50 flex items-center justify-center mx-auto mb-4 border border-blue-100">
                    <i class="fa-solid fa-route text-2xl text-blue-400"></i>
                </div>
                <p class="text-gray-500 font-medium mb-1">No hay pasos definidos</p>
                <p class="text-gray-400 text-sm">Crea el primer paso del proceso</p>
            </div>
        @endforelse
    </div>

    {{-- Create/Edit Modal --}}
    @if($showForm)
        <div class="fixed inset-0 bg-black/50 backdrop-blur-sm flex items-center justify-center z-50 overflow-y-auto py-8 px-4" x-data="{}" x-init="$watch('showForm', v => { if(v) document.body.style.overflow = 'hidden'; else document.body.style.overflow = ''; })">
            <div class="bg-white rounded-2xl shadow-2xl w-full max-w-xl max-h-[90vh] overflow-y-auto" style="animation: modalFadeIn 0.3s ease-out">
                <div class="sticky top-0 bg-white border-b border-gray-100 px-6 py-4 flex justify-between items-center rounded-t-2xl z-10">
                    <h4 class="text-lg font-bold text-gray-900">{{ $editingId ? 'Editar' : 'Nuevo' }} Paso</h4>
                    <button wire:click="resetForm"
                            class="w-9 h-9 flex items-center justify-center rounded-lg text-gray-400 hover:text-gray-600 hover:bg-gray-100 transition-all"
                            aria-label="Cerrar">
                        <i class="fa-solid fa-xmark text-lg"></i>
                    </button>
                </div>
                <div class="px-6 py-5">
                    @if ($errors->any())
                        <div class="mb-4 p-4 rounded-xl bg-red-50 border border-red-200 text-red-600 text-sm animate-slide-down">
                            @foreach ($errors->all() as $error)
                                <p class="flex items-start gap-2"><i class="fa-solid fa-circle-exclamation mt-0.5 text-xs"></i>{{ $error }}</p>
                            @endforeach
                        </div>
                    @endif
                    <div class="space-y-5">
                        <div class="md:col-span-2">
                            <label class="block text-sm font-semibold text-gray-700 mb-1.5">Título *</label>
                            <input type="text"
                                   class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all bg-white"
                                   wire:model="title"
                                   placeholder="Ej: Diagnóstico Inicial">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1.5">Número de Paso *</label>
                            <input type="text"
                                   class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all bg-white"
                                   wire:model="stepNumber"
                                   placeholder="01"
                                   maxlength="10">
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-sm font-semibold text-gray-700 mb-1.5">Descripción</label>
                            <textarea class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all bg-white"
                                      rows="3"
                                      wire:model="description"
                                      placeholder="Descripción del paso..."></textarea>
                        </div>

                        {{-- ================================================= --}}
                        {{-- ÍCONO: menú visual desplegable (se ve el dibujo, no el nombre de código) --}}
                        {{-- ================================================= --}}
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1.5">Ícono (opcional)</label>

                            @php
                                $stepIconOptions = [
                                    'fa-solid fa-magnifying-glass' => 'Diagnóstico',
                                    'fa-solid fa-clipboard-list' => 'Lista de Verificación',
                                    'fa-solid fa-screwdriver-wrench' => 'Mantenimiento',
                                    'fa-solid fa-wrench' => 'Reparación',
                                    'fa-solid fa-gauge-high' => 'Diagnóstico Avanzado',
                                    'fa-solid fa-car' => 'Vehículo',
                                    'fa-solid fa-car-side' => 'Vehículo (lateral)',
                                    'fa-solid fa-circle-check' => 'Aprobado',
                                    'fa-solid fa-check' => 'Verificación',
                                    'fa-solid fa-file-invoice' => 'Presupuesto',
                                    'fa-solid fa-credit-card' => 'Pago',
                                    'fa-solid fa-handshake' => 'Acuerdo / Entrega',
                                    'fa-solid fa-key' => 'Entrega de Llaves',
                                    'fa-solid fa-route' => 'Proceso / Ruta',
                                    'fa-solid fa-shield-halved' => 'Garantía',
                                    'fa-solid fa-truck' => 'Transporte',
                                    'fa-solid fa-phone' => 'Contacto',
                                    'fa-solid fa-calendar-check' => 'Cita Agendada',
                                    'fa-solid fa-thumbs-up' => 'Satisfacción',
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
                                        {{ $icon ? ($stepIconOptions[$icon] ?? 'Ícono personalizado') : 'Selecciona un ícono...' }}
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

                                        @foreach($stepIconOptions as $iconClass => $iconLabel)
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
                <div class="sticky bottom-0 bg-white border-t border-gray-100 px-6 py-4 flex justify-end gap-3 rounded-b-2xl">
                    <button type="button"
                            class="px-5 py-2.5 rounded-xl bg-gray-100 text-gray-600 hover:bg-gray-200 font-semibold transition-all"
                            wire:click="resetForm">
                        Cancelar
                    </button>
                    <button type="button"
                            class="px-5 py-2.5 rounded-xl bg-blue-600 text-white hover:bg-blue-700 font-semibold transition-all shadow-sm"
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
</x-cms.layout>