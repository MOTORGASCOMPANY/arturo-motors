<div wire:loading.attr="disabled">
    <div class="container mx-auto py-12">
        <div class="bg-gray-200 p-8 rounded-xl w-full">
            <div class="items-center pb-6 md:block sm:block">
                <!-- Titulo y subtitulo -->
                <div class="px-2 w-full mb-4">
                    <h2 class="text-gray-600 font-semibold text-2xl">
                        <i class="fas fa-tools mr-2"></i>Órdenes de servicio
                    </h2>
                    <span class="text-xs">Todas las ordenes de servicio</span>
                </div>
                <!-- Filtros con el mismo estilo pero adaptables -->
                <div class="w-full flex flex-wrap items-center justify-between gap-4">
                    <!-- Buscar -->
                    <div class="flex bg-gray-50 items-center w-full lg:w-2/6 p-2 rounded-md">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400" viewBox="0 0 20 20"
                            fill="currentColor">
                            <path fill-rule="evenodd"
                                d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z"
                                clip-rule="evenodd" />
                        </svg>
                        <input class="bg-gray-50 outline-none block rounded-md border-indigo-500 w-full border-none focus:ring-0"
                            type="text" wire:model.live.debounce.400ms="buscar" placeholder="Nombre, documento o placa...">
                    </div>               
                    <!-- Fecha desde (Estilo Intacto) -->
                    <div class="flex items-center bg-white border border-gray-300 p-2 rounded-lg shadow-sm">
                        <?php if (isset($component)) { $__componentOriginald8ba2b4c22a13c55321e34443c386276 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginald8ba2b4c22a13c55321e34443c386276 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.label','data' => ['class' => 'mr-2','value' => 'Desde']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('label'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'mr-2','value' => 'Desde']); ?>
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
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.input','data' => ['type' => 'date','wire:model.live' => 'desde','class' => 'w-full border-gray-300 rounded-lg shadow-sm focus:ring-0 text-sm transition']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('input'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['type' => 'date','wire:model.live' => 'desde','class' => 'w-full border-gray-300 rounded-lg shadow-sm focus:ring-0 text-sm transition']); ?>
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
                    <!-- Fecha hasta (Estilo Intacto) -->
                    <div class="flex items-center bg-white border border-gray-300 p-2 rounded-lg shadow-sm">
                        <?php if (isset($component)) { $__componentOriginald8ba2b4c22a13c55321e34443c386276 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginald8ba2b4c22a13c55321e34443c386276 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.label','data' => ['class' => 'mr-2','value' => 'Hasta']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('label'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'mr-2','value' => 'Hasta']); ?>
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
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.input','data' => ['type' => 'date','wire:model.live' => 'hasta','class' => 'w-full border-gray-300 rounded-lg shadow-sm focus:ring-0 text-sm transition']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('input'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['type' => 'date','wire:model.live' => 'hasta','class' => 'w-full border-gray-300 rounded-lg shadow-sm focus:ring-0 text-sm transition']); ?>
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
                    <!-- Tipos (Estilo Intacto) -->
                    <div class="flex items-center bg-white border border-gray-300 p-2 rounded-lg shadow-sm">
                        <?php if (isset($component)) { $__componentOriginald8ba2b4c22a13c55321e34443c386276 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginald8ba2b4c22a13c55321e34443c386276 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.label','data' => ['class' => 'mr-2','value' => 'Tipo']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('label'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'mr-2','value' => 'Tipo']); ?>
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
                        <select wire:model.live="tipo" class="border-none bg-transparent text-gray-700 text-sm focus:ring-0 focus:outline-none cursor-pointer">
                            <option value="todos">Todos</option>
                            <option value="simple">Simple</option>
                            <option value="conversion">Conversión</option>
                        </select>
                        <button wire:click="limpiarFiltros" type="button" class="text-xs text-gray-500 hover:text-indigo-800 font-bold ml-2 underline whitespace-nowrap">
                            Limpiar filtros
                        </button>
                    </div>
                    <!-- Boton crear (Estilo Intacto) -->
                    <div>
                        <a href="<?php echo e(route('ordenes.simple.crear')); ?>" class="bg-indigo-500 px-5 py-3 rounded-md text-white font-semibold tracking-wide cursor-pointer hover:bg-indigo-600 transition inline-block">
                            Nueva orden &nbsp;<i class="fas fa-plus"></i>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Tabla -->
            <!--[if BLOCK]><![endif]--><?php if($ordenes->count()): ?>
                <div class="overflow-x-auto">
                    <table class="min-w-full leading-normal rounded-md overflow-hidden">
                        <thead>
                            <tr>
                                <th class="px-4 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase">
                                    Fecha
                                </th>
                                <th class="px-4 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase">
                                    Folio
                                </th>
                                <th class="px-4 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase">
                                    Cliente
                                </th>
                                <th class="px-4 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase">
                                    Vehículo
                                </th>
                                <th class="px-4 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase">
                                    Servicio
                                </th>
                                <th class="px-4 py-3 border-b-2 border-gray-200 bg-gray-100 text-right text-xs font-semibold text-gray-600 uppercase">
                                    Monto
                                </th>
                                <th class="px-4 py-3 border-b-2 border-gray-200 bg-gray-100 text-center text-xs font-semibold text-gray-600 uppercase">
                                    Estado
                                </th>
                                <th class="px-4 py-3 border-b-2 border-gray-200 bg-gray-100"></th>
                            </tr>
                        </thead>
                        <tbody>
                            <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $ordenes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $orden): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <tr>
                                    <td class="px-4 py-3 border-b border-gray-200 bg-white text-sm">
                                        <?php echo e($orden->created_at->format('d/m/Y H:i')); ?>

                                    </td>
                                    <td class="px-4 py-3 font-medium border-b border-gray-200 bg-white text-sm">
                                        <?php echo e($orden->comprobante->folio ?? '—'); ?>

                                    </td>
                                    <td class="px-4 py-3 border-b border-gray-200 bg-white text-sm">
                                        <?php echo e($orden->cliente->nombre); ?> <?php echo e($orden->cliente->apellido); ?>

                                    </td>
                                    <td class="px-4 py-3 border-b border-gray-200 bg-white text-sm">
                                        <?php echo e($orden->vehiculo->placa); ?>

                                    </td>
                                    <td class="px-4 py-3 border-b border-gray-200 bg-white text-sm">
                                        <?php echo e($orden->service->nombre); ?>

                                        <!--[if BLOCK]><![endif]--><?php if($orden->service->tipo === 'conversion'): ?>
                                            <span
                                                class="ml-1 text-xs bg-purple-100 text-purple-700 px-2 py-0.5 rounded-full">Conversión</span>
                                        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                    </td>
                                    <td class="px-4 py-3 text-right font-semibold border-b border-gray-200 bg-white text-sm">
                                        S/ <?php echo e(number_format($orden->precio_final, 2)); ?>

                                    </td>
                                    <td class="px-4 py-3 text-center border-b border-gray-200 bg-white text-sm">
                                        <span class="px-2 py-1 rounded-full text-xs font-semibold
                                                <?php echo e(match (true) {
                                                    str_contains($orden->estado, 'cancel') || str_contains($orden->estado, 'rechaz') => 'bg-red-100 text-red-700',
                                                    in_array($orden->estado, ['entregada', 'entregado']) => 'bg-emerald-100 text-emerald-700',
                                                    default => 'bg-amber-100 text-amber-700',
                                                }); ?>">
                                            <?php echo e(ucfirst(str_replace('_', ' ', $orden->estado))); ?>

                                        </span>
                                    </td>
                                    
                                    <td class="px-4 py-4 border-b border-gray-200 bg-white text-sm text-right whitespace-nowrap">
                                        <div class="inline-flex items-center justify-end gap-1.5">
                                            <!-- Botón 1: Ver detalles -->
                                            <div class="relative inline-block group">
                                                <a href="<?php echo e(route('ordenes.detalle', $orden->id)); ?>"
                                                class="inline-flex items-center justify-center w-8 h-8 text-indigo-700 bg-indigo-50 hover:bg-indigo-100 hover:text-indigo-900 rounded-lg transition-colors duration-150">
                                                    <i class="fa-solid fa-eye text-xs"></i>
                                                </a>                                                
                                                <!-- Tooltip 1 -->
                                                <div class="absolute bottom-full mb-2 left-1/2 -translate-x-1/2 hidden group-hover:flex flex-col items-center pointer-events-none z-10">
                                                    <span class="relative z-10 p-1.5 text-[10px] font-semibold leading-none text-white whitespace-nowrap bg-gray-800 rounded shadow-md">
                                                        Ver detalle
                                                    </span>
                                                    <div class="w-2 h-2 -mt-1 rotate-45 bg-gray-800"></div>
                                                </div>
                                            </div>
                                            <!-- Botón 2: Ver PDF -->
                                            <!--[if BLOCK]><![endif]--><?php if($orden->comprobante): ?>
                                                <div class="relative inline-block group">
                                                    <a href="<?php echo e(route('comprobantes.pdf', $orden->id)); ?>" target="_blank" rel="noopener noreferrer"
                                                        class="inline-flex items-center justify-center w-8 h-8 text-red-700 bg-red-50 hover:bg-red-100 hover:text-red-900 rounded-lg transition-colors duration-150">
                                                        <i class="fa-solid fa-file-pdf text-xs"></i>
                                                    </a>                                               
                                                    <!-- Tooltip 2 -->
                                                    <div class="absolute bottom-full mb-2 left-1/2 -translate-x-1/2 hidden group-hover:flex flex-col items-center pointer-events-none z-10">
                                                        <span class="relative z-10 p-1.5 text-[10px] font-semibold leading-none text-white whitespace-nowrap bg-gray-800 rounded shadow-md">
                                                            Ver PDF
                                                        </span>
                                                        <div class="w-2 h-2 -mt-1 rotate-45 bg-gray-800"></div>
                                                    </div>
                                                </div>
                                            <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
                        </tbody>
                    </table>
                </div>

                <div class="p-4 border-t border-gray-200/60">
                    <?php echo e($ordenes->links()); ?>

                </div>
            <?php else: ?>
                <div class="px-6 py-4 text-center font-bold bg-indigo-200 rounded-md">
                    No hay órdenes registradas.
                </div>
            <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
        </div>
    </div>
</div>
<?php /**PATH C:\xampp\htdocs\arturo-motors\resources\views/livewire/service-orders/listado.blade.php ENDPATH**/ ?>