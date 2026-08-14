<?php if (isset($component)) { $__componentOriginal86f7263b3d35f5f80a9e44bfc341b0ac = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal86f7263b3d35f5f80a9e44bfc341b0ac = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.cms.layout','data' => ['title' => 'Información de Contacto','description' => 'Administra los datos de contacto que se muestran en el landing page','headerIcon' => '<i class="fa-solid fa-location-dot text-blue-600"></i>']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('cms.layout'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'Información de Contacto','description' => 'Administra los datos de contacto que se muestran en el landing page','headerIcon' => '<i class="fa-solid fa-location-dot text-blue-600"></i>']); ?>
    
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-8 pb-4 border-b border-gray-200">
        <div>
            <h2 class="text-xl font-semibold text-gray-900"><?php echo e(count($contacts) ?? 0); ?> elementos de contacto</h2>
            <p class="text-gray-500 text-sm">Dirección, teléfono, horario, WhatsApp, email y mapa</p>
        </div>
        <button wire:click="create"
                class="w-full sm:w-auto px-5 py-2.5 rounded-xl bg-blue-600 text-white font-semibold shadow-sm hover:bg-blue-700 hover:shadow-md transition-all flex items-center justify-center gap-2">
            <i class="fa-solid fa-plus"></i> Nuevo Contacto
        </button>
    </div>

    
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

    
    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
        <?php $__empty_1 = true; $__currentLoopData = $contacts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $contact): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
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
                <div class="p-6 flex-1">
                    <div class="flex items-start justify-between mb-4">
                        <?php if (isset($component)) { $__componentOriginal73ede9e18dd1b68555db6f5f96755480 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal73ede9e18dd1b68555db6f5f96755480 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.cms.icon-box','data' => ['icon' => $typeIcons[$contact['type']] ?? 'fa-solid fa-map-location-dot','bgClass' => 'bg-blue-50 border-blue-100 text-blue-600']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('cms.icon-box'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['icon' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($typeIcons[$contact['type']] ?? 'fa-solid fa-map-location-dot'),'bgClass' => 'bg-blue-50 border-blue-100 text-blue-600']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal73ede9e18dd1b68555db6f5f96755480)): ?>
<?php $attributes = $__attributesOriginal73ede9e18dd1b68555db6f5f96755480; ?>
<?php unset($__attributesOriginal73ede9e18dd1b68555db6f5f96755480); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal73ede9e18dd1b68555db6f5f96755480)): ?>
<?php $component = $__componentOriginal73ede9e18dd1b68555db6f5f96755480; ?>
<?php unset($__componentOriginal73ede9e18dd1b68555db6f5f96755480); ?>
<?php endif; ?>
                        <?php if (isset($component)) { $__componentOriginal726e09f7b3ddb3254115eba0aadc9cf1 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal726e09f7b3ddb3254115eba0aadc9cf1 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.cms.status-badge','data' => ['active' => $contact['is_active']]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('cms.status-badge'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['active' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($contact['is_active'])]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal726e09f7b3ddb3254115eba0aadc9cf1)): ?>
<?php $attributes = $__attributesOriginal726e09f7b3ddb3254115eba0aadc9cf1; ?>
<?php unset($__attributesOriginal726e09f7b3ddb3254115eba0aadc9cf1); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal726e09f7b3ddb3254115eba0aadc9cf1)): ?>
<?php $component = $__componentOriginal726e09f7b3ddb3254115eba0aadc9cf1; ?>
<?php unset($__componentOriginal726e09f7b3ddb3254115eba0aadc9cf1); ?>
<?php endif; ?>
                    </div>
                    <h5 class="font-bold text-gray-900 text-lg mb-1"><?php echo e($contact['label']); ?></h5>
                    <p class="text-gray-500 text-sm"><?php echo e(Str::limit($contact['value'], 60)); ?></p>
                    <?php if($contact['type'] === 'map_iframe'): ?>
                        <p class="text-xs text-gray-400 mt-2"><i class="fa-solid fa-code mr-1"></i>Iframe embebido</p>
                    <?php endif; ?>
                </div>
                <div class="border-t border-gray-100 bg-gray-50/80 px-6 py-3.5 flex items-center justify-between">
                    <div class="flex gap-1">
                        <?php if (isset($component)) { $__componentOriginald33697478d33dbb46e4d5cecf0cf51f9 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginald33697478d33dbb46e4d5cecf0cf51f9 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.cms.action-button','data' => ['icon' => 'fa-solid fa-pen','variant' => 'warning','wireClick' => 'edit('.e($contact['id']).')','title' => 'Editar']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('cms.action-button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['icon' => 'fa-solid fa-pen','variant' => 'warning','wireClick' => 'edit('.e($contact['id']).')','title' => 'Editar']); ?>
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
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.cms.action-button','data' => ['icon' => 'fa-solid fa-'.e($contact['is_active'] ? 'eye-slash' : 'eye').'','variant' => ''.e($contact['is_active'] ? 'ghost' : 'success').'','wireClick' => 'toggleActive('.e($contact['id']).')','title' => ''.e($contact['is_active'] ? 'Desactivar' : 'Activar').'']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('cms.action-button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['icon' => 'fa-solid fa-'.e($contact['is_active'] ? 'eye-slash' : 'eye').'','variant' => ''.e($contact['is_active'] ? 'ghost' : 'success').'','wireClick' => 'toggleActive('.e($contact['id']).')','title' => ''.e($contact['is_active'] ? 'Desactivar' : 'Activar').'']); ?>
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
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.cms.action-button','data' => ['icon' => 'fa-solid fa-trash','variant' => 'danger','wireClick' => 'delete('.e($contact['id']).')','title' => 'Eliminar']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('cms.action-button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['icon' => 'fa-solid fa-trash','variant' => 'danger','wireClick' => 'delete('.e($contact['id']).')','title' => 'Eliminar']); ?>
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
                    <i class="fa-solid fa-location-dot text-2xl text-blue-400"></i>
                </div>
                <p class="text-gray-500 font-medium mb-1">No hay información de contacto</p>
                <p class="text-gray-400 text-sm">Agrega dirección, teléfono, horario, WhatsApp, etc.</p>
            </div>
        <?php endif; ?>
    </div>

    
    <?php if($showForm): ?>
        <div class="fixed inset-0 bg-black/50 backdrop-blur-sm flex items-center justify-center z-50 overflow-y-auto py-8 px-4" x-data="{}" x-init="$watch('showForm', v => { if(v) document.body.style.overflow = 'hidden'; else document.body.style.overflow = ''; })">
            <div class="bg-white rounded-2xl shadow-2xl w-full max-w-xl max-h-[90vh] overflow-y-auto" style="animation: modalFadeIn 0.3s ease-out">
                <div class="sticky top-0 bg-white border-b border-gray-100 px-6 py-4 flex justify-between items-center rounded-t-2xl z-10">
                    <h4 class="text-lg font-bold text-gray-900"><?php echo e($editingId ? 'Editar' : 'Nuevo'); ?> Contacto</h4>
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
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1.5">Tipo *</label>
                            <select class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all bg-white"
                                    wire:model="type">
                                <?php $__currentLoopData = $types; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($key); ?>"><?php echo e($label); ?></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1.5">Etiqueta *</label>
                            <input type="text"
                                   class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all bg-white"
                                   wire:model="label"
                                   placeholder="Ej: Dirección, Teléfono, Horario">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1.5">Valor *</label>
                            <textarea class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all bg-white"
                                      rows="2"
                                      wire:model="value"
                                      placeholder="Valor del contacto (dirección, teléfono, iframe, etc.)"></textarea>
                        </div>

                        
                        
                        
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1.5">Ícono</label>

                            <?php
                                $typeIconLabels = [
                                    'address' => 'Dirección',
                                    'phone' => 'Teléfono',
                                    'schedule' => 'Horario',
                                    'whatsapp' => 'WhatsApp',
                                    'email' => 'Correo Electrónico',
                                    'map_iframe' => 'Mapa',
                                ];

                                $contactIconOptions = collect($typeIcons)
                                    ->mapWithKeys(fn ($iconClass, $key) => [
                                        $iconClass => $typeIconLabels[$key] ?? ucfirst(str_replace('_', ' ', $key)),
                                    ])
                                    ->toArray();

                                $currentIcon = $icon ?: ($typeIcons[$type] ?? null);
                            ?>

                            <div class="relative" x-data="{ open: false }">

                                <button type="button"
                                        @click="open = !open"
                                        @click.outside="open = false"
                                        class="w-full flex items-center gap-3 border border-gray-200 rounded-xl px-3.5 py-2.5 bg-white hover:border-blue-400 transition-all text-left">

                                    <div class="w-9 h-9 shrink-0 rounded-lg bg-gray-100 border border-gray-200 flex items-center justify-center text-gray-500 text-base">
                                        <i class="<?php echo e($currentIcon ?: 'fa-solid fa-image'); ?>"></i>
                                    </div>

                                    <span class="flex-1 text-sm text-gray-700 truncate">
                                        <?php echo e($currentIcon ? ($contactIconOptions[$currentIcon] ?? 'Ícono personalizado') : 'Selecciona un ícono...'); ?>

                                    </span>

                                    <i class="fa-solid fa-chevron-down text-xs text-gray-400 transition-transform" :class="open ? 'rotate-180' : ''"></i>

                                </button>

                                <div x-show="open"
                                     x-cloak
                                     x-transition:enter="transition ease-out duration-150"
                                     x-transition:enter-start="opacity-0 scale-95"
                                     x-transition:enter-end="opacity-100 scale-100"
                                     class="absolute z-30 mt-2 w-full bg-white border border-gray-200 rounded-xl shadow-xl p-3">

                                    <div class="grid grid-cols-5 sm:grid-cols-6 gap-2">

                                        <?php $__currentLoopData = $contactIconOptions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $iconClass => $iconLabel): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <button type="button"
                                                    wire:click="$set('icon', '<?php echo e($iconClass); ?>')"
                                                    @click="open = false"
                                                    title="<?php echo e($iconLabel); ?>"
                                                    class="aspect-square rounded-lg border flex items-center justify-center text-lg transition-all <?php echo e($currentIcon === $iconClass ? 'bg-blue-600 text-white border-blue-600' : 'bg-gray-50 text-gray-600 border-gray-200 hover:bg-blue-50 hover:border-blue-300 hover:text-blue-600'); ?>">
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

    
    <?php
        $typeIcons = [
            'address' => 'fa-solid fa-map-location-dot',
            'phone' => 'fa-solid fa-phone',
            'schedule' => 'fa-solid fa-clock',
            'whatsapp' => 'fa-brands fa-whatsapp',
            'email' => 'fa-solid fa-envelope',
            'map_iframe' => 'fa-solid fa-map',
        ];
    ?>
 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal86f7263b3d35f5f80a9e44bfc341b0ac)): ?>
<?php $attributes = $__attributesOriginal86f7263b3d35f5f80a9e44bfc341b0ac; ?>
<?php unset($__attributesOriginal86f7263b3d35f5f80a9e44bfc341b0ac); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal86f7263b3d35f5f80a9e44bfc341b0ac)): ?>
<?php $component = $__componentOriginal86f7263b3d35f5f80a9e44bfc341b0ac; ?>
<?php unset($__componentOriginal86f7263b3d35f5f80a9e44bfc341b0ac); ?>
<?php endif; ?><?php /**PATH C:\xampp\htdocs\arturo-motors\resources\views\livewire\cms\gestionar-contacto.blade.php ENDPATH**/ ?>