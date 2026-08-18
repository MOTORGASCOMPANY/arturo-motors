<div>
    <?php if (isset($component)) { $__componentOriginal49bd1c1dd878e22e0fb84faabf295a3f = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal49bd1c1dd878e22e0fb84faabf295a3f = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.dialog-modal','data' => ['wire:model' => 'abierto']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('dialog-modal'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['wire:model' => 'abierto']); ?>
         <?php $__env->slot('title', null, []); ?> 
                <h3 class="text-xl font-bold">Recepción de Boleta</h3>
         <?php $__env->endSlot(); ?>

         <?php $__env->slot('content', null, []); ?> 
            <div class="space-y-4">
                <div class="bg-gray-50 p-2 rounded-xl border border-gray-100">
                    <p class="text-xs font-bold text-gray-400 uppercase mb-1">Empleado</p>
                    <p class="text-lg font-bold text-gray-800"><?php echo e(Auth::user()->name ?? null); ?></p>
                    <p class="text-sm text-gray-500 italic">Documento: <?php echo e(Auth::user()->dni ?? null); ?></p>
                </div>

                <div class="bg-indigo-50/50 p-6 rounded-xl border border-indigo-100 relative overflow-hidden">
                    <div class="flex items-start space-x-4 relative z-10">
                        <div class="pt-1">
                            <input type="checkbox" wire:model.live="confirmacion"
                                   class="w-5 h-5 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500 cursor-pointer">
                        </div>
                        <div>
                            <p class="font-bold text-indigo-900 text-base">
                                "Recibí conforme, acepto y autorizo mi firma digital."
                            </p>
                            <p class="text-sm text-indigo-700 mt-1">
                                Al marcar esta casilla, usted declara la conformidad de la boleta de pago recibida.
                            </p>
                        </div>
                    </div>
                    <i class="fas fa-file-signature absolute -right-4 -bottom-4 text-6xl text-indigo-100/50"></i>
                </div>
            </div>
         <?php $__env->endSlot(); ?>

         <?php $__env->slot('footer', null, []); ?> 
            <div class="flex space-x-3">
                <?php if (isset($component)) { $__componentOriginal3b0e04e43cf890250cc4d85cff4d94af = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal3b0e04e43cf890250cc4d85cff4d94af = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.secondary-button','data' => ['wire:click' => '$set(\'abierto\', false)']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('secondary-button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['wire:click' => '$set(\'abierto\', false)']); ?>
                    CANCELAR
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

                <button wire:click="procesarFirma" <?php if(!$confirmacion): echo 'disabled'; endif; ?>
                        class="px-6 py-2 bg-slate-800 hover:bg-slate-900 text-white rounded-lg font-bold transition disabled:opacity-50 disabled:cursor-not-allowed flex items-center shadow-lg">
                    <i class="fas fa-fingerprint mr-2"></i>
                    FIRMAR DIGITALMENTE
                </button>
            </div>
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
<?php /**PATH C:\xampp\htdocs\Arturo\resources\views/livewire/r-r-h-h/firmar-boleta.blade.php ENDPATH**/ ?>