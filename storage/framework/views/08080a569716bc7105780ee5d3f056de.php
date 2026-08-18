<div>
    <!--[if BLOCK]><![endif]--><?php if(!$completado): ?>
        <div class="bg-white p-6 rounded-xl border border-gray-200 shadow-sm space-y-4">
            <h3 class="text-gray-700 font-semibold text-base border-b border-gray-100 pb-2 flex items-center gap-2">
                <i class="fas fa-wallet text-gray-400"></i> Detalle del Pago
            </h3>

            <div class="p-4 bg-emerald-50 border border-emerald-200 text-center rounded-xl">
                <span class="text-xs uppercase tracking-wider text-emerald-700 font-bold block mb-1">Monto Total a Cobrar</span>
                <div class="text-3xl font-black text-emerald-700">S/ <?php echo e(number_format($monto, 2)); ?></div>
            </div>

            <div>
                <?php if (isset($component)) { $__componentOriginald8ba2b4c22a13c55321e34443c386276 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginald8ba2b4c22a13c55321e34443c386276 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.label','data' => ['for' => 'metodoPago','value' => 'Método de pago','class' => 'font-semibold text-gray-700 mb-1']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('label'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['for' => 'metodoPago','value' => 'Método de pago','class' => 'font-semibold text-gray-700 mb-1']); ?>
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
                <select wire:model="metodoPago" id="metodoPago" 
                        class="w-full rounded-lg border-gray-300 text-sm focus:border-emerald-500 focus:ring-emerald-500 shadow-sm">
                    <option value="efectivo">💵 Efectivo</option>
                    <option value="tarjeta">💳 Tarjeta (Débito/Crédito)</option>
                    <option value="transferencia">📲 Transferencia / Yape / Plin</option>
                    <option value="otro">Otros</option>
                </select>
                <?php if (isset($component)) { $__componentOriginalf94ed9c5393ef72725d159fe01139746 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalf94ed9c5393ef72725d159fe01139746 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.input-error','data' => ['for' => 'metodoPago','class' => 'mt-1']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('input-error'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['for' => 'metodoPago','class' => 'mt-1']); ?>
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

            <?php if (isset($component)) { $__componentOriginalf94ed9c5393ef72725d159fe01139746 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalf94ed9c5393ef72725d159fe01139746 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.input-error','data' => ['for' => 'caja','class' => 'mt-1']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('input-error'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['for' => 'caja','class' => 'mt-1']); ?>
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

            <button wire:click="procesar" 
                    wire:loading.attr="disabled" 
                    type="button"
                    class="w-full bg-emerald-600 hover:bg-emerald-700 text-white font-semibold py-3 px-4 rounded-xl transition shadow-sm flex items-center justify-center gap-2">
                <i class="fas fa-check-circle"></i> Confirmar cobro
            </button>
        </div>
    <?php else: ?>
        <div class="bg-white p-8 rounded-xl border border-gray-200 shadow-sm text-center space-y-4 max-w-lg mx-auto">
            <div class="w-16 h-16 bg-emerald-100 text-emerald-600 rounded-full flex items-center justify-center mx-auto">
                <i class="fas fa-check text-2xl"></i>
            </div>
            <h3 class="text-xl font-bold text-gray-800">¡Cobro completado!</h3>
            <p class="text-sm text-gray-600">
                Comprobante generado: <span class="font-bold text-gray-800"><?php echo e($folioGenerado); ?></span>
            </p>

            <!--[if BLOCK]><![endif]--><?php if($orden): ?>
                <div class="pt-4 flex flex-col sm:flex-row gap-3 justify-center">
                    <a href="<?php echo e(route('comprobantes.pdf', $orden->id)); ?>" target="_blank"
                       class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2.5 rounded-lg text-sm font-semibold transition inline-flex items-center justify-center gap-2 shadow-sm">
                        <i class="fas fa-file-pdf"></i> Ver comprobante PDF
                    </a>
                </div>
            <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
        </div>
    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
</div><?php /**PATH C:\xampp\htdocs\Arturo\resources\views/livewire/procesar-cobro.blade.php ENDPATH**/ ?>