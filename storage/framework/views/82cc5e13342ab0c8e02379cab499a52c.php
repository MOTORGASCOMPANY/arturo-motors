<div wire:loading.class="opacity-50 pointer-events-none">
    <div class="container mx-auto py-12">
        <div class="bg-gray-200 p-8 rounded-xl w-full">
            <div class="items-center pb-6 md:block sm:block">
                <!-- Titulo y subtitulo -->
                <div class="px-2 w-full mb-4">
                    <h2 class="text-gray-600 font-semibold text-2xl">
                        <i class="fas fa-tools mr-2"></i>Mis conversiones
                    </h2>
                    <span class="text-xs text-gray-500">Vehículos asignados para evaluación e instalación</span>
                </div>
                <!-- Filtros -->
                <div class="w-full flex flex-wrap items-center justify-between gap-4">
                    <!-- Filtro Estado -->
                    <div class="flex items-center bg-white border border-gray-300 p-2 rounded-lg shadow-sm">
                        <?php if (isset($component)) { $__componentOriginald8ba2b4c22a13c55321e34443c386276 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginald8ba2b4c22a13c55321e34443c386276 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.label','data' => ['class' => 'mr-2','value' => 'Estado']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('label'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'mr-2','value' => 'Estado']); ?>
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
                        <select wire:model.live="estado"
                            class="border-none bg-transparent text-gray-700 text-sm focus:ring-0 focus:outline-none cursor-pointer">
                            <option value="pendientes">Solo pendientes</option>
                            <option value="todas">Todas (incluye finalizadas)</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Tabla -->
            <!--[if BLOCK]><![endif]--><?php if($ordenes->count()): ?>
                <div class="overflow-x-auto">
                    <table class="min-w-full leading-normal rounded-md overflow-hidden">
                        <thead>
                            <tr>
                                <th
                                    class="px-4 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase">
                                    Fecha
                                </th>
                                <th
                                    class="px-4 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase">
                                    Cliente
                                </th>
                                <th
                                    class="px-4 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase">
                                    Vehículo
                                </th>
                                <th
                                    class="px-4 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase">
                                    Servicio
                                </th>
                                <th
                                    class="px-4 py-3 border-b-2 border-gray-200 bg-gray-100 text-center text-xs font-semibold text-gray-600 uppercase">
                                    Estado
                                </th>
                                <th
                                    class="px-4 py-3 border-b-2 border-gray-200 bg-gray-100 text-right text-xs font-semibold text-gray-600 uppercase">
                                    Acciones
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $ordenes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $orden): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <tr wire:key="mis-conversion-<?php echo e($orden->id); ?>">
                                    <td class="px-4 py-3 border-b border-gray-200 bg-white text-sm whitespace-nowrap">
                                        <?php echo e($orden->created_at->format('d/m/Y')); ?>

                                    </td>
                                    <td class="px-4 py-3 border-b border-gray-200 bg-white text-sm">
                                        <?php echo e($orden->cliente->nombre); ?> <?php echo e($orden->cliente->apellido); ?>

                                    </td>
                                    <td class="px-4 py-3 border-b border-gray-200 bg-white text-sm whitespace-nowrap">
                                        <span class="font-bold text-gray-800"><?php echo e($orden->vehiculo->placa); ?></span> — <?php echo e($orden->vehiculo->marca); ?>

                                    </td>
                                    <td class="px-4 py-3 border-b border-gray-200 bg-white text-sm">
                                        <?php echo e($orden->service->nombre); ?>

                                    </td>
                                    <td class="px-4 py-3 text-center border-b border-gray-200 bg-white text-sm whitespace-nowrap">
                                        <span
                                            class="px-2 py-1 rounded-full text-xs font-semibold
                                            <?php echo e(match ($orden->estado) {
                                                'en_evaluacion' => 'bg-amber-100 text-amber-700',
                                                'aprobado_conversion' => 'bg-blue-100 text-blue-700',
                                                'en_conversion' => 'bg-purple-100 text-purple-700',
                                                'conversion_completada' => 'bg-emerald-100 text-emerald-700',
                                                'evaluacion_rechazada', 'cancelado' => 'bg-red-100 text-red-700',
                                                default => 'bg-gray-100 text-gray-600',
                                            }); ?>">
                                            <?php echo e(ucfirst(str_replace('_', ' ', $orden->estado))); ?>

                                        </span>
                                    </td>
                                    <td class="px-4 py-3 text-right border-b border-gray-200 bg-white text-sm whitespace-nowrap">
                                        <!--[if BLOCK]><![endif]--><?php switch($orden->estado):
                                            case ('en_evaluacion'): ?>
                                                <a href="<?php echo e(route('conversiones.evaluar', $orden->id)); ?>"
                                                    class="bg-amber-600 hover:bg-amber-700 text-white text-xs font-semibold px-3 py-1.5 rounded-lg transition inline-block shadow-sm">
                                                    <i class="fas fa-clipboard-check mr-1"></i> Evaluar
                                                </a>
                                            <?php break; ?>

                                            <?php case ('aprobado_conversion'): ?>
                                                <span class="text-xs text-gray-500 font-medium bg-gray-100 px-2.5 py-1 rounded border border-gray-200">
                                                    <i class="fas fa-clock mr-1 text-gray-400"></i> Esperando equipos
                                                </span>
                                            <?php break; ?>

                                            <?php case ('en_conversion'): ?>
                                                <a href="<?php echo e(route('conversiones.realizar', $orden->id)); ?>"
                                                    class="bg-purple-600 hover:bg-purple-700 text-white text-xs font-semibold px-3 py-1.5 rounded-lg transition inline-block shadow-sm">
                                                    <i class="fas fa-wrench mr-1"></i> Continuar
                                                </a>
                                            <?php break; ?>

                                            <?php case ('conversion_completada'): ?>
                                                <span class="text-xs text-emerald-600 font-medium">
                                                    <i class="fas fa-check-circle mr-1"></i> Completada
                                                </span>
                                            <?php break; ?>

                                            <?php default: ?>
                                                <span class="text-xs text-gray-400">—</span>
                                        <?php endswitch; ?><!--[if ENDBLOCK]><![endif]-->
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
                
                <div class="px-6 py-8 text-center font-semibold bg-indigo-50 text-indigo-700 rounded-lg border border-indigo-100 shadow-sm">
                    <i class="fas fa-inbox text-3xl mb-2 block text-indigo-400"></i>
                    No tienes conversiones asignadas actualmente.
                </div>
            <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
        </div>
    </div>
</div>
<?php /**PATH C:\xampp\htdocs\Arturo\resources\views/livewire/conversiones/mis-asignadas.blade.php ENDPATH**/ ?>