<?php if (isset($component)) { $__componentOriginal86f7263b3d35f5f80a9e44bfc341b0ac = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal86f7263b3d35f5f80a9e44bfc341b0ac = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.cms.layout','data' => ['title' => 'Proceso de Trabajo','description' => 'Administra los pasos del proceso que se muestran en el landing page','headerIcon' => '<i class="fa-solid fa-route text-blue-600"></i>']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('cms.layout'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'Proceso de Trabajo','description' => 'Administra los pasos del proceso que se muestran en el landing page','headerIcon' => '<i class="fa-solid fa-route text-blue-600"></i>']); ?>
    
    <?php if($successMessage): ?>
        <div x-data="{ show: true }"
             x-show="show"
             x-transition:leave="transition ease-in duration-300"
             x-init="setTimeout(() => { show = false; window.Livewire.find('<?php echo e($_instance->getId()); ?>').call('clearSuccessMessage') }, 3000)"
             class="mb-6 flex items-center gap-3 p-4 rounded-xl bg-green-50 border border-green-200 text-green-700 shadow-sm animate-slide-down">
            <i class="fa-solid fa-circle-check text-lg"></i>
            <span class="flex-1 font-medium"><?php echo e($successMessage); ?></span>
            <button @click="show = false; window.Livewire.find('<?php echo e($_instance->getId()); ?>').call('clearSuccessMessage')" class="text-green-500 hover:opacity-70"><i class="fa-solid fa-xmark"></i></button>
        </div>
    <?php endif; ?>

    
    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-6">
        <?php $__empty_1 = true; $__currentLoopData = $steps; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $step): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <?php if (isset($component)) { $__componentOriginal22c3416241bd13185beb9fb89a01cdd3 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal22c3416241bd13185beb9fb89a01cdd3 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.cms.card','data' => ['class' => 'flex flex-col h-full group','style' => 'animation: cardEntry 0.4s ease-out '.e($loop->index * 0.06).'s both']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('cms.card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'flex flex-col h-full group','style' => 'animation: cardEntry 0.4s ease-out '.e($loop->index * 0.06).'s both']); ?>
                <div class="p-6 flex-1 text-center">
                    <div class="w-16 h-16 rounded-2xl bg-blue-50 flex items-center justify-center mx-auto mb-4 border border-blue-100">
                        <span class="text-2xl font-bold text-blue-600"><?php echo e($step['step_number']); ?></span>
                    </div>
                    <h5 class="font-bold text-gray-900 text-lg mb-2"><?php echo e($step['title']); ?></h5>
                    <p class="text-gray-500 text-sm"><?php echo e($step['description']); ?></p>
                    <?php if($step['icon']): ?>
                        <div class="mt-3 text-center">
                            <i class="<?php echo e($step['icon']); ?> text-3xl text-gray-300"></i>
                        </div>
                    <?php endif; ?>
                </div>
                <div class="border-t border-gray-100 bg-gray-50/80 px-6 py-3.5 flex items-center justify-center gap-1">
                    <?php if (isset($component)) { $__componentOriginald33697478d33dbb46e4d5cecf0cf51f9 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginald33697478d33dbb46e4d5cecf0cf51f9 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.cms.action-button','data' => ['icon' => 'fa-solid fa-pen','variant' => 'warning','wireClick' => 'edit('.e($step['id']).')','title' => 'Editar']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('cms.action-button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['icon' => 'fa-solid fa-pen','variant' => 'warning','wireClick' => 'edit('.e($step['id']).')','title' => 'Editar']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginald33697478d33dbb46e4d5cecf0cf51f9)): ?>
<?php $attributes = $__attributesOriginald33697478d33dbb46e4d5cecf0cf51f9; ?>
<?php unset($__attributesOriginald33697478d33dbb46e4d5cecf0cf51f9); ?>
<?php endif; ?>
<?php if (isset($__componentOriginald33697478d33dbb46e4d5cecf0cf51f9)): ?>
<?php $component = $__componentOriginald33697478d33dbb46e4d5cecf0cf51f9; ?>
<?php unset($__componentOriginald33697478d33dbb46e4d5cecf0cf51f9); ?>
<?php endif; ?>
                    <?php if (isset($component)) { $__componentOriginald33697478d33dbb46e4d5cecf0cf51f9 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginald33697478d33dbb46e4d5cecf0cf51f9 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.cms.action-button','data' => ['icon' => 'fa-solid fa-'.e($step['is_active'] ? 'eye-slash' : 'eye').'','variant' => ''.e($step['is_active'] ? 'ghost' : 'success').'','wireClick' => 'toggleActive('.e($step['id']).')','title' => ''.e($step['is_active'] ? 'Desactivar' : 'Activar').'']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('cms.action-button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['icon' => 'fa-solid fa-'.e($step['is_active'] ? 'eye-slash' : 'eye').'','variant' => ''.e($step['is_active'] ? 'ghost' : 'success').'','wireClick' => 'toggleActive('.e($step['id']).')','title' => ''.e($step['is_active'] ? 'Desactivar' : 'Activar').'']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginald33697478d33dbb46e4d5cecf0cf51f9)): ?>
<?php $attributes = $__attributesOriginald33697478d33dbb46e4d5cecf0cf51f9; ?>
<?php unset($__attributesOriginald33697478d33dbb46e4d5cecf0cf51f9); ?>
<?php endif; ?>
<?php if (isset($__componentOriginald33697478d33dbb46e4d5cecf0cf51f9)): ?>
<?php $component = $__componentOriginald33697478d33dbb46e4d5cecf0cf51f9; ?>
<?php unset($__componentOriginald33697478d33dbb46e4d5cecf0cf51f9); ?>
<?php endif; ?>
                    <?php if (isset($component)) { $__componentOriginald33697478d33dbb46e4d5cecf0cf51f9 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginald33697478d33dbb46e4d5cecf0cf51f9 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.cms.action-button','data' => ['icon' => 'fa-solid fa-trash','variant' => 'danger','wireClick' => 'delete('.e($step['id']).')','title' => 'Eliminar']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('cms.action-button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['icon' => 'fa-solid fa-trash','variant' => 'danger','wireClick' => 'delete('.e($step['id']).')','title' => 'Eliminar']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginald33697478d33dbb46e4d5cecf0cf51f9)): ?>
<?php $attributes = $__attributesOriginald33697478d33dbb46e4d5cecf0cf51f9; ?>
<?php unset($__attributesOriginald33697478d33dbb46e4d5cecf0cf51f9); ?>
<?php endif; ?>
<?php if (isset($__componentOriginald33697478d33dbb46e4d5cecf0cf51f9)): ?>
<?php $component = $__componentOriginald33697478d33dbb46e4d5cecf0cf51f9; ?>
<?php unset($__componentOriginald33697478d33dbb46e4d5cecf0cf51f9); ?>
<?php endif; ?>
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
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <div class="col-span-full x-cms.card p-16 text-center" style="animation: emptyPulse 3s ease-in-out infinite">
                <div class="w-16 h-16 rounded-2xl bg-blue-50 flex items-center justify-center mx-auto mb-4 border border-blue-100">
                    <i class="fa-solid fa-route text-2xl text-blue-400"></i>
                </div>
                <p class="text-gray-500 font-medium mb-1">No hay pasos definidos</p>
                <p class="text-gray-400 text-sm">Crea el primer paso del proceso</p>
            </div>
        <?php endif; ?>
    </div>

    
    <?php if($showForm): ?>
        <div class="fixed inset-0 bg-black/50 backdrop-blur-sm flex items-center justify-center z-50 overflow-y-auto py-8 px-4" x-data="{}" x-init="$watch('showForm', v => { if(v) document.body.style.overflow = 'hidden'; else document.body.style.overflow = ''; })">
            <div class="bg-white rounded-2xl shadow-2xl w-full max-w-xl max-h-[90vh] overflow-y-auto" style="animation: modalFadeIn 0.3s ease-out">
                <div class="sticky top-0 bg-white border-b border-gray-100 px-6 py-4 flex justify-between items-center rounded-t-2xl z-10">
                    <h4 class="text-lg font-bold text-gray-900"><?php echo e($editingId ? 'Editar' : 'Nuevo'); ?> Paso</h4>
                    <button wire:click="resetForm"
                            class="w-9 h-9 flex items-center justify-center rounded-lg text-gray-400 hover:text-gray-600 hover:bg-gray-100 transition-all"
                            aria-label="Cerrar">
                        <i class="fa-solid fa-xmark text-lg"></i>
                    </button>
                </div>
                <div class="px-6 py-5">
                    <?php if($errors->any()): ?>
                        <div class="mb-4 p-4 rounded-xl bg-red-50 border border-red-200 text-red-600 text-sm animate-slide-down">
                            <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <p class="flex items-start gap-2"><i class="fa-solid fa-circle-exclamation mt-0.5 text-xs"></i><?php echo e($error); ?></p>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </div>
                    <?php endif; ?>
                    <div class="space-y-5">
                        <div class="md:col-span-2">
                            <label class="block text-sm font-semibold text-gray-700 mb-1.5">Título *</label>
                            <input type="text"
                                   class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all bg-white"
                                   wire:model="title"
                                   placeholder="Ej: Diagnóstico Inicial">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1.5">Número de Paso *</label>
                            <input type="text"
                                   class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all bg-white"
                                   wire:model="stepNumber"
                                   placeholder="01"
                                   maxlength="10">
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-sm font-semibold text-gray-700 mb-1.5">Descripción</label>
                            <textarea class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all bg-white"
                                      rows="3"
                                      wire:model="description"
                                      placeholder="Descripción del paso..."></textarea>
                        </div>

                        
                        
                        
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1.5">Ícono (opcional)</label>

                            <?php
                                $stepIconOptions = [
                                    'fa-solid fa-magnifying-glass' => 'Diagnóstico',
                                    'fa-solid fa-clipboard-list' => 'Lista de Verificación',
                                    'fa-solid fa-screwdriver-wrench' => 'Mantenimiento',
                                    'fa-solid fa-wrench' => 'Reparación',
                                    'fa-solid fa-gauge-high' => 'Diagnóstico Avanzado',
                                    'fa-solid fa-car' => 'Vehículo',
                                    'fa-solid fa-car-side' => 'Vehículo (lateral)',
                                    'fa-solid fa-circle-check' => 'Aprobado',
                                    'fa-solid fa-check' => 'Verificación',
                                    'fa-solid fa-file-invoice' => 'Presupuesto',
                                    'fa-solid fa-credit-card' => 'Pago',
                                    'fa-solid fa-handshake' => 'Acuerdo / Entrega',
                                    'fa-solid fa-key' => 'Entrega de Llaves',
                                    'fa-solid fa-route' => 'Proceso / Ruta',
                                    'fa-solid fa-shield-halved' => 'Garantía',
                                    'fa-solid fa-truck' => 'Transporte',
                                    'fa-solid fa-phone' => 'Contacto',
                                    'fa-solid fa-calendar-check' => 'Cita Agendada',
                                    'fa-solid fa-thumbs-up' => 'Satisfacción',
                                ];
                            ?>

                            <div class="relative" x-data="{ open: false }">

                                <button type="button"
                                        @click="open = !open"
                                        @click.outside="open = false"
                                        class="w-full flex items-center gap-3 border border-gray-200 rounded-xl px-3.5 py-2.5 bg-white hover:border-blue-400 transition-all text-left">

                                    <div class="w-9 h-9 shrink-0 rounded-lg bg-gray-100 border border-gray-200 flex items-center justify-center text-gray-500 text-base">
                                        <i class="<?php echo e($icon ?: 'fa-solid fa-image'); ?>"></i>
                                    </div>

                                    <span class="flex-1 text-sm text-gray-700 truncate">
                                        <?php echo e($icon ? ($stepIconOptions[$icon] ?? 'Ícono personalizado') : 'Selecciona un ícono...'); ?>

                                    </span>

                                    <i class="fa-solid fa-chevron-down text-xs text-gray-400 transition-transform" :class="open ? 'rotate-180' : ''"></i>

                                </button>

                                <div x-show="open"
                                     x-cloak
                                     x-transition:enter="transition ease-out duration-150"
                                     x-transition:enter-start="opacity-0 scale-95"
                                     x-transition:enter-end="opacity-100 scale-100"
                                     class="absolute z-30 mt-2 w-full bg-white border border-gray-200 rounded-xl shadow-xl p-3">

                                    <div class="grid grid-cols-5 sm:grid-cols-6 gap-2 max-h-56 overflow-y-auto pr-1">

                                        <button type="button"
                                                wire:click="$set('icon', '')"
                                                @click="open = false"
                                                title="Sin ícono"
                                                class="aspect-square rounded-lg border flex items-center justify-center text-sm transition-all <?php echo e(!$icon ? 'bg-blue-600 text-white border-blue-600' : 'bg-gray-50 text-gray-400 border-gray-200 hover:bg-gray-100'); ?>">
                                            <i class="fa-solid fa-ban"></i>
                                        </button>

                                        <?php $__currentLoopData = $stepIconOptions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $iconClass => $iconLabel): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <button type="button"
                                                    wire:click="$set('icon', '<?php echo e($iconClass); ?>')"
                                                    @click="open = false"
                                                    title="<?php echo e($iconLabel); ?>"
                                                    class="aspect-square rounded-lg border flex items-center justify-center text-lg transition-all <?php echo e($icon === $iconClass ? 'bg-blue-600 text-white border-blue-600' : 'bg-gray-50 text-gray-600 border-gray-200 hover:bg-blue-50 hover:border-blue-300 hover:text-blue-600'); ?>">
                                                <i class="<?php echo e($iconClass); ?>"></i>
                                            </button>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                                    </div>

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
                <div class="sticky bottom-0 bg-white border-t border-gray-100 px-6 py-4 flex justify-end gap-3 rounded-b-2xl">
                    <button type="button"
                            class="px-5 py-2.5 rounded-xl bg-gray-100 text-gray-600 hover:bg-gray-200 font-semibold transition-all"
                            wire:click="resetForm">
                        Cancelar
                    </button>
                    <button type="button"
                            class="px-5 py-2.5 rounded-xl bg-blue-600 text-white hover:bg-blue-700 font-semibold transition-all shadow-sm"
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
    <?php endif; ?>

    
    <style>
        @keyframes modalFadeIn { from { opacity: 0; transform: scale(0.95) translateY(10px); } to { opacity: 1; transform: scale(1) translateY(0); } }
        @keyframes slideDown { from { opacity: 0; transform: translateY(-10px); } to { opacity: 1; transform: translateY(0); } }
        @keyframes cardEntry { from { opacity: 0; transform: translateY(20px) scale(0.97); } to { opacity: 1; transform: translateY(0) scale(1); } }
        @keyframes emptyPulse { 0%, 100% { opacity: 1; } 50% { opacity: 0.6; } }
        .animate-slide-down { animation: slideDown 0.3s ease-out; }
        [x-cloak] { display: none !important; }
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
<?php endif; ?><?php /**PATH C:\xampp\htdocs\arturo-motors\resources\views\livewire\cms\gestionar-pasos.blade.php ENDPATH**/ ?>