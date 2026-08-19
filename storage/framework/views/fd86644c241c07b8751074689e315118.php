<div wire:loading.class="opacity-50 pointer-events-none" class="max-w-2xl mx-auto px-4 py-8">
    <div class="bg-gray-200 rounded-2xl shadow-md border border-gray-300 overflow-hidden">

        <!-- Header -->
        <div class="p-6 border-b border-gray-300/80 bg-gray-200">
            <h2 class="text-xl font-bold text-gray-900">Realizar conversión — Orden #<?php echo e($orden->id); ?></h2>
            <p class="text-sm text-gray-600 mt-1">
                <?php echo e($orden->cliente->nombre); ?> <?php echo e($orden->cliente->apellido); ?> —
                <span class="font-bold text-gray-800"><?php echo e($orden->vehiculo->placa); ?></span> 
                <span class="text-gray-500">(<?php echo e($orden->service->nombre); ?>)</span>
            </p>
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

            <!-- Equipos asignados -->
            <div>
                <h3 class="text-xs font-bold text-gray-600 uppercase tracking-wider mb-2">Equipos asignados</h3>
                <div class="space-y-1.5">
                    <!--[if BLOCK]><![endif]--><?php $__empty_1 = true; $__currentLoopData = $orden->items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <div class="flex justify-between items-center border border-gray-300/90 rounded-xl px-3.5 py-2.5 text-sm bg-gray-100 shadow-sm">
                            <span class="text-gray-800">
                                <strong class="font-semibold text-gray-900"><?php echo e($item->producto->categoria->nombre ?? 'N/A'); ?>:</strong> 
                                <?php echo e($item->producto->nombre ?? 'Producto'); ?> 
                                <span class="text-gray-500 text-xs font-mono ml-1">(Serie: <?php echo e($item->serie); ?>)</span>
                            </span>
                            <span class="px-2.5 py-0.5 rounded-full text-xs font-bold tracking-wide shadow-sm
                                <?php echo e($item->estado === 'instalado' ? 'bg-emerald-600 text-white' : 'bg-blue-600 text-white'); ?>">
                                <?php echo e(ucfirst($item->estado)); ?>

                            </span>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <div class="p-3 bg-gray-100 rounded-xl border border-gray-300/80 text-center">
                            <p class="text-sm text-gray-500 italic">No hay equipos registrados para esta orden.</p>
                        </div>
                    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                </div>
            </div>

            <!-- Estado del proceso -->
            <div class="p-4 bg-gray-100 rounded-xl border border-gray-300/90 space-y-3 shadow-sm">
                <div class="flex justify-between items-center text-sm">
                    <span class="text-gray-700 font-medium">Inicio de conversión</span>
                    <!--[if BLOCK]><![endif]--><?php if($orden->fecha_inicio_conversion): ?>
                        <span class="font-bold text-gray-900 bg-gray-200 px-2.5 py-1 rounded-lg border border-gray-300">
                            <?php echo e($orden->fecha_inicio_conversion->format('d/m/Y H:i')); ?>

                        </span>
                    <?php else: ?>
                        <button wire:click="iniciar" 
                                wire:loading.attr="disabled" 
                                type="button"
                                class="bg-purple-700 hover:bg-purple-800 text-white text-xs font-semibold px-3.5 py-2 rounded-lg transition-colors shadow-sm">
                            Iniciar conversión
                        </button>
                    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                </div>

                <div class="flex justify-between items-center text-sm">
                    <span class="text-gray-700 font-medium">Fin de conversión</span>
                    <!--[if BLOCK]><![endif]--><?php if($orden->fecha_fin_conversion): ?>
                        <span class="font-bold text-emerald-700 bg-emerald-100 px-2.5 py-1 rounded-lg border border-emerald-300/60">
                            <?php echo e($orden->fecha_fin_conversion->format('d/m/Y H:i')); ?>

                        </span>
                    <?php else: ?>
                        <span class="text-xs text-gray-500 font-medium italic">Pendiente</span>
                    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                </div>
            </div>

            <!-- Acciones principales -->
            <!--[if BLOCK]><![endif]--><?php if($orden->estado === 'en_conversion'): ?>
                <button wire:click="finalizar" 
                        wire:loading.attr="disabled" 
                        type="button"
                        class="w-full bg-emerald-600 hover:bg-emerald-700 active:bg-emerald-800 text-white font-bold py-3.5 rounded-xl transition-colors shadow-sm text-sm">
                    Finalizar conversión (pruebas OK)
                </button>
            <?php else: ?>
                <div class="text-center p-4 bg-emerald-100/80 border border-emerald-300 text-emerald-900 rounded-xl text-sm font-semibold shadow-sm">
                    Conversión completada. Queda pendiente la entrega y cobro.
                </div>
            <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
        </div>
    </div>
</div><?php /**PATH C:\xampp\htdocs\arturo-motors\resources\views/livewire/conversiones/realizar.blade.php ENDPATH**/ ?>