<?php if (isset($component)) { $__componentOriginal86f7263b3d35f5f80a9e44bfc341b0ac = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal86f7263b3d35f5f80a9e44bfc341b0ac = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.cms.layout','data' => ['title' => 'Logo y Favicon','description' => 'Sube y gestiona el logo principal y el favicon del sitio','headerIcon' => '<i class="fa-solid fa-image text-blue-600"></i>']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('cms.layout'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'Logo y Favicon','description' => 'Sube y gestiona el logo principal y el favicon del sitio','headerIcon' => '<i class="fa-solid fa-image text-blue-600"></i>']); ?>
    
    <?php if($successMessage): ?>
        <div x-data="{ show: true }" x-show="show" x-transition:leave="transition ease-in duration-300" x-init="setTimeout(() => { show = false; window.Livewire.find('<?php echo e($_instance->getId()); ?>').call('clearSuccessMessage') }, 3000)" class="mb-6 flex items-center gap-3 p-4 rounded-xl bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 text-green-700 dark:text-green-400 shadow-sm animate-slide-down" role="alert">
            <i class="fa-solid fa-circle-check text-lg flex-shrink-0"></i>
            <span class="flex-1 font-medium"><?php echo e($successMessage); ?></span>
            <button @click="show = false; window.Livewire.find('<?php echo e($_instance->getId()); ?>').call('clearSuccessMessage')" class="text-green-500 dark:text-green-400 hover:opacity-70" aria-label="Cerrar"><i class="fa-solid fa-xmark"></i></button>
        </div>
    <?php endif; ?>

    <?php if($errorMessage): ?>
        <div x-data="{ show: true }" x-show="show" x-transition:leave="transition ease-in duration-300" x-init="setTimeout(() => { show = false; window.Livewire.find('<?php echo e($_instance->getId()); ?>').call('clearErrorMessage') }, 3000)" class="mb-6 flex items-center gap-3 p-4 rounded-xl bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 text-red-700 dark:text-red-400 shadow-sm animate-slide-down" role="alert">
            <i class="fa-solid fa-circle-exclamation text-lg flex-shrink-0"></i>
            <span class="flex-1 font-medium"><?php echo e($errorMessage); ?></span>
            <button @click="show = false; window.Livewire.find('<?php echo e($_instance->getId()); ?>').call('clearErrorMessage')" class="text-red-500 dark:text-red-400 hover:opacity-70" aria-label="Cerrar"><i class="fa-solid fa-xmark"></i></button>
        </div>
    <?php endif; ?>

    
    <?php if (isset($component)) { $__componentOriginal22c3416241bd13185beb9fb89a01cdd3 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal22c3416241bd13185beb9fb89a01cdd3 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.cms.card','data' => ['class' => 'mb-8','style' => 'animation: cardEntry 0.4s ease-out']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('cms.card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'mb-8','style' => 'animation: cardEntry 0.4s ease-out']); ?>
        <div class="p-6">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white flex items-center gap-2">
                    <i class="fa-solid fa-cube text-blue-600"></i>
                    Logo Principal
                </h3>
                <span class="text-xs text-gray-500 dark:text-gray-400">Formatos: PNG, WebP, SVG • Máx 2MB • 200-800px ancho</span>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                
                <div class="md:col-span-1">
                    <div class="bg-gray-50 dark:bg-gray-800 rounded-xl p-8 border border-gray-200 dark:border-gray-700 min-h-[200px] flex items-center justify-center">
                        <?php if($currentLogo): ?>
                            <div class="relative max-w-full max-h-[180px]">
                                <picture>
                                    <?php if($currentLogo->webpUrl()): ?>
                                        <source type="image/webp" srcset="<?php echo e($currentLogo->webpUrl()); ?>">
                                    <?php endif; ?>
                                    <img src="<?php echo e(asset('storage/' . $currentLogo->file_path)); ?>" alt="Logo actual" class="max-w-full max-h-[180px] object-contain">
                                </picture>
                                <div class="absolute top-2 right-2">
                                    <button type="button" wire:click="removeLogo" class="w-8 h-8 rounded-full bg-red-500 text-white flex items-center justify-center shadow-lg hover:bg-red-600 transition-all" aria-label="Eliminar logo" wire:loading.attr="disabled">
                                        <i class="fa-solid fa-trash text-sm"></i>
                                    </button>
                                </div>
                            </div>
                        <?php else: ?>
                            <div class="text-center text-gray-500 dark:text-gray-400">
                                <i class="fa-solid fa-image text-6xl mb-3"></i>
                                <p class="font-medium">No hay logo configurado</p>
                                <p class="text-sm mt-1">Sube uno usando el formulario</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                
                <div class="md:col-span-1 space-y-5">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Seleccionar Logo</label>
                        <div class="relative">
                            <input type="file" id="logo-file" wire:model="logoFile" accept="image/png,image/webp,image/svg+xml" class="hidden" @change="$dispatch('fileSelected', $event.target.files[0]?.name)">
                            <div class="border-2 border-dashed border-gray-300 dark:border-gray-600 rounded-xl p-8 text-center hover:border-blue-400 dark:hover:border-blue-500 transition-colors cursor-pointer" onclick="document.getElementById('logo-file').click()">
                                <i class="fa-solid fa-cloud-arrow-up text-4xl text-gray-400 dark:text-gray-500 mb-3"></i>
                                <p class="text-gray-600 dark:text-gray-300">Arrastra tu logo aquí o haz clic para seleccionar</p>
                                <p class="text-xs text-gray-400 dark:text-gray-500 mt-2">PNG, WebP, SVG • Máx 2MB</p>
                            </div>
                        </div>
                        <div x-data="{ fileName: '' }" x-on:fileSelected.window="fileName = $event.detail" class="mt-3 p-3 bg-gray-50 dark:bg-gray-800 rounded-lg" x-show="fileName" x-transition>
                            <p class="text-sm text-gray-600 dark:text-gray-300 flex items-center justify-between">
                                <span x-text="fileName"></span>
                                <span class="text-xs text-blue-600 dark:text-blue-400">Listo para subir</span>
                            </p>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Alt Text (Accesibilidad)</label>
                        <input type="text" class="w-full px-4 py-2.5 border border-gray-200 dark:border-gray-600 rounded-lg text-sm bg-white dark:bg-gray-800 dark:text-white" placeholder="Logo de la empresa" value="Logo de la empresa" readonly>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Se establece automáticamente</p>
                    </div>

                    <button type="button" class="w-full px-5 py-3 rounded-xl bg-blue-600 text-white hover:bg-blue-700 font-semibold transition-all shadow-sm flex items-center justify-center gap-2" wire:click="uploadLogo" wire:loading.attr="disabled" :disabled="!logoFile">
                        <span wire:loading.remove><i class="fa-solid fa-upload mr-1"></i>Subir Logo</span>
                        <span wire:loading class="flex items-center gap-2"><svg class="animate-spin h-5 w-5" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" fill="none"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>Subiendo...</span>
                    </button>

                    <?php if($currentLogo): ?>
                        <button type="button" class="w-full px-5 py-2.5 rounded-xl bg-red-50 text-red-600 dark:bg-red-900/20 dark:text-red-400 hover:bg-red-100 dark:hover:bg-red-900/30 font-semibold transition-all flex items-center justify-center gap-2" wire:click="removeLogo" wire:loading.attr="disabled">
                            <i class="fa-solid fa-trash mr-1"></i>Eliminar Logo
                        </button>
                    <?php endif; ?>
                </div>
            </div>
        </div>
     <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal22c3416241bd13185beb9fb89a01cdd3)): ?>
<?php $attributes = $__attributesOriginal22c3416241bd13185beb9fb89a01cdd3; ?>
<?php unset($__attributesOriginal22c3416241bd13185beb9fb89a01cdd3); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal22c3416241bd13185beb9fb89a01cdd3)): ?>
<?php $component = $__componentOriginal22c3416241bd13185beb9fb89a01cdd3; ?>
<?php unset($__componentOriginal22c3416241bd13185beb9fb89a01cdd3); ?>
<?php endif; ?>

    
    <?php if (isset($component)) { $__componentOriginal22c3416241bd13185beb9fb89a01cdd3 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal22c3416241bd13185beb9fb89a01cdd3 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.cms.card','data' => ['class' => 'mb-8','style' => 'animation: cardEntry 0.4s ease-out 0.1s both']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('cms.card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'mb-8','style' => 'animation: cardEntry 0.4s ease-out 0.1s both']); ?>
        <div class="p-6">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white flex items-center gap-2">
                    <i class="fa-solid fa-globe text-amber-600"></i>
                    Favicon
                </h3>
                <span class="text-xs text-gray-500 dark:text-gray-400">Exactamente 32x32px • PNG, ICO, WebP • Máx 512KB</span>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                
                <div class="md:col-span-1">
                    <div class="bg-gray-50 dark:bg-gray-800 rounded-xl p-8 border border-gray-200 dark:border-gray-700 min-h-[200px] flex flex-col items-center justify-center">
                        <?php if($currentFavicon): ?>
                            <div class="relative mb-4">
                                <img src="<?php echo e($faviconPreview); ?>" alt="Favicon actual" class="w-32 h-32 rounded-lg border border-gray-200 dark:border-gray-700 object-contain bg-white dark:bg-gray-700">
                                <div class="absolute top-2 right-2">
                                    <button type="button" wire:click="removeFavicon" class="w-8 h-8 rounded-full bg-red-500 text-white flex items-center justify-center shadow-lg hover:bg-red-600 transition-all" aria-label="Eliminar favicon" wire:loading.attr="disabled">
                                        <i class="fa-solid fa-trash text-sm"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="text-center">
                                <p class="font-medium text-gray-900 dark:text-white">Favicon Actual</p>
                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">32 × 32 px</p>
                            </div>
                        <?php else: ?>
                            <div class="text-center text-gray-500 dark:text-gray-400">
                                <i class="fa-solid fa-globe text-6xl mb-3"></i>
                                <p class="font-medium">No hay favicon configurado</p>
                                <p class="text-sm mt-1">Se usa el genérico del navegador</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                
                <div class="md:col-span-1 space-y-5">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Seleccionar Favicon</label>
                        <div class="relative">
                            <input type="file" id="favicon-file" wire:model="faviconFile" accept="image/png,image/x-icon,image/webp" class="hidden" @change="$dispatch('faviconSelected', $event.target.files[0]?.name)">
                            <div class="border-2 border-dashed border-gray-300 dark:border-gray-600 rounded-xl p-8 text-center hover:border-blue-400 dark:hover:border-blue-500 transition-colors cursor-pointer" onclick="document.getElementById('favicon-file').click()">
                                <i class="fa-solid fa-globe text-4xl text-gray-400 dark:text-gray-500 mb-3"></i>
                                <p class="text-gray-600 dark:text-gray-300">Arrastra tu favicon aquí o haz clic para seleccionar</p>
                                <p class="text-xs text-gray-400 dark:text-gray-500 mt-2">PNG, ICO, WebP • 32x32px • Máx 512KB</p>
                            </div>
                        </div>
                        <div x-data="{ faviconName: '' }" x-on:faviconSelected.window="faviconName = $event.detail" class="mt-3 p-3 bg-gray-50 dark:bg-gray-800 rounded-lg" x-show="faviconName" x-transition>
                            <p class="text-sm text-gray-600 dark:text-gray-300 flex items-center justify-between">
                                <span x-text="faviconName"></span>
                                <span class="text-xs text-blue-600 dark:text-blue-400">Listo para subir</span>
                            </p>
                        </div>
                    </div>

                    <button type="button" class="w-full px-5 py-3 rounded-xl bg-amber-600 text-white hover:bg-amber-700 font-semibold transition-all shadow-sm flex items-center justify-center gap-2" wire:click="uploadFavicon" wire:loading.attr="disabled" :disabled="!faviconFile">
                        <span wire:loading.remove><i class="fa-solid fa-upload mr-1"></i>Subir Favicon</span>
                        <span wire:loading class="flex items-center gap-2"><svg class="animate-spin h-5 w-5" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" fill="none"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>Subiendo...</span>
                    </button>

                    <?php if($currentFavicon): ?>
                        <button type="button" class="w-full px-5 py-2.5 rounded-xl bg-red-50 text-red-600 dark:bg-red-900/20 dark:text-red-400 hover:bg-red-100 dark:hover:bg-red-900/30 font-semibold transition-all flex items-center justify-center gap-2" wire:click="removeFavicon" wire:loading.attr="disabled">
                            <i class="fa-solid fa-trash mr-1"></i>Eliminar Favicon
                        </button>
                    <?php endif; ?>
                </div>
            </div>
        </div>
     <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal22c3416241bd13185beb9fb89a01cdd3)): ?>
<?php $attributes = $__attributesOriginal22c3416241bd13185beb9fb89a01cdd3; ?>
<?php unset($__attributesOriginal22c3416241bd13185beb9fb89a01cdd3); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal22c3416241bd13185beb9fb89a01cdd3)): ?>
<?php $component = $__componentOriginal22c3416241bd13185beb9fb89a01cdd3; ?>
<?php unset($__componentOriginal22c3416241bd13185beb9fb89a01cdd3); ?>
<?php endif; ?>

    
    <?php if (isset($component)) { $__componentOriginal22c3416241bd13185beb9fb89a01cdd3 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal22c3416241bd13185beb9fb89a01cdd3 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.cms.card','data' => ['style' => 'animation: cardEntry 0.4s ease-out 0.2s both']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('cms.card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['style' => 'animation: cardEntry 0.4s ease-out 0.2s both']); ?>
        <div class="p-6">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-6 flex items-center gap-2">
                <i class="fa-solid fa-eye text-green-600"></i>
                Vista Previa en Navbar
            </h3>
            <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-4">
                <nav class="flex items-center justify-between" style="background: rgba(255,255,255,0.95); backdrop-filter: blur(12px);">
                    <a href="#" class="flex items-center gap-2" style="color: #0a0f1e;">
                        <?php if($currentLogo): ?>
                            <img src="<?php echo e($logoPreview); ?>" alt="Logo" class="h-8 w-auto" style="max-width: 120px;">
                        <?php else: ?>
                            <div class="w-8 h-8 rounded-xl" style="background: linear-gradient(135deg, #f59e0b, #ef4444);"></div>
                            <span class="font-bold text-xl">Arturo Motors</span>
                        <?php endif; ?>
                    </a>
                    <button class="px-3 py-1.5 rounded-full text-xs font-medium" style="background: linear-gradient(135deg, #f59e0b, #ef4444); color: white;">Cotizar</button>
                </nav>
            </div>
        </div>
     <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal22c3416241bd13185beb9fb89a01cdd3)): ?>
<?php $attributes = $__attributesOriginal22c3416241bd13185beb9fb89a01cdd3; ?>
<?php unset($__attributesOriginal22c3416241bd13185beb9fb89a01cdd3); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal22c3416241bd13185beb9fb89a01cdd3)): ?>
<?php $component = $__componentOriginal22c3416241bd13185beb9fb89a01cdd3; ?>
<?php unset($__componentOriginal22c3416241bd13185beb9fb89a01cdd3); ?>
<?php endif; ?>

    
    <style>
        @keyframes cardEntry { from { opacity: 0; transform: translateY(20px) scale(0.97); } to { opacity: 1; transform: translateY(0) scale(1); } }
        .animate-slide-down { animation: slideDown 0.3s ease-out; }
        @keyframes slideDown { from { opacity: 0; transform: translateY(-10px); } to { opacity: 1; transform: translateY(0); } }
    </style>
 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal86f7263b3d35f5f80a9e44bfc341b0ac)): ?>
<?php $attributes = $__attributesOriginal86f7263b3d35f5f80a9e44bfc341b0ac; ?>
<?php unset($__attributesOriginal86f7263b3d35f5f80a9e44bfc341b0ac); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal86f7263b3d35f5f80a9e44bfc341b0ac)): ?>
<?php $component = $__componentOriginal86f7263b3d35f5f80a9e44bfc341b0ac; ?>
<?php unset($__componentOriginal86f7263b3d35f5f80a9e44bfc341b0ac); ?>
<?php endif; ?><?php /**PATH C:\xampp\htdocs\arturo-motors\resources\views\livewire\cms\gestionar-logo.blade.php ENDPATH**/ ?>