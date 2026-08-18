<div>
    <div class="container mx-auto py-4">
        <?php if (isset($component)) { $__componentOriginal30adf5429f2b1c99561b1d92d89fa7d5 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal30adf5429f2b1c99561b1d92d89fa7d5 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.custom-table','data' => []] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('custom-table'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
             <?php $__env->slot('titulo', null, []); ?> 
                <h2 class="text-teal-600 font-bold text-3xl flex items-center gap-2">
                    <i class="fa-solid fa-user-tag fa-xl text-teal-500"></i>
                    Roles
                </h2>
             <?php $__env->endSlot(); ?>

             <?php $__env->slot('btnAgregar', null, []); ?> 
                <?php
$__split = function ($name, $params = []) {
    return [$name, $params];
};
[$__name, $__params] = $__split('create-rol');

$__html = app('livewire')->mount($__name, $__params, 'lw-1172025389-0', $__slots ?? [], get_defined_vars());

echo $__html;

unset($__html);
unset($__name);
unset($__params);
unset($__split);
if (isset($__slots)) unset($__slots);
?>
             <?php $__env->endSlot(); ?>

             <?php $__env->slot('contenido', null, []); ?> 
                <!--[if BLOCK]><![endif]--><?php if(count($roles)): ?>
                    <div class="overflow-x-auto bg-white rounded-lg shadow">
                        <table class="w-full whitespace-nowrap rounded-lg overflow-hidden shadow">
                            <thead class="bg-slate-600 border-b font-bold text-white">
                                <tr>
                                    <th scope="col"
                                        class="text-sm font-medium font-semibold text-white px-6 py-4 text-left"
                                        wire:click="order('id')">
                                        Id
                                        <!--[if BLOCK]><![endif]--><?php if($sort == 'id'): ?>
                                            <!--[if BLOCK]><![endif]--><?php if($direction == 'asc'): ?>
                                                <i class="fas fa-sort-numeric-up-alt float-right mt-0.5"></i>
                                            <?php else: ?>
                                                <i class="fas fa-sort-numeric-down-alt float-right mt-0.5"></i>
                                            <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                        <?php else: ?>
                                            <i class="fas fa-sort float-right mt-0.5"></i>
                                        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                    </th>
                                    <th scope="col"
                                        class="text-sm font-medium font-semibold text-white px-6 py-4 text-left"
                                        wire:click="order('name')">
                                        Nombre
                                        <!--[if BLOCK]><![endif]--><?php if($sort == 'name'): ?>
                                            <!--[if BLOCK]><![endif]--><?php if($direction == 'asc'): ?>
                                                <i class="fas fa-sort-alpha-up-alt float-right mt-0.5"></i>
                                            <?php else: ?>
                                                <i class="fas fa-sort-alpha-down-alt float-right mt-0.5"></i>
                                            <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                        <?php else: ?>
                                            <i class="fas fa-sort float-right mt-0.5"></i>
                                        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                    </th>
                                    <th scope="col"
                                        class="text-sm font-medium font-semibold text-white px-6 py-4 text-left"
                                        wire:click="order('created_at')">
                                        Fecha de creación
                                        <!--[if BLOCK]><![endif]--><?php if($sort == 'created_at'): ?>
                                            <!--[if BLOCK]><![endif]--><?php if($direction == 'asc'): ?>
                                                <i class="fas fa-sort-numeric-up-alt float-right mt-0.5"></i>
                                            <?php else: ?>
                                                <i class="fas fa-sort-numeric-down-alt float-right mt-0.5"></i>
                                            <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                        <?php else: ?>
                                            <i class="fas fa-sort float-right mt-0.5"></i>
                                        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                    </th>
                                    <th scope="col"
                                        class="text-sm font-medium font-semibold text-white px-6 py-4 text-left">
                                        Acciones
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-300">
                                <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $roles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <tr tabindex="0"
                                        class="focus:outline-none h-16 border border-slate-300 hover:bg-gray-200">
                                        <td class="pl-5">
                                            <div class="flex items-center">
                                                <p class="text-slate-900 p-1 bg-slate-200 rounded-md">
                                                    <?php echo e($item->id); ?>

                                                </p>
                                            </div>
                                        </td>
                                        <td class="pl-2">
                                            <div class="flex items-center">
                                                <p class="text-sm font-medium leading-none text-gray-600 mr-2">
                                                    <?php echo e($item->name); ?>

                                                </p>
                                            </div>
                                        </td>
                                        <td class="pl-2">
                                            <div class="flex items-center">
                                                <p class="text-sm leading-none text-gray-600 ml-2">
                                                    <?php echo e(optional($item->created_at)->format('d-m-Y H:i:s')); ?>

                                                </p>
                                            </div>
                                        </td>
                                        <td class="pl-2">
                                            <div class="flex items-center justify-center">
                                                <p class="text-gray-900 whitespace-no-wrap">
                                                    <button wire:click="editaRol(<?php echo e($item->id); ?>)"
                                                        class="px-3 py-2 bg-slate-400 hover:bg-slate-600 text-white rounded-lg shadow transition">
                                                        <i class="fas fa-pen text-white"></i>
                                                    </button>
                                                </p>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
                            </tbody>
                        </table>
                    </div>

                    <!--[if BLOCK]><![endif]--><?php if($roles->hasPages()): ?>
                        <div>
                            <div class="-mx-4 sm:-mx-8 px-4 sm:px-8 py-2 overflow-x-auto">
                                <div class="inline-block min-w-full shadow rounded-lg overflow-hidden">
                                    <div class="px-5 py-5 bg-white border-t">
                                        <?php echo e($roles->links()); ?>

                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                <?php else: ?>
                    <div class="px-6 py-4 text-center font-bold bg-slate-200 rounded-md">
                        No se encontro ningun registro.
                    </div>
                <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
             <?php $__env->endSlot(); ?>

         <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal30adf5429f2b1c99561b1d92d89fa7d5)): ?>
<?php $attributes = $__attributesOriginal30adf5429f2b1c99561b1d92d89fa7d5; ?>
<?php unset($__attributesOriginal30adf5429f2b1c99561b1d92d89fa7d5); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal30adf5429f2b1c99561b1d92d89fa7d5)): ?>
<?php $component = $__componentOriginal30adf5429f2b1c99561b1d92d89fa7d5; ?>
<?php unset($__componentOriginal30adf5429f2b1c99561b1d92d89fa7d5); ?>
<?php endif; ?>
    </div>

    
    <?php if (isset($component)) { $__componentOriginal49bd1c1dd878e22e0fb84faabf295a3f = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal49bd1c1dd878e22e0fb84faabf295a3f = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.dialog-modal','data' => ['wire:model' => 'editando','wire:loading.attr' => 'disabled']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('dialog-modal'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['wire:model' => 'editando','wire:loading.attr' => 'disabled']); ?>
         <?php $__env->slot('title', null, ['class' => 'font-bold']); ?> 
            <h1 class="text-xl font-bold"><i class="fa-solid fa-pen text-white"></i> &nbsp;Editar Rol</h1>
         <?php $__env->endSlot(); ?>

         <?php $__env->slot('content', null, []); ?> 
            <div class="mb-4">
                <?php if (isset($component)) { $__componentOriginald8ba2b4c22a13c55321e34443c386276 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginald8ba2b4c22a13c55321e34443c386276 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.label','data' => ['value' => 'Nombre:']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('label'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['value' => 'Nombre:']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginald8ba2b4c22a13c55321e34443c386276)): ?>
<?php $attributes = $__attributesOriginald8ba2b4c22a13c55321e34443c386276; ?>
<?php unset($__attributesOriginald8ba2b4c22a13c55321e34443c386276); ?>
<?php endif; ?>
<?php if (isset($__componentOriginald8ba2b4c22a13c55321e34443c386276)): ?>
<?php $component = $__componentOriginald8ba2b4c22a13c55321e34443c386276; ?>
<?php unset($__componentOriginald8ba2b4c22a13c55321e34443c386276); ?>
<?php endif; ?>
                <?php if (isset($component)) { $__componentOriginalc2fcfa88dc54fee60e0757a7e0572df1 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalc2fcfa88dc54fee60e0757a7e0572df1 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.input','data' => ['wire:model' => 'name','type' => 'text','class' => 'w-full']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('input'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['wire:model' => 'name','type' => 'text','class' => 'w-full']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalc2fcfa88dc54fee60e0757a7e0572df1)): ?>
<?php $attributes = $__attributesOriginalc2fcfa88dc54fee60e0757a7e0572df1; ?>
<?php unset($__attributesOriginalc2fcfa88dc54fee60e0757a7e0572df1); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalc2fcfa88dc54fee60e0757a7e0572df1)): ?>
<?php $component = $__componentOriginalc2fcfa88dc54fee60e0757a7e0572df1; ?>
<?php unset($__componentOriginalc2fcfa88dc54fee60e0757a7e0572df1); ?>
<?php endif; ?>
                <?php if (isset($component)) { $__componentOriginalf94ed9c5393ef72725d159fe01139746 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalf94ed9c5393ef72725d159fe01139746 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.input-error','data' => ['for' => 'name']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('input-error'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['for' => 'name']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalf94ed9c5393ef72725d159fe01139746)): ?>
<?php $attributes = $__attributesOriginalf94ed9c5393ef72725d159fe01139746; ?>
<?php unset($__attributesOriginalf94ed9c5393ef72725d159fe01139746); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalf94ed9c5393ef72725d159fe01139746)): ?>
<?php $component = $__componentOriginalf94ed9c5393ef72725d159fe01139746; ?>
<?php unset($__componentOriginalf94ed9c5393ef72725d159fe01139746); ?>
<?php endif; ?>
            </div>
            <div class="mb-4">
                <?php if (isset($component)) { $__componentOriginald8ba2b4c22a13c55321e34443c386276 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginald8ba2b4c22a13c55321e34443c386276 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.label','data' => ['value' => 'Permisos:']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('label'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['value' => 'Permisos:']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginald8ba2b4c22a13c55321e34443c386276)): ?>
<?php $attributes = $__attributesOriginald8ba2b4c22a13c55321e34443c386276; ?>
<?php unset($__attributesOriginald8ba2b4c22a13c55321e34443c386276); ?>
<?php endif; ?>
<?php if (isset($__componentOriginald8ba2b4c22a13c55321e34443c386276)): ?>
<?php $component = $__componentOriginald8ba2b4c22a13c55321e34443c386276; ?>
<?php unset($__componentOriginald8ba2b4c22a13c55321e34443c386276); ?>
<?php endif; ?>
                <!--[if BLOCK]><![endif]--><?php if(isset($permisos)): ?>
                    <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $permisos; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $permiso): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div class="flex items-center pl-3">
                            <input wire:model="selectedPermisos" id="<?php echo e($permiso->id . 'checkbox'); ?>" type="checkbox"
                                value="<?php echo e($permiso->name); ?>"
                                class="w-4 h-4 text-slate-600 bg-slate-100 border-gray-300 rounded outline-none  focus:ring-slate-600">
                            <label for="<?php echo e($permiso->id . 'checkbox'); ?>"
                                class="py-2 ml-2 text-sm font-medium text-gray-900 ">
                                <?php echo e($permiso->descripcion ? $permiso->descripcion : $permiso->name); ?>

                            </label>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
                <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
            </div>
            <?php if (isset($component)) { $__componentOriginalf94ed9c5393ef72725d159fe01139746 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalf94ed9c5393ef72725d159fe01139746 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.input-error','data' => ['for' => 'selectedPermisos']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('input-error'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['for' => 'selectedPermisos']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalf94ed9c5393ef72725d159fe01139746)): ?>
<?php $attributes = $__attributesOriginalf94ed9c5393ef72725d159fe01139746; ?>
<?php unset($__attributesOriginalf94ed9c5393ef72725d159fe01139746); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalf94ed9c5393ef72725d159fe01139746)): ?>
<?php $component = $__componentOriginalf94ed9c5393ef72725d159fe01139746; ?>
<?php unset($__componentOriginalf94ed9c5393ef72725d159fe01139746); ?>
<?php endif; ?>
         <?php $__env->endSlot(); ?>

         <?php $__env->slot('footer', null, []); ?> 
            <?php if (isset($component)) { $__componentOriginal3b0e04e43cf890250cc4d85cff4d94af = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal3b0e04e43cf890250cc4d85cff4d94af = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.secondary-button','data' => ['wire:click' => '$set(\'editando\',false)','class' => 'mx-2']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('secondary-button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['wire:click' => '$set(\'editando\',false)','class' => 'mx-2']); ?>
                Cancelar
             <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal3b0e04e43cf890250cc4d85cff4d94af)): ?>
<?php $attributes = $__attributesOriginal3b0e04e43cf890250cc4d85cff4d94af; ?>
<?php unset($__attributesOriginal3b0e04e43cf890250cc4d85cff4d94af); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal3b0e04e43cf890250cc4d85cff4d94af)): ?>
<?php $component = $__componentOriginal3b0e04e43cf890250cc4d85cff4d94af; ?>
<?php unset($__componentOriginal3b0e04e43cf890250cc4d85cff4d94af); ?>
<?php endif; ?>
            <?php if (isset($component)) { $__componentOriginald0f1fd2689e4bb7060122a5b91fe8561 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginald0f1fd2689e4bb7060122a5b91fe8561 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.button','data' => ['wire:click' => 'actualizar','wire:loading.attr' => 'disabled','wire:target' => 'actualizar']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['wire:click' => 'actualizar','wire:loading.attr' => 'disabled','wire:target' => 'actualizar']); ?>
                Actualizar
             <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginald0f1fd2689e4bb7060122a5b91fe8561)): ?>
<?php $attributes = $__attributesOriginald0f1fd2689e4bb7060122a5b91fe8561; ?>
<?php unset($__attributesOriginald0f1fd2689e4bb7060122a5b91fe8561); ?>
<?php endif; ?>
<?php if (isset($__componentOriginald0f1fd2689e4bb7060122a5b91fe8561)): ?>
<?php $component = $__componentOriginald0f1fd2689e4bb7060122a5b91fe8561; ?>
<?php unset($__componentOriginald0f1fd2689e4bb7060122a5b91fe8561); ?>
<?php endif; ?>

         <?php $__env->endSlot(); ?>

     <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal49bd1c1dd878e22e0fb84faabf295a3f)): ?>
<?php $attributes = $__attributesOriginal49bd1c1dd878e22e0fb84faabf295a3f; ?>
<?php unset($__attributesOriginal49bd1c1dd878e22e0fb84faabf295a3f); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal49bd1c1dd878e22e0fb84faabf295a3f)): ?>
<?php $component = $__componentOriginal49bd1c1dd878e22e0fb84faabf295a3f; ?>
<?php unset($__componentOriginal49bd1c1dd878e22e0fb84faabf295a3f); ?>
<?php endif; ?>
</div>
<?php /**PATH C:\xampp\htdocs\Lifegas\resources\views/livewire/admin-roles.blade.php ENDPATH**/ ?>