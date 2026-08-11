<div>
    <div class="flex justify-between items-center mb-8 pb-4 border-b border-gray-200 m-4">
        <h4 class="text-2xl font-bold text-gray-900 flex items-center gap-3">
            <i class="fa-solid fa-location-dot text-blue-600"></i>Información de Contacto
        </h4>
        <button class="bg-blue-600 text-white px-5 py-2.5 rounded-xl hover:bg-blue-700 font-semibold transition-all shadow-sm hover:shadow-md" wire:click="create">
            <i class="fa-solid fa-plus mr-1.5"></i>Nuevo
        </button>
    </div>

    <!--[if BLOCK]><![endif]--><?php if(session()->has('success')): ?>
        <div class="bg-green-50 border border-green-200 text-green-700 px-5 py-3.5 rounded-xl mb-6 flex justify-between items-center shadow-sm">
            <span class="flex items-center gap-2 font-medium"><i class="fa-solid fa-circle-check"></i><?php echo e(session('success')); ?></span>
            <button onclick="this.parentElement.remove()" class="text-green-500 hover:text-green-700"><i class="fa-solid fa-xmark"></i></button>
        </div>
    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden m-4">
        <table class="w-full">
            <thead class="bg-gray-50/80 border-b border-gray-100">
                <tr>
                    <th class="px-6 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Tipo</th>
                    <th class="px-6 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Etiqueta</th>
                    <th class="px-6 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Valor</th>
                    <th class="px-6 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Estado</th>
                    <th class="px-6 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Acciones</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                <!--[if BLOCK]><![endif]--><?php $__empty_1 = true; $__currentLoopData = $contacts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $contact): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <tr class="hover:bg-gray-50/50 transition-colors">
                        <td class="px-6 py-4">
                            <span class="inline-flex items-center gap-1.5 bg-blue-50 text-blue-600 text-xs font-semibold px-3 py-1.5 rounded-full border border-blue-100">
                                <i class="<?php echo e($contact['icon'] ?? 'fa-solid fa-circle'); ?> text-[10px]"></i>
                                <?php echo e($types[$contact['type']] ?? $contact['type']); ?>

                            </span>
                        </td>
                        <td class="px-6 py-4 text-sm font-medium text-gray-900"><?php echo e($contact['label']); ?></td>
                        <td class="px-6 py-4 text-sm text-gray-500 max-w-xs truncate"><?php echo e(Str::limit($contact['value'], 60)); ?></td>
                        <td class="px-6 py-4">
                            <span class="text-xs font-semibold px-3 py-1.5 rounded-full <?php echo e($contact['is_active'] ? 'bg-green-50 text-green-600 border border-green-200' : 'bg-gray-50 text-gray-400 border border-gray-200'); ?>">
                                <?php echo e($contact['is_active'] ? 'Activo' : 'Inactivo'); ?>

                            </span>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex gap-2">
                                <button class="w-9 h-9 flex items-center justify-center rounded-lg bg-amber-50 border border-amber-200 text-amber-600 hover:bg-amber-100 transition-all" wire:click="edit(<?php echo e($contact['id']); ?>)">
                                    <i class="fa-solid fa-pen text-xs"></i>
                                </button>
                                <button class="w-9 h-9 flex items-center justify-center rounded-lg border transition-all <?php echo e($contact['is_active'] ? 'bg-gray-50 border-gray-200 text-gray-400 hover:bg-gray-100' : 'bg-green-50 border-green-200 text-green-600 hover:bg-green-100'); ?>" wire:click="toggleActive(<?php echo e($contact['id']); ?>)">
                                    <i class="fa-solid fa-<?php echo e($contact['is_active'] ? 'eye-slash' : 'eye'); ?> text-xs"></i>
                                </button>
                                <button class="w-9 h-9 flex items-center justify-center rounded-lg bg-red-50 border border-red-200 text-red-500 hover:bg-red-100 transition-all" wire:click="delete(<?php echo e($contact['id']); ?>)" wire:confirm="¿Eliminar?">
                                    <i class="fa-solid fa-trash text-xs"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr>
                        <td colspan="5" class="px-6 py-16 text-center">
                            <div class="w-16 h-16 rounded-2xl bg-blue-50 flex items-center justify-center mx-auto mb-4 border border-blue-100">
                                <i class="fa-solid fa-location-dot text-2xl text-blue-400"></i>
                            </div>
                            <p class="text-gray-500 font-medium">No hay información de contacto</p>
                        </td>
                    </tr>
                <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
            </tbody>
        </table>
    </div>

    <!--[if BLOCK]><![endif]--><?php if($showForm): ?>
        <div class="fixed inset-0 bg-black/50 backdrop-blur-sm flex items-start justify-center z-50 overflow-y-auto py-8 px-4">
            <div class="bg-white rounded-2xl shadow-2xl w-full max-w-2xl my-auto">
                <div class="sticky top-0 bg-white border-b border-gray-100 px-6 py-4 flex justify-between items-center rounded-t-2xl z-10">
                    <h5 class="text-lg font-bold text-gray-900"><?php echo e($editingId ? 'Editar' : 'Nuevo'); ?> Contacto</h5>
                    <button wire:click="resetForm" class="w-9 h-9 flex items-center justify-center rounded-lg text-gray-400 hover:text-gray-600 hover:bg-gray-100 transition-all">
                        <i class="fa-solid fa-xmark text-lg"></i>
                    </button>
                </div>
                <div class="px-6 py-5">
                    <!--[if BLOCK]><![endif]--><?php if($errors->any()): ?>
                        <div class="bg-red-50 border border-red-200 text-red-600 px-4 py-3 rounded-xl text-sm mb-4">
                            <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <p><?php echo e($error); ?></p>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
                        </div>
                    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1.5">Tipo *</label>
                            <select class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all" wire:model.live="type">
                                <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $types; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($key); ?>"><?php echo e($label); ?></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1.5">Etiqueta *</label>
                            <input type="text" class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all" wire:model="label" placeholder="Dirección del Taller">
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-sm font-semibold text-gray-700 mb-1.5">Valor *</label>
                            <!--[if BLOCK]><![endif]--><?php if($type === 'map_iframe'): ?>
                                <textarea class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all font-mono text-xs" rows="4" wire:model="value" placeholder="<iframe src='...'></iframe>"></textarea>
                            <?php elseif($type === 'whatsapp'): ?>
                                <div class="flex items-center gap-2">
                                    <span class="text-gray-400 text-sm">wa.me/</span>
                                    <input type="text" class="flex-1 border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all" wire:model="value" placeholder="51943694464">
                                </div>
                                <p class="text-xs text-gray-400 mt-1.5">Solo el número sin espacios ni guiones (ej: 51943694464)</p>
                            <?php elseif($type === 'email'): ?>
                                <input type="email" class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all" wire:model="value" placeholder="correo@ejemplo.com">
                            <?php else: ?>
                                <input type="text" class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all" wire:model="value" placeholder="Prolongación Av. Perú 5176, Callao">
                            <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
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
                        <div class="flex items-end">
                            <label class="flex items-center gap-2.5 cursor-pointer">
                                <input type="checkbox" class="w-4 h-4 text-blue-600 rounded-lg focus:ring-blue-500 border-gray-300" wire:model="active">
                                <span class="text-sm font-medium text-gray-700">Activo</span>
                            </label>
                        </div>
                    </div>
                </div>
                <div class="sticky bottom-0 bg-white border-t border-gray-100 px-6 py-4 flex justify-end gap-3 rounded-b-2xl">
                    <button type="button" class="px-5 py-2.5 rounded-xl bg-gray-100 text-gray-600 hover:bg-gray-200 font-semibold transition-all" wire:click="resetForm">Cancelar</button>
                    <button type="button" class="px-5 py-2.5 rounded-xl bg-blue-600 text-white hover:bg-blue-700 font-semibold transition-all shadow-sm" wire:click="save">Guardar</button>
                </div>
            </div>
        </div>
    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
</div>
<?php /**PATH C:\xampp\htdocs\arturo-motors\resources\views/livewire/cms/gestionar-contacto.blade.php ENDPATH**/ ?>