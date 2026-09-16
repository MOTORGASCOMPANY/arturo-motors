<div>
    <x-cms.layout
        title="Información de Contacto"
        description="Administra los datos de contacto que se muestran en el landing page"
        headerIcon='<i class="fa-solid fa-location-dot text-white"></i>'
    >
        <div class="px-6 lg:px-8">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-8 pb-4 border-b border-gray-200">
                <div>
                    <h2 class="text-xl font-semibold text-gray-900">{{ count($contacts) ?? 0 }} elementos de contacto</h2>
                    <p class="text-gray-500 text-sm">Dirección, teléfono, horario, WhatsApp, email y mapa</p>
                </div>
                <button wire:click="$dispatch('abrir-crear-contacto')"
                        class="w-full sm:w-auto px-5 py-2.5 rounded-xl bg-blue-600 text-white font-semibold shadow-sm shadow-blue-200 hover:bg-blue-700 hover:shadow-md hover:shadow-blue-300 hover:-translate-y-0.5 transition-all duration-300 flex items-center justify-center gap-2">
                    <i class="fa-solid fa-plus"></i> Nuevo Contacto
                </button>
            </div>

            {{-- Contacts Grid --}}
            <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
                @forelse ($contacts as $contact)
                    @php
                        $typeIcons = [
                            'address' => 'fa-solid fa-map-location-dot',
                            'phone' => 'fa-solid fa-phone',
                            'schedule' => 'fa-solid fa-clock',
                            'whatsapp' => 'fa-brands fa-whatsapp',
                            'email' => 'fa-solid fa-envelope',
                            'map_iframe' => 'fa-solid fa-map',
                        ];
                    @endphp
                    <x-cms.card class="flex flex-col h-full group bg-white border border-gray-200 hover:border-blue-200 hover:shadow-lg hover:shadow-blue-100/50 transition-all duration-300" style="animation: cardEntry 0.4s ease-out {{ $loop->index * 0.06 }}s both">
                        <div class="p-6 flex-1">
                            <div class="flex items-start justify-between mb-4">
                                <x-cms.icon-box :icon="$typeIcons[$contact['type']] ?? 'fa-solid fa-map-location-dot'"
                                                 bgClass="bg-blue-50 border-blue-100 text-blue-600 group-hover:bg-blue-600 group-hover:text-white transition-colors duration-300" />
                                <x-cms.status-badge :active="$contact['is_active']" />
                            </div>
                            <h5 class="font-bold text-gray-900 text-lg mb-1 group-hover:text-blue-700 transition-colors">{{ $contact['label'] }}</h5>
                            <p class="text-slate-600 text-sm leading-relaxed">{{ Str::limit($contact['value'], 60) }}</p>
                            @if($contact['type'] === 'map_iframe')
                                <p class="text-xs text-gray-400 mt-2"><i class="fa-solid fa-code mr-1"></i>Iframe embebido</p>
                            @endif
                        </div>
                        <div class="border-t border-gray-50 bg-gray-50/30 px-6 py-3.5 flex items-center justify-between">
                            <div class="flex gap-1">
                                <x-cms.action-button icon="fa-solid fa-pen" variant="warning" wireClick="$dispatch('abrir-editar-contacto', { id: {{ $contact['id'] }} })" title="Editar" />
                                <x-cms.action-button icon="fa-solid fa-{{ $contact['is_active'] ? 'eye-slash' : 'eye' }}" variant="{{ $contact['is_active'] ? 'ghost' : 'success' }}" onclick="confirmToggleContact({{ $contact['id'] }})" title="{{ $contact['is_active'] ? 'Desactivar' : 'Activar' }}" />
                                <x-cms.action-button icon="fa-solid fa-trash" variant="danger" onclick="confirmDeleteContact({{ $contact['id'] }})" title="Eliminar" />
                            </div>
                        </div>
                    </x-cms.card>
                @empty
                    <div class="col-span-full bg-white rounded-2xl shadow-sm border border-gray-100 p-16 text-center" style="animation: emptyPulse 3s ease-in-out infinite">
                        <div class="w-16 h-16 rounded-2xl bg-blue-600 flex items-center justify-center mx-auto mb-4 shadow-sm">
                            <i class="fa-solid fa-location-dot text-2xl text-white"></i>
                        </div>
                        <p class="text-gray-500 font-medium mb-1">No hay información de contacto</p>
                        <p class="text-gray-400 text-sm mb-4">Agrega dirección, teléfono, horario, WhatsApp, etc.</p>
                    </div>
                @endforelse
            </div>

            <style>
                @keyframes cardEntry { from { opacity: 0; transform: translateY(20px) scale(0.97); } to { opacity: 1; transform: translateY(0) scale(1); } }
                @keyframes emptyPulse { 0%, 100% { opacity: 1; } 50% { opacity: 0.6; } }
            </style>

            <script>
                function confirmDeleteContact(id) {
                    Swal.fire({
                        title: '¿Eliminar contacto?',
                        text: 'Esta acción no se puede deshacer.',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#dc2626',
                        cancelButtonColor: '#6b7280',
                        confirmButtonText: 'Sí, eliminar',
                        cancelButtonText: 'Cancelar',
                        customClass: { popup: 'rounded-xl shadow-xl border border-gray-200', confirmButton: 'rounded-lg px-5 py-2 font-semibold text-sm', cancelButton: 'rounded-lg px-5 py-2 font-semibold text-sm' }
                    }).then((result) => {
                        if (result.isConfirmed) { @this.call('delete', id) }
                    });
                }
                function confirmToggleContact(id) {
                    Swal.fire({
                        title: '¿Cambiar estado?',
                        text: 'Se activará o desactivará este contacto.',
                        icon: 'question',
                        showCancelButton: true,
                        confirmButtonColor: '#2563eb',
                        cancelButtonColor: '#6b7280',
                        confirmButtonText: 'Sí, cambiar',
                        cancelButtonText: 'Cancelar',
                        customClass: { popup: 'rounded-xl shadow-xl border border-gray-200', confirmButton: 'rounded-lg px-5 py-2 font-semibold text-sm', cancelButton: 'rounded-lg px-5 py-2 font-semibold text-sm' }
                    }).then((result) => {
                        if (result.isConfirmed) { @this.call('toggleActive', id) }
                    });
                }
            </script>
        </div>
    </x-cms.layout>

    {{-- Componente anidado para formulario --}}
    <livewire:cms.contacto.form-contacto />
</div>
