<x-cms.layout
    title="Apariencia y Colores"
    description="Personaliza la paleta de colores completa del landing page"
    headerIcon='<i class="fa-solid fa-palette text-blue-600"></i>'
>
    {{-- Success/Error Messages --}}
    @if($successMessage)
        <div x-data="{ show: true }" x-show="show" x-transition:leave="transition ease-in duration-300" x-init="setTimeout(() => { show = false; @this.call('clearSuccessMessage') }, 3000)" class="mb-6 flex items-center gap-3 p-4 rounded-xl bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 text-green-700 dark:text-green-400 shadow-sm animate-slide-down" role="alert">
            <i class="fa-solid fa-circle-check text-lg flex-shrink-0"></i>
            <span class="flex-1 font-medium">{{ $successMessage }}</span>
            <button @click="show = false; @this.call('clearSuccessMessage')" class="text-green-500 dark:text-green-400 hover:opacity-70" aria-label="Cerrar"><i class="fa-solid fa-xmark"></i></button>
        </div>
    @endif

    @if($errorMessage)
        <div x-data="{ show: true }" x-show="show" x-transition:leave="transition ease-in duration-300" x-init="setTimeout(() => { show = false; @this.call('clearErrorMessage') }, 3000)" class="mb-6 flex items-center gap-3 p-4 rounded-xl bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 text-red-700 dark:text-red-400 shadow-sm animate-slide-down" role="alert">
            <i class="fa-solid fa-circle-exclamation text-lg flex-shrink-0"></i>
            <span class="flex-1 font-medium">{{ $errorMessage }}</span>
            <button @click="show = false; @this.call('clearErrorMessage')" class="text-red-500 dark:text-red-400 hover:opacity-70" aria-label="Cerrar"><i class="fa-solid fa-xmark"></i></button>
        </div>
    @endif

    {{-- Color Preview Card --}}
    <x-cms.card class="mb-8" style="animation: cardEntry 0.4s ease-out">
        <div class="p-6">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white flex items-center gap-2">
                    <i class="fa-solid fa-eye text-blue-600"></i>
                    Vista Previa en Tiempo Real
                </h3>
                <span class="text-xs text-gray-500 dark:text-gray-400">Los cambios se reflejan al instante</span>
            </div>

            <div class="rounded-xl overflow-hidden border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800" x-data="colorPreview()" x-init="init()" :style="previewStyle">
                <!-- Navbar Preview -->
                <div class="px-4 py-3 border-b border-gray-200 dark:border-gray-700 flex items-center justify-between" :style="{ backgroundColor: colors.surface }">
                    <div class="flex items-center gap-2">
                        <div class="w-8 h-8 rounded-xl" :style="{ background: 'linear-gradient(135deg, ' + colors.primary + ', ' + colors.secondary + ')' }" aria-hidden="true"></div>
                        <span class="font-bold text-lg" :style="{ color: colors.text_primary }">Arturo Motors</span>
                    </div>
                    <button class="px-3 py-1.5 rounded-full text-xs font-medium" :style="{ backgroundColor: colors.primary, color: colors.text_primary }">Cotizar</button>
                </div>

                <!-- Hero Preview -->
                <div class="p-8 text-center" :style="{ backgroundColor: colors.background, color: colors.text_primary }">
                    <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-xs font-medium mb-4" :style="{ backgroundColor: colors.accent + '20', borderColor: colors.accent, color: colors.accent }">
                        <span class="w-2 h-2 rounded-full" :style="{ backgroundColor: colors.success, animation: 'pulse 2s infinite' }"></span>
                        Taller Autorizado & Certificado
                    </div>
                    <h1 class="text-3xl font-bold mb-4" :style="{ color: colors.text_primary }">
                        Potencia tu Vehículo con <br>
                        <span :style="{ background: 'linear-gradient(135deg, ' + colors.primary + ', ' + colors.secondary + ')', WebkitBackgroundClip: 'text', WebkitTextFillColor: 'transparent', backgroundClip: 'text' }">GNV & GLP</span>
                    </h1>
                    <p class="text-lg mb-6 max-w-2xl mx-auto" :style="{ color: colors.text_secondary }">Conversiones de alta precisión, certificaciones oficiales y mantenimiento especializado.</p>
                    <div class="flex flex-col sm:flex-row gap-3 justify-center">
                        <button class="px-6 py-3 rounded-full font-semibold text-lg" :style="{ background: 'linear-gradient(135deg, ' + colors.primary + ', ' + colors.secondary + ')', color: colors.text_primary }">Agendar Cita</button>
                        <button class="px-6 py-3 rounded-full font-semibold text-lg border-2" :style="{ borderColor: colors.border, color: colors.text_primary, backgroundColor: 'transparent' }">Nuestros Servicios</button>
                    </div>
                </div>

                <!-- Card Preview -->
                <div class="p-6 border-t border-gray-200 dark:border-gray-700">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        @foreach(['primary' => 'Primario', 'secondary' => 'Secundario', 'accent' => 'Acento'] as $key => $label)
                            <div class="p-4 rounded-xl text-center" :style="{ backgroundColor: colors.surface, borderColor: colors.border, borderWidth: '1px', borderStyle: 'solid' }">
                                <div class="w-12 h-12 rounded-xl mx-auto mb-2 flex items-center justify-center" :style="{ backgroundColor: colors[$key] }">
                                    <i class="fa-solid fa-circle text-white"></i>
                                </div>
                                <span class="text-xs font-medium" :style="{ color: colors.text_primary }">{{ $label }}</span>
                                <code class="text-xs block mt-1" :style="{ color: colors.text_muted }" x-text="colors['{{ $key }}']"></code>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </x-cms.card>

    {{-- Color Configuration --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        {{-- Primary Colors --}}
        <x-cms.card class="lg:col-span-1" style="animation: cardEntry 0.4s ease-out 0.1s both">
            <div class="p-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-6 flex items-center gap-2">
                    <i class="fa-solid fa-circle text-amber-500"></i>
                    Colores Principales
                </h3>
                <div class="space-y-5">
                    @foreach([
                        'primary' => ['label' => 'Primario (Botones, Enlaces)', 'desc' => 'Color principal de la marca - botones CTA, enlaces activos'],
                        'primary_hover' => ['label' => 'Primario Hover', 'desc' => 'Variación oscura para hover de botones primarios'],
                        'secondary' => ['label' => 'Secundario (Acentos)', 'desc' => 'Color secundario - badges, elementos decorativos'],
                        'secondary_hover' => ['label' => 'Secundario Hover', 'desc' => 'Variación para hover de elementos secundarios'],
                        'accent' => ['label' => 'Acento (Badges, Focus)', 'desc' => 'Color de acento - badges, focus rings, elementos interactivos'],
                    ] as $key => $info)
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2 flex items-center justify-between">
                                <span>{{ $info['label'] }}</span>
                                <code class="text-xs px-2 py-0.5 rounded bg-gray-100 dark:bg-gray-800" x-text="colors['{{ $key }}']"></code>
                            </label>
                            <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">{{ $info['desc'] }}</p>
                            <div class="flex items-center gap-3">
                                <input type="color" class="w-12 h-12 rounded-lg border border-gray-200 dark:border-gray-700 cursor-pointer" wire:model.defer="colors.{{ $key }}" @input="$dispatch('colorChanged', { key: '{{ $key }}', value: $event.target.value })" :style="{ backgroundColor: colors['{{ $key }}'] }" aria-label="{{ $info['label'] }}">
                                <input type="text" class="flex-1 px-3 py-2 border border-gray-200 dark:border-gray-600 rounded-lg text-sm font-mono bg-white dark:bg-gray-800 dark:text-white" wire:model.defer="colors.{{ $key }}" placeholder="#f59e0b" @input="$dispatch('colorChanged', { key: '{{ $key }}', value: $event.target.value })" aria-label="{{ $info['label'] }}-hex">
                            </div>
                            @error("colors.{{ $key }}")
                                <p class="text-xs text-red-600 dark:text-red-400 mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    @endforeach
                </div>
            </div>
        </x-cms.card>

        {{-- Background & Surface --}}
        <x-cms.card class="lg:col-span-1" style="animation: cardEntry 0.4s ease-out 0.15s both">
            <div class="p-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-6 flex items-center gap-2">
                    <i class="fa-solid fa-fill-drip text-blue-600"></i>
                    Fondos y Superficies
                </h3>
                <div class="space-y-5">
                    @foreach([
                        'background' => ['label' => 'Fondo Principal', 'desc' => 'Color de fondo del hero y secciones oscuras'],
                        'surface' => ['label' => 'Superficie (Tarjetas)', 'desc' => 'Fondo de tarjetas, navbar, modales'],
                        'border' => ['label' => 'Bordes', 'desc' => 'Color de bordes sutiles, divisores'],
                    ] as $key => $info)
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2 flex items-center justify-between">
                                <span>{{ $info['label'] }}</span>
                                <code class="text-xs px-2 py-0.5 rounded bg-gray-100 dark:bg-gray-800" x-text="colors['{{ $key }}']"></code>
                            </label>
                            <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">{{ $info['desc'] }}</p>
                            <div class="flex items-center gap-3">
                                <input type="color" class="w-12 h-12 rounded-lg border border-gray-200 dark:border-gray-700 cursor-pointer" wire:model.defer="colors.{{ $key }}" @input="$dispatch('colorChanged', { key: '{{ $key }}', value: $event.target.value })" :style="{ backgroundColor: colors['{{ $key }}'] }" aria-label="{{ $info['label'] }}">
                                <input type="text" class="flex-1 px-3 py-2 border border-gray-200 dark:border-gray-600 rounded-lg text-sm font-mono bg-white dark:bg-gray-800 dark:text-white" wire:model.defer="colors.{{ $key }}" placeholder="#0a0f1e" @input="$dispatch('colorChanged', { key: '{{ $key }}', value: $event.target.value })" aria-label="{{ $info['label'] }}-hex">
                            </div>
                            @error("colors.{{ $key }}")
                                <p class="text-xs text-red-600 dark:text-red-400 mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    @endforeach
                </div>
            </div>
        </x-cms.card>

        {{-- Text Colors --}}
        <x-cms.card class="lg:col-span-1" style="animation: cardEntry 0.4s ease-out 0.2s both">
            <div class="p-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-6 flex items-center gap-2">
                    <i class="fa-solid fa-font text-green-600"></i>
                    Tipografía
                </h3>
                <div class="space-y-5">
                    @foreach([
                        'text_primary' => ['label' => 'Texto Principal', 'desc' => 'Títulos, encabezados, texto importante'],
                        'text_secondary' => ['label' => 'Texto Secundario', 'desc' => 'Párrafos, descripciones, texto de apoyo'],
                        'text_muted' => ['label' => 'Texto Muted', 'desc' => 'Texto secundario, placeholders, labels'],
                    ] as $key => $info)
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2 flex items-center justify-between">
                                <span>{{ $info['label'] }}</span>
                                <code class="text-xs px-2 py-0.5 rounded bg-gray-100 dark:bg-gray-800" x-text="colors['{{ $key }}']"></code>
                            </label>
                            <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">{{ $info['desc'] }}</p>
                            <div class="flex items-center gap-3">
                                <input type="color" class="w-12 h-12 rounded-lg border border-gray-200 dark:border-gray-700 cursor-pointer" wire:model.defer="colors.{{ $key }}" @input="$dispatch('colorChanged', { key: '{{ $key }}', value: $event.target.value })" :style="{ backgroundColor: colors['{{ $key }}'] }" aria-label="{{ $info['label'] }}">
                                <input type="text" class="flex-1 px-3 py-2 border border-gray-200 dark:border-gray-600 rounded-lg text-sm font-mono bg-white dark:bg-gray-800 dark:text-white" wire:model.defer="colors.{{ $key }}" placeholder="#ffffff" @input="$dispatch('colorChanged', { key: '{{ $key }}', value: $event.target.value })" aria-label="{{ $info['label'] }}-hex">
                            </div>
                            @error("colors.{{ $key }}")
                                <p class="text-xs text-red-600 dark:text-red-400 mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    @endforeach
                </div>
            </div>
        </x-cms.card>

        {{-- Semantic Colors --}}
        <x-cms.card class="lg:col-span-1" style="animation: cardEntry 0.4s ease-out 0.25s both">
            <div class="p-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-6 flex items-center gap-2">
                    <i class="fa-solid fa-triangle-exclamation text-red-600"></i>
                    Colores Semánticos
                </h3>
                <div class="space-y-5">
                    @foreach([
                        'success' => ['label' => 'Éxito', 'desc' => 'Mensajes de éxito, estados positivos'],
                        'warning' => ['label' => 'Advertencia', 'desc' => 'Alertas, advertencias, estados de espera'],
                        'error' => ['label' => 'Error', 'desc' => 'Mensajes de error, estados destructivos'],
                    ] as $key => $info)
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2 flex items-center justify-between">
                                <span>{{ $info['label'] }}</span>
                                <code class="text-xs px-2 py-0.5 rounded bg-gray-100 dark:bg-gray-800" x-text="colors['{{ $key }}']"></code>
                            </label>
                            <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">{{ $info['desc'] }}</p>
                            <div class="flex items-center gap-3">
                                <input type="color" class="w-12 h-12 rounded-lg border border-gray-200 dark:border-gray-700 cursor-pointer" wire:model.defer="colors.{{ $key }}" @input="$dispatch('colorChanged', { key: '{{ $key }}', value: $event.target.value })" :style="{ backgroundColor: colors['{{ $key }}'] }" aria-label="{{ $info['label'] }}">
                                <input type="text" class="flex-1 px-3 py-2 border border-gray-200 dark:border-gray-600 rounded-lg text-sm font-mono bg-white dark:bg-gray-800 dark:text-white" wire:model.defer="colors.{{ $key }}" placeholder="#22c55e" @input="$dispatch('colorChanged', { key: '{{ $key }}', value: $event.target.value })" aria-label="{{ $info['label'] }}-hex">
                            </div>
                            @error("colors.{{ $key }}")
                                <p class="text-xs text-red-600 dark:text-red-400 mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    @endforeach
                </div>
            </div>
        </x-cms.card>
    </div>

    {{-- Actions --}}
    <div class="mt-8 flex flex-col sm:flex-row gap-4">
        <button type="button" class="px-6 py-3 rounded-xl bg-blue-600 text-white hover:bg-blue-700 font-semibold transition-all shadow-sm flex items-center justify-center gap-2" onclick="confirmSaveColors()" wire:loading.attr="disabled">
            <span wire:loading.remove><i class="fa-solid fa-check mr-1"></i>Guardar Colores</span>
            <span wire:loading class="flex items-center gap-2"><svg class="animate-spin h-5 w-5" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" fill="none"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>Guardando...</span>
        </button>
        <button type="button" class="px-6 py-3 rounded-xl bg-gray-100 dark:bg-gray-800 text-gray-600 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-700 font-semibold transition-all flex items-center justify-center gap-2" onclick="confirmResetColors()" wire:loading.attr="disabled">
            <i class="fa-solid fa-rotate-left mr-1"></i>Restablecer Valores por Defecto
        </button>
    </div>

    {{-- Alpine.js for color preview --}}
    <script>
        function colorPreview() {
            return {
                colors: @js($colors),
                previewStyle: '',
                init() {
                    this.updatePreview();
                    this.$watch('colors', () => this.updatePreview(), { deep: true });
                },
                updatePreview() {
                    this.previewStyle = `
                        --color-primary: ${this.colors.primary};
                        --color-secondary: ${this.colors.secondary};
                        --color-accent: ${this.colors.accent};
                        --color-background: ${this.colors.background};
                        --color-surface: ${this.colors.surface};
                        --color-text-primary: ${this.colors.text_primary};
                        --color-text-secondary: ${this.colors.text_secondary};
                        --color-text-muted: ${this.colors.text_muted};
                        --color-border: ${this.colors.border};
                        --color-success: ${this.colors.success};
                        --color-warning: ${this.colors.warning};
                        --color-error: ${this.colors.error};
                    `;
                }
            }
        }
    </script>

    {{-- Styles --}}
    <style>
        @keyframes cardEntry { from { opacity: 0; transform: translateY(20px) scale(0.97); } to { opacity: 1; transform: translateY(0) scale(1); } }
        @keyframes pulse { 0%, 100% { opacity: 1; } 50% { opacity: 0.5; } }
        .animate-slide-down { animation: slideDown 0.3s ease-out; }
        @keyframes slideDown { from { opacity: 0; transform: translateY(-10px); } to { opacity: 1; transform: translateY(0); } }
        
        input[type="color"] {
            -webkit-appearance: none;
            appearance: none;
            border: none;
            padding: 0;
            cursor: pointer;
        }
        input[type="color"]::-webkit-color-swatch-wrapper { padding: 0; }
        input[type="color"]::-webkit-color-swatch { border: none; border-radius: 8px; }
        input[type="color"]::-moz-color-swatch { border: none; border-radius: 8px; }
    </style>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        function confirmSaveColors() {
            Swal.fire({
                title: '¿Guardar colores?',
                text: 'Se actualizará la paleta de colores del sitio.',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#2563eb',
                cancelButtonColor: '#64748b',
                confirmButtonText: 'Sí, guardar',
                cancelButtonText: 'Cancelar',
                customClass: { popup: 'rounded-2xl shadow-xl', confirmButton: 'rounded-xl px-5 py-2.5 font-semibold text-sm', cancelButton: 'rounded-xl px-5 py-2.5 font-semibold text-sm' }
            }).then((result) => {
                if (result.isConfirmed) { @this.call('saveColors') }
            });
        }
        function confirmResetColors() {
            Swal.fire({
                title: '¿Restablecer colores?',
                text: 'Se perderán todos los cambios personalizados.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc2626',
                cancelButtonColor: '#64748b',
                confirmButtonText: 'Sí, restablecer',
                cancelButtonText: 'Cancelar',
                customClass: { popup: 'rounded-2xl shadow-xl', confirmButton: 'rounded-xl px-5 py-2.5 font-semibold text-sm', cancelButton: 'rounded-xl px-5 py-2.5 font-semibold text-sm' }
            }).then((result) => {
                if (result.isConfirmed) { @this.call('resetColors') }
            });
        }
    </script>
</x-cms.layout>