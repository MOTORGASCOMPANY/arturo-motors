<div>
    <x-cms.layout
        title="Servicios del Landing"
        description="Administra los servicios que se muestran en el landing page"
        headerIcon='<i class="fa-solid fa-wrench text-white"></i>'
    >
        <div class="px-6 lg:px-8">

            {{-- Header with Create Button --}}
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-8 pb-4 border-b border-gray-200">
                <div>
                    <h2 class="text-xl font-semibold text-gray-900">{{ count($services) ?? 0 }} servicios</h2>
                    <p class="text-gray-500 text-sm">Gestiona los servicios que ofrece Arturo Motors</p>
                </div>
                <button wire:click="$dispatch('abrir-crear-servicio')"
                        class="w-full sm:w-auto px-5 py-2.5 rounded-xl bg-blue-600 text-white font-semibold shadow-sm hover:bg-blue-700 hover:shadow-md transition-all flex items-center justify-center gap-2">
                    <i class="fa-solid fa-plus"></i> Nuevo Servicio
                </button>
            </div>

            {{-- Services Grid --}}
            <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
                @forelse ($services as $service)
                    <x-cms.card
                        wire:key="service-{{ $service['id'] }}"
                        x-data="{ highlight: false }"
                        x-on:service-moved.window="if ($event.detail.id === {{ $service['id'] }}) { highlight = true; setTimeout(() => highlight = false, 650) }"
                        :class="{ 'ring-2 ring-blue-400 shadow-lg scale-[1.015]': highlight }"
                        class="flex flex-col h-full group transition-all duration-500 ease-out bg-white"
                        style="animation: cardEntry 0.4s ease-out {{ $loop->index * 0.06 }}s both">
                        <div class="p-6 flex-1">
                            <div class="flex items-start justify-between mb-4">
                                <div class="w-12 h-12 rounded-xl bg-blue-50 flex items-center justify-center text-blue-600 text-xl border border-blue-100 shadow-sm">
                                    <i class="{{ $service['icon'] ?? 'fa-solid fa-cog' }}"></i>
                                </div>
                                <x-cms.status-badge :active="$service['is_active']" />
                            </div>
                            <h6 class="font-bold text-gray-900 text-lg mb-2">{{ $service['title'] }}</h6>
                            <p class="text-gray-500 text-sm leading-relaxed mb-3">{{ Str::limit($service['description'], 100) }}</p>
                            @if($service['features'])
                                <div class="flex flex-wrap gap-1.5">
                                    @foreach(array_slice($service['features'], 0, 3) as $f)
                                        <span class="bg-blue-50 text-blue-700 text-xs px-2.5 py-1 rounded-lg border border-blue-100">{{ $f }}</span>
                                    @endforeach
                                    @if(count($service['features']) > 3)
                                        <span class="text-gray-400 text-xs">+{{ count($service['features']) - 3 }}</span>
                                    @endif
                                </div>
                            @endif
                        </div>
                        <div class="border-t border-gray-100 bg-white px-6 py-3.5 flex items-center justify-between">
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
                                <x-cms.action-button icon="fa-solid fa-pen" variant="warning" wireClick="$dispatch('abrir-editar-servicio', { id: {{ $service['id'] }} })" title="Editar" />
                                <x-cms.action-button icon="fa-solid fa-{{ $service['is_active'] ? 'eye-slash' : 'eye' }}" variant="{{ $service['is_active'] ? 'ghost' : 'success' }}" onclick="confirmToggleService({{ $service['id'] }})" title="{{ $service['is_active'] ? 'Desactivar' : 'Activar' }}" />
                                <x-cms.action-button icon="fa-solid fa-trash" variant="danger" onclick="confirmDeleteService({{ $service['id'] }})" title="Eliminar" />
                            </div>
                        </div>
                    </x-cms.card>
                @empty
                    <div class="col-span-full bg-white rounded-2xl shadow-sm border border-gray-100 p-16 text-center" style="animation: emptyPulse 3s ease-in-out infinite">
                        <div class="w-16 h-16 rounded-2xl bg-blue-600 flex items-center justify-center mx-auto mb-4 shadow-sm">
                            <i class="fa-solid fa-wrench text-2xl text-white"></i>
                        </div>
                        <p class="text-gray-500 font-medium mb-1">No hay servicios creados</p>
                        <p class="text-gray-400 text-sm">Usa el botón <span class="font-semibold text-blue-600">"Nuevo Servicio"</span> de arriba para crear el primero</p>
                    </div>
                @endforelse
            </div>

            {{-- Styles --}}
            <style>
                @keyframes cardEntry { from { opacity: 0; transform: translateY(20px) scale(0.97); } to { opacity: 1; transform: translateY(0) scale(1); } }
                @keyframes emptyPulse { 0%, 100% { opacity: 1; } 50% { opacity: 0.6; } }
            </style>

            <script>
                function confirmDeleteService(id) {
                    Swal.fire({
                        title: '¿Eliminar servicio?',
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
                function confirmToggleService(id) {
                    Swal.fire({
                        title: '¿Cambiar estado?',
                        text: 'Se activará o desactivará este servicio.',
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
    <livewire:cms.servicios.form-servicio />
</div>
