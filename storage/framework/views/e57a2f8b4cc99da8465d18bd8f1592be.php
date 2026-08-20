<?php if (isset($component)) { $__componentOriginal86f7263b3d35f5f80a9e44bfc341b0ac = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal86f7263b3d35f5f80a9e44bfc341b0ac = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.cms.layout','data' => ['title' => 'Apariencia y Colores','description' => 'Personaliza la paleta de colores completa del landing page','headerIcon' => '<i class="fa-solid fa-palette text-blue-600"></i>']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('cms.layout'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'Apariencia y Colores','description' => 'Personaliza la paleta de colores completa del landing page','headerIcon' => '<i class="fa-solid fa-palette text-blue-600"></i>']); ?>
    
    <!--[if BLOCK]><![endif]--><?php if($successMessage): ?>
        <div x-data="{ show: true }" x-show="show" x-transition:leave="transition ease-in duration-300" x-init="setTimeout(() => { show = false; window.Livewire.find('<?php echo e($_instance->getId()); ?>').call('clearSuccessMessage') }, 3000)" class="mb-6 flex items-center gap-3 p-4 rounded-xl bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 text-green-700 dark:text-green-400 shadow-sm animate-slide-down" role="alert">
            <i class="fa-solid fa-circle-check text-lg flex-shrink-0"></i>
            <span class="flex-1 font-medium"><?php echo e($successMessage); ?></span>
            <button @click="show = false; window.Livewire.find('<?php echo e($_instance->getId()); ?>').call('clearSuccessMessage')" class="text-green-500 dark:text-green-400 hover:opacity-70" aria-label="Cerrar"><i class="fa-solid fa-xmark"></i></button>
        </div>
    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->

    <!--[if BLOCK]><![endif]--><?php if($errorMessage): ?>
        <div x-data="{ show: true }" x-show="show" x-transition:leave="transition ease-in duration-300" x-init="setTimeout(() => { show = false; window.Livewire.find('<?php echo e($_instance->getId()); ?>').call('clearErrorMessage') }, 3000)" class="mb-6 flex items-center gap-3 p-4 rounded-xl bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 text-red-700 dark:text-red-400 shadow-sm animate-slide-down" role="alert">
            <i class="fa-solid fa-circle-exclamation text-lg flex-shrink-0"></i>
            <span class="flex-1 font-medium"><?php echo e($errorMessage); ?></span>
            <button @click="show = false; window.Livewire.find('<?php echo e($_instance->getId()); ?>').call('clearErrorMessage')" class="text-red-500 dark:text-red-400 hover:opacity-70" aria-label="Cerrar"><i class="fa-solid fa-xmark"></i></button>
        </div>
    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->

    
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
                        <!--[if BLOCK]><![endif]--><?php $__currentLoopData = ['primary' => 'Primario', 'secondary' => 'Secundario', 'accent' => 'Acento']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <div class="p-4 rounded-xl text-center" :style="{ backgroundColor: colors.surface, borderColor: colors.border, borderWidth: '1px', borderStyle: 'solid' }">
                                <div class="w-12 h-12 rounded-xl mx-auto mb-2 flex items-center justify-center" :style="{ backgroundColor: colors[$key] }">
                                    <i class="fa-solid fa-circle text-white"></i>
                                </div>
                                <span class="text-xs font-medium" :style="{ color: colors.text_primary }"><?php echo e($label); ?></span>
                                <code class="text-xs block mt-1" :style="{ color: colors.text_muted }" x-text="colors['<?php echo e($key); ?>']"></code>
                            </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
                    </div>
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

    
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        
        <?php if (isset($component)) { $__componentOriginal22c3416241bd13185beb9fb89a01cdd3 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal22c3416241bd13185beb9fb89a01cdd3 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.cms.card','data' => ['class' => 'lg:col-span-1','style' => 'animation: cardEntry 0.4s ease-out 0.1s both']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('cms.card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'lg:col-span-1','style' => 'animation: cardEntry 0.4s ease-out 0.1s both']); ?>
            <div class="p-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-6 flex items-center gap-2">
                    <i class="fa-solid fa-circle text-amber-500"></i>
                    Colores Principales
                </h3>
                <div class="space-y-5">
                    <!--[if BLOCK]><![endif]--><?php $__currentLoopData = [
                        'primary' => ['label' => 'Primario (Botones, Enlaces)', 'desc' => 'Color principal de la marca - botones CTA, enlaces activos'],
                        'primary_hover' => ['label' => 'Primario Hover', 'desc' => 'Variación oscura para hover de botones primarios'],
                        'secondary' => ['label' => 'Secundario (Acentos)', 'desc' => 'Color secundario - badges, elementos decorativos'],
                        'secondary_hover' => ['label' => 'Secundario Hover', 'desc' => 'Variación para hover de elementos secundarios'],
                        'accent' => ['label' => 'Acento (Badges, Focus)', 'desc' => 'Color de acento - badges, focus rings, elementos interactivos'],
                    ]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $info): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2 flex items-center justify-between">
                                <span><?php echo e($info['label']); ?></span>
                                <code class="text-xs px-2 py-0.5 rounded bg-gray-100 dark:bg-gray-800" x-text="colors['<?php echo e($key); ?>']"></code>
                            </label>
                            <p class="text-xs text-gray-500 dark:text-gray-400 mb-1"><?php echo e($info['desc']); ?></p>
                            <div class="flex items-center gap-3">
                                <input type="color" class="w-12 h-12 rounded-lg border border-gray-200 dark:border-gray-700 cursor-pointer" wire:model.defer="colors.<?php echo e($key); ?>" @input="$dispatch('colorChanged', { key: '<?php echo e($key); ?>', value: $event.target.value })" :style="{ backgroundColor: colors['<?php echo e($key); ?>'] }" aria-label="<?php echo e($info['label']); ?>">
                                <input type="text" class="flex-1 px-3 py-2 border border-gray-200 dark:border-gray-600 rounded-lg text-sm font-mono bg-white dark:bg-gray-800 dark:text-white" wire:model.defer="colors.<?php echo e($key); ?>" placeholder="#f59e0b" @input="$dispatch('colorChanged', { key: '<?php echo e($key); ?>', value: $event.target.value })" aria-label="<?php echo e($info['label']); ?>-hex">
                            </div>
                            <!--[if BLOCK]><![endif]--><?php $__errorArgs = ["colors.<?php echo e($key); ?>"];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <p class="text-xs text-red-600 dark:text-red-400 mt-1"><?php echo e($message); ?></p>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><!--[if ENDBLOCK]><![endif]-->
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
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
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.cms.card','data' => ['class' => 'lg:col-span-1','style' => 'animation: cardEntry 0.4s ease-out 0.15s both']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('cms.card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'lg:col-span-1','style' => 'animation: cardEntry 0.4s ease-out 0.15s both']); ?>
            <div class="p-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-6 flex items-center gap-2">
                    <i class="fa-solid fa-fill-drip text-blue-600"></i>
                    Fondos y Superficies
                </h3>
                <div class="space-y-5">
                    <!--[if BLOCK]><![endif]--><?php $__currentLoopData = [
                        'background' => ['label' => 'Fondo Principal', 'desc' => 'Color de fondo del hero y secciones oscuras'],
                        'surface' => ['label' => 'Superficie (Tarjetas)', 'desc' => 'Fondo de tarjetas, navbar, modales'],
                        'border' => ['label' => 'Bordes', 'desc' => 'Color de bordes sutiles, divisores'],
                    ]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $info): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2 flex items-center justify-between">
                                <span><?php echo e($info['label']); ?></span>
                                <code class="text-xs px-2 py-0.5 rounded bg-gray-100 dark:bg-gray-800" x-text="colors['<?php echo e($key); ?>']"></code>
                            </label>
                            <p class="text-xs text-gray-500 dark:text-gray-400 mb-1"><?php echo e($info['desc']); ?></p>
                            <div class="flex items-center gap-3">
                                <input type="color" class="w-12 h-12 rounded-lg border border-gray-200 dark:border-gray-700 cursor-pointer" wire:model.defer="colors.<?php echo e($key); ?>" @input="$dispatch('colorChanged', { key: '<?php echo e($key); ?>', value: $event.target.value })" :style="{ backgroundColor: colors['<?php echo e($key); ?>'] }" aria-label="<?php echo e($info['label']); ?>">
                                <input type="text" class="flex-1 px-3 py-2 border border-gray-200 dark:border-gray-600 rounded-lg text-sm font-mono bg-white dark:bg-gray-800 dark:text-white" wire:model.defer="colors.<?php echo e($key); ?>" placeholder="#0a0f1e" @input="$dispatch('colorChanged', { key: '<?php echo e($key); ?>', value: $event.target.value })" aria-label="<?php echo e($info['label']); ?>-hex">
                            </div>
                            <!--[if BLOCK]><![endif]--><?php $__errorArgs = ["colors.<?php echo e($key); ?>"];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <p class="text-xs text-red-600 dark:text-red-400 mt-1"><?php echo e($message); ?></p>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><!--[if ENDBLOCK]><![endif]-->
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
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
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.cms.card','data' => ['class' => 'lg:col-span-1','style' => 'animation: cardEntry 0.4s ease-out 0.2s both']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('cms.card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'lg:col-span-1','style' => 'animation: cardEntry 0.4s ease-out 0.2s both']); ?>
            <div class="p-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-6 flex items-center gap-2">
                    <i class="fa-solid fa-font text-green-600"></i>
                    Tipografía
                </h3>
                <div class="space-y-5">
                    <!--[if BLOCK]><![endif]--><?php $__currentLoopData = [
                        'text_primary' => ['label' => 'Texto Principal', 'desc' => 'Títulos, encabezados, texto importante'],
                        'text_secondary' => ['label' => 'Texto Secundario', 'desc' => 'Párrafos, descripciones, texto de apoyo'],
                        'text_muted' => ['label' => 'Texto Muted', 'desc' => 'Texto secundario, placeholders, labels'],
                    ]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $info): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2 flex items-center justify-between">
                                <span><?php echo e($info['label']); ?></span>
                                <code class="text-xs px-2 py-0.5 rounded bg-gray-100 dark:bg-gray-800" x-text="colors['<?php echo e($key); ?>']"></code>
                            </label>
                            <p class="text-xs text-gray-500 dark:text-gray-400 mb-1"><?php echo e($info['desc']); ?></p>
                            <div class="flex items-center gap-3">
                                <input type="color" class="w-12 h-12 rounded-lg border border-gray-200 dark:border-gray-700 cursor-pointer" wire:model.defer="colors.<?php echo e($key); ?>" @input="$dispatch('colorChanged', { key: '<?php echo e($key); ?>', value: $event.target.value })" :style="{ backgroundColor: colors['<?php echo e($key); ?>'] }" aria-label="<?php echo e($info['label']); ?>">
                                <input type="text" class="flex-1 px-3 py-2 border border-gray-200 dark:border-gray-600 rounded-lg text-sm font-mono bg-white dark:bg-gray-800 dark:text-white" wire:model.defer="colors.<?php echo e($key); ?>" placeholder="#ffffff" @input="$dispatch('colorChanged', { key: '<?php echo e($key); ?>', value: $event.target.value })" aria-label="<?php echo e($info['label']); ?>-hex">
                            </div>
                            <!--[if BLOCK]><![endif]--><?php $__errorArgs = ["colors.<?php echo e($key); ?>"];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <p class="text-xs text-red-600 dark:text-red-400 mt-1"><?php echo e($message); ?></p>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><!--[if ENDBLOCK]><![endif]-->
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
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
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.cms.card','data' => ['class' => 'lg:col-span-1','style' => 'animation: cardEntry 0.4s ease-out 0.25s both']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('cms.card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'lg:col-span-1','style' => 'animation: cardEntry 0.4s ease-out 0.25s both']); ?>
            <div class="p-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-6 flex items-center gap-2">
                    <i class="fa-solid fa-triangle-exclamation text-red-600"></i>
                    Colores Semánticos
                </h3>
                <div class="space-y-5">
                    <!--[if BLOCK]><![endif]--><?php $__currentLoopData = [
                        'success' => ['label' => 'Éxito', 'desc' => 'Mensajes de éxito, estados positivos'],
                        'warning' => ['label' => 'Advertencia', 'desc' => 'Alertas, advertencias, estados de espera'],
                        'error' => ['label' => 'Error', 'desc' => 'Mensajes de error, estados destructivos'],
                    ]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $info): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2 flex items-center justify-between">
                                <span><?php echo e($info['label']); ?></span>
                                <code class="text-xs px-2 py-0.5 rounded bg-gray-100 dark:bg-gray-800" x-text="colors['<?php echo e($key); ?>']"></code>
                            </label>
                            <p class="text-xs text-gray-500 dark:text-gray-400 mb-1"><?php echo e($info['desc']); ?></p>
                            <div class="flex items-center gap-3">
                                <input type="color" class="w-12 h-12 rounded-lg border border-gray-200 dark:border-gray-700 cursor-pointer" wire:model.defer="colors.<?php echo e($key); ?>" @input="$dispatch('colorChanged', { key: '<?php echo e($key); ?>', value: $event.target.value })" :style="{ backgroundColor: colors['<?php echo e($key); ?>'] }" aria-label="<?php echo e($info['label']); ?>">
                                <input type="text" class="flex-1 px-3 py-2 border border-gray-200 dark:border-gray-600 rounded-lg text-sm font-mono bg-white dark:bg-gray-800 dark:text-white" wire:model.defer="colors.<?php echo e($key); ?>" placeholder="#22c55e" @input="$dispatch('colorChanged', { key: '<?php echo e($key); ?>', value: $event.target.value })" aria-label="<?php echo e($info['label']); ?>-hex">
                            </div>
                            <!--[if BLOCK]><![endif]--><?php $__errorArgs = ["colors.<?php echo e($key); ?>"];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <p class="text-xs text-red-600 dark:text-red-400 mt-1"><?php echo e($message); ?></p>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><!--[if ENDBLOCK]><![endif]-->
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
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
    </div>

    
    <div class="mt-8 flex flex-col sm:flex-row gap-4">
        <button type="button" class="px-6 py-3 rounded-xl bg-blue-600 text-white hover:bg-blue-700 font-semibold transition-all shadow-sm flex items-center justify-center gap-2" onclick="confirmSaveColors()" wire:loading.attr="disabled">
            <span wire:loading.remove><i class="fa-solid fa-check mr-1"></i>Guardar Colores</span>
            <span wire:loading class="flex items-center gap-2"><svg class="animate-spin h-5 w-5" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" fill="none"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>Guardando...</span>
        </button>
        <button type="button" class="px-6 py-3 rounded-xl bg-gray-100 dark:bg-gray-800 text-gray-600 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-700 font-semibold transition-all flex items-center justify-center gap-2" onclick="confirmResetColors()" wire:loading.attr="disabled">
            <i class="fa-solid fa-rotate-left mr-1"></i>Restablecer Valores por Defecto
        </button>
    </div>

    
    <script>
        function colorPreview() {
            return {
                colors: <?php echo \Illuminate\Support\Js::from($colors)->toHtml() ?>,
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
                if (result.isConfirmed) { window.Livewire.find('<?php echo e($_instance->getId()); ?>').call('saveColors') }
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
                if (result.isConfirmed) { window.Livewire.find('<?php echo e($_instance->getId()); ?>').call('resetColors') }
            });
        }
    </script>
 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal86f7263b3d35f5f80a9e44bfc341b0ac)): ?>
<?php $attributes = $__attributesOriginal86f7263b3d35f5f80a9e44bfc341b0ac; ?>
<?php unset($__attributesOriginal86f7263b3d35f5f80a9e44bfc341b0ac); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal86f7263b3d35f5f80a9e44bfc341b0ac)): ?>
<?php $component = $__componentOriginal86f7263b3d35f5f80a9e44bfc341b0ac; ?>
<?php unset($__componentOriginal86f7263b3d35f5f80a9e44bfc341b0ac); ?>
<?php endif; ?><?php /**PATH C:\xampp\htdocs\arturo-motors\resources\views/livewire/cms/gestionar-apariencia.blade.php ENDPATH**/ ?>