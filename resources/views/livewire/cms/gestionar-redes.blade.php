<div>
    <x-cms.layout
        title="Redes Sociales"
        description="Administra los enlaces a redes sociales que se muestran en el footer del landing page"
        headerIcon='<i class="fa-brands fa-instagram text-white"></i>'
    >
        <div class="px-6 lg:px-8">
            @php
                $platformBadges = [
                    'facebook'  => ['label' => 'FB', 'bg' => '#1877F2'],
                    'instagram' => ['label' => 'IG', 'bg' => '#E1306C'],
                    'whatsapp'  => ['label' => 'WA', 'bg' => '#25D366'],
                    'tiktok'    => ['label' => 'TT', 'bg' => '#000000'],
                    'youtube'   => ['label' => 'YT', 'bg' => '#FF0000'],
                    'twitter'   => ['label' => 'X',  'bg' => '#000000'],
                    'linkedin'  => ['label' => 'IN', 'bg' => '#0A66C2'],
                ];

                $badgeFor = function ($platform) use ($platformBadges) {
                    return $platformBadges[$platform] ?? [
                        'label' => strtoupper(Str::substr($platform ?: '?', 0, 2)),
                        'bg' => '#2563EB',
                    ];
                };
            @endphp

            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-8 pb-4 border-b border-gray-200">
                <div>
                    <h2 class="text-xl font-semibold text-gray-900">{{ count($links) }} redes sociales</h2>
                    <p class="text-gray-500 text-sm">Facebook, Instagram, WhatsApp, TikTok, YouTube, X/Twitter, LinkedIn</p>
                </div>
                <button wire:click="$dispatch('abrir-crear-redes')"
                        class="w-full sm:w-auto px-5 py-2.5 rounded-xl bg-blue-600 text-white font-semibold shadow-sm shadow-blue-200 hover:bg-blue-700 hover:shadow-md hover:shadow-blue-300 hover:-translate-y-0.5 transition-all duration-300 flex items-center justify-center gap-2">
                    <i class="fa-solid fa-plus"></i> Nueva Red
                </button>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
                @forelse ($links as $link)
                    @php $badge = $badgeFor($link['platform']); @endphp
                    <x-cms.card class="flex flex-col h-full group bg-white border border-gray-200 hover:border-blue-200 hover:shadow-lg hover:shadow-blue-100/50 transition-all duration-300" style="animation: cardEntry 0.4s ease-out {{ $loop->index * 0.06 }}s both">
                        <div class="p-6 flex-1">
                            <div class="flex items-start justify-between mb-4">
                                <div class="w-12 h-12 rounded-xl flex items-center justify-center font-extrabold text-sm tracking-wide shadow-sm transition-transform duration-300 group-hover:scale-105"
                                     style="background-color: {{ $badge['bg'] }}; color: #FFFFFF;">
                                    {{ $badge['label'] }}
                                </div>
                                <x-cms.status-badge :active="$link['is_active']" />
                            </div>
                            <h5 class="font-bold text-gray-900 text-lg mb-1 capitalize group-hover:text-blue-700 transition-colors">{{ $link['platform'] }}</h5>
                            <p class="text-slate-600 text-sm break-all">{{ Str::limit($link['url'], 50) }}</p>
                        </div>
                        <div class="border-t border-gray-50 bg-gray-50/30 px-6 py-3.5 flex items-center justify-between">
                            <div class="flex gap-1">
                                <x-cms.action-button icon="fa-solid fa-pen" variant="warning" wireClick="$dispatch('abrir-editar-redes', { id: {{ $link['id'] }} })" title="Editar" />
                                <x-cms.action-button icon="fa-solid fa-{{ $link['is_active'] ? 'eye-slash' : 'eye' }}" variant="{{ $link['is_active'] ? 'ghost' : 'success' }}" onclick="confirmToggleLink({{ $link['id'] }})" title="{{ $link['is_active'] ? 'Desactivar' : 'Activar' }}" />
                                <x-cms.action-button icon="fa-solid fa-trash" variant="danger" onclick="confirmDeleteLink({{ $link['id'] }})" title="Eliminar" />
                            </div>
                            <a href="{{ $link['url'] }}" target="_blank"
                               class="w-9 h-9 flex items-center justify-center rounded-lg bg-blue-50 text-blue-600 hover:bg-blue-100 hover:text-blue-700 transition-all"
                               title="Visitar">
                                <i class="fa-solid fa-external-link-alt text-xs"></i>
                            </a>
                        </div>
                    </x-cms.card>
                @empty
                    <div class="col-span-full bg-white rounded-2xl shadow-sm border border-gray-100 p-16 text-center" style="animation: emptyPulse 3s ease-in-out infinite">
                        <div class="w-16 h-16 rounded-2xl bg-blue-600 flex items-center justify-center mx-auto mb-4 shadow-sm">
                            <i class="fa-brands fa-instagram text-2xl text-white"></i>
                        </div>
                        <p class="text-gray-500 font-medium mb-1">No hay redes sociales</p>
                        <p class="text-gray-400 text-sm mb-4">Agrega Facebook, Instagram, WhatsApp, etc.</p>
                    </div>
                @endforelse
            </div>

            <style>
                @keyframes cardEntry { from { opacity: 0; transform: translateY(20px) scale(0.97); } to { opacity: 1; transform: translateY(0) scale(1); } }
                @keyframes emptyPulse { 0%, 100% { opacity: 1; } 50% { opacity: 0.6; } }
            </style>

            <script>
                function confirmDeleteLink(id) {
                    Swal.fire({
                        title: '¿Eliminar red social?',
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
                function confirmToggleLink(id) {
                    Swal.fire({
                        title: '¿Cambiar estado?',
                        text: 'Se activará o desactivará esta red social.',
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

    <livewire:cms.redes.form-redes />
</div>
