<div class="bg-gray-50 min-h-screen">
    <style>
        @keyframes modalFadeIn { from { opacity: 0; transform: scale(0.95) translateY(10px); } to { opacity: 1; transform: scale(1) translateY(0); } }
        @keyframes errorSlideIn { 0% { opacity: 0; transform: translateY(-20px); } 60% { transform: translateX(6px); } 80% { transform: translateX(-4px); } 100% { opacity: 1; transform: translateY(0) translateX(0); } }
        @keyframes successFlash { 0% { opacity: 0; transform: scale(0.9); } 50% { opacity: 1; transform: scale(1.02); } 100% { opacity: 1; transform: scale(1); } }
        @keyframes spin { to { transform: rotate(360deg); } }
        @keyframes emptyPulse { 0%, 100% { opacity: 1; } 50% { opacity: 0.6; } }
        @keyframes cardEntry { from { opacity: 0; transform: translateY(20px) scale(0.97); } to { opacity: 1; transform: translateY(0) scale(1); } }
    </style>

    <div class="flex justify-between items-center mb-8 pb-4 border-b border-gray-200 m-4">
        <h4 class="text-2xl font-bold text-gray-900 flex items-center gap-3">
            <i class="fa-solid fa-wrench text-blue-600"></i>Servicios del Landing
        </h4>
        <button class="bg-blue-600 text-white px-5 py-2.5 rounded-xl hover:bg-blue-700 font-semibold transition-all shadow-sm hover:shadow-md" wire:click="create">
            <i class="fa-solid fa-plus mr-1.5"></i>Nuevo Servicio
        </button>
    </div>

    
    <!--[if BLOCK]><![endif]--><?php if($successMessage): ?>
        <div x-data="{ show: true }" x-init="setTimeout(() => { show = false; $wire.clearSuccessMessage() }, 3000)"
             x-show="show" x-transition:leave="transition ease-in duration-300" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
             class="bg-blue-50 border border-blue-200 text-blue-700 px-5 py-3.5 rounded-xl mb-6 mx-4 flex justify-between items-center shadow-sm" style="animation: successFlash 0.4s ease-out">
            <span class="flex items-center gap-2 font-medium"><i class="fa-solid fa-circle-check"></i><?php echo e($successMessage); ?></span>
            <button @click="show = false; $wire.clearSuccessMessage()" class="text-blue-500 hover:text-blue-700"><i class="fa-solid fa-xmark"></i></button>
        </div>
    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->

    <!--[if BLOCK]><![endif]--><?php if(session()->has('success') && !$successMessage): ?>
        <div class="bg-blue-50 border border-blue-200 text-blue-700 px-5 py-3.5 rounded-xl mb-6 mx-4 flex justify-between items-center shadow-sm" style="animation: successFlash 0.4s ease-out">
            <span class="flex items-center gap-2 font-medium"><i class="fa-solid fa-circle-check"></i><?php echo e(session('success')); ?></span>
            <button onclick="this.parentElement.remove()" class="text-blue-500 hover:text-blue-700"><i class="fa-solid fa-xmark"></i></button>
        </div>
    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->

    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6 m-4">
        <!--[if BLOCK]><![endif]--><?php $__empty_1 = true; $__currentLoopData = $services; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $service): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 hover:shadow-lg hover:-translate-y-0.5 transition-all duration-200 overflow-hidden flex flex-col" style="animation: cardEntry 0.4s ease-out <?php echo e($loop->index * 0.06); ?>s both">
                <div class="p-6 flex-1">
                    <div class="flex items-start justify-between mb-4">
                        <div class="w-12 h-12 rounded-xl bg-blue-50 flex items-center justify-center text-blue-600 text-xl border border-blue-100">
                            <i class="<?php echo e($service['icon'] ?? 'fa-solid fa-cog'); ?>"></i>
                        </div>
                        <span class="text-xs font-semibold px-3 py-1 rounded-full <?php echo e($service['is_active'] ? 'bg-blue-50 text-blue-600 border border-blue-200' : 'bg-gray-50 text-gray-400 border border-gray-200'); ?>">
                            <?php echo e($service['is_active'] ? 'Activo' : 'Inactivo'); ?>

                        </span>
                    </div>
                    <h6 class="font-bold text-gray-900 text-lg mb-2"><?php echo e($service['title']); ?></h6>
                    <p class="text-gray-500 text-sm leading-relaxed mb-3"><?php echo e(Str::limit($service['description'], 100)); ?></p>
                    <!--[if BLOCK]><![endif]--><?php if($service['features']): ?>
                        <div class="flex flex-wrap gap-1.5">
                            <!--[if BLOCK]><![endif]--><?php $__currentLoopData = array_slice($service['features'], 0, 3); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $f): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <span class="bg-gray-50 text-gray-600 text-xs px-2.5 py-1 rounded-lg border border-gray-100"><?php echo e($f); ?></span>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
                            <!--[if BLOCK]><![endif]--><?php if(count($service['features']) > 3): ?>
                                <span class="text-gray-400 text-xs">+<?php echo e(count($service['features']) - 3); ?></span>
                            <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                        </div>
                    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                </div>
                <div class="border-t border-gray-100 bg-gray-50/80 px-6 py-3.5 flex items-center justify-between">
                    <div class="flex gap-2">
                        <button class="w-9 h-9 flex items-center justify-center rounded-lg bg-white border border-gray-200 text-gray-400 hover:text-blue-600 hover:border-blue-300 transition-all" wire:click="moveUp(<?php echo e($service['id']); ?>)" title="Subir">
                            <i class="fa-solid fa-arrow-up text-xs"></i>
                        </button>
                        <button class="w-9 h-9 flex items-center justify-center rounded-lg bg-white border border-gray-200 text-gray-400 hover:text-blue-600 hover:border-blue-300 transition-all" wire:click="moveDown(<?php echo e($service['id']); ?>)" title="Bajar">
                            <i class="fa-solid fa-arrow-down text-xs"></i>
                        </button>
                    </div>
                    <div class="flex gap-2">
                        <button class="w-9 h-9 flex items-center justify-center rounded-lg bg-blue-50 border border-blue-200 text-blue-600 hover:bg-blue-100 transition-all" wire:click="edit(<?php echo e($service['id']); ?>)" title="Editar">
                            <i class="fa-solid fa-pen text-xs"></i>
                        </button>
                        <button class="w-9 h-9 flex items-center justify-center rounded-lg border transition-all <?php echo e($service['is_active'] ? 'bg-gray-50 border-gray-200 text-gray-400 hover:bg-gray-100' : 'bg-blue-50 border-blue-200 text-blue-600 hover:bg-blue-100'); ?>" wire:click="toggleActive(<?php echo e($service['id']); ?>)" title="<?php echo e($service['is_active'] ? 'Desactivar' : 'Activar'); ?>">
                            <i class="fa-solid fa-<?php echo e($service['is_active'] ? 'eye-slash' : 'eye'); ?> text-xs"></i>
                        </button>
                        <button class="w-9 h-9 flex items-center justify-center rounded-lg bg-red-50 border border-red-200 text-red-500 hover:bg-red-100 transition-all"
                                wire:click="delete(<?php echo e($service['id']); ?>)"
                                wire:confirm="¿Seguro que querés eliminar este servicio? Esta acción no se puede deshacer."
                                title="Eliminar">
                            <i class="fa-solid fa-trash text-xs"></i>
                        </button>
                    </div>
                </div>
            </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <div class="col-span-full bg-white rounded-2xl shadow-sm border border-gray-100 p-16 text-center" style="animation: emptyPulse 3s ease-in-out infinite">
                <div class="w-16 h-16 rounded-2xl bg-blue-50 flex items-center justify-center mx-auto mb-4 border border-blue-100">
                    <i class="fa-solid fa-wrench text-2xl text-blue-400"></i>
                </div>
                <p class="text-gray-500 font-medium mb-1">No hay servicios creados</p>
                <p class="text-gray-400 text-sm">Creá el primer servicio para mostrar en el landing</p>
            </div>
        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
    </div>

    <!--[if BLOCK]><![endif]--><?php if($showForm): ?>
        <div class="fixed inset-0 bg-black/50 backdrop-blur-sm flex items-start justify-center z-50 overflow-y-auto py-8 px-4">
            <div class="bg-white rounded-2xl shadow-2xl w-full max-w-2xl my-auto" style="animation: modalFadeIn 0.3s ease-out">
                <div class="sticky top-0 bg-white border-b border-gray-100 px-6 py-4 flex justify-between items-center rounded-t-2xl z-10">
                    <h5 class="text-lg font-bold text-gray-900"><?php echo e($editingId ? 'Editar' : 'Nuevo'); ?> Servicio</h5>
                    <button wire:click="resetForm" class="w-9 h-9 flex items-center justify-center rounded-lg text-gray-400 hover:text-gray-600 hover:bg-gray-100 transition-all">
                        <i class="fa-solid fa-xmark text-lg"></i>
                    </button>
                </div>
                <div class="px-6 py-5">
                    <!--[if BLOCK]><![endif]--><?php if($errors->any()): ?>
                        <div class="bg-red-50 border border-red-200 text-red-600 px-4 py-3 rounded-xl text-sm mb-4" style="animation: errorSlideIn 0.4s ease-out">
                            <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <p class="flex items-start gap-2"><i class="fa-solid fa-circle-exclamation mt-0.5 text-xs"></i><?php echo e($error); ?></p>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
                        </div>
                    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <div class="md:col-span-2">
                            <label class="block text-sm font-semibold text-gray-700 mb-1.5">Título *</label>
                            <input type="text" class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all" wire:model.live="title" placeholder="Ej: Conversión a GNV / GLP">
                            <p class="text-xs text-gray-400 mt-1.5">El ícono se asigna automáticamente según el título</p>
                        </div>
                        <div>
                            <?php if (isset($component)) { $__componentOriginal28bc5fd3481414e5f9fd53a00cd35171 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal28bc5fd3481414e5f9fd53a00cd35171 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.icon-picker','data' => ['model' => 'icon','label' => 'Ícono']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('icon-picker'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['model' => 'icon','label' => 'Ícono']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal28bc5fd3481414e5f9fd53a00cd35171)): ?>
<?php $attributes = $__attributesOriginal28bc5fd3481414e5f9fd53a00cd35171; ?>
<?php unset($__attributesOriginal28bc5fd3481414e5f9fd53a00cd35171); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal28bc5fd3481414e5f9fd53a00cd35171)): ?>
<?php $component = $__componentOriginal28bc5fd3481414e5f9fd53a00cd35171; ?>
<?php unset($__componentOriginal28bc5fd3481414e5f9fd53a00cd35171); ?>
<?php endif; ?>
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1.5">Texto del Botón CTA</label>
                            <input type="text" class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all" wire:model="ctaText" placeholder="Cotizar Conversión">
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-sm font-semibold text-gray-700 mb-1.5">Descripción</label>
                            <textarea class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all" rows="3" wire:model="description"></textarea>
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-sm font-semibold text-gray-700 mb-1.5">Features (una por línea)</label>
                            <textarea class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all" rows="3" wire:model="features" placeholder="Equipos italianos&#10;Garantía 1 año&#10;Certificación inicial"></textarea>
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-sm font-semibold text-gray-700 mb-1.5">Link del Botón (WhatsApp)</label>
                            <input type="text" class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all" wire:model="ctaLink" placeholder="https://wa.me/51943694464?text=...">
                        </div>
                        <div class="md:col-span-2">
                            <label class="flex items-center gap-2.5 cursor-pointer">
                                <input type="checkbox" class="w-4 h-4 text-blue-600 rounded-lg focus:ring-blue-500 border-gray-300" wire:model="active">
                                <span class="text-sm font-medium text-gray-700">Activo</span>
                            </label>
                        </div>
                    </div>
                </div>
                <div class="sticky bottom-0 bg-white border-t border-gray-100 px-6 py-4 flex justify-end gap-3 rounded-b-2xl">
                    <button type="button" class="px-5 py-2.5 rounded-xl bg-gray-100 text-gray-600 hover:bg-gray-200 font-semibold transition-all" wire:click="resetForm">Cancelar</button>
                    <button type="button" class="px-5 py-2.5 rounded-xl bg-blue-600 text-white hover:bg-blue-700 font-semibold transition-all shadow-sm relative overflow-hidden"
                            wire:click="save"
                            wire:loading.attr="disabled"
                            wire:target="save">
                        <span wire:loading.remove wire:target="save"><i class="fa-solid fa-check mr-1"></i>Guardar</span>
                        <span wire:loading wire:target="save" class="flex items-center gap-2">
                            <svg class="animate-spin h-4 w-4" style="animation: spin 1s linear infinite" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" fill="none"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>
                            Guardando...
                        </span>
                    </button>
                </div>
            </div>
        </div>
    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
</div><?php /**PATH C:\xampp\htdocs\arturo-motors\resources\views/livewire/cms/gestionar-servicios.blade.php ENDPATH**/ ?>