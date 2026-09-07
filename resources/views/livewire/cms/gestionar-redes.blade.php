<x-cms.layout
    title="Redes Sociales"
    description="Administra los enlaces a redes sociales que se muestran en el footer del landing page"
    headerIcon='<i class="fa-brands fa-instagram text-blue-600"></i>'
>
    {{-- Identidad visual por plataforma: SOLO color + iniciales.               --}}
    {{-- No depende de ninguna librería de íconos externa (FontAwesome, etc.),  --}}
    {{-- así que siempre se ve, sin importar si el CDN de íconos carga o no.    --}}
    @php
        $platformBadges = [
            'facebook'  => ['label' => 'FB', 'bg' => '#1877F2'],
            'instagram' => ['label' => 'IG', 'bg' => '#E1306C'],
            'whatsapp'  => ['label' => 'WA', 'bg' => '#25D366'],
            'tiktok'    => ['label' => 'TT', 'bg' => '#000000'],
            'youtube'   => ['label' => 'YT', 'bg' => '#FF0000'],
            'twitter'   => ['label' => 'X',  'bg' => '#000000'],
            'x'         => ['label' => 'X',  'bg' => '#000000'],
            'linkedin'  => ['label' => 'IN', 'bg' => '#0A66C2'],
        ];

        $badgeFor = function ($platform) use ($platformBadges) {
            return $platformBadges[$platform] ?? [
                'label' => strtoupper(Str::substr($platform ?: '?', 0, 2)),
                'bg' => '#2563EB',
            ];
        };
    @endphp

    {{-- Header with Create Button --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-8 pb-4 border-b border-blue-100">
        <div>
            <h2 class="text-xl font-semibold text-blue-950">{{ count($links) }} redes sociales</h2>
            <p class="text-blue-700/70 text-sm">Facebook, Instagram, WhatsApp, TikTok, YouTube, X/Twitter, LinkedIn</p>
        </div>
        <button wire:click="create"
                class="w-full sm:w-auto px-5 py-2.5 rounded-xl bg-blue-600 text-white font-semibold shadow-sm shadow-blue-200 hover:bg-blue-700 hover:shadow-md hover:shadow-blue-300 hover:-translate-y-0.5 transition-all duration-300 flex items-center justify-center gap-2">
            <i class="fa-solid fa-plus"></i> Nueva Red
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

    {{-- Links Grid --}}
    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
        @forelse ($links as $link)
            @php
                $badge = $badgeFor($link['platform']);
            @endphp
            <x-cms.card class="flex flex-col h-full group bg-white border border-blue-100 hover:border-blue-300 hover:shadow-lg hover:shadow-blue-100/50 transition-all duration-300" style="animation: cardEntry 0.4s ease-out {{ $loop->index * 0.06 }}s both">
                <div class="p-6 flex-1">
                    <div class="flex items-start justify-between mb-4">
                        <div class="w-12 h-12 rounded-xl flex items-center justify-center font-extrabold text-sm tracking-wide shadow-sm transition-transform duration-300 group-hover:scale-105"
                             style="background-color: {{ $badge['bg'] }}; color: #FFFFFF;">
                            {{ $badge['label'] }}
                        </div>
                        <x-cms.status-badge :active="$link['is_active']" />
                    </div>
                    <h5 class="font-bold text-blue-950 text-lg mb-1 capitalize group-hover:text-blue-700 transition-colors">{{ $link['platform'] }}</h5>
                    <p class="text-slate-600 text-sm break-all">{{ Str::limit($link['url'], 50) }}</p>
                </div>
                <div class="border-t border-blue-50 bg-blue-50/30 px-6 py-3.5 flex items-center justify-between">
                    <div class="flex gap-1">
                        <x-cms.action-button icon="fa-solid fa-pen" variant="warning" wireClick="edit({{ $link['id'] }})" title="Editar" />
                        <x-cms.action-button icon="fa-solid fa-{{ $link['is_active'] ? 'eye-slash' : 'eye' }}" variant="{{ $link['is_active'] ? 'ghost' : 'success' }}" onclick="confirmToggleLink({{ $link['id'] }})" title="{{ $link['is_active'] ? 'Desactivar' : 'Activar' }}" />
                        <x-cms.action-button icon="fa-solid fa-trash" variant="danger" onclick="confirmDeleteLink({{ $link['id'] }})" title="Eliminar" />
                    </div>
                    <a href="{{ $link['url'] }}" target="_blank"
                       class="w-9 h-9 flex items-center justify-center rounded-lg bg-blue-100 text-blue-600 hover:bg-blue-200 hover:text-blue-800 transition-all"
                       title="Visitar">
                        <i class="fa-solid fa-external-link-alt text-xs"></i>
                    </a>
                </div>
            </x-cms.card>
        @empty
            <div class="col-span-full bg-white rounded-2xl shadow-sm border border-gray-100 p-16 text-center" style="animation: emptyPulse 3s ease-in-out infinite">
                <div class="w-16 h-16 rounded-2xl bg-blue-50 flex items-center justify-center mx-auto mb-4 border border-blue-100">
                    <i class="fa-brands fa-instagram text-2xl text-blue-400"></i>
                </div>
                <p class="text-gray-500 font-medium mb-1">No hay redes sociales</p>
                <p class="text-gray-400 text-sm mb-4">Agrega Facebook, Instagram, WhatsApp, etc.</p>
                <button wire:click="create" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-blue-600 text-white text-sm font-semibold hover:bg-blue-700 transition-all">
                    <i class="fa-solid fa-plus"></i> Agregar Red
                </button>
            </div>
        @endforelse
    </div>

    {{-- Create/Edit Modal --}}
    @if($showForm)
        <div class="fixed inset-0 bg-black/50 backdrop-blur-sm flex items-center justify-center z-50 overflow-y-auto py-8 px-4" x-data="{}" x-init="$watch('showForm', v => { if(v) document.body.style.overflow = 'hidden'; else document.body.style.overflow = ''; })">
            <div class="bg-white rounded-2xl shadow-2xl w-full max-w-xl max-h-[90vh] overflow-y-auto" style="animation: modalFadeIn 0.3s ease-out">
                <div class="sticky top-0 bg-white border-b border-gray-100 px-6 py-4 flex justify-between items-center rounded-t-2xl z-10">
                    <h4 class="text-lg font-bold text-gray-900">{{ $editingId ? 'Editar' : 'Nueva' }} Red Social</h4>
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
                            <label class="block text-sm font-semibold text-gray-700 mb-1.5 pl-3 border-l-3 border-blue-500">Plataforma *</label>
                            <select class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all bg-white shadow-sm"
                                    wire:model.live="platform">
                                @foreach($platforms as $key => $label)
                                    <option value="{{ $key }}">{{ $label }}</option>
                                @endforeach
                            </select>
                            <p class="text-xs text-gray-400 mt-1 pl-3">Al cambiar la plataforma, el ícono sugerido se actualiza abajo.</p>
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1.5 pl-3 border-l-3 border-blue-500">URL *</label>
                            <input type="url"
                                   class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all bg-white shadow-sm"
                                   wire:model="url"
                                   placeholder="https://facebook.com/arturomotors">
                        </div>

                        {{-- ================================================= --}}
                        {{-- ÍCONO: se genera automático según la plataforma,   --}}
                        {{-- solo se muestra la vista previa (color + iniciales)--}}
                        {{-- No depende de ninguna librería de íconos externa.  --}}
                        {{-- ================================================= --}}
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1.5 pl-3 border-l-3 border-blue-500">Vista previa</label>

                            @php $previewBadge = $badgeFor($platform); @endphp
                            <div class="flex items-center gap-3 p-3 rounded-xl border border-gray-200 bg-gray-50">
                                <div class="w-12 h-12 shrink-0 rounded-xl flex items-center justify-center font-extrabold text-sm tracking-wide shadow-sm"
                                     style="background-color: {{ $previewBadge['bg'] }}; color: #FFFFFF;">
                                    {{ $previewBadge['label'] }}
                                </div>
                                <div>
                                    <p class="text-sm font-semibold text-gray-800 capitalize">{{ $platform ?: 'Selecciona una plataforma' }}</p>
                                    <p class="text-xs text-gray-400">Así se verá en la tarjeta de la lista</p>
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
        function confirmDeleteLink(id) {
            Swal.fire({
                title: '¿Eliminar red social?',
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
        function confirmToggleLink(id) {
            Swal.fire({
                title: '¿Cambiar estado?',
                text: 'Se activará o desactivará esta red social.',
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