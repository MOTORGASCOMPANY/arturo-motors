<div>
    <script>
        if ('scrollRestoration' in history) {
            history.scrollRestoration = 'manual';
        }
    </script>

    <style>
        @keyframes toastIn {
            from {
                opacity: 0;
                transform: translateX(24px);
            }
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        @keyframes toastOut {
            from {
                opacity: 1;
                transform: translateX(0);
            }
            to {
                opacity: 0;
                transform: translateX(24px);
            }
        }

        @keyframes spin-slow {
            100% {
                transform: rotate(360deg);
            }
        }

        @keyframes spin-reverse {
            100% {
                transform: rotate(-360deg);
            }
        }

        @keyframes float-gas {
            0%, 100% {
                transform: translateY(0) scale(1);
                filter: drop-shadow(0 0 15px rgba(52,211,153,0.6));
            }

            50% {
                transform: translateY(-8px) scale(1.05);
                filter: drop-shadow(0 0 25px rgba(52,211,153,0.9));
            }
        }

        .hide-scrollbar::-webkit-scrollbar {
            display: none;
        }

        .hide-scrollbar {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }
    </style>


    <div id="toast-stack"
         class="fixed top-4 right-4 sm:top-6 sm:right-6 z-[99999] flex flex-col gap-3 w-72 sm:w-80 pointer-events-none">
    </div>

    @if($successMessage)
        <div x-data
             x-init="showToast('success', @js($successMessage)); $wire.clearSuccessMessage()"
             style="display:none">
        </div>
    @endif

    @if($errorMessage)
        <div x-data
             x-init="showToast('error', @js($errorMessage)); $wire.clearErrorMessage()"
             style="display:none">
        </div>
    @endif

    @if (session()->has('success') && !$successMessage)
        <div x-data
             x-init="showToast('success', @js(session('success')))"
             style="display:none">
        </div>
    @endif


    <div x-data="{ show: false, startTime: 0 }"
         x-show="show"
         x-cloak
         @uploading.window="show = true; startTime = Date.now()"
         @upload-done.window="setTimeout(() => { show = false }, Math.max(0, 1500 - (Date.now() - startTime)))"
         class="fixed inset-0 z-[9999] flex items-center justify-center backdrop-blur-xl px-4"
         style="background: radial-gradient(circle at center, rgba(15,23,42,0.85) 0%, rgba(2,6,23,0.95) 100%);"
         x-transition:enter="transition ease-out duration-500"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-500"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0">

        <div class="relative flex flex-col items-center justify-center text-center">

            <div class="relative w-32 h-32 sm:w-40 sm:h-40 flex items-center justify-center mb-6 sm:mb-8">

                <div class="absolute inset-0 rounded-full border-t-4 border-b-4 border-emerald-400 opacity-80"
                     style="animation: spin-slow 2s linear infinite;">
                </div>

                <div class="absolute inset-3 rounded-full border-l-4 border-r-4 border-blue-600 opacity-80"
                     style="animation: spin-reverse 3s linear infinite;">
                </div>

                <div class="absolute inset-6 rounded-full border-t-4 border-b-4 border-blue-400 opacity-50"
                     style="animation: spin-slow 4s linear infinite;">
                </div>

                <div class="absolute inset-0 flex items-center justify-center"
                     style="animation: float-gas 2s ease-in-out infinite;">

                    <i class="fa-solid fa-cloud-arrow-up text-4xl sm:text-5xl text-emerald-400"></i>

                </div>

            </div>

            <h3 class="text-white text-lg sm:text-2xl font-bold tracking-[0.15em] sm:tracking-[0.2em] uppercase flex items-center justify-center gap-2 sm:gap-3 drop-shadow-lg mb-2 sm:mb-3">

                Subiendo Imagen

                <span class="flex gap-1.5">

                    <span class="w-1.5 h-1.5 sm:w-2 sm:h-2 bg-emerald-400 rounded-full animate-bounce"></span>

                    <span class="w-1.5 h-1.5 sm:w-2 sm:h-2 bg-emerald-400 rounded-full animate-bounce"
                          style="animation-delay: 0.15s"></span>

                    <span class="w-1.5 h-1.5 sm:w-2 sm:h-2 bg-emerald-400 rounded-full animate-bounce"
                          style="animation-delay: 0.3s"></span>

                </span>

            </h3>

            <p class="text-emerald-100/60 font-medium tracking-wider sm:tracking-widest text-xs sm:text-sm uppercase">
                Procesando y aplicando cambios...
            </p>

        </div>

    </div>
     
    <div class="m-8 flex flex-col sm:flex-row sm:justify-between sm:items-center gap-3 mb-6 pb-4 border-b border-blue-100 mx-3 sm:mx-4">

        <h4 class="text-xl sm:text-2xl font-bold text-blue-950 flex items-center gap-2.5 sm:gap-3">

            <i class="fa-solid fa-edit text-blue-600"></i>

            Gestionar Contenido

        </h4>

        <span class="self-start sm:self-auto bg-blue-50 text-blue-700 text-xs font-semibold px-3.5 py-1.5 sm:px-4 sm:py-2 rounded-full border border-blue-200">

            {{ $pageTitle }}

        </span>

    </div>


    <div x-data="{ activeTab: '{{ $sections[0]['id'] ?? '' }}' }"
         class="mx-3 sm:mx-4">

        <div class="flex overflow-x-auto gap-2.5 sm:gap-3 pb-4 mb-4 hide-scrollbar">

            @foreach($sections as $section)

                <button
                    @click="activeTab = '{{ $section['id'] }}'"
                    :class="activeTab === '{{ $section['id'] }}' ? 'bg-blue-600 text-white shadow-md shadow-blue-200 border-blue-600' : 'bg-white text-slate-600 border-blue-100 hover:bg-blue-50 hover:text-blue-700'"
                    class="px-4 sm:px-5 py-2 sm:py-2.5 rounded-xl font-semibold text-xs sm:text-sm whitespace-nowrap border transition-all duration-300 flex items-center gap-2 shrink-0">

                    <i class="fa-solid fa-layer-group text-xs"
                       :class="activeTab === '{{ $section['id'] }}' ? 'text-white' : 'text-blue-400'">
                    </i>

                    {{ $section['title'] }}

                </button>

            @endforeach

        </div>


        <div class="relative">

            @foreach ($sections as $section)

                @php

                    $imageLimits = [
                        'hero' => 5,
                        'about' => 2
                    ];

                    $maxImages = $imageLimits[$section['key']] ?? 0;

                    $currentCount = count($section['media_items'] ?? []);

                    $canUpload = $maxImages > 0 && $currentCount < $maxImages;

                    $hasImages = $maxImages > 0;

                @endphp


                <div
                    x-show="activeTab === '{{ $section['id'] }}'"
                    x-transition:enter="transition ease-out duration-300"
                    x-transition:enter-start="opacity-0 transform translate-y-4"
                    x-transition:enter-end="opacity-100 transform translate-y-0"
                    style="display: none;"
                    class="bg-white rounded-2xl shadow-lg shadow-blue-900/5 border border-blue-100 overflow-hidden">


                    <div class="flex flex-col lg:flex-row items-stretch">


                        <div class="w-full lg:w-1/2 lg:border-r p-4 sm:p-6 border-b lg:border-b-0 border-blue-100 bg-blue-50/35 flex flex-col">

                            <div class="flex items-center gap-2 mb-3 sm:mb-4">

                                <div class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></div>

                                <p class="text-xs font-semibold text-blue-800/60 uppercase tracking-wider">
                                    Vista en tiempo real
                                </p>

                            </div>

                            <div class="flex-1 flex flex-col justify-center">

                                <x-section-preview
                                    :section="$section"
                                    :refreshKey="$refreshKey"
                                    :highlight="$highlightSection === $section['key']"
                                />

                            </div>

                        </div>


                        <div class="w-full lg:w-1/2 p-4 sm:p-6 flex flex-col gap-4 sm:gap-5">

                            <div>

                                <div class="flex items-center gap-3 mb-4 sm:mb-5">

                                    <div class="w-10 h-10 sm:w-12 sm:h-12 rounded-xl bg-blue-50 flex items-center justify-center text-blue-600 border border-blue-100 shrink-0">

                                        <i class="fa-solid fa-pen-nib text-base sm:text-lg"></i>

                                    </div>

                                    <div class="min-w-0">

                                        <h6 class="font-bold text-blue-950 text-base sm:text-lg truncate">
                                            {{ $section['title'] }}
                                        </h6>

                                        <p class="text-slate-500 text-xs truncate">

                                            Clave interna:

                                            <code class="bg-blue-50 text-blue-700 px-1.5 py-0.5 rounded font-mono">
                                                {{ $section['key'] }}
                                            </code>

                                        </p>

                                    </div>

                                </div>


                                @if($hasImages && $currentCount > 0)

                                    <div class="mb-4 sm:mb-5">

                                        <p class="text-xs font-semibold text-blue-900 mb-2.5 sm:mb-3 flex flex-col sm:flex-row sm:justify-between sm:items-center gap-1">

                                            <span>
                                                Galería de Imágenes
                                            </span>

                                            <span class="bg-blue-100 text-blue-700 px-2.5 py-0.5 rounded-full self-start sm:self-auto">

                                                {{ $currentCount }}/{{ $maxImages }} permitidas

                                            </span>

                                        </p>


                                        <div class="grid grid-cols-2 sm:grid-cols-3 gap-2.5 sm:gap-3">

                                            @foreach($section['media_items'] as $pm)

                                                <div class="relative group rounded-xl overflow-hidden border border-blue-100 bg-white shadow-sm">

                                                    <img
                                                        src="{{ asset('storage/' . $pm['media']['file_path']) }}"
                                                        class="w-full h-20 sm:h-24 object-cover">

                                                    <div class="absolute inset-0 bg-blue-950/60 opacity-0 group-hover:opacity-100 transition-all duration-300 flex flex-col items-center justify-center gap-1.5 sm:gap-2 backdrop-blur-[2px] p-2">

                                                        <a
                                                            href="{{ asset('storage/' . $pm['media']['file_path']) }}"
                                                            target="_blank"
                                                            class="bg-white text-blue-900 text-[11px] sm:text-xs font-semibold px-2.5 py-1 sm:px-3 sm:py-1.5 rounded-lg hover:bg-blue-50 flex items-center gap-1 transition-all w-full justify-center">

                                                            <i class="fa-solid fa-expand"></i>

                                                            Ver

                                                        </a>

                                                        <button
                                                            type="button"
                                                            onclick="confirmDeleteImage({{ $pm['id'] }})"
                                                            class="bg-red-500 text-white text-[11px] sm:text-xs font-semibold px-2.5 py-1 sm:px-3 sm:py-1.5 rounded-lg hover:bg-red-600 flex items-center gap-1 transition-all w-full justify-center">

                                                            <i class="fa-solid fa-trash"></i>

                                                            Borrar

                                                        </button>

                                                    </div>

                                                </div>

                                            @endforeach

                                        </div>

                                    </div>

                                @endif


                                @if($hasImages)

                                    <div class="mb-4 sm:mb-5">

                                        @if($canUpload)

                                            <div class="bg-white rounded-xl p-3 sm:p-3.5 border border-blue-100 shadow-sm">

                                                <input
                                                    type="file"
                                                    id="file-{{ $section['id'] }}"
                                                    accept="image/*"
                                                    class="hidden">

                                                <div class="flex flex-col sm:flex-row gap-2.5 sm:gap-3 items-center">

                                                    <button
                                                        type="button"
                                                        onclick="document.getElementById('file-{{ $section['id'] }}').click()"
                                                        class="w-full sm:flex-1 text-xs sm:text-sm border-2 border-dashed border-blue-200 rounded-xl px-3 py-2 sm:px-4 sm:py-2.5 bg-blue-50/50 text-left text-blue-800 hover:bg-blue-50 hover:border-blue-400 transition-all cursor-pointer flex items-center truncate">

                                                        <i class="fa-solid fa-image text-blue-500 mr-2 text-base shrink-0"></i>

                                                        <span
                                                            id="file-label-{{ $section['id'] }}"
                                                            class="truncate">

                                                            Selecciona una imagen...

                                                        </span>

                                                    </button>

                                                    <button
                                                        type="button"
                                                        onclick="jsUpload({{ $section['id'] }}, this)"
                                                        class="w-full sm:w-auto bg-blue-600 text-white px-4 sm:px-5 py-2 sm:py-2.5 rounded-xl hover:bg-blue-700 hover:shadow-md hover:shadow-blue-200 font-semibold transition-all flex justify-center items-center gap-2 text-sm shrink-0">

                                                        <i class="fa-solid fa-upload"></i>

                                                        Subir

                                                    </button>

                                                </div>


                                                <div
                                                    id="upload-progress-{{ $section['id'] }}"
                                                    class="hidden mt-2">

                                                    <div class="h-1.5 bg-blue-100 rounded-full overflow-hidden">

                                                        <div
                                                            id="upload-bar-{{ $section['id'] }}"
                                                            class="h-full bg-emerald-500 rounded-full transition-all duration-300"
                                                            style="width: 0%">
                                                        </div>

                                                    </div>

                                                </div>


                                                <p class="text-[10px] text-blue-600/70 mt-1.5">

                                                    <i class="fa-solid fa-circle-info mr-1"></i>

                                                    JPG, PNG, WebP. Máx: 5MB.

                                                </p>

                                            </div>

                                        @else

                                            <div class="bg-emerald-50 rounded-xl p-3 border border-emerald-200 text-center flex items-center justify-center gap-2">

                                                <i class="fa-solid fa-circle-check text-emerald-500 text-base"></i>

                                                <p class="text-emerald-700 text-xs font-semibold">

                                                    Límite de imágenes alcanzado ({{ $maxImages }})

                                                </p>

                                            </div>

                                        @endif

                                    </div>

                                @endif

                            </div>


                            <div class="bg-blue-50/50 rounded-xl p-3.5 sm:p-4 border border-blue-100 space-y-3">

                                <h6 class="text-xs sm:text-sm font-bold text-blue-950 border-b border-blue-100 pb-1.5">
                                    Contenido de Texto
                                </h6>

                                @if ($errors->any())

                                    <div class="bg-red-50 border border-red-200 text-red-700 px-3 py-2 rounded-xl text-xs shadow-sm">

                                        @foreach ($errors->all() as $error)

                                            <p class="flex items-start gap-1.5">

                                                <i class="fa-solid fa-circle-exclamation mt-0.5"></i>

                                                {{ $error }}

                                            </p>

                                        @endforeach

                                    </div>

                                @endif


                                <div class="space-y-3">

                                    <div>

                                        <label class="block text-[11px] font-bold text-blue-900 mb-1 uppercase tracking-wide">

                                            Título principal

                                        </label>

                                        <input
                                            type="text"
                                            class="w-full border border-blue-200 rounded-xl px-3.5 py-2 text-xs sm:text-sm focus:ring-2 focus:ring-blue-600 focus:border-blue-600 bg-white text-blue-950 transition-all"
                                            wire:model="sectionData.{{ $section['id'] }}.title"
                                            wire:focus="editSection({{ $section['id'] }})">

                                    </div>


                                    <div>

                                        <label class="block text-[11px] font-bold text-blue-900 mb-1 uppercase tracking-wide">

                                            Subtítulo

                                        </label>

                                        <input
                                            type="text"
                                            class="w-full border border-blue-200 rounded-xl px-3.5 py-2 text-xs sm:text-sm focus:ring-2 focus:ring-blue-600 focus:border-blue-600 bg-white text-blue-950 transition-all"
                                            wire:model="sectionData.{{ $section['id'] }}.subtitle"
                                            wire:focus="editSection({{ $section['id'] }})">

                                    </div>


                                    <div>

                                        <label class="block text-[11px] font-bold text-blue-900 mb-1 uppercase tracking-wide">

                                            Descripción

                                        </label>

                                        <textarea
                                            class="w-full border border-blue-200 rounded-xl px-3.5 py-2 text-xs sm:text-sm focus:ring-2 focus:ring-blue-600 focus:border-blue-600 bg-white text-blue-950 transition-all"
                                            rows="3"
                                            wire:model="sectionData.{{ $section['id'] }}.description"
                                            wire:focus="editSection({{ $section['id'] }})"></textarea>

                                    </div>


                                    <div class="flex justify-end pt-1">

                                        <button
                                            type="button"
                                            class="w-full sm:w-auto px-5 py-2 rounded-xl bg-blue-600 text-white hover:bg-blue-700 hover:shadow-md hover:shadow-blue-200 text-xs sm:text-sm font-bold transition-all flex items-center justify-center gap-2"
                                            onclick="confirmSaveSection({{ $section['id'] }})">

                                            <i class="fa-solid fa-check"></i>

                                            Guardar Cambios

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


    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>


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

            var stack = document.getElementById('toast-stack');

            if (!stack || !message) return;

            var isError = type === 'error';

            var toast = document.createElement('div');

            toast.className = 'pointer-events-auto flex items-center gap-3 px-4 py-3.5 rounded-xl shadow-lg border font-medium text-xs sm:text-sm ' +
                (isError
                    ? 'bg-red-50 border-red-200 text-red-700'
                    : 'bg-blue-50 border-blue-200 text-blue-800');

            toast.style.animation = 'toastIn 0.35s ease-out';

            toast.innerHTML =
                '<i class="fa-solid ' +
                (isError
                    ? 'fa-circle-exclamation'
                    : 'fa-circle-check text-blue-600') +
                '"></i>' +
                '<span class="flex-1">' +
                message +
                '</span>' +
                '<button class="opacity-60 hover:opacity-100" onclick="dismissToast(this.parentElement)">' +
                '<i class="fa-solid fa-xmark"></i>' +
                '</button>';

            stack.appendChild(toast);

            setTimeout(function () {

                dismissToast(toast);

            }, 3500);

        }


        function dismissToast(toast) {

            if (!toast) return;

            toast.style.animation = 'toastOut 0.3s ease-in forwards';

            setTimeout(function () {

                toast.remove();

            }, 300);

        }


        function restoreScrollTarget() {

            var savedY = sessionStorage.getItem('cms_scroll_y');

            return savedY !== null
                ? parseInt(savedY, 10)
                : 0;

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

            var pendingToast =
                sessionStorage.getItem('cms_pending_toast');

            if (pendingToast) {

                sessionStorage.removeItem('cms_pending_toast');

                try {

                    var t = JSON.parse(pendingToast);

                    showToast(
                        t.type,
                        t.message
                    );

                } catch (e) {}

            }

        });


        document.addEventListener('change', function(e) {

            if (e.target.type === 'file' && e.target.files.length) {

                var id =
                    e.target.id.replace('file-', '');

                var label =
                    document.getElementById(
                        'file-label-' + id
                    );

                if (label) {

                    label.textContent =
                        e.target.files[0].name;

                }

            }

        }, true);

        // se confirma la acción de guardar cambios en una sección
        function confirmSaveSection(sectionId) {

            Swal.fire({

                title: '¿Guardar cambios?',

                text: 'Se actualizará el contenido de esta sección en el sitio.',

                icon: 'question',

                showCancelButton: true,

                confirmButtonColor: '#2563eb',

                cancelButtonColor: '#64748b',

                confirmButtonText: 'Sí, guardar',

                cancelButtonText: 'Cancelar',

                customClass: {

                    popup: 'rounded-2xl shadow-xl border border-blue-100',

                    confirmButton: 'rounded-xl px-5 py-2.5 font-semibold text-sm',

                    cancelButton: 'rounded-xl px-5 py-2.5 font-semibold text-sm'

                }

            }).then((result) => {

                if (result.isConfirmed) {

                    @this.call(
                        'saveSection',
                        sectionId
                    );

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

                cancelButtonColor: '#64748b',

                confirmButtonText: 'Sí, eliminar',

                cancelButtonText: 'Cancelar',

                customClass: {

                    popup: 'rounded-2xl shadow-xl border border-red-100',

                    confirmButton: 'rounded-xl px-5 py-2.5 font-semibold text-sm',

                    cancelButton: 'rounded-xl px-5 py-2.5 font-semibold text-sm'

                }

            }).then((result) => {

                if (result.isConfirmed) {

                    sessionStorage.setItem(
                        'cms_scroll_y',
                        window.scrollY
                    );

                    sessionStorage.setItem(
                        'cms_pending_toast',
                        JSON.stringify({
                            type: 'success',
                            message: 'Imagen eliminada correctamente'
                        })
                    );

                    @this.call(
                        'removeMedia',
                        mediaId
                    ).then(() => {

                        window.location.reload();

                    });

                }

            });

        }


        function jsUpload(sectionId, btn) {

            var fileInput =
                document.getElementById(
                    'file-' + sectionId
                );

            if (!fileInput.files.length) {

                showToast(
                    'error',
                    'Elegí una imagen primero antes de subir.'
                );

                return;

            }


            var startTime = Date.now();

            window.dispatchEvent(
                new CustomEvent('uploading')
            );


            var formData = new FormData();

            formData.append(
                'file',
                fileInput.files[0]
            );

            formData.append(
                'section_id',
                sectionId
            );

            formData.append(
                '_token',
                document.querySelector(
                    'meta[name="csrf-token"]'
                ).content
            );


            var progress =
                document.getElementById(
                    'upload-progress-' + sectionId
                );

            var bar =
                document.getElementById(
                    'upload-bar-' + sectionId
                );


            progress.classList.remove('hidden');

            bar.style.width = '30%';

            btn.disabled = true;

            btn.innerHTML =
                '<i class="fa-solid fa-spinner fa-spin mr-2"></i>Subiendo...';


            fetch('{{ route("cms.upload-media") }}', {

                method: 'POST',

                body: formData,

                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }

            })

            .then(function(r) {

                return r.json();

            })

            .then(function(data) {

                bar.style.width = '100%';

                if (data.success) {

                    btn.innerHTML =
                        '<i class="fa-solid fa-check mr-2"></i>¡Listo!';

                    btn.classList.replace(
                        'bg-blue-600',
                        'bg-emerald-600'
                    );

                    fileInput.value = '';

                    var label =
                        document.getElementById(
                            'file-label-' + sectionId
                        );

                    if (label) {

                        label.textContent =
                            'Selecciona una imagen...';

                    }

                    sessionStorage.setItem(
                        'cms_scroll_y',
                        window.scrollY
                    );

                    sessionStorage.setItem(
                        'cms_pending_toast',
                        JSON.stringify({
                            type: 'success',
                            message: 'Imagen subida correctamente'
                        })
                    );

                    setTimeout(function() {

                        window.dispatchEvent(
                            new CustomEvent('upload-done')
                        );

                        setTimeout(function() {

                            window.location.reload();

                        }, 500);

                    }, Math.max(
                        0,
                        1500 - (Date.now() - startTime)
                    ));

                } else {

                    window.dispatchEvent(
                        new CustomEvent('upload-done')
                    );

                    showToast(
                        'error',
                        data.error ||
                        'Error al subir imagen'
                    );

                    progress.classList.add('hidden');

                    bar.style.width = '0%';

                    btn.innerHTML =
                        '<i class="fa-solid fa-upload mr-2"></i>Subir';

                    btn.disabled = false;

                }

            })

            .catch(function() {

                window.dispatchEvent(
                    new CustomEvent('upload-done')
                );

                showToast(
                    'error',
                    'Error de conexión. Revisá tu red y probá de nuevo.'
                );

                progress.classList.add('hidden');

                bar.style.width = '0%';

                btn.innerHTML =
                    '<i class="fa-solid fa-upload mr-2"></i>Subir';

                btn.disabled = false;

            });

        }


        function initPreviewSkeletons() {

            document.querySelectorAll('.preview-iframe').forEach(function(iframe) {

                var wrapper =
                    iframe.closest('[wire\\:ignore]') ||
                    iframe.closest('div');

                var skeleton =
                    wrapper
                        ? wrapper.querySelector('.preview-skeleton')
                        : null;

                if (!skeleton) return;

                var hide = function() {

                    skeleton.style.opacity = '0';

                    skeleton.style.transition =
                        'opacity 0.25s ease';

                    setTimeout(function() {

                        skeleton.style.display =
                            'none';

                    }, 250);

                };

                iframe.addEventListener(
                    'load',
                    hide,
                    { once: true }
                );

                setTimeout(
                    hide,
                    6000
                );

            });

        }


        document.addEventListener(
            'DOMContentLoaded',
            initPreviewSkeletons
        );

    </script>

</div>