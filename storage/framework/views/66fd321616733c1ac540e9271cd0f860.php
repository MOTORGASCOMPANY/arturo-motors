<div class="max-w-3xl mx-auto px-4 py-8">
    <div class="bg-gray-200 rounded-2xl shadow-sm border border-gray-300 overflow-hidden">

        <!-- Cabecera -->
        <div class="p-6 border-b border-gray-300 flex items-center justify-between flex-wrap gap-4">
            <div>
                <h2 class="text-xl font-bold text-gray-800">Evaluación técnica previa</h2>
                <p class="text-sm text-gray-600 mt-1">
                    <?php echo e($orden->cliente?->nombre); ?> <?php echo e($orden->cliente?->apellido); ?> —
                    <span class="font-semibold text-gray-800"><?php echo e($orden->vehiculo?->placa); ?></span> 
                    (<?php echo e($orden->vehiculo?->marca); ?> <?php echo e($orden->vehiculo?->modelo); ?>)
                </p>
            </div>
            
            <!-- Acciones rápidas de checklist -->
            <div class="flex items-center gap-2">
                <button type="button" wire:click="marcarTodo" class="text-xs font-medium text-emerald-800 bg-emerald-100 hover:bg-emerald-200 border border-emerald-300 px-2.5 py-1.5 rounded-lg transition cursor-pointer">
                    Marcar todo
                </button>
                <button type="button" wire:click="desmarcarTodo" class="text-xs font-medium text-gray-700 bg-white hover:bg-gray-100 border border-gray-300 px-2.5 py-1.5 rounded-lg transition cursor-pointer">
                    Desmarcar todo
                </button>
            </div>
        </div>

        <div class="p-6 space-y-6">
            <?php if (isset($component)) { $__componentOriginalf94ed9c5393ef72725d159fe01139746 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalf94ed9c5393ef72725d159fe01139746 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.input-error','data' => ['for' => 'general']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('input-error'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['for' => 'general']); ?>
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

            <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $gruposChecklist; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $grupo => $items): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div>
                    <h3 class="text-xs font-bold text-gray-600 uppercase tracking-wider mb-2"><?php echo e($grupo); ?></h3>
                    <div class="grid grid-cols-2 sm:grid-cols-3 gap-2">
                        <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $clave => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <label class="flex items-center gap-2 text-sm border rounded-lg px-3 py-2 cursor-pointer transition-colors
                                          <?php echo e(($checklist[$clave] ?? false) ? 'border-emerald-500 bg-emerald-50 text-emerald-900 font-medium shadow-xs' : 'border-gray-300 bg-white text-gray-700 hover:bg-gray-50'); ?>">
                                <input type="checkbox" wire:model.live="checklist.<?php echo e($clave); ?>"
                                       class="rounded text-emerald-600 focus:ring-emerald-500 border-gray-300">
                                <span class="select-none"><?php echo e($label); ?></span>
                            </label>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
                    </div>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->

            <div class="pt-2">
                <?php if (isset($component)) { $__componentOriginald8ba2b4c22a13c55321e34443c386276 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginald8ba2b4c22a13c55321e34443c386276 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.label','data' => ['for' => 'observaciones','value' => 'Observaciones (obligatorio si se rechaza)','class' => 'font-medium text-gray-700']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('label'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['for' => 'observaciones','value' => 'Observaciones (obligatorio si se rechaza)','class' => 'font-medium text-gray-700']); ?>
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
                <textarea wire:model="observaciones" rows="3"
                          class="w-full bg-white rounded-lg border-gray-300 text-sm focus:border-emerald-500 focus:ring-emerald-500 mt-1 shadow-xs"
                          placeholder="Ej: falta llanta de repuesto y el vehículo tiene fuga de aceite"></textarea>
                <?php if (isset($component)) { $__componentOriginalf94ed9c5393ef72725d159fe01139746 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalf94ed9c5393ef72725d159fe01139746 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.input-error','data' => ['for' => 'observaciones','class' => 'mt-1']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('input-error'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['for' => 'observaciones','class' => 'mt-1']); ?>
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
        </div>

        <!-- Botones de Acción -->
        <div class="p-6 border-t border-gray-300 flex justify-between gap-3">
            <button wire:click="guardarEvaluacion(false)" 
                    wire:loading.attr="disabled" 
                    type="button"
                    class="flex-1 bg-red-600 hover:bg-red-700 disabled:opacity-50 text-white font-semibold py-2.5 rounded-xl text-sm transition flex items-center justify-center gap-2 cursor-pointer">
                <span wire:loading.remove wire:target="guardarEvaluacion(false)">No apto</span>
                <span wire:loading wire:target="guardarEvaluacion(false)">Guardando...</span>
            </button>
            
            <button wire:click="guardarEvaluacion(true)" 
                    wire:loading.attr="disabled" 
                    type="button"
                    class="flex-1 bg-emerald-600 hover:bg-emerald-700 disabled:opacity-50 text-white font-semibold py-2.5 rounded-xl text-sm transition flex items-center justify-center gap-2 cursor-pointer">
                <span wire:loading.remove wire:target="guardarEvaluacion(true)">Apto para conversión</span>
                <span wire:loading wire:target="guardarEvaluacion(true)">Guardando...</span>
            </button>
        </div>
    </div>
</div><?php /**PATH C:\xampp\htdocs\arturo-motors\resources\views/livewire/conversiones/evaluar.blade.php ENDPATH**/ ?>