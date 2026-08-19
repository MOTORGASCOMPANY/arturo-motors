<?php if (isset($component)) { $__componentOriginal86f7263b3d35f5f80a9e44bfc341b0ac = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal86f7263b3d35f5f80a9e44bfc341b0ac = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.cms.layout','data' => ['title' => 'Por Qué Elegirnos','description' => 'Administra las tarjetas de ventajas competitivas que se muestran en el landing page','headerIcon' => '<i class="fa-solid fa-shield-halved text-blue-600"></i>']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('cms.layout'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'Por Qué Elegirnos','description' => 'Administra las tarjetas de ventajas competitivas que se muestran en el landing page','headerIcon' => '<i class="fa-solid fa-shield-halved text-blue-600"></i>']); ?>
    
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-8 pb-4 border-b border-blue-100">
        <div>
            <h2 class="text-xl font-semibold text-blue-950"><?php echo e(count($cards) ?? 0); ?> tarjetas de ventajas</h2>
            <p class="text-blue-700/70 text-sm">Destaca por qué los clientes deben elegir Arturo Motors</p>
        </div>
        <button wire:click="create"
                class="w-full sm:w-auto px-5 py-2.5 rounded-xl bg-blue-600 text-white font-semibold shadow-sm shadow-blue-200 hover:bg-blue-700 hover:shadow-md hover:shadow-blue-300 hover:-translate-y-0.5 transition-all duration-300 flex items-center justify-center gap-2">
            <i class="fa-solid fa-plus"></i> Nueva Tarjeta
        </button>
    </div>

    
    <!--[if BLOCK]><![endif]--><?php if($successMessage): ?>
        <div x-data="{ show: true }"
             x-show="show"
             x-transition:leave="transition ease-in duration-300"
             x-init="setTimeout(() => { show = false; window.Livewire.find('<?php echo e($_instance->getId()); ?>').call('clearSuccessMessage') }, 3000)"
             class="mb-6 flex items-center gap-3 p-4 rounded-xl bg-blue-50 border border-blue-200 text-blue-800 shadow-sm animate-slide-down">
            <i class="fa-solid fa-circle-check text-lg text-blue-600"></i>
            <span class="flex-1 font-medium"><?php echo e($successMessage); ?></span>
            <button @click="show = false; window.Livewire.find('<?php echo e($_instance->getId()); ?>').call('clearSuccessMessage')" class="text-blue-500 hover:text-blue-700 hover:bg-blue-100 p-1.5 rounded-lg transition-colors">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>
    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->

    
    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
        <!--[if BLOCK]><![endif]--><?php $__empty_1 = true; $__currentLoopData = $cards; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $card): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <?php if (isset($component)) { $__componentOriginal22c3416241bd13185beb9fb89a01cdd3 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal22c3416241bd13185beb9fb89a01cdd3 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.cms.card','data' => ['class' => 'flex flex-col h-full group bg-white border border-blue-100 hover:border-blue-300 hover:shadow-lg hover:shadow-blue-100/50 transition-all duration-300','style' => 'animation: cardEntry 0.4s ease-out '.e($loop->index * 0.06).'s both']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('cms.card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'flex flex-col h-full group bg-white border border-blue-100 hover:border-blue-300 hover:shadow-lg hover:shadow-blue-100/50 transition-all duration-300','style' => 'animation: cardEntry 0.4s ease-out '.e($loop->index * 0.06).'s both']); ?>
                <div class="p-6 flex-1">
                    <div class="flex items-start justify-between mb-4">
                        <div class="w-12 h-12 rounded-xl bg-blue-50 flex items-center justify-center text-blue-600 border border-blue-100 group-hover:bg-blue-600 group-hover:text-white transition-colors duration-300">
                            <i class="<?php echo e($card['icon'] ?? 'fa-solid fa-star'); ?> text-xl"></i>
                        </div>
                        <?php if (isset($component)) { $__componentOriginal726e09f7b3ddb3254115eba0aadc9cf1 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal726e09f7b3ddb3254115eba0aadc9cf1 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.cms.status-badge','data' => ['active' => $card['is_active']]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('cms.status-badge'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['active' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($card['is_active'])]); ?>
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
                    <h5 class="font-bold text-blue-950 text-lg mb-2 group-hover:text-blue-700 transition-colors"><?php echo e($card['title']); ?></h5>
                    <p class="text-slate-600 text-sm leading-relaxed"><?php echo e($card['description']); ?></p>
                </div>
                <div class="border-t border-blue-50 bg-blue-50/30 px-6 py-3.5 flex items-center justify-between">
                    <div class="flex gap-1">
                        
                        <?php if (isset($component)) { $__componentOriginald33697478d33dbb46e4d5cecf0cf51f9 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginald33697478d33dbb46e4d5cecf0cf51f9 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.cms.action-button','data' => ['icon' => 'fa-solid fa-pen','variant' => 'warning','wireClick' => 'edit('.e($card['id']).')','title' => 'Editar']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('cms.action-button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['icon' => 'fa-solid fa-pen','variant' => 'warning','wireClick' => 'edit('.e($card['id']).')','title' => 'Editar']); ?>
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
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.cms.action-button','data' => ['icon' => 'fa-solid fa-'.e($card['is_active'] ? 'eye-slash' : 'eye').'','variant' => ''.e($card['is_active'] ? 'ghost' : 'success').'','onclick' => 'confirmToggleCard('.e($card['id']).')','title' => ''.e($card['is_active'] ? 'Desactivar' : 'Activar').'']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('cms.action-button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['icon' => 'fa-solid fa-'.e($card['is_active'] ? 'eye-slash' : 'eye').'','variant' => ''.e($card['is_active'] ? 'ghost' : 'success').'','onclick' => 'confirmToggleCard('.e($card['id']).')','title' => ''.e($card['is_active'] ? 'Desactivar' : 'Activar').'']); ?>
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
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.cms.action-button','data' => ['icon' => 'fa-solid fa-trash','variant' => 'danger','onclick' => 'confirmDeleteCard('.e($card['id']).')','title' => 'Eliminar']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('cms.action-button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['icon' => 'fa-solid fa-trash','variant' => 'danger','onclick' => 'confirmDeleteCard('.e($card['id']).')','title' => 'Eliminar']); ?>
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
            <div class="col-span-full bg-white border border-dashed border-blue-200 rounded-2xl p-16 text-center shadow-sm" style="animation: emptyPulse 3s ease-in-out infinite">
                <div class="w-16 h-16 rounded-2xl bg-blue-50 flex items-center justify-center mx-auto mb-4 border border-blue-100">
                    <i class="fa-solid fa-shield-halved text-2xl text-blue-500"></i>
                </div>
                <p class="text-blue-900 font-semibold mb-1">No hay tarjetas de ventajas</p>
                <p class="text-blue-600/70 text-sm">Crea la primera tarjeta para destacar tus ventajas</p>
            </div>
        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
    </div>

    
    <!--[if BLOCK]><![endif]--><?php if($showForm): ?>
        <div class="fixed inset-0 bg-blue-950/20 backdrop-blur-sm flex items-center justify-center z-50 overflow-y-auto py-8 px-4" x-data="{}" x-init="$watch('showForm', v => { if(v) document.body.style.overflow = 'hidden'; else document.body.style.overflow = ''; })">
            <div class="bg-white rounded-2xl shadow-2xl shadow-blue-900/10 w-full max-w-xl max-h-[90vh] overflow-y-auto" style="animation: modalFadeIn 0.3s ease-out">
                <div class="sticky top-0 bg-white border-b border-blue-100 px-6 py-4 flex justify-between items-center rounded-t-2xl z-10">
                    <h4 class="text-lg font-bold text-blue-950"><?php echo e($editingId ? 'Editar' : 'Nueva'); ?> Tarjeta</h4>
                    <button wire:click="resetForm"
                            class="w-9 h-9 flex items-center justify-center rounded-lg text-blue-400 hover:text-blue-700 hover:bg-blue-50 transition-all"
                            aria-label="Cerrar">
                        <i class="fa-solid fa-xmark text-lg"></i>
                    </button>
                </div>
                <div class="px-6 py-5">
                    <!--[if BLOCK]><![endif]--><?php if($errors->any()): ?>
                        <div class="mb-4 p-4 rounded-xl bg-red-50 border border-red-200 text-red-700 text-sm animate-slide-down">
                            <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <p class="flex items-start gap-2"><i class="fa-solid fa-circle-exclamation mt-0.5 text-xs"></i><?php echo e($error); ?></p>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
                        </div>
                    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                    <div class="space-y-5">
                        <div>
                            <label class="block text-sm font-semibold text-blue-900 mb-1.5">Título *</label>
                            <input type="text"
                                   class="w-full border border-blue-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-blue-600 focus:border-blue-600 transition-all bg-white text-blue-950 placeholder:text-blue-300"
                                   wire:model="title"
                                   placeholder="Ej: Garantía 100%">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-blue-900 mb-1.5">Descripción</label>
                            <textarea class="w-full border border-blue-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-blue-600 focus:border-blue-600 transition-all bg-white text-blue-950 placeholder:text-blue-300"
                                      rows="3"
                                      wire:model="description"
                                      placeholder="Descripción de la ventaja..."></textarea>
                        </div>

                        
                        
                        
                        <div>
                            <label class="block text-sm font-semibold text-blue-900 mb-1.5">Ícono (opcional)</label>

                            <?php
                                $cardIconOptions = [
                                    'fa-solid fa-shield-halved' => 'Garantía / Seguridad',
                                    'fa-solid fa-medal' => 'Calidad Certificada',
                                    'fa-solid fa-award' => 'Premiado',
                                    'fa-solid fa-star' => 'Excelencia',
                                    'fa-solid fa-thumbs-up' => 'Satisfacción Garantizada',
                                    'fa-solid fa-heart' => 'Compromiso',
                                    'fa-solid fa-hand-holding-dollar' => 'Mejor Precio',
                                    'fa-solid fa-dollar-sign' => 'Precios Justos',
                                    'fa-solid fa-truck' => 'Entrega / Transporte',
                                    'fa-solid fa-clock' => 'Rapidez',
                                    'fa-solid fa-headset' => 'Atención al Cliente',
                                    'fa-solid fa-certificate' => 'Certificación',
                                    'fa-solid fa-users' => 'Equipo Experto',
                                    'fa-solid fa-trophy' => 'Trayectoria',
                                    'fa-solid fa-gem' => 'Calidad Premium',
                                    'fa-solid fa-lock' => 'Confianza y Seguridad',
                                    'fa-solid fa-tools' => 'Servicio Técnico',
                                    'fa-solid fa-car' => 'Especialistas Automotrices',
                                ];
                            ?>

                            <div class="relative" x-data="{ open: false }">

                                <button type="button"
                                        @click="open = !open"
                                        @click.outside="open = false"
                                        class="w-full flex items-center gap-3 border border-blue-200 rounded-xl px-3.5 py-2.5 bg-white hover:border-blue-400 transition-all text-left">

                                    <div class="w-9 h-9 shrink-0 rounded-lg bg-blue-50 border border-blue-100 flex items-center justify-center text-blue-600 text-base">
                                        <i class="<?php echo e($icon ?: 'fa-solid fa-image'); ?>"></i>
                                    </div>

                                    <span class="flex-1 text-sm text-blue-950 truncate">
                                        <?php echo e($icon ? ($cardIconOptions[$icon] ?? 'Ícono personalizado') : 'Selecciona un ícono...'); ?>

                                    </span>

                                    <i class="fa-solid fa-chevron-down text-xs text-blue-400 transition-transform" :class="open ? 'rotate-180' : ''"></i>

                                </button>

                                <div x-show="open"
                                     x-cloak
                                     x-transition:enter="transition ease-out duration-150"
                                     x-transition:enter-start="opacity-0 scale-95"
                                     x-transition:enter-end="opacity-100 scale-100"
                                     class="absolute z-30 mt-2 w-full bg-white border border-blue-100 rounded-xl shadow-xl p-3">

                                    <div class="grid grid-cols-5 sm:grid-cols-6 gap-2 max-h-56 overflow-y-auto pr-1">

                                        <button type="button"
                                                wire:click="$set('icon', '')"
                                                @click="open = false"
                                                title="Sin ícono"
                                                class="aspect-square rounded-lg border flex items-center justify-center text-sm transition-all <?php echo e(!$icon ? 'bg-blue-600 text-white border-blue-600' : 'bg-blue-50/50 text-blue-300 border-blue-100 hover:bg-blue-100'); ?>">
                                            <i class="fa-solid fa-ban"></i>
                                        </button>

                                        <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $cardIconOptions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $iconClass => $iconLabel): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <button type="button"
                                                    wire:click="$set('icon', '<?php echo e($iconClass); ?>')"
                                                    @click="open = false"
                                                    title="<?php echo e($iconLabel); ?>"
                                                    class="aspect-square rounded-lg border flex items-center justify-center text-lg transition-all <?php echo e($icon === $iconClass ? 'bg-blue-600 text-white border-blue-600' : 'bg-blue-50/50 text-blue-600 border-blue-100 hover:bg-blue-100 hover:border-blue-300'); ?>">
                                                <i class="<?php echo e($iconClass); ?>"></i>
                                            </button>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->

                                    </div>

                                </div>

                            </div>

                        </div>

                        <div class="flex items-center gap-2.5">
                            <input type="checkbox"
                                   class="w-4 h-4 text-blue-600 rounded-md focus:ring-blue-600 border-blue-300 bg-white"
                                   wire:model="active">
                            <span class="text-sm font-semibold text-blue-900">Activo</span>
                        </div>
                    </div>
                </div>
                <div class="sticky bottom-0 bg-white border-t border-blue-100 px-6 py-4 flex justify-end gap-3 rounded-b-2xl">
                    <button type="button"
                            class="px-5 py-2.5 rounded-xl bg-white border border-blue-200 text-blue-700 hover:bg-blue-50 hover:border-blue-300 font-semibold transition-all"
                            wire:click="resetForm">
                        Cancelar
                    </button>
                    <button type="button"
                            class="px-5 py-2.5 rounded-xl bg-blue-600 text-white hover:bg-blue-700 hover:shadow-md hover:shadow-blue-200 font-semibold transition-all"
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
    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->

    
    <style>
        @keyframes modalFadeIn { from { opacity: 0; transform: scale(0.95) translateY(10px); } to { opacity: 1; transform: scale(1) translateY(0); } }
        @keyframes slideDown { from { opacity: 0; transform: translateY(-10px); } to { opacity: 1; transform: translateY(0); } }
        @keyframes cardEntry { from { opacity: 0; transform: translateY(20px) scale(0.97); } to { opacity: 1; transform: translateY(0) scale(1); } }
        @keyframes emptyPulse { 0%, 100% { opacity: 1; } 50% { opacity: 0.7; } }
        .animate-slide-down { animation: slideDown 0.3s ease-out; }
        [x-cloak] { display: none !important; }
    </style>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        function confirmDeleteCard(id) {
            Swal.fire({
                title: '¿Eliminar tarjeta?',
                text: 'Esta acción no se puede deshacer.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc2626',
                cancelButtonColor: '#64748b',
                confirmButtonText: 'Sí, eliminar',
                cancelButtonText: 'Cancelar',
                customClass: { popup: 'rounded-2xl shadow-xl', confirmButton: 'rounded-xl px-5 py-2.5 font-semibold text-sm', cancelButton: 'rounded-xl px-5 py-2.5 font-semibold text-sm' }
            }).then((result) => {
                if (result.isConfirmed) { window.Livewire.find('<?php echo e($_instance->getId()); ?>').call('delete', id) }
            });
        }
        function confirmToggleCard(id) {
            Swal.fire({
                title: '¿Cambiar estado?',
                text: 'Se activará o desactivará esta tarjeta.',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#2563eb',
                cancelButtonColor: '#64748b',
                confirmButtonText: 'Sí, cambiar',
                cancelButtonText: 'Cancelar',
                customClass: { popup: 'rounded-2xl shadow-xl', confirmButton: 'rounded-xl px-5 py-2.5 font-semibold text-sm', cancelButton: 'rounded-xl px-5 py-2.5 font-semibold text-sm' }
            }).then((result) => {
                if (result.isConfirmed) { window.Livewire.find('<?php echo e($_instance->getId()); ?>').call('toggleActive', id) }
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
<?php endif; ?><?php /**PATH C:\xampp\htdocs\arturo-motors\resources\views/livewire/cms/gestionar-por-que.blade.php ENDPATH**/ ?>