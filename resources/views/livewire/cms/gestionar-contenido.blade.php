<div>
    <x-cms.layout
        title="Gestionar Secciones"
        description="Administra las secciones del landing page"
    >
    <div class="h-full flex flex-col">
        <script>
            if ('scrollRestoration' in history) {
                history.scrollRestoration = 'manual';
            }
        </script>

        <style>
            .hide-scrollbar::-webkit-scrollbar { display: none; }
            .hide-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
            [x-cloak] { display: none !important; }
        </style>

        @php
            $sectionIconMap = [
                'hero' => 'fa-house', 'about' => 'fa-circle-info', 'services' => 'fa-briefcase',
                'features' => 'fa-star', 'testimonials' => 'fa-quote-left', 'contact' => 'fa-envelope',
                'gallery' => 'fa-images', 'cta' => 'fa-bullhorn', 'team' => 'fa-people-group',
                'pricing' => 'fa-tag', 'faq' => 'fa-circle-question', 'footer' => 'fa-grip-lines',
            ];
        @endphp

        <div class="px-6 lg:px-8 flex-1 flex flex-col"
             x-data="{
                activeTab: '{{ $sections[0]['id'] ?? '' }}',
                reloadIframe(sectionId) {
                    this.$nextTick(() => {
                        const panel = document.getElementById('panel-' + sectionId);
                        if (!panel) return;
                        const iframe = panel.querySelector('.preview-iframe');
                        if (!iframe) return;
                        const src = iframe.getAttribute('src');
                        if (!src) return;
                        const parts = src.split('#');
                        const baseUrl = parts[0].split('?')[0];
                        const anchor = parts.length > 1 ? '#' + parts[1] : '';
                        iframe.src = baseUrl + '?v=' + Date.now() + anchor;
                    });
                }
             }"
             x-init="reloadIframe(activeTab)">

            <div class="bg-white rounded-xl shadow-sm border border-gray-200/80 p-2 flex overflow-x-auto gap-2 hide-scrollbar shrink-0 mb-2">
                @foreach($sections as $section)
                    @php
                        $icon = $sectionIconMap[$section['key']] ?? 'fa-layer-group';
                    @endphp
                    <button
                        @click="activeTab = '{{ $section['id'] }}'; reloadIframe('{{ $section['id'] }}')"
                        data-tab-id="{{ $section['id'] }}"
                        :class="activeTab === '{{ $section['id'] }}'
                            ? 'bg-blue-600 text-white shadow-sm'
                            : 'bg-gray-50 text-gray-600 hover:bg-gray-100 border border-gray-200'"
                        class="px-3 sm:px-4 py-2 rounded-lg font-semibold text-xs sm:text-sm whitespace-nowrap transition-all duration-200 flex items-center gap-2 shrink-0">
                        <i class="fa-solid {{ $icon }} text-[10px]"></i>
                        {{ $section['title'] }}
                    </button>
                @endforeach
            </div>

            <p class="text-xs text-gray-400 mb-2 shrink-0">
                Seleccioná una sección para ver su vista previa y editar su contenido.
            </p>

            <div class="relative flex-1 pb-4">

                @foreach ($sections as $index => $section)

                    @php
                        $imageLimits = ['hero' => 5, 'about' => 2];
                        $maxImages = $imageLimits[$section['key']] ?? 0;
                        $currentCount = count($section['media_items'] ?? []);
                        $icon = $sectionIconMap[$section['key']] ?? 'fa-layer-group';
                    @endphp

                    <div
                        id="panel-{{ $section['id'] }}"
                        x-show="activeTab === '{{ $section['id'] }}'"
                        class="bg-white rounded-xl shadow-sm border border-gray-200/85 h-full flex flex-col">

                        <div class="flex items-center gap-3 px-5 py-2.5 bg-blue-600 text-white shrink-0">
                            <i class="fa-solid {{ $icon }} text-sm"></i>
                            <span class="font-bold text-sm">{{ $section['title'] }}</span>
                            <span class="ml-auto text-xs text-blue-100 font-mono">{{ $section['key'] }}</span>
                        </div>

                        <div class="flex flex-col lg:flex-row items-stretch flex-1">

                            <div class="w-full lg:w-1/2 lg:border-r p-4 border-b lg:border-b-0 border-gray-200 flex flex-col">
                                <p class="text-xs font-semibold text-gray-500 uppercase mb-2 shrink-0">Vista previa</p>

                                <div class="flex-1 flex flex-col justify-center">
                                    <x-section-preview
                                        :section="$section"
                                        :refreshKey="$refreshKey"
                                        :highlight="$highlightSection === $section['key']"
                                    />
                                </div>
                            </div>

                            <div class="w-full lg:w-1/2 p-4 flex flex-col gap-3 overflow-y-auto">
                                <div>
                                    {{-- Componente anidado: Galería de imágenes --}}
                                    @if($maxImages > 0)
                                        <livewire:cms.contenido.galeria-imagenes
                                            wire:key="galeria-{{ $section['id'] }}"
                                            :sectionId="$section['id']"
                                            :maxImages="$maxImages"
                                            :mediaItems="$section['media_items'] ?? []"
                                        />
                                    @endif
                                </div>

                                <div class="bg-gray-50 rounded-lg p-3 border border-gray-200 space-y-2.5">
                                    <h6 class="text-xs font-bold text-gray-800 border-b border-gray-200 pb-1.5 flex items-center gap-1.5">
                                        <i class="fa-solid fa-pen-nib text-gray-400"></i>
                                        Contenido de Texto
                                    </h6>

                                    @if ($errors->any())
                                        <div class="bg-red-50 border border-red-200 text-red-700 px-3 py-1.5 rounded-lg text-xs">
                                            @foreach ($errors->all() as $error)
                                                <p class="flex items-start gap-1.5"><i class="fa-solid fa-circle-exclamation mt-0.5"></i>{{ $error }}</p>
                                            @endforeach
                                        </div>
                                    @endif

                                    <div class="space-y-2">
                                        <div>
                                            <label class="block text-[11px] font-semibold text-gray-700 mb-0.5">Título principal</label>
                                            <input type="text" class="w-full border border-gray-200 rounded-lg px-3 py-1.5 text-xs focus:ring-2 focus:ring-blue-600 focus:border-blue-600 bg-white transition-all" wire:model="sectionData.{{ $section['id'] }}.title">
                                        </div>

                                        <div>
                                            <label class="block text-[11px] font-semibold text-gray-700 mb-0.5">Subtítulo</label>
                                            <input type="text" class="w-full border border-gray-200 rounded-lg px-3 py-1.5 text-xs focus:ring-2 focus:ring-blue-600 focus:border-blue-600 bg-white transition-all" wire:model="sectionData.{{ $section['id'] }}.subtitle">
                                        </div>

                                        <div>
                                            <label class="block text-[11px] font-semibold text-gray-700 mb-0.5">Descripción</label>
                                            <textarea class="w-full border border-gray-200 rounded-lg px-3 py-1.5 text-xs focus:ring-2 focus:ring-blue-600 focus:border-blue-600 bg-white transition-all resize-none" rows="2" wire:model="sectionData.{{ $section['id'] }}.description"></textarea>
                                        </div>

                                        <div class="flex justify-end pt-0.5">
                                            <button type="button" class="w-full sm:w-auto px-4 py-1.5 rounded-lg bg-blue-600 text-white hover:bg-blue-700 text-xs font-semibold transition-all flex items-center justify-center gap-2" onclick="confirmSaveSection({{ $section['id'] }})">
                                                <i class="fa-solid fa-check"></i> Guardar Cambios
                                            </button>
                                        </div>
                                    </div>
                                </div>

                            </div>

                        </div>

                    </div>

                @endforeach

            </div>

        </div>

        <script>
            function confirmSaveSection(sectionId) {
                Swal.fire({
                    title: '¿Guardar cambios?',
                    text: 'Se actualizará el contenido de esta sección en el sitio.',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#2563eb',
                    cancelButtonColor: '#6b7280',
                    confirmButtonText: 'Sí, guardar',
                    cancelButtonText: 'Cancelar',
                    customClass: {
                        popup: 'rounded-xl shadow-xl border border-gray-200',
                        confirmButton: 'rounded-lg px-5 py-2 font-semibold text-sm',
                        cancelButton: 'rounded-lg px-5 py-2 font-semibold text-sm'
                    }
                }).then((result) => {
                    if (result.isConfirmed) {
                        @this.call('saveSection', sectionId);
                    }
                });
            }

            Livewire.on('refresh-preview', function() {
                document.querySelectorAll('.preview-iframe').forEach(function(iframe) {
                    var src = iframe.getAttribute('src');
                    if (src) {
                        var parts = src.split('#');
                        var baseUrl = parts[0].split('?')[0];
                        var anchor = parts.length > 1 ? '#' + parts[1] : '';
                        iframe.src = baseUrl + '?v=' + Date.now() + anchor;
                    }
                });
            });
        </script>
    </div>
    </x-cms.layout>
    </div>
