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
    
        @if($successMessage)
            <div x-data
                 x-init="Swal.fire({toast:true,position:'top-end',icon:'success',title:@js($successMessage),showConfirmButton:false,timer:3500,timerProgressBar:true}); $wire.clearSuccessMessage()"
                 style="display:none">
            </div>
        @endif
    
        @if($errorMessage)
            <div x-data
                 x-init="Swal.fire({toast:true,position:'top-end',icon:'error',title:@js($errorMessage),showConfirmButton:false,timer:3500,timerProgressBar:true}); $wire.clearErrorMessage()"
                 style="display:none">
            </div>
        @endif
    
        @if (session()->has('success') && !$successMessage)
            <div x-data
                 x-init="Swal.fire({toast:true,position:'top-end',icon:'success',title:@js(session('success')),showConfirmButton:false,timer:3500,timerProgressBar:true})"
                 style="display:none">
            </div>
        @endif
    
        <div x-data="{ show: false, startTime: 0 }"
             x-show="show"
             x-cloak
             @uploading.window="show = true; startTime = Date.now()"
             @upload-done.window="setTimeout(() => { show = false }, Math.max(0, 1500 - (Date.now() - startTime)))"
             class="fixed inset-0 z-[9999] flex items-center justify-center bg-gray-900/80 backdrop-blur-sm px-4"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0">
            <div class="bg-white rounded-xl shadow-2xl p-8 flex flex-col items-center gap-4">
                <div class="w-12 h-12 border-4 border-gray-200 border-t-blue-600 rounded-full animate-spin"></div>
                <div class="text-center">
                    <p class="text-gray-900 font-bold text-sm">Subiendo imagen</p>
                    <p class="text-gray-500 text-xs mt-1">Procesando...</p>
                </div>
            </div>
        </div>
    
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
                        $canUpload = $maxImages > 0 && $currentCount < $maxImages;
                        $hasImages = $maxImages > 0;
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
                                    @if($hasImages && $currentCount > 0)
                                        <div class="mb-3">
                                            <p class="text-xs font-semibold text-gray-500 mb-2 flex justify-between items-center">
                                                <span class="flex items-center gap-1.5">
                                                    <i class="fa-solid fa-images text-gray-400"></i>
                                                    Galería de Imágenes
                                                </span>
                                                <span class="bg-blue-50 text-blue-600 px-2.5 py-0.5 rounded-full text-[10px] font-semibold">
                                                    {{ $currentCount }}/{{ $maxImages }}
                                                </span>
                                            </p>
                                            <div class="grid grid-cols-2 sm:grid-cols-3 gap-2">
                                                @foreach($section['media_items'] as $pm)
                                                    <div class="relative group rounded-lg overflow-hidden border border-gray-200 bg-white hover:shadow-md transition-shadow">
                                                        <img src="{{ asset('storage/' . $pm['media']['file_path']) }}" class="w-full h-16 object-cover">
                                                        <div class="absolute inset-0 bg-gray-900/60 opacity-0 group-hover:opacity-100 transition-all flex flex-col items-center justify-center gap-1 p-2">
                                                            <a href="{{ asset('storage/' . $pm['media']['file_path']) }}" target="_blank"
                                                               class="bg-white text-gray-800 text-[10px] font-semibold px-2 py-1 rounded hover:bg-gray-100 flex items-center gap-1 w-full justify-center">
                                                                <i class="fa-solid fa-expand"></i> Ver
                                                            </a>
                                                            <button type="button" onclick="confirmDeleteImage({{ $pm['id'] }})"
                                                                    class="bg-red-500 text-white text-[10px] font-semibold px-2 py-1 rounded hover:bg-red-600 flex items-center gap-1 w-full justify-center">
                                                                <i class="fa-solid fa-trash"></i> Borrar
                                                            </button>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    @endif
    
                                    @if($hasImages)
                                        <div class="mb-3">
                                            @if($canUpload)
                                                <div x-data="{ dragOver: false }"
                                                     @dragover.prevent="dragOver = true"
                                                     @dragleave.prevent="dragOver = false"
                                                     @drop.prevent="dragOver = false; $refs['fileInput{{ $section['id'] }}'].files = $event.dataTransfer.files; $refs['fileInput{{ $section['id'] }}'].dispatchEvent(new Event('change'))"
                                                     :class="dragOver ? 'border-blue-400 bg-blue-50' : 'border-gray-200 bg-white'"
                                                     class="rounded-lg p-2.5 border transition-colors">
                                                    <input type="file" id="file-{{ $section['id'] }}" x-ref="fileInput{{ $section['id'] }}" accept="image/*" class="hidden">
                                                    <div class="flex flex-col sm:flex-row gap-2 items-center">
                                                        <button type="button" onclick="document.getElementById('file-{{ $section['id'] }}').click()"
                                                            class="w-full sm:flex-1 text-xs border-2 border-dashed border-gray-300 rounded-lg px-3 py-1.5 bg-gray-50 text-left text-gray-600 hover:bg-gray-100 hover:border-blue-400 transition-all cursor-pointer flex items-center truncate">
                                                            <i class="fa-solid fa-image text-gray-400 mr-2 shrink-0"></i>
                                                            <span id="file-label-{{ $section['id'] }}" class="truncate">Arrastrá una imagen o hacé clic acá...</span>
                                                        </button>
                                                        <button type="button" onclick="jsUpload({{ $section['id'] }}, this)"
                                                            class="w-full sm:w-auto bg-blue-600 text-white px-3 py-1.5 rounded-lg hover:bg-blue-700 font-semibold transition-all flex justify-center items-center gap-2 text-xs shrink-0">
                                                            <i class="fa-solid fa-upload"></i> Subir
                                                        </button>
                                                    </div>
                                                    <div id="upload-progress-{{ $section['id'] }}" class="hidden mt-2">
                                                        <div class="h-1.5 bg-gray-100 rounded-full overflow-hidden">
                                                            <div id="upload-bar-{{ $section['id'] }}" class="h-full bg-emerald-500 rounded-full transition-all" style="width: 0%"></div>
                                                        </div>
                                                    </div>
                                                    <p class="text-[10px] text-gray-400 mt-1"><i class="fa-solid fa-circle-info mr-1"></i>JPG, PNG, WebP. Máx: 5MB.</p>
                                                </div>
                                            @else
                                                <div class="bg-gray-50 rounded-lg p-2.5 border border-gray-200 text-center flex items-center justify-center gap-2">
                                                    <i class="fa-solid fa-circle-check text-gray-400"></i>
                                                    <p class="text-gray-500 text-xs font-semibold">Límite alcanzado ({{ $maxImages }})</p>
                                                </div>
                                            @endif
                                        </div>
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
            (function () {
                var lastGoodY = window.scrollY;
                var userGesture = false;
                var gestureTimer = null;
    
                function markUserGesture() {
                    userGesture = true;
                    clearTimeout(gestureTimer);
                    gestureTimer = setTimeout(function () {
                        userGesture = false;
                    }, 250);
                }
    
                ['wheel', 'touchmove', 'touchstart', 'keydown', 'mousedown'].forEach(function (evt) {
                    window.addEventListener(evt, markUserGesture, { passive: true });
                });
    
                window.addEventListener('scroll', function () {
                    var current = window.scrollY;
                    if (!userGesture && Math.abs(current - lastGoodY) > 150) {
                        window.scrollTo(0, lastGoodY);
                        return;
                    }
                    lastGoodY = current;
                }, { passive: true });
            })();
    
            function showToast(type, message) {
                if (!message) return;
                Swal.fire({
                    toast: true,
                    position: 'top-end',
                    icon: type === 'error' ? 'error' : 'success',
                    title: message,
                    showConfirmButton: false,
                    timer: 3500,
                    timerProgressBar: true
                });
            }
    
            function restoreScrollTarget() {
                var savedY = sessionStorage.getItem('cms_scroll_y');
                return savedY !== null ? parseInt(savedY, 10) : 0;
            }
    
            function intentionalScrollTo(y) {
                window.dispatchEvent(new Event('wheel'));
                window.scrollTo(0, y);
            }
    
            window.addEventListener('load', function () {
                var targetY = restoreScrollTarget();
    
                intentionalScrollTo(targetY);
    
                [30, 100, 300, 600, 1000].forEach(function (delay) {
                    setTimeout(function () {
                        intentionalScrollTo(targetY);
                    }, delay);
                });
    
                sessionStorage.removeItem('cms_scroll_y');
    
                var pendingToast = sessionStorage.getItem('cms_pending_toast');
    
                if (pendingToast) {
                    sessionStorage.removeItem('cms_pending_toast');
                    try {
                        var t = JSON.parse(pendingToast);
                        showToast(t.type, t.message);
                    } catch (e) {}
                }
            });
    
            document.addEventListener('change', function(e) {
                if (e.target.type === 'file' && e.target.files.length) {
                    var id = e.target.id.replace('file-', '');
                    var label = document.getElementById('file-label-' + id);
                    if (label) {
                        label.textContent = e.target.files[0].name;
                    }
                }
            }, true);
    
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
    
            function confirmDeleteImage(mediaId) {
                Swal.fire({
                    title: '¿Estás seguro?',
                    text: 'Esta acción eliminará la imagen de forma permanente.',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#dc2626',
                    cancelButtonColor: '#6b7280',
                    confirmButtonText: 'Sí, eliminar',
                    cancelButtonText: 'Cancelar',
                    customClass: {
                        popup: 'rounded-xl shadow-xl border border-gray-200',
                        confirmButton: 'rounded-lg px-5 py-2 font-semibold text-sm',
                        cancelButton: 'rounded-lg px-5 py-2 font-semibold text-sm'
                    }
                }).then((result) => {
                    if (result.isConfirmed) {
                        sessionStorage.setItem('cms_scroll_y', window.scrollY);
                        sessionStorage.setItem('cms_pending_toast', JSON.stringify({
                            type: 'success',
                            message: 'Imagen eliminada correctamente'
                        }));
    
                        @this.call('removeMedia', mediaId).then(() => {
                            window.location.reload();
                        }).catch(() => {
                            window.location.reload();
                        });
                    }
                });
            }
    
            function jsUpload(sectionId, btn) {
                var fileInput = document.getElementById('file-' + sectionId);
    
                if (!fileInput.files.length) {
                    showToast('error', 'Elegí una imagen primero antes de subir.');
                    return;
                }
    
                var startTime = Date.now();
    
                window.dispatchEvent(new CustomEvent('uploading'));
    
                var formData = new FormData();
                formData.append('file', fileInput.files[0]);
                formData.append('section_id', sectionId);
                formData.append('_token', document.querySelector('meta[name="csrf-token"]').content);
    
                var progress = document.getElementById('upload-progress-' + sectionId);
                var bar = document.getElementById('upload-bar-' + sectionId);
    
                progress.classList.remove('hidden');
                bar.style.width = '30%';
    
                btn.disabled = true;
                btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin mr-2"></i>Subiendo...';
    
                fetch('{{ route("cms.upload-media") }}', {
                    method: 'POST',
                    body: formData,
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                })
                .then(function(r) { return r.json(); })
                .then(function(data) {
                    bar.style.width = '100%';
    
                    if (data.success) {
                        btn.innerHTML = '<i class="fa-solid fa-check mr-2"></i>¡Listo!';
                        btn.classList.replace('bg-blue-600', 'bg-emerald-600');
    
                        fileInput.value = '';
    
                        var label = document.getElementById('file-label-' + sectionId);
                        if (label) {
                            label.textContent = 'Arrastrá una imagen o hacé clic acá...';
                        }
    
                        sessionStorage.setItem('cms_scroll_y', window.scrollY);
                        sessionStorage.setItem('cms_pending_toast', JSON.stringify({
                            type: 'success',
                            message: 'Imagen subida correctamente'
                        }));
    
                        setTimeout(function() {
                            window.dispatchEvent(new CustomEvent('upload-done'));
    
                            setTimeout(function() {
                                window.location.reload();
                            }, 500);
                        }, Math.max(0, 1500 - (Date.now() - startTime)));
    
                    } else {
                        window.dispatchEvent(new CustomEvent('upload-done'));
    
                        showToast('error', data.error || 'Error al subir imagen');
    
                        progress.classList.add('hidden');
                        bar.style.width = '0%';
    
                        btn.innerHTML = '<i class="fa-solid fa-upload mr-2"></i>Subir';
                        btn.disabled = false;
                    }
                })
                .catch(function() {
                    window.dispatchEvent(new CustomEvent('upload-done'));
    
                    showToast('error', 'Error de conexión. Revisá tu red y probá de nuevo.');
    
                    progress.classList.add('hidden');
                    bar.style.width = '0%';
    
                    btn.innerHTML = '<i class="fa-solid fa-upload mr-2"></i>Subir';
                    btn.disabled = false;
                });
            }
    
            function initPreviewSkeletons() {
                document.querySelectorAll('.preview-iframe').forEach(function(iframe) {
                    var wrapper = iframe.closest('[wire\\:ignore]') || iframe.closest('div');
                    var skeleton = wrapper ? wrapper.querySelector('.preview-skeleton') : null;
    
                    if (!skeleton) return;
    
                    skeleton.style.opacity = '';
                    skeleton.style.transition = '';
                    skeleton.style.display = '';
    
                    var hide = function() {
                        skeleton.style.opacity = '0';
                        skeleton.style.transition = 'opacity 0.25s ease';
                        setTimeout(function() {
                            skeleton.style.display = 'none';
                        }, 250);
                    };
    
                    iframe.addEventListener('load', hide, { once: true });
                    setTimeout(hide, 6000);
                });
            }
    
            document.addEventListener('DOMContentLoaded', initPreviewSkeletons);
    
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
                initPreviewSkeletons();
            });
        </script>
    </div>
    </x-cms.layout>
    </div>