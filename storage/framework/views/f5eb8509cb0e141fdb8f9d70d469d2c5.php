<div wire:loading.class="opacity-50 pointer-events-none">
    <div class="max-w-5xl mx-auto py-12">
        <div class="bg-gray-200 p-8 rounded-xl w-full">
            
            <!-- Encabezado -->
            <div class="flex items-center justify-between pb-4 border-b border-gray-300 mb-6">
                <div>
                    <h2 class="text-gray-700 font-semibold text-2xl flex items-center gap-2">
                        <i class="fas fa-hand-holding-usd text-emerald-600"></i> Entrega y cobro de conversión
                    </h2>
                    <span class="text-xs text-gray-500">Orden de Servicio #<?php echo e($orden->id); ?></span>
                </div>
                <div>
                    <a href="<?php echo e(route('conversiones.entregas-pendientes')); ?>" 
                       class="bg-gray-500 hover:bg-gray-600 text-white text-xs font-semibold px-4 py-2 rounded-lg transition inline-flex items-center gap-2 shadow-sm">
                        <i class="fas fa-arrow-left"></i> Volver a pendientes
                    </a>
                </div>
            </div>

            <!--[if BLOCK]><![endif]--><?php if(!$completado): ?>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <!-- Resumen de la Orden -->
                    <div class="md:col-span-2 bg-white p-6 rounded-xl border border-gray-200 space-y-4 shadow-sm">
                        <h3 class="text-gray-700 font-semibold text-base border-b border-gray-100 pb-2 flex items-center gap-2">
                            <i class="fas fa-info-circle text-gray-400"></i> Detalle del servicio
                        </h3>

                        <div class="grid grid-cols-2 gap-4 text-sm">
                            <div>
                                <span class="text-xs text-gray-500 uppercase font-bold block">Cliente</span>
                                <span class="text-gray-800 font-medium"><?php echo e($orden->cliente->nombre); ?> <?php echo e($orden->cliente->apellido); ?></span>
                            </div>
                            <div>
                                <span class="text-xs text-gray-500 uppercase font-bold block">Vehículo</span>
                                <span class="text-gray-800 font-bold bg-gray-100 px-2 py-0.5 rounded border border-gray-200 inline-block">
                                    <?php echo e($orden->vehiculo->placa); ?>

                                </span>
                            </div>
                            <div>
                                <span class="text-xs text-gray-500 uppercase font-bold block">Servicio</span>
                                <span class="text-gray-800"><?php echo e($orden->service->nombre); ?></span>
                            </div>
                            <div>
                                <span class="text-xs text-gray-500 uppercase font-bold block">Fecha Fin Conversión</span>
                                <span class="text-gray-800">
                                    <?php echo e($orden->fecha_fin_conversion ? $orden->fecha_fin_conversion->format('d/m/Y H:i') : '—'); ?>

                                </span>
                            </div>
                        </div>

                        <!-- Monto total -->
                        <div class="mt-6 p-4 bg-emerald-50 border border-emerald-200 text-center rounded-xl">
                            <span class="text-xs uppercase tracking-wider text-emerald-700 font-bold block mb-1">Monto Total a Cobrar</span>
                            <div class="text-3xl font-black text-emerald-700">S/ <?php echo e(number_format($orden->precio_final, 2)); ?></div>
                        </div>
                    </div>

                    <!-- Formulario de Cobro -->
                    <div class="bg-white p-6 rounded-xl border border-gray-200 shadow-sm flex flex-col justify-between space-y-4">
                        <div class="space-y-4">
                            <h3 class="text-gray-700 font-semibold text-base border-b border-gray-100 pb-2 flex items-center gap-2">
                                <i class="fas fa-wallet text-gray-400"></i> Pago
                            </h3>

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
                        </div>

                        <button wire:click="procesarCobro" 
                                wire:loading.attr="disabled" 
                                type="button"
                                class="w-full bg-emerald-600 hover:bg-emerald-700 text-white font-semibold py-3 px-4 rounded-xl transition shadow-sm flex items-center justify-center gap-2">
                            <i class="fas fa-check-circle"></i> Confirmar cobro y entregar
                        </button>
                    </div>
                </div>
            <?php else: ?>
                <!-- Estado completado -->
                <div class="bg-white p-8 rounded-xl border border-gray-200 shadow-sm text-center space-y-4 max-w-lg mx-auto">
                    <div class="w-16 h-16 bg-emerald-100 text-emerald-600 rounded-full flex items-center justify-center mx-auto">
                        <i class="fas fa-check text-2xl"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-800">¡Entrega y cobro completados!</h3>
                    <p class="text-sm text-gray-600">
                        Comprobante generado: <span class="font-bold text-gray-800"><?php echo e($folioGenerado); ?></span>
                    </p>

                    <div class="pt-4 flex flex-col sm:flex-row gap-3 justify-center">
                        <a href="<?php echo e(route('comprobantes.pdf', $orden->id)); ?>" target="_blank"
                           class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2.5 rounded-lg text-sm font-semibold transition inline-flex items-center justify-center gap-2 shadow-sm">
                            <i class="fas fa-file-pdf"></i> Ver comprobante PDF
                        </a>
                        <a href="<?php echo e(route('conversiones.entregas-pendientes')); ?>"
                           class="bg-gray-200 hover:bg-gray-300 text-gray-700 px-5 py-2.5 rounded-lg text-sm font-semibold transition inline-flex items-center justify-center gap-2">
                            Volver a la lista
                        </a>
                    </div>
                </div>
            <?php endif; ?><!--[if ENDBLOCK]><![endif]-->

        </div>
    </div>
</div><?php /**PATH C:\xampp\htdocs\arturo-motors\resources\views/livewire/conversiones/entregar-cobrar.blade.php ENDPATH**/ ?>