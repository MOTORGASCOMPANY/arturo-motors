<div>
    <style>
        @keyframes modalFadeIn { from { opacity: 0; transform: scale(0.95) translateY(10px); } to { opacity: 1; transform: scale(1) translateY(0); } }
        @keyframes errorSlideIn { 0% { opacity: 0; transform: translateY(-20px); } 60% { transform: translateX(6px); } 80% { transform: translateX(-4px); } 100% { opacity: 1; transform: translateY(0) translateX(0); } }
        @keyframes successFlash { 0% { opacity: 0; transform: scale(0.9); } 50% { opacity: 1; transform: scale(1.02); } 100% { opacity: 1; transform: scale(1); } }
        @keyframes spin { to { transform: rotate(360deg); } }
        @keyframes emptyPulse { 0%, 100% { opacity: 1; } 50% { opacity: 0.6; } }
        @keyframes deleteShake { 0%, 100% { transform: translateX(0); } 20% { transform: translateX(-4px); } 40% { transform: translateX(4px); } 60% { transform: translateX(-2px); } 80% { transform: translateX(2px); } }
    </style>

    <div class="flex justify-between items-center mb-8 pb-4 border-b border-gray-200 m-4">
        <h4 class="text-2xl font-bold text-gray-900 flex items-center gap-3">
            <i class="fa-solid fa-location-dot text-blue-600"></i>Información de Contacto
        </h4>
        <button class="bg-blue-600 text-white px-5 py-2.5 rounded-xl hover:bg-blue-700 font-semibold transition-all shadow-sm hover:shadow-md" wire:click="create">
            <i class="fa-solid fa-plus mr-1.5"></i>Nuevo
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

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden m-4">
        <table class="w-full">
            <thead class="bg-gray-50/80 border-b border-gray-100">
                <tr>
                    <th class="px-6 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Tipo</th>
                    <th class="px-6 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Etiqueta</th>
                    <th class="px-6 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Valor</th>
                    <th class="px-6 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Estado</th>
                    <th class="px-6 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Acciones</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse ($contacts as $contact)
                    <tr class="hover:bg-gray-50/50 transition-colors">
                        <td class="px-6 py-4">
                            <span class="inline-flex items-center gap-1.5 bg-blue-50 text-blue-600 text-xs font-semibold px-3 py-1.5 rounded-full border border-blue-100">
                                <i class="{{ $contact['icon'] ?? 'fa-solid fa-circle' }} text-[10px]"></i>
                                {{ $types[$contact['type']] ?? $contact['type'] }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-sm font-medium text-gray-900">{{ $contact['label'] }}</td>
                        <td class="px-6 py-4 text-sm text-gray-500 max-w-xs truncate">{{ Str::limit($contact['value'], 60) }}</td>
                        <td class="px-6 py-4">
                            <span class="text-xs font-semibold px-3 py-1.5 rounded-full {{ $contact['is_active'] ? 'bg-green-50 text-green-600 border border-green-200' : 'bg-gray-50 text-gray-400 border border-gray-200' }}">
                                {{ $contact['is_active'] ? 'Activo' : 'Inactivo' }}
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex gap-2">
                                <button class="w-9 h-9 flex items-center justify-center rounded-lg bg-amber-50 border border-amber-200 text-amber-600 hover:bg-amber-100 transition-all" wire:click="edit({{ $contact['id'] }})">
                                    <i class="fa-solid fa-pen text-xs"></i>
                                </button>
                                <button class="w-9 h-9 flex items-center justify-center rounded-lg border transition-all {{ $contact['is_active'] ? 'bg-gray-50 border-gray-200 text-gray-400 hover:bg-gray-100' : 'bg-green-50 border-green-200 text-green-600 hover:bg-green-100' }}" wire:click="toggleActive({{ $contact['id'] }})">
                                    <i class="fa-solid fa-{{ $contact['is_active'] ? 'eye-slash' : 'eye' }} text-xs"></i>
                                </button>
                                <button class="w-9 h-9 flex items-center justify-center rounded-lg bg-red-50 border border-red-200 text-red-500 hover:bg-red-100 transition-all"
                                        onclick="window.dispatchEvent(new CustomEvent('confirm-modal:show', { detail: { title: 'Eliminar contacto', message: '¿Seguro que querés eliminar este contacto? Esta acción no se puede deshacer.', action: { componentId: $wire.__instance.id, method: 'delete', params: [{{ $contact['id'] }}] } } }))">
                                    <i class="fa-solid fa-trash text-xs"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-6 py-16 text-center" style="animation: emptyPulse 3s ease-in-out infinite">
                            <div class="w-16 h-16 rounded-2xl bg-blue-50 flex items-center justify-center mx-auto mb-4 border border-blue-100">
                                <i class="fa-solid fa-location-dot text-2xl text-blue-400"></i>
                            </div>
                            <p class="text-gray-500 font-medium">No hay información de contacto</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($showForm)
        <div class="fixed inset-0 bg-black/50 backdrop-blur-sm flex items-start justify-center z-50 overflow-y-auto py-8 px-4">
            <div class="bg-white rounded-2xl shadow-2xl w-full max-w-2xl my-auto" style="animation: modalFadeIn 0.3s ease-out">
                <div class="sticky top-0 bg-white border-b border-gray-100 px-6 py-4 flex justify-between items-center rounded-t-2xl z-10">
                    <h5 class="text-lg font-bold text-gray-900">{{ $editingId ? 'Editar' : 'Nuevo' }} Contacto</h5>
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
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1.5">Tipo *</label>
                            <select class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all" wire:model.live="type">
                                @foreach($types as $key => $label)
                                    <option value="{{ $key }}">{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1.5">Etiqueta *</label>
                            <input type="text" class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all" wire:model="label" placeholder="Dirección del Taller">
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-sm font-semibold text-gray-700 mb-1.5">Valor *</label>
                            @if($type === 'map_iframe')
                                <textarea class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all font-mono text-xs" rows="4" wire:model="value" placeholder="<iframe src='...'></iframe>"></textarea>
                            @elseif($type === 'whatsapp')
                                <div class="flex items-center gap-2">
                                    <span class="text-gray-400 text-sm">wa.me/</span>
                                    <input type="text" class="flex-1 border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all" wire:model="value" placeholder="51943694464">
                                </div>
                                <p class="text-xs text-gray-400 mt-1.5">Solo el número sin espacios ni guiones (ej: 51943694464)</p>
                            @elseif($type === 'phone')
                                <div x-data="{ phone: @entangle('value') }">
                                    <div class="flex items-center gap-2">
                                        <span class="text-gray-400 text-sm">+51</span>
                                        <input type="tel" class="flex-1 border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all" wire:model="value" placeholder="943694464"
                                               @input="phone = phone.replace(/[^0-9]/g, '')"
                                               maxlength="15">
                                    </div>
                                    <div class="flex items-center gap-2 mt-1.5">
                                        <p class="text-xs text-gray-400">Solo números (ej: 943694464)</p>
                                        <span class="text-xs" :class="value && value.length >= 9 ? 'text-green-500' : 'text-gray-300'">
                                            <i class="fa-solid fa-circle-check" x-show="value && value.length >= 9"></i>
                                            <span x-text="value ? value.length + '/15' : ''" x-show="value"></span>
                                        </span>
                                    </div>
                                </div>
                            @elseif($type === 'email')
                                <input type="email" class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all" wire:model="value" placeholder="correo@ejemplo.com">
                            @elseif($type === 'address')
                                <div x-data="{ charCount: @entangle('value')?.length || 0 }">
                                    <input type="text" class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all" wire:model="value" placeholder="Prolongación Av. Perú 5176, Callao"
                                           @input="charCount = $el.value.length" maxlength="255">
                                    <div class="flex justify-between mt-1.5">
                                        <p class="text-xs text-gray-400">Dirección completa del taller</p>
                                        <span class="text-xs" :class="charCount > 200 ? 'text-amber-500' : 'text-gray-300'" x-text="charCount + '/255'"></span>
                                    </div>
                                </div>
                            @else
                                <input type="text" class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all" wire:model="value" placeholder="Prolongación Av. Perú 5176, Callao">
                            @endif
                        </div>
                        <div>
                            <x-icon-picker model="icon" label="Ícono" />
                        </div>
                        <div class="flex items-end">
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
