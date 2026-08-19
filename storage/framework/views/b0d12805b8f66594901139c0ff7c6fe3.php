<div class="max-w-md mx-auto px-4 py-8 my-6 lg:my-10">
    <div class="bg-gray-200 rounded-2xl shadow-sm border border-gray-300/80 overflow-hidden transition-all">
        <!-- Encabezado del Tarjetón -->
        <div class="p-6 border-b border-gray-300/70">
            <h2 class="text-xl font-bold tracking-tight text-gray-800 flex items-center gap-2.5">
                <i class="fas fa-lock text-red-600"></i>
                Cierre de Caja
            </h2>
            <p class="text-xs text-gray-600 mt-1">Arqueo y finalización del turno actual de caja.</p>
        </div>

        <!-- Contenido principal -->
        <div class="p-6 bg-white/60">
            <!--[if BLOCK]><![endif]--><?php if(!$sesion): ?>
                <!-- ESTADO: SIN CAJA ABIERTA -->
                <div class="text-center space-y-3 py-4">
                    <div class="w-16 h-16 bg-gray-300/60 text-gray-500 rounded-full flex items-center justify-center mx-auto ring-8 ring-gray-200/50">
                        <i class="fas fa-inbox text-2xl"></i>
                    </div>
                    <div class="space-y-1">
                        <h3 class="text-base font-bold text-gray-700">Sin Caja Activa</h3>
                        <p class="text-xs text-gray-500">No hay ninguna sesión de caja abierta para realizar el arqueo o cierre.</p>
                    </div>
                </div>
            <?php else: ?>
                <!-- DESGLOSE DE MONTOS Y FORMULARIO -->
                <form wire:submit.prevent="cerrar" class="space-y-5">
                    <!-- Cuadro Resumen de Caja -->
                    <div class="bg-white border border-gray-300/70 rounded-xl p-4 space-y-2.5 shadow-xs">
                        <div class="flex justify-between items-center text-xs">
                            <span class="text-gray-600 font-medium"><i class="fas fa-vault text-gray-400 mr-1.5"></i> Monto de Apertura:</span>
                            <span class="font-semibold text-gray-800">S/ <?php echo e(number_format($sesion->monto_apertura, 2)); ?></span>
                        </div>
                        
                        <div class="flex justify-between items-center text-xs border-t border-gray-100 pt-2">
                            <span class="text-emerald-700 font-medium"><i class="fas fa-arrow-down text-emerald-500 mr-1.5"></i> + Ingresos:</span>
                            <span class="font-semibold text-emerald-700">S/ <?php echo e(number_format($this->totalIngresos, 2)); ?></span>
                        </div>
                        
                        <div class="flex justify-between items-center text-xs border-t border-gray-100 pt-2">
                            <span class="text-red-700 font-medium"><i class="fas fa-arrow-up text-red-500 mr-1.5"></i> − Egresos:</span>
                            <span class="font-semibold text-red-700">S/ <?php echo e(number_format($this->totalEgresos, 2)); ?></span>
                        </div>
                        
                        <div class="flex justify-between items-center text-xs border-t-2 border-gray-200 pt-2.5 mt-1 font-bold">
                            <span class="text-gray-800"><i class="fas fa-calculator text-blue-600 mr-1.5"></i> Monto Esperado:</span>
                            <span class="text-sm text-blue-700">S/ <?php echo e(number_format($this->montoEsperado, 2)); ?></span>
                        </div>
                    </div>

                    <!-- Input Monto Real Contado -->
                    <div>
                        <?php if (isset($component)) { $__componentOriginald8ba2b4c22a13c55321e34443c386276 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginald8ba2b4c22a13c55321e34443c386276 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.label','data' => ['for' => 'montoCierre','value' => 'Monto Real Contado en Caja (S/)','class' => 'text-gray-700 font-medium mb-1.5 text-xs uppercase tracking-wider']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('label'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['for' => 'montoCierre','value' => 'Monto Real Contado en Caja (S/)','class' => 'text-gray-700 font-medium mb-1.5 text-xs uppercase tracking-wider']); ?>
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
                        
                        <div class="relative rounded-xl shadow-xs">
                            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-gray-400 font-semibold text-sm">
                                S/
                            </div>
                            <?php if (isset($component)) { $__componentOriginalc2fcfa88dc54fee60e0757a7e0572df1 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalc2fcfa88dc54fee60e0757a7e0572df1 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.input','data' => ['id' => 'montoCierre','type' => 'number','step' => '0.01','wire:model' => 'montoCierre','class' => 'w-full pl-9 pr-4 py-2.5 bg-white border-gray-300 focus:border-red-500 focus:ring-red-500 rounded-xl text-gray-900 font-semibold text-base','placeholder' => '0.00']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('input'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['id' => 'montoCierre','type' => 'number','step' => '0.01','wire:model' => 'montoCierre','class' => 'w-full pl-9 pr-4 py-2.5 bg-white border-gray-300 focus:border-red-500 focus:ring-red-500 rounded-xl text-gray-900 font-semibold text-base','placeholder' => '0.00']); ?>
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
                        </div>

                        <?php if (isset($component)) { $__componentOriginalf94ed9c5393ef72725d159fe01139746 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalf94ed9c5393ef72725d159fe01139746 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.input-error','data' => ['for' => 'montoCierre','class' => 'mt-1.5']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('input-error'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['for' => 'montoCierre','class' => 'mt-1.5']); ?>
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
                        <?php if (isset($component)) { $__componentOriginalf94ed9c5393ef72725d159fe01139746 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalf94ed9c5393ef72725d159fe01139746 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.input-error','data' => ['for' => 'general','class' => 'mt-1.5']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('input-error'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['for' => 'general','class' => 'mt-1.5']); ?>
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

                    <!-- Botón Cierre de Caja -->
                    <div class="pt-2">
                        <?php if (isset($component)) { $__componentOriginald0f1fd2689e4bb7060122a5b91fe8561 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginald0f1fd2689e4bb7060122a5b91fe8561 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.button','data' => ['type' => 'submit','wire:loading.attr' => 'disabled','class' => 'w-full justify-center bg-red-600 hover:bg-red-700 active:bg-red-800 text-white font-medium py-3 rounded-xl shadow-xs transition-all text-sm']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['type' => 'submit','wire:loading.attr' => 'disabled','class' => 'w-full justify-center bg-red-600 hover:bg-red-700 active:bg-red-800 text-white font-medium py-3 rounded-xl shadow-xs transition-all text-sm']); ?>
                            <i class="fas fa-lock mr-2" wire:loading.remove wire:target="cerrar"></i>
                            <span wire:loading.remove wire:target="cerrar">Cerrar Caja Ahora</span>
                            <span wire:loading wire:target="cerrar">Procesando Cierre...</span>
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
                    </div>
                </form>
            <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
        </div>
    </div>
</div><?php /**PATH C:\xampp\htdocs\arturo-motors\resources\views/livewire/caja/cerrar-caja.blade.php ENDPATH**/ ?>